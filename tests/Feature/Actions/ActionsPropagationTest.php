<?php

namespace Tests\Feature\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\User;
use App\Resource;
use App\SpatiePermission;
use Tests\Credentials;

class ActionsPropagationTest extends TestCase {
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

	public function test_can_propagate_store_actions() {
		$data = [
			'name' => 'print',
			'text' => 'Imprimir'
		];
		$this->json( 'post',
			Credentials::API_URL . '/actions', $data, $this->authHeaders
		)->assertStatus( 200 )
		     ->getContent();
		$this->assertDatabaseHas( 'actions', $data );

		// for each resource, there must be a corresponding action
		$resources_qty            = Resource::get()->count();
		$filtered_permissions_qty = SpatiePermission::where( 'name', 'like', $data['name'] . ' %' )->get()->count();
		$this->assertEquals( $resources_qty, $filtered_permissions_qty );
	}

	public function test_can_propagate_update_actions() {
		$newData = [
			'name' => 'oldaction',
			'text' => 'Old name'
		];

		$oldData = [
			'name' => 'newaction',
			'text' => 'New name'
		];

		// create old data first
		$response = $this->json( 'post',
			Credentials::API_URL . '/actions', $oldData, $this->authHeaders
		)->assertStatus( 200 )->getOriginalContent();

		// update the data
		$this->json( 'put',
			Credentials::API_URL . "/actions/{$response['id']}",
			$newData, $this->authHeaders
		)->assertStatus( 200 );

		$this->assertDatabaseHas( 'actions', $newData );

		$resources_qty           = Resource::get()->count();
		$updated_permissions_qty = SpatiePermission::where( 'name', 'like', $newData['name'] . ' %' )->get()->count();
		$this->assertEquals( $resources_qty, $updated_permissions_qty );

		$old_permissions_qty = SpatiePermission::where( 'name', 'like', $oldData['name'] . ' %' )->get()->count();
		$this->assertEquals( 0, $old_permissions_qty );
	}

	public function test_can_propagate_delete_resources() {
		$newData = [
			'name' => 'rosies',
			'text' => 'Rosies'
		];

		// create old data first
		$response = $this->json( 'post',
			Credentials::API_URL . '/actions', $newData, $this->authHeaders
		)->assertStatus( 200 )->getOriginalContent();
		$this->assertDatabaseHas( 'actions', $newData );
		$new_permissions_qty = SpatiePermission::where( 'name', 'like', $newData["name"] . ' %' )->get()->count();
		$this->assertEquals( Resource::get()->count(), $new_permissions_qty );

		$this->json( 'delete',
			Credentials::API_URL . "/actions/{$response['id']}",
			[], $this->authHeaders )->assertStatus( 200 );
		$this->assertDatabaseMissing( 'actions', $newData );

		$old_permissions_qty = SpatiePermission::where( 'name', 'like', $newData["name"] . ' %' )->get()->count();
		$this->assertEquals( 0, $old_permissions_qty );
	}
}
