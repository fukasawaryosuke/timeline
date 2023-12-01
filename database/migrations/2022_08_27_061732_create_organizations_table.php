
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class CreateOrganizationsTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::create("organizations", function (Blueprint $table) {

						$table->unsignedBigInteger('id')->autoIncrement();
						$table->string('organization_name',200)->nullable();
						$table->unsignedBigInteger('division_cd')->nullable()->unsigned();
						$table->timestamps();
						$table->softDeletes();
						//$table->foreign("id")->references("id")->on("users");



						// ----------------------------------------------------
						// -- SELECT [organizations]--
						// ----------------------------------------------------
						// $query = DB::table("organizations")
						// ->leftJoin("users","users.id", "=", "organizations.id")
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
                Schema::dropIfExists("organizations");
            }
        }
