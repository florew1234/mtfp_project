<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOutilcollecteActeurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outilcollecte_acteur', function (Blueprint $table) {
            $table->unsignedBigInteger('idDepart')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outilcollecte_acteur', function (Blueprint $table) {
            //
        });
    }
}
