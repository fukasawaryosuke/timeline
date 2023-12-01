<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("organizations", function (Blueprint $table) {
            $table->unsignedInteger('level')
            ->default(1)
            ->index()
            ->after('organization_name');

            $table->unsignedInteger('display_order')
            ->default(1)
            ->after('level');

            $table->unsignedBigInteger('parent_division_cd')
            ->nullable()
            ->index()
            ->after('division_cd');

            $table->boolean('is_last_level')
            ->default(false)
            ->after('parent_division_cd');

            $table->dateTime('synced_at')
            ->nullable()
            ->after('in_house_system_id');

            $table->index('division_cd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'level',
                'display_order',
                'parent_division_cd',
                'is_last_level',
                'synced_at',
            ]);
            $table->dropIndex(['division_cd']);
        });
    }
}
