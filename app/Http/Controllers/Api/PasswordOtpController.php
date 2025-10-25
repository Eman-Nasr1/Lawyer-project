<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use App\Notifications\SendOtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\DB;

class PasswordOtpController extends Controller
{
    const CODE_LENGTH = 4;           // ✅ أربع أرقام
    const EXPIRES_MINUTES = 10;
    const TOKEN_EXPIRES_MINUTES = 15;
    const MAX_ATTEMPTS = 5;
    const RESEND_COOLDOWN_SECONDS = 60;

    // دالة مساعدة لإرسال OTP لأي غرض: reset | verify | login
    public static function sendOtpFor(User $user, string $purpose = 'reset', string $channel = 'email'): array
    {
        // كود 4 أرقام (مع leading zeros)
        $code = sprintf('%0'.self::CODE_LENGTH.'d', random_int(0, (10**self::CODE_LENGTH)-1));

        // تهدئة: لو لسه مُرسل خلال دقيقة
        $latest = Otp::where('user_id',$user->id)->where('purpose',$purpose)->latest()->first();
        if ($latest && $latest->created_at->diffInSeconds(now()) < self::RESEND_COOLDOWN_SECONDS) {
            $wait = self::RESEND_COOLDOWN_SECONDS - $latest->created_at->diffInSeconds(now());
            throw new \RuntimeException("Please wait {$wait} seconds before requesting another code.");
        }

        Otp::create([
            'user_id'   => $user->id,
            'channel'   => $channel,
            'purpose'   => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at'=> now()->addMinutes(self::EXPIRES_MINUTES),
        ]);

        // إرسال
        if ($channel === 'email') {
            $user->notify(new SendOtpCode($code, $purpose));
        } else {
            // SmsService::send($user->phone, "رمز التحقق: {$code}");
        }

        return [
            'channel' => $channel,
            'destination' => $channel === 'email'
                ? self::maskEmail($user->email)
                : self::maskPhone($user->phone),
            'expires_in_seconds' => self::EXPIRES_MINUTES * 60,
            'resend_cooldown'    => self::RESEND_COOLDOWN_SECONDS,
        ];
    }

    // 1) طلب كود لنسيت كلمة المرور
    public function forgot(Request $request)
    {
        $data = $request->validate([
            'email'   => 'nullable|email|exists:users,email',
            'phone'   => 'nullable|string|exists:users,phone',
            'channel' => 'nullable|in:email,phone',
        ]);
        $channel = $data['channel'] ?? 'email';
        $user = !empty($data['email'])
            ? User::where('email',$data['email'])->first()
            : User::where('phone',$data['phone'] ?? '')->first();

        if (!$user) return response()->json(['status'=>'fail','message'=>'User not found'], 422);

        $info = self::sendOtpFor($user, 'reset', $channel);

        return response()->json([
            'status'=>'success',
            'message'=>'OTP sent.',
            'data'=>$info
        ], 201);
    }

    // 2) التحقق من الكود (لـ reset أو verify)
    public function verify(Request $request)
    {
        $data = $request->validate([
            'email'   => 'nullable|email',
            'phone'   => 'nullable|string',
            'code'    => 'required|string|size:'.self::CODE_LENGTH, // ✅ 4 أرقام
            'purpose' => 'required|in:reset,verify',
        ]);

        $user = !empty($data['email'])
            ? User::where('email',$data['email'])->first()
            : User::where('phone',$data['phone'] ?? '')->first();

        if (!$user) return response()->json(['status'=>'fail','message'=>'User not found'], 422);

        $otp = Otp::where('user_id',$user->id)
            ->where('purpose',$data['purpose'])
            ->whereNull('consumed_at')
            ->latest()->first();

        if (!$otp) return response()->json(['status'=>'fail','message'=>'No OTP to verify.'], 422);
        if ($otp->isExpired()) return response()->json(['status'=>'fail','message'=>'OTP expired.'], 422);
        if ($otp->attempts >= self::MAX_ATTEMPTS) return response()->json(['status'=>'fail','message'=>'Too many attempts.'], 429);

        $otp->increment('attempts');

        if (!Hash::check($data['code'], $otp->code_hash)) {
            return response()->json(['status'=>'fail','message'=>'Invalid code.'], 422);
        }

        // نجاح
        $otp->consumed_at = now();
        $otp->otp_token = (string) Str::uuid();
        $otp->otp_token_expires_at = now()->addMinutes(self::TOKEN_EXPIRES_MINUTES);
        $otp->save();

        // لو الغرض verify فعّلي الحساب
        if ($data['purpose'] === 'verify') {
            if (!empty($data['email']) && is_null($user->email_verified_at)) {
                $user->forceFill(['email_verified_at'=>now()])->save();
            }
            if (!empty($data['phone']) && is_null($user->phone_verified_at)) {
                $user->forceFill(['phone_verified_at'=>now()])->save();
            }
        }

        return response()->json([
            'status'=>'success',
            'message'=>'OTP verified.',
            'data'=>[
                'otp_token'=>$otp->otp_token,
                'token_expires_in_seconds'=> self::TOKEN_EXPIRES_MINUTES * 60,
            ],
        ]);
    }

    // 3) إعادة إرسال
    public function resend(Request $request) { return $this->forgot($request); }

    // 4) تعيين كلمة مرور جديدة بعد التحقق
    public function reset(Request $request)
    {
        $data = $request->validate([
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string',
            'otp_token'=> 'required|string',
            'password' => ['required','confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $user = !empty($data['email'])
            ? User::where('email',$data['email'])->first()
            : User::where('phone',$data['phone'] ?? '')->first();

        if (!$user) return response()->json(['status'=>'fail','message'=>'User not found'], 422);

        $otp = Otp::where('user_id',$user->id)
            ->where('purpose','reset')
            ->where('otp_token',$data['otp_token'])
            ->latest()->first();

        if (!$otp) return response()->json(['status'=>'fail','message'=>'Invalid otp_token.'], 422);
        if ($otp->tokenExpired()) return response()->json(['status'=>'fail','message'=>'otp_token expired.'], 422);

        DB::transaction(function () use ($user,$data,$otp) {
            $user->update(['password'=>Hash::make($data['password'])]);
            $user->tokens()->delete(); // اختياري: تسجيل الخروج من كل الأجهزة
            $otp->otp_token_expires_at = now()->subMinute(); // يبطل استخدامه
            $otp->save();
        });

        return response()->json(['status'=>'success','message'=>'Password has been reset successfully.']);
    }

    // Helpers
    private static function maskEmail(string $email): string {
        [$n,$d] = explode('@',$email);
        $n = substr($n,0,1) . str_repeat('*', max(strlen($n)-2,1)) . substr($n,-1);
        return $n.'@'.$d;
    }
    private static function maskPhone(?string $p): string {
        if (!$p) return '';
        return str_repeat('*', max(strlen($p)-4,0)) . substr($p,-4);
    }
}
