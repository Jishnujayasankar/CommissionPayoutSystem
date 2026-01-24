<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommissionLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['int_level' => 1, 'dec_percentage' => 10.00, 'bool_active' => true],
            ['int_level' => 2, 'dec_percentage' => 5.00, 'bool_active' => true],
            ['int_level' => 3, 'dec_percentage' => 3.00, 'bool_active' => true],
            ['int_level' => 4, 'dec_percentage' => 2.00, 'bool_active' => true],
            ['int_level' => 5, 'dec_percentage' => 1.00, 'bool_active' => true],
        ];

        foreach ($levels as $level) {
            DB::table('tbl_commission_levels')->insert([
                'int_level' => $level['int_level'],
                'dec_percentage' => $level['dec_percentage'],
                'bool_active' => $level['bool_active'],
                'tim_created_at' => now(),
                'tim_updated_at' => now(),
            ]);
        }
    }
}
