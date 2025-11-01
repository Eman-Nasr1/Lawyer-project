<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * - يدعم role: client | lawyer
     * - لو lawyer: بيعمل سجل في جدول lawyers + يربط التخصصات (specialty_id أو specialties[])
     * - بيرجع token وبيانات المستخدم + علاقاته
     */

    public function register(Request $request)
    {
        // قواعد عامة
        $baseRules = [
            'name'     => ['required', 'string', 'max:190'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:50', 'unique:users,phone'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'     => ['nullable', 'in:lawyer,client,company'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        // قواعد المحامي
        $lawyerRules = [
            'professional_card_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'years_of_experience'     => ['nullable', 'integer', 'min:0', 'max:100'],
            'bio'                     => ['nullable', 'string'],
            'specialty_id'            => ['nullable', 'integer', 'exists:specialties,id'],
            'specialties'             => ['nullable', 'array', 'min:1'],
            'specialties.*'           => ['integer', 'exists:specialties,id'],
        ];

        // قواعد الشركة (بدون name/address/city/lat/lng)
        $companyRules = [
            'professional_card_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'years_of_experience'     => ['nullable', 'integer', 'min:0', 'max:100'],
            'description'    => ['nullable', 'string'],
            'specialty_id'            => ['nullable', 'integer', 'exists:specialties,id'],
            'specialties'             => ['nullable', 'array', 'min:1'],
            'specialties.*'           => ['integer', 'exists:specialties,id'],
        ];

        $role  = $request->input('role', 'client');
        $rules = $baseRules;
        if ($role === 'lawyer') {
            $rules = array_merge($rules, $lawyerRules);
        }
        if ($role === 'company') {
            $rules = array_merge($rules, $companyRules);
        }

        $data = $request->validate($rules);

        // رفع الملفات خارج الترانزاكشن
        $avatarPath = $cardPath = null;

        try {
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            if ($request->hasFile('professional_card_image')) {
                $cardPath = $request->file('professional_card_image')->store('lawyers/cards', 'public');
            }

            $user = DB::transaction(function () use ($data, $avatarPath, $cardPath, $role) {
                // 1) User
                $user = User::create([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'phone'    => $data['phone'] ?? null,
                    'password' => Hash::make($data['password']),
                    'type'     => $role,
                    'avatar'   => $avatarPath,
                ]);

                // 2) Spatie Role
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($role);
                }

                // 3) Lawyer
                if ($role === 'lawyer') {
                    $lawyer = Lawyer::create([
                        'user_id'                 => $user->id,
                        'professional_card_image' => $cardPath,
                        'years_of_experience'     => isset($data['years_of_experience']) ? (int)$data['years_of_experience'] : 0,
                        'bio'                     => $data['bio'] ?? null,
                    ]);

                    $specialtyIds = [];
                    if (!empty($data['specialties']) && is_array($data['specialties'])) {
                        $specialtyIds = $data['specialties'];
                    } elseif (!empty($data['specialty_id'])) {
                        $specialtyIds = [(int) $data['specialty_id']];
                    }
                    if (!empty($specialtyIds)) {
                        $lawyer->specialties()->sync($specialtyIds);
                    }
                }

                // 4) Company (بدون اسم/عنوان)
                if ($role === 'company') {
                    $company = Company::create([
                        'user_id'                 => $user->id,
                        'professional_card_image' => $cardPath,
                        'years_of_experience'     => isset($data['years_of_experience']) ? (int)$data['years_of_experience'] : 0,
                        'description'             => $data['description'] ?? null,
                        'is_approved'             => false,
                    ]);

                    $specialtyIds = [];
                    if (!empty($data['specialties']) && is_array($data['specialties'])) {
                        $specialtyIds = $data['specialties'];
                    } elseif (!empty($data['specialty_id'])) {
                        $specialtyIds = [(int) $data['specialty_id']];
                    }
                    if (!empty($specialtyIds)) {
                        $company->specialties()->sync($specialtyIds);
                    }
                }

                return $user;
            });
        } catch (\Throwable $e) {
            if ($avatarPath) Storage::disk('public')->delete($avatarPath);
            if ($cardPath)   Storage::disk('public')->delete($cardPath);
            throw $e;
        }

        $token = $user->createToken('api', ['*'])->plainTextToken;
        $user->load('roles', 'permissions', 'lawyer.specialties', 'company.specialties'); // العناوين هتتجاب من endpoints منفصلة

        try {
            $verification = \App\Http\Controllers\Api\PasswordOtpController::sendOtpFor($user, 'verify', 'email'); // أو 'phone'
            return response()->json([
                'status'  => 'pending_verification',
                'message' => 'Registration successful. Please verify using the OTP sent to your email.',
                'data'    => [
                    'user' => $user,
                    'verification' => $verification
                ]
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status'  => 'success_but_otp_delayed',
                'message' => $e->getMessage(),
                'data'    => ['user' => $user]
            ], 201);
        }
        return response()->json([
            'status' => 'success',
            'data'   => [
                'user'       => $user,
                'token'      => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Invalid credentials.'
            ], 422);
        }

        $token = $user->createToken('api', ['*'])->plainTextToken;

        $user->load('roles', 'permissions', 'lawyer.specialties', 'company.specialties');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user'       => $user,
                'token'      => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * GET /api/me
     * (يتطلب Bearer Token)
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles', 'permissions', 'lawyer.specialties', 'company.specialties');
        return response()->json([
            'status' => 'success',
            'data'   => ['user' => $user],
        ]);
    }

    /**
     * POST /api/logout
     * يحذف التوكن الحالي فقط
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out']);
    }

    /**
     * POST /api/logout-all
     * يحذف كل توكنات المستخدم (من كل الأجهزة)
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out from all devices']);
    }
}
