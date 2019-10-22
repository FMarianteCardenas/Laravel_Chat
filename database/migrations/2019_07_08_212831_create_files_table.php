<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename') ; 
            $table->string('mime'); 
            $table->string('path'); 
            $table->integer('size');
            $table->boolean('read')->default(false);
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->timestamps();

            $table->foreign('from')->references('id')->on('users');
            $table->foreign('to')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
