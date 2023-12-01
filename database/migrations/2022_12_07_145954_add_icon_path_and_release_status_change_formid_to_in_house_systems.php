<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIconPathAndReleaseStatusChangeFormidToInHouseSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_house_systems', function (Blueprint $table) {
            //
            $table->string('description')->after('system_name');
            $table
                ->string('icon_path')
                ->after('system_name')
                ->nullable();
            $table->boolean('release_status')->after('icon_path');
            $table
                ->unsignedBigInteger('report_form_id')
                ->nullable(true)
                ->change();
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
            $table->dropColumn('description');
            $table->dropColumn('icon_path');
            $table->dropColumn('release_status');
            $table
                ->unsignedBigInteger('report_form_id')
                ->nullable(false)
                ->change();
        });
    }
}
