<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnUsersIsInitialPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //NOTE:userテーブルのヒューマンアカデミーの既存のis_initial_passwordカラムをfalseに変更
            $HumanAcademyCompanyCode = config('app.human_academy_company_code');
            User::where(function ($query) use ($HumanAcademyCompanyCode) {
                $query->where('company_code', $HumanAcademyCompanyCode)
                    ->orWhere('company_code', null) // company_codeがnullの場合も対象にする
                    ->orWhere('company_code', ''); // company_codeが空文字の場合も対象にする
            })
                ->update(['is_initial_password' => false]);

            $table->boolean('is_initial_password')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_initial_password')->default(true)->change();
        });
    }
}
