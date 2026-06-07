<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('disk', 20)->default('r2');
            $table->string('path', 500);
            $table->string('original_name');
            $table->integer('size_bytes');
            $table->string('mime_type', 50);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestampTz('created_at');

            $table->index(['activity_id'], 'activity_images_activity_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_images');
    }
}
