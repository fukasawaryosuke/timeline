
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateVideosTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("videos", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->unsignedBigInteger('diary_id')->nullable()->unsigned();
						$table->tinyInteger('is_complete')->nullable();
						$table->json('original_field')->nullable();
						$table->json('task_field')->nullable();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("diary_id")->references("id")->on("diaries");



						// ----------------------------------------------------
						// -- SELECT [videos]--
						// ----------------------------------------------------
						// $query = DB::table("videos")
						// ->leftJoin("diaries","diaries.id", "=", "videos.diary_id")
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
                Schema::dropIfExists("videos");
            }
        }
