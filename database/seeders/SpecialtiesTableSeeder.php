<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtiesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('specialties')->insert([
            ['name' => 'جنائي', 'slug' => 'criminal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مدني', 'slug' => 'civil', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أحوال شخصية', 'slug' => 'family', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تجاري', 'slug' => 'commercial', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'عمال وشؤون إجتماعية', 'slug' => 'labor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'إداري', 'slug' => 'administrative', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ضرائب', 'slug' => 'tax', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'عقاري', 'slug' => 'real-estate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ملكية فكرية', 'slug' => 'ip', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'هجرة وجنسية', 'slug' => 'immigration', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تحكيم', 'slug' => 'arbitration', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
