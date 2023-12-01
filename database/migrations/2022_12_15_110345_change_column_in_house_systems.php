<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInHouseSystems extends Migration
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
                ->string('description')
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
            $table
                ->string('description')
                ->nullable(false)
                ->change();
        });
    }
}
