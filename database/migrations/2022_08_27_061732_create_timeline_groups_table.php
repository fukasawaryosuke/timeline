
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateTimelineGroupsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("timeline_groups", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->string('group_name',200)->nullable();
						$table->json('systems')->nullable();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("systems")->references("id")->on("in_house_systems");



						// ----------------------------------------------------
						// -- SELECT [timeline_groups]--
						// ----------------------------------------------------
						// $query = DB::table("timeline_groups")
						// ->leftJoin("in_house_systems","in_house_systems.id", "=", "timeline_groups.systems")
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
                Schema::dropIfExists("timeline_groups");
            }
        }
