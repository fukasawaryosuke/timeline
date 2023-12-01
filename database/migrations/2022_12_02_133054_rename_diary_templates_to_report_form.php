<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDiaryTemplatesToReportForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('diary_templates', 'report_forms');
        Schema::table('report_forms', function (Blueprint $table) {
            //
            $table
                ->string('name', 30)
                ->require()
                ->after('id');
            $table
                ->boolean('required')
                ->after('diary')
                ->default(false);
        });
        Schema::table('in_house_systems', function (Blueprint $table) {
            //
            $table
                ->unsignedBigInteger('report_form_id')
                ->after('system_name')
                ->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_house_systems', function (Blueprint $table) {
            //
            $table->dropColumn('report_form_id');
        });
        Schema::table('report_forms', function (Blueprint $table) {
            //
            $table->dropColumn('name');
            $table->dropColumn('required');
        });
        Schema::rename('report_forms', 'diary_templates');
    }
}
