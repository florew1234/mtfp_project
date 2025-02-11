<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class CreateCcspReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ccsp_reports', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_recieved");
            $table->integer("customer_satisfied");
            $table->text("unsatified_reason")->nullable();
            $table->text("difficult")->nullable();
            $table->text("solution")->nullable();
            $table->text("observation")->nullable();
            $table->longText("summary")->nullable();
            $table->date("start_date");
            $table->date("end_date");
            $table->enum("type",Config("global_data.ccsp_report_types"));
            $table->date("validation_date")->nullable();
            $table->integer("status");
            $table->unsignedBigInteger('user_id');
           // $table->foreign('user_id')->references("id")->on('users');
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
        Schema::dropIfExists('ccsp_reports');
    }
}
