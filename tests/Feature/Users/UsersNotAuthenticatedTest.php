<?php

namespace Tests\Feature\Users;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class UsersNotAuthenticatedTest extends TestCase {
	use RefreshDatabase;
	protected $authHeaders;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );
	}

	public function test_cannot_index_users() {
		$this->json( 'get',
			Credentials::API_URL . '/users'
		)->assertStatus( 401 );
	}

	public function test_cannot_store_users() {
		$data = [
			'first_name'     => 'Alex',
			'second_name'    => 'Jones',
			'first_surname'  => 'Mendez',
			'second_surname' => 'Lopez',
			'email'          => 'alex@gmail.com',
			'password'       => 'alex123',
			'roles'          => []
		];
		$this->json( 'post',
			Credentials::API_URL . '/users', $data
		)->assertStatus( 401 )
		     ->getContent();
		unset( $data["password"] );
		unset( $data["roles"] );
		$this->assertDatabaseMissing( 'users', $data );
	}

	public function test_cannot_update_users() {
		$user    = User::first();
		$newData = [ "email" => 'jake@gmail.com' ];
		$this->json( 'put',
			Credentials::API_URL . "/users/{$user->id}", $newData
		)->assertStatus( 401 );

		$this->assertDatabaseMissing( 'users', $newData );
		$this->assertDatabaseHas( 'users', $user->toArray() );
	}

	public function test_cannot_delete_users() {
		$user = User::first();

		$this->json( 'delete',
			Credentials::API_URL . "/users/{$user->id}"
		)->assertStatus( 401 );
		$this->assertDatabaseHas( 'users', $user->toArray() );
	}
}
