<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcspsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('ccsps', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('address');
        //     $table->string('email');
        //     $table->string('phone');
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
        Schema::dropIfExists('ccsps');
    }
}
