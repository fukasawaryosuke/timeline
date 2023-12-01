
    <?php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Database\Migrations\Migration;

        class AddDivisionCdToDiariesTable extends Migration
        {
            /**
             * Run the migrations.
             *
             * @return void
             */
            public function up()
            {
                Schema::table("diaries", function (Blueprint $table) {
                    $table->unsignedBigInteger('division_cd')
                    ->nullable()
                    ->after('in_house_system_id');
                });
            }

            /**
             * Reverse the migrations.
             *
             * @return void
             */
            public function down()
            {
                Schema::table('diaries', function (Blueprint $table) {
                    $table->dropColumn('division_cd');
                });
            }
        }
