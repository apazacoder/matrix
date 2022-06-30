<?php

namespace Tests\Feature\Resources;

use App\Resource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class ResourcesNotAuthenticatedTest extends TestCase {
	use RefreshDatabase;
	protected $authHeaders;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );
	}

	public function test_cannot_index_resources() {
		$this->json( 'get',
			Credentials::API_URL . '/resources'
		)->assertStatus( 401 );
	}

	public function test_cannot_store_resources() {
		$data = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$this->json( 'post',
			Credentials::API_URL . '/resources', $data
		)->assertStatus( 401 )
		     ->getContent();
		$this->assertDatabaseMissing( 'resources', $data );
	}

	public function test_cannot_update_resources() {
		$newData = [
			'name' => 'new roar',
			'text' => 'Nuevo rugir'
		];
		$resource  = factory( Resource::class )->create();

		$this->json( 'put',
			Credentials::API_URL . "/resources/{$resource->id}",
			$newData )->assertStatus( 401 );

		$this->assertDatabaseMissing( 'resources', $newData );
		$this->assertDatabaseHas('resources', $resource->toArray());
	}

	public function test_cannot_delete_resources() {
		$resource = factory( Resource::class )->create();

		$this->json( 'delete',
			Credentials::API_URL . "/resources/{$resource->id}"
		)->assertStatus( 401 );
		$this->assertDatabaseHas( 'resources', $resource->toArray() );
	}
}
