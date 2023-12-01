
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateDiariesNotificationsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("diary_notifications", function (Blueprint $table) {

						$table->unsignedBigInteger('diary_id')->nullable()->unsigned();
						$table->unsignedBigInteger('user_id')->nullable()->unsigned();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("diary_id")->references("id")->on("diaries");



						// ----------------------------------------------------
						// -- SELECT [diaries_notifications]--
						// ----------------------------------------------------
						// $query = DB::table("diaries_notifications")
						// ->leftJoin("diaries","diaries.id", "=", "diaries_notifications.diary_id")
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
                Schema::dropIfExists("diaries_notifications");
            }
        }
