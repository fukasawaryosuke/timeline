<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceInHouseSystems extends Migration
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
            $table
                ->integer('sequence')
                ->unsigned()
                ->after('report_form_id');
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
            $table->dropColumn('sequence');
        });
    }
}
