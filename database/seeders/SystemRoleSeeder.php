<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemRole;
use Illuminate\Support\Facades\DB;

class SystemRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemRole::firstOrCreate(['id' => 1, 'system_role_name' => 'general']);
        SystemRole::firstOrCreate(['id' => 2, 'system_role_name' => 'administrator']);
    }
}
