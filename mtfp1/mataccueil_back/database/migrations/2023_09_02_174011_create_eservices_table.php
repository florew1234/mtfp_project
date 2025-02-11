<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEservicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('eservices', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('resume');
        //     $table->string('link');
        //     $table->boolean('is_published')->default(false);
        //     $table->foreignId('user_id')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eservices');
    }
}
