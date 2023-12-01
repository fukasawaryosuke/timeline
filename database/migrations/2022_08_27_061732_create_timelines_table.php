
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateTimelinesTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("timelines", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->unsignedBigInteger('group_id')->nullable()->unsigned();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("group_id")->references("id")->on("timeline_groups");



						// ----------------------------------------------------
						// -- SELECT [timelines]--
						// ----------------------------------------------------
						// $query = DB::table("timelines")
						// ->leftJoin("timeline_groups","timeline_groups.id", "=", "timelines.group_id")
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
                Schema::dropIfExists("timelines");
            }
        }
