<?php

namespace Tests\Feature\Resources;

use App\Resource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class ResourcesAuthenticatedTest extends TestCase {
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

	public function test_can_index_resources() {
		$this->json( 'get',
			Credentials::API_URL . '/resources', [], $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"resources"
		] );
	}

	public function test_can_store_resources() {
		$data     = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/resources', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseHas( 'resources', $data );
	}

	public function test_can_update_resources() {
		$newData  = [
			'name' => 'new roar',
			'text' => 'Nuevo rugir'
		];
		$resource = factory( Resource::class )->create();

		$response = $this->json( 'put',
			Credentials::API_URL . "/resources/{$resource->id}",
			$newData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseHas( 'resources', $newData );
	}

	public function test_can_delete_resources() {
		$data = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$res  = $this->json( 'post',
			Credentials::API_URL . '/resources', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();

		$response = $this->json( 'delete',
			Credentials::API_URL . "/resources/{$res['id']}",
			[], $this->authHeaders
		)->assertStatus( 200
		)->getOriginalContent();
		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseMissing( 'resources', $data );
	}
}
