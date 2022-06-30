<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('first_name', 120)->nullable();
      $table->string('second_name', 120)->nullable();
      $table->string('first_surname', 120)->nullable();
      $table->string('second_surname', 120)->nullable();
      $table->string('ci', 16)->nullable();
      $table->string('exp', 4)->nullable();
      $table->string('position', 240)->nullable();
      $table->string('email', 120)->unique()->nullable();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password', 120);
      $table->string('api_token', 80)
        ->unique()
        ->nullable()
        ->default(null);
      $table->rememberToken();

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
    Schema::dropIfExists('users');
  }
}
