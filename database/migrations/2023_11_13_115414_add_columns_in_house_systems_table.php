<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInHouseSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_house_systems', function (Blueprint $table) {
            $table->string('company_code', 4)->after('sequence');
            $table->boolean('is_active')->default(true)->after('division_cd');
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
            $table->dropColumn('company_code');
            $table->dropColumn('is_active');
        });
    }
}
