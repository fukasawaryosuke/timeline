
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateInHouseSystemsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("in_house_systems", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->string('system_name',200)->nullable();
						$table->unsignedBigInteger('parent_id')->nullable()->unsigned();
						$table->unsignedBigInteger('division_cd')->nullable()->unsigned();
                        $table->softDeletes();
                        $table->timestamps();
						//$table->foreign("id")->references("id")->on("diary_templates");



						// ----------------------------------------------------
						// -- SELECT [in_house_systems]--
						// ----------------------------------------------------
						// $query = DB::table("in_house_systems")
						// ->leftJoin("diary_templates","diary_templates.id", "=", "in_house_systems.id")
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
                Schema::dropIfExists("in_house_systems");
            }
        }
