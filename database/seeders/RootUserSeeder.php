<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RootUserSeeder extends Seeder
{
    /**
     * Seed the root user (parent to all)
     */
    public function run(): void
    {
        DB::table('tbl_users')->insert([
            'vchr_name' => 'Root Admin',
            'vchr_email' => 'root@system.com',
            'fk_bint_parent_id' => null, // Root has no parent
            'tim_created_at' => now(),
            'tim_updated_at' => now(),
        ]);
    }
}
