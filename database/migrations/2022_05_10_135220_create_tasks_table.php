<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tasks', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('issue', 512);
      $table->string('description', 1024);

      $table->bigInteger('user_id')->unsigned()->nullable()
        ->index('user_id_tasks_index');
      $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('restrict')
        ->onUpdate('cascade');

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
    Schema::dropIfExists('tasks');
  }
}
