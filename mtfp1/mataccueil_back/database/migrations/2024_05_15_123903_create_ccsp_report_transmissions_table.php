<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcspReportTransmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ccsp_report_transmissions', function (Blueprint $table) {
            $table->id();
            $table->text('instruction')->nullable();
            $table->datetime('delay')->nullable();
            $table->boolean('is_last')->default(true);
            $table->float('sens');
            $table->unsignedBigInteger('user_up');
            //$table->foreign('user_up')->references("id")->on('users');
            $table->unsignedBigInteger('ua_up');
            //$table->foreign('ua_up')->references("id")->on('structures');
            $table->unsignedBigInteger('user_down');
           // $table->foreign('user_down')->references("id")->on('users');
            $table->unsignedBigInteger('ua_down');
           // $table->foreign('ua_down')->references("id")->on('structures');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ccsp_report_transmissions');
    }
}
