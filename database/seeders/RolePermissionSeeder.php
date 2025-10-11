<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // guard المستخدم في الـ API
        $guard = 'sanctum';

        // 1) إنشاء الأدوار (Idempotent)
        $adminRole  = Role::firstOrCreate(['name' => 'admin',  'guard_name' => $guard]);
        $lawyerRole = Role::firstOrCreate(['name' => 'lawyer', 'guard_name' => $guard]);
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => $guard]);

        // 2) صلاحيات (اختياري — أمثلة)
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'lawyers.view',
            'lawyers.approve',
            'lawyers.feature',

            'specialties.view',
            'specialties.create',
            'specialties.update',
            'specialties.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name'       => $perm,
                'guard_name' => $guard,
            ]);
        }

        // 3) ربط الصلاحيات بالأدوار (اختياري)
        $adminRole->syncPermissions($permissions);

        // مثال: صلاحيات محدودة للمحامي
        $lawyerRole->syncPermissions([
            'lawyers.view',
        ]);

        // مثال: العميل بدون صلاحيات نظامية
        $clientRole->syncPermissions([]);

        // 4) تنظيف كاش Spatie (مهم بعد التغييرات)
        Artisan::call('permission:cache-reset');
    }
}
