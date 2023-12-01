
    <?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class CreateDiaryTemplatesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create("diary_templates", function (Blueprint $table) {

                $table->unsignedBigInteger('id')->autoIncrement();
                $table->json('diary')->nullable();
                $table->unsignedBigInteger('create_by')->nullable();
                $table->softDeletes();
                $table->timestamps();


                // ----------------------------------------------------
                // -- SELECT [diary_templates]--
                // ----------------------------------------------------
                // $query = DB::table("diary_templates")
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
            Schema::dropIfExists("diary_templates");
        }
    }
