<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToCommentNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_notifications', function (Blueprint $table) {
            $table->index('comment_id');
            $table->index('notification_type');
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
            $table->dropIndex('comment_notifications_comment_id_index');
            $table->dropIndex('comment_notifications_notification_type_index');
        });
    }
}
