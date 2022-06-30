<?php

namespace Tests\Feature\Users;

use App\User;
use App\SpatieRole as Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class RolesAuthenticatedTest extends TestCase {
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

	public function test_can_index_roles() {
		$this->json( 'get',
			Credentials::API_URL . '/roles', [], $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
				"roles",
				"permissions"
			]
		)->getOriginalContent();

	}

	public function test_can_store_roles() {
		$data     = [
			'name'        => 'third_assistant',
			'text'        => 'Asistente III',
			'permissions' => []
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/roles', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $data["permissions"] );
		$this->assertDatabaseHas( 'roles', $data );
	}

	public function test_can_update_roles() {
		$oldData  = [
			'name'        => 'fourth_assistant',
			'text'        => 'Asistente IV',
			'permissions' => []
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/roles', $oldData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();

		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );

		$newData = [
			'name'        => 'fifth_assistant',
			'text'        => 'Asistente V',
			'permissions' => []
		];

		$response = $this->json( 'put',
			Credentials::API_URL . "/roles/{$response['id']}",
			$newData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $newData["permissions"] );
		$this->assertDatabaseHas( 'roles', $newData );
	}

	public function test_can_delete_roles() {
		$data     = [
			'name'        => 'sixth_assistant',
			'text'        => 'Asistente VI',
			'permissions' => [],
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/roles', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );

		$response = $this->json( 'delete',
			Credentials::API_URL . "/roles/{$response['id']}",
			[], $this->authHeaders
		)->assertStatus( 200
		)->getOriginalContent();
		$this->assertEquals( [], $response["errors"] );
		$this->assertEmpty( $response["errors"] );
		unset( $data["permissions"] );
		$this->assertDatabaseMissing( 'roles', $data );
	}
}
