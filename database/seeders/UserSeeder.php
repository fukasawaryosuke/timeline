<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * 独自ログイン用のユーザ作成
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate([
            'system_role_id' => 1,
            'user_name' => '検証用 三',
            'mailadd' => 'test3@test.test',
            'division_cd' => 19924,
            'company_code' => '0002',
            'password' => 'human123',
        ]);
        $user->timelineHumanId();

        $user = User::firstOrCreate([
            'system_role_id' => 2,
            'user_name' => '検証用 四',
            'mailadd' => 'test4@test.test',
            'division_cd' => 19924,
            'company_code' => '0002',
            'password' => 'human123',
        ]);
        $user->timelineHumanId();
    }
}
