<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJourneyActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journey_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journey_id')->constrained('journeys')->cascadeOnDelete();
            $table->foreignUuid('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('status', 20)->default('upcoming');
            $table->timestampTz('status_changed_at')->nullable();
            $table->timestampTz('reminder_sent_at')->nullable();
            $table->timestampsTz();

            $table->unique(['journey_id', 'activity_id']);
            $table->index(['journey_id', 'status'], 'ja_journey_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journey_activities');
    }
}
