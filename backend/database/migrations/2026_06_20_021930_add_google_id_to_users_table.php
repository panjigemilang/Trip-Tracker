<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar_url')->nullable()->after('google_id');
        });

        $driver = \Illuminate\Support\Facades\Schema::connection(null)->getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN password DROP NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar_url']);
        });

        $driver = \Illuminate\Support\Facades\Schema::connection(null)->getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ALTER COLUMN password SET NOT NULL');
        }
    }
}
