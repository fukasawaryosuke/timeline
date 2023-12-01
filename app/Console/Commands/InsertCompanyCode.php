<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\InHouseSystem;
use App\Models\ReportForm;
use App\Models\User;
use App\Models\Company;

class InsertCompanyCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insertCompanyCode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ヒューマンアカデミーの法人コードを付与';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo 'command start';

        $humanAcademyCompanyCode = config('app.human_academy_company_code');

        Company::create([
            'company_code' => $humanAcademyCompanyCode,
            'company_name' => 'ヒューマンアカデミー株式会社',
            'company_icon_path' => '',
            'is_active' => 1,
        ]);

        Organization::all()->each(function ($o) use ($humanAcademyCompanyCode) {
            $o->company_code = $humanAcademyCompanyCode;
            $o->save();
        });

        InHouseSystem::all()->each(function ($i) use ($humanAcademyCompanyCode) {
            $i->company_code = $humanAcademyCompanyCode;
            $i->save();
        });

        ReportForm::all()->each(function ($r) use ($humanAcademyCompanyCode) {
            $r->company_code = $humanAcademyCompanyCode;
            $r->save();
        });

        User::all()->each(function ($u) use ($humanAcademyCompanyCode) {
            $u->company_code = $humanAcademyCompanyCode;
            $u->save();
        });

        echo 'command end';
        return 0;
    }
}
