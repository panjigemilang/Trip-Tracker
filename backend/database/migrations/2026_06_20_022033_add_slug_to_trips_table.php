<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Backfill slugs for existing trips
        \App\Models\Trip::all()->each(function ($trip) {
            $trip->slug = \Illuminate\Support\Str::slug($trip->title) ?: 'trip';
            $trip->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
