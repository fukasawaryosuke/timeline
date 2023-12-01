
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateSystemRolesTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("system_roles", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->string('system_role_name')->nullable();
						$table->timestamps();
						$table->softDeletes();


						// ----------------------------------------------------
						// -- SELECT [system_roles]--
						// ----------------------------------------------------
						// $query = DB::table("system_roles")
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
                Schema::dropIfExists("system_roles");
            }
        }
