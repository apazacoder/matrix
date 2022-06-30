<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Resource;
use Faker\Generator as Faker;

$factory->define( Resource::class, function ( Faker $faker ) {
	return [
		"name" => $faker->name,
		"text" => $faker->text( 50 )
	];
} );
