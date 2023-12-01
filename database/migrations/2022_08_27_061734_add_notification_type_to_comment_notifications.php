<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationTypeToCommentNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_notifications', function (Blueprint $table) {
            $table->integer('notification_type')
            ->after('comment_id')
            ->default(1)
            ->comment('1=mention, 2=diary comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_notifications', function (Blueprint $table) {
            $table->dropColumn('notification_type');
        });
    }
}
