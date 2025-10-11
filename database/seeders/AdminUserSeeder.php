<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الجارد المستخدم مع الـ API
        $guard = 'sanctum';

        // 1) تأكيد وجود دور admin على نفس الجارد
        $adminRole = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => $guard,
        ]);

        // 2) إنشاء الأدمن الافتراضي (عدّلي الإيميل والباسورد هنا لو حابة)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // مفتاح التمييز
            [
                'name'     => 'Super Admin',
                'phone'    => '01000000000',
                'password' => Hash::make('Admin@123456'),
                'type'     => 'admin',
                'avatar'   => null,
            ]
        );

        // 3) ربط الدور
        // لو موديل User مضبوط على guard sanctum هيشتغل مباشرًا
        $admin->assignRole($adminRole);

        // 4) إعادة تهيئة كاش صلاحيات Spatie
        Artisan::call('permission:cache-reset');
    }
}
