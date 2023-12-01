
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateSystemRolePermissionsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("system_role_permissions", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->unsignedBigInteger('system_role_id')->nullable()->unsigned();
						$table->integer('permission_no')->nullable();
						$table->string('permission_name',20)->nullable();
						$table->tinyInteger('is_enable')->nullable();
						$table->timestamps();
						$table->softDeletes();

                    //*********************************
                    // Foreign KEY [ Uncomment if you want to use!! ]
                    //*********************************
                        //$table->foreign("system_role_id")->references("id")->on("system_roles");



						// ----------------------------------------------------
						// -- SELECT [system_role_permissions]--
						// ----------------------------------------------------
						// $query = DB::table("system_role_permissions")
						// ->leftJoin("system_roles","system_roles.id", "=", "system_role_permissions.system_role_id")
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
                Schema::dropIfExists("system_role_permissions");
            }
        }
