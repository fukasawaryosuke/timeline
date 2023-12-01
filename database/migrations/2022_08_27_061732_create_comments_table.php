
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateCommentsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("comments", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->unsignedBigInteger('diary_id')->nullable()->unsigned();
						$table->string('comment', 2000);
						$table->unsignedBigInteger('created_by')->nullable()->unsigned();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("diary_id")->references("id")->on("diaries");
						//$table->foreign("created_by")->references("id")->on("users");

						// ----------------------------------------------------
						// -- SELECT [comments]--
						// ----------------------------------------------------
						// $query = DB::table("comments")
						// ->leftJoin("diaries","diaries.id", "=", "comments.diary_id")
						// ->leftJoin("users","users.id", "=", "comments.created_by")
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
                Schema::dropIfExists("comments");
            }
        }
