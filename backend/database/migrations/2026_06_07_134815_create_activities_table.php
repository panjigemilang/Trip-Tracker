<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            $table->string('title');
            $table->string('location', 500)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order');
            $table->timestampsTz();

            $table->index(['trip_id', 'date', 'time'], 'activities_trip_schedule_idx');
            $table->index(['trip_id', 'sort_order'], 'activities_trip_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
