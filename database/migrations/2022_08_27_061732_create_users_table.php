
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateUsersTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("users", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->string('human_id',50);
						$table->unsignedBigInteger('system_role_id')->nullable();
						$table->string('user_name',200)->nullable();
						$table->string('mailadd',200)->nullable();
						$table->unsignedBigInteger('division_cd')->nullable()->unsigned();
						$table->timestamps();
						$table->softDeletes();
						//$table->foreign("system_role_id")->references("id")->on("system_roles");


						// ----------------------------------------------------
						// -- SELECT [users]--
						// ----------------------------------------------------
						// $query = DB::table("users")
						// ->leftJoin("system_roles","system_roles.id", "=", "users.system_role_id")
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
                Schema::dropIfExists("users");
            }
        }
