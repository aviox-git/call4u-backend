<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnualEventSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annual_event_selections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('annual_date_id');
            $table->foreign('annual_date_id')->references('id')->on('annual_dates');
            $table->unsignedBigInteger('event_selection_id');
            $table->foreign('event_selection_id')->references('id')->on('event_selections');
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
        Schema::dropIfExists('annual_event_selections');
    }
}
