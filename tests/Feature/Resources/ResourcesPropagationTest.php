<?php

namespace Tests\Feature\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\User;
use App\Action;
use App\SpatiePermission;
use Tests\Credentials;


// If a resource is modified the change must be effective on permissions table
class ResourcesPropagationTest extends TestCase {
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

	public function test_can_propagate_store_resources() {
		$data = [
			'name' => 'users',
			'text' => 'Usuarios'
		];
		$this->json( 'post',
			Credentials::API_URL . '/resources', $data, $this->authHeaders
		)->assertStatus( 200 )
		     ->getContent();
		$this->assertDatabaseHas( 'resources', $data );

		// for each resource, there must be a corresponding action
		$actions_qty              = Action::get()->count();
		$filtered_permissions_qty = SpatiePermission::where( 'name', 'like', '%' . $data['name'] )->get()->count();
		$this->assertEquals( $actions_qty, $filtered_permissions_qty );
	}

	public function test_can_propagate_update_resources() {
		$newData = [
			'name' => 'invoices',
			'text' => 'Facturas'
		];

		$oldData = [
			'name' => 'raxies',
			'text' => 'Raxies'
		];

		// create old data first
		$response = $this->json( 'post',
			Credentials::API_URL . '/resources', $oldData, $this->authHeaders
		)->assertStatus( 200 )->getOriginalContent();

		// update the data
		$this->json( 'put',
			Credentials::API_URL . "/resources/{$response['id']}",
			$newData, $this->authHeaders
		)->assertStatus( 200 );

		$this->assertDatabaseHas( 'resources', $newData );

		$actions_qty             = Action::get()->count();
		$updated_permissions_qty = SpatiePermission::where( 'name', 'like', '% ' . $newData['name'] )->get()->count();
		$this->assertEquals( $actions_qty, $updated_permissions_qty );

		$old_permissions_qty = SpatiePermission::where( 'name', 'like', '%' . $oldData['name'] )->get()->count();
		$this->assertEquals( 0, $old_permissions_qty );
	}

	public function test_can_propagate_delete_resources() {
		$newData = [
			'name' => 'raxies',
			'text' => 'Raxies'
		];

		// create old data first
		$response = $this->json( 'post',
			Credentials::API_URL . '/resources', $newData, $this->authHeaders
		)->assertStatus( 200 )->getOriginalContent();
		$this->assertDatabaseHas( 'resources', $newData );
		$new_permissions_qty = SpatiePermission::where( 'name', 'like', '% ' . $newData["name"] )->get()->count();
		$this->assertEquals( Action::get()->count(), $new_permissions_qty );

		$this->json( 'delete',
			Credentials::API_URL . "/resources/{$response['id']}",
			[], $this->authHeaders )->assertStatus( 200 );
		$this->assertDatabaseMissing( 'resources', $newData );

		$old_permissions_qty = SpatiePermission::where( 'name', 'like', '% ' . $newData["name"] )->get()->count();
		$this->assertEquals( 0, $old_permissions_qty );
	}
}


