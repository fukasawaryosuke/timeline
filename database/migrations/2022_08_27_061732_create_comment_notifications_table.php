
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateCommentNotificationsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("comment_notifications", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->unsignedBigInteger('user_id')->nullable()->unsigned();
						$table->unsignedBigInteger('comment_id')->nullable()->unsigned();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("user_id")->references("id")->on("users");
						//$table->foreign("comment_id")->references("id")->on("comments");



						// ----------------------------------------------------
						// -- SELECT [comment_notifications]--
						// ----------------------------------------------------
						// $query = DB::table("comment_notifications")
						// ->leftJoin("users","users.id", "=", "comment_notifications.user_id")
						// ->leftJoin("comments","comments.id", "=", "comment_notifications.comment_id")
						// ->get();
						// dd($query); //For checking



                });
            }

            /**
             * Reverse the migrations.
             *
             * @return void
             */
            public function down()
            {
                Schema::dropIfExists("comment_notifications");
            }
        }
