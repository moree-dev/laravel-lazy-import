<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_path', 255);
            $table->string('driver');
            $table->enum('status', ['pending', 'running', 'paused', 'aborted']);
            $table->bigInteger('last_position')->nullable();
            $table->dateTime('ran_at')->nullable();
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
        Schema::dropIfExists('data_imports');
    }
}
