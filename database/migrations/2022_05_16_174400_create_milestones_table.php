<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMilestonesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('milestones', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('description', 1024);
      $table->date('scheduled_at');
      $table->timestamp('completed_at')->nullable();
      $table->string('status');

      $table->bigInteger('task_id')->unsigned()
        ->index('task_id_milestones_index');
      $table->foreign('task_id')->references('id')->on('tasks')
        ->onDelete('cascade')
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
    Schema::dropIfExists('milestones');
  }
}
