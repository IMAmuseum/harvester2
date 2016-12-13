<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('object_id')->unsigned()->index();
            $table->integer('related_id')->unsigned()->index();
            $table->text('relationship')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('object_id')->references('id')->on('objects')->onDelete('cascade');
            $table->foreign('related_id')->references('id')->on('objects')->onDelete('cascade');
            $table->unique(['object_id', 'related_id']); //, 'relation_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('relationships');
    }
}
