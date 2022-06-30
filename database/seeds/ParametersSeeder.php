<?php

use Illuminate\Database\Seeder;
use App\Parameter;

class ParametersSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Parameter::create([
      'name' => 'exchange_rate', // doesn't change
      'text' => 'Tipo de cambio',
      'value' => '6.96'
    ]);
  }
}
