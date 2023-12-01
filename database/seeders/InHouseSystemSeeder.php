<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InHouseSystem;

class InHouseSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InHouseSystem::firstOrCreate([
                    'id' => 1,
                    'system_name' => '５分日報',
                    'release_status' => 1,
                    'description' => '1日の終わりに今日の気付きや課題を日々振り返りしていきましょう。
                    明日からどう行動していくのか、どのように自分で課題解決をしていくのか考える事を習慣化していきましょう。
                    提出対象：拠点長及び当日の拠点締め報告者以外の社員全員
                    備考：個人目標がない方（管理社員の方 等）は、拠点目標と実績を入力してください',
                    'report_form_id' => 1,
                    'sequence' => 1,
                ],
        );
    }
}
