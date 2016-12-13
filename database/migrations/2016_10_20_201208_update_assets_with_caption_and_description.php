<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssetsWithCaptionAndDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Laravel/SQLite bug cant drop multiple columns in the same statment
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('caption');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
