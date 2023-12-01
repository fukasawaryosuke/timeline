<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsReportFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_forms', function (Blueprint $table) {
            $table->string('company_code', 4)->after('required');
            $table->boolean('is_active')->default(true)->after('company_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_forms', function (Blueprint $table) {
            $table->dropColumn('company_code');
            $table->dropColumn('is_active');
        });
    }
}
