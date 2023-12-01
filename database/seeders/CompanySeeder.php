<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::firstOrCreate([
            'id' => 1,
            'company_code' => $this->generateCompanyCode(1),
            'company_name' => 'ヒューマンアカデミー株式会社',
            'company_icon_path' => '',
        ]);

        Company::firstOrCreate([
            'id' => 2,
            'company_code' => $this->generateCompanyCode(2),
            'company_name' => 'テスト株式会社',
            'company_icon_path' => '',
        ]);

    }

    private function generateCompanyCode($companyId)
    {
        return str_pad($companyId, 4, '0', STR_PAD_LEFT);
    }
}
