<?php

namespace Tests\Feature\Users;

use App\User;
use App\SpatieRole as Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class RolesNotAuthenticatedTest extends TestCase {
	use RefreshDatabase;
	protected $authHeaders;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );
	}

	public function test_cannot_index_roles() {
		$this->json( 'get',
			Credentials::API_URL . '/roles'
		)->assertStatus( 401 );
	}

	public function test_cannot_store_roles() {
		$data = [
			'name'        => 'boss_lvl1',
			'text'        => 'Jefe Nivel 1',
			'permissions' => []
		];
		$this->json( 'post',
			Credentials::API_URL . '/roles', $data
		)->assertStatus( 401 )
		     ->getContent();
		unset( $data["permissions"] );
		$this->assertDatabaseMissing( 'roles', $data );
	}

	public function test_cannot_update_roles() {
		$role    = Role::first();
		$newData = [ "name" => 'boss_lvl2' ];
		$this->json( 'put',
			Credentials::API_URL . "/roles/{$role->id}", $newData
		)->assertStatus( 401 );

		$this->assertDatabaseMissing( 'roles', $newData );
		$this->assertDatabaseHas( 'roles', $role->toArray() );
	}

	public function test_cannot_delete_roles() {
		$role = Role::first();

		$this->json( 'delete',
			Credentials::API_URL . "/roles/{$role->id}"
		)->assertStatus( 401 );
		$this->assertDatabaseHas( 'roles', $role->toArray() );
	}
}
