<?php

namespace Tests\Feature\Users;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class UsersAuthenticatedTest extends TestCase {
	use RefreshDatabase;
	protected $authHeaders;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );

		User::loginWithEmail( Credentials::ADMIN_EMAIL );
		$this->authHeaders = [
			'Authorization' => 'Bearer ' . Auth::user()->api_token
		];
	}

	public function test_can_index_users() {
		$this->json( 'get',
			Credentials::API_URL . '/users', [], $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"users"
		] );
	}

	public function test_can_store_users() {
		$data     = [
			'first_name'     => 'Alex',
			'second_name'    => 'Jones',
			'first_surname'  => 'Mendez',
			'second_surname' => 'Lopez',
			'email'          => 'alex@gmail.com',
			'password'       => 'alex123',
			'roles'          => []
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/users', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $data["roles"] );
		unset( $data["password"] );
		$this->assertDatabaseHas( 'users', $data );
	}

	public function test_can_update_users() {
		$oldData  = [
			'first_name'     => 'Alexis',
			'second_name'    => 'Jones',
			'first_surname'  => 'Mendez',
			'second_surname' => 'Lopez',
			'email'          => 'alexis@gmail.com',
			'password'       => 'alex123',
			'roles'          => []
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/users', $oldData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();

		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );

		$newData = [
			'first_name'     => 'Jon',
			'second_name'    => 'Jones',
			'first_surname'  => 'Mendez',
			'second_surname' => 'Lopez',
			'email'          => 'jon@gmail.com',
			'roles'          => []
		];

		$response = $this->json( 'put',
			Credentials::API_URL . "/users/{$response['id']}",
			$newData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $newData["roles"] );
		$this->assertDatabaseHas( 'users', $newData );
	}

	public function test_can_delete_users() {
		$data     = [
			'first_name'     => 'Roger',
			'second_name'    => '',
			'first_surname'  => 'Mendez',
			'second_surname' => 'Lopez',
			'email'          => 'roger@gmail.com',
			'roles'          => [],
			'password'       => 'roger123'
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/users', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );

		$response = $this->json( 'delete',
			Credentials::API_URL . "/users/{$response['id']}",
			[], $this->authHeaders
		)->assertStatus( 200
		)->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $data["roles"] );
		unset( $data["password"] );
		$this->assertDatabaseMissing( 'users', $data );
	}
}
