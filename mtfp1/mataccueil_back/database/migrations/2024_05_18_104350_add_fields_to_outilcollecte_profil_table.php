<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOutilcollecteProfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outilcollecte_profil', function (Blueprint $table) {
            $table->boolean('superviseurcentrecom')->default(false);
            $table->boolean('validateurcentrecom')->default(false);
            $table->boolean('coordonnateurcentrecom')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outilcollecte_profil', function (Blueprint $table) {
            //
        });
    }
}
