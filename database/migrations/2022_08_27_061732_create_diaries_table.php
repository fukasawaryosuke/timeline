
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateDiariesTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("diaries", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->json('diary')->nullable();
						$table->unsignedBigInteger('in_house_system_id')->nullable()->unsigned();
						$table->unsignedBigInteger('created_by')->nullable()->unsigned();
						$table->timestamps();
						$table->softDeletes();
						//$table->foreign("in_house_system_id")->references("id")->on("in_house_systems");
						//$table->foreign("created_by")->references("id")->on("users");

						// ----------------------------------------------------
						// -- SELECT [diaries]--
						// ----------------------------------------------------
						// $query = DB::table("diaries")
						// ->leftJoin("in_house_systems","in_house_systems.id", "=", "diaries.in_house_system_id")
						// ->leftJoin("users","users.id", "=", "diaries.created_at")
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
                Schema::dropIfExists("diaries");
            }
        }
