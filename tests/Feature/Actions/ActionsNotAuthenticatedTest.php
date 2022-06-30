<?php
/**
 * ActionsManagementTest.php, Creado el 13/12/2020 a las 17:35
 */

namespace Tests\Feature\Actions;

use App\Action;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class ActionsNotAuthenticatedTest extends TestCase {
	use RefreshDatabase;
	protected $authHeaders;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );
	}

	public function test_cannot_index_actions() {
		$this->json( 'get',
			Credentials::API_URL . '/actions'
		)->assertStatus( 401 );
	}

	public function test_cannot_store_actions() {
		$data = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$this->json( 'post',
			Credentials::API_URL . '/actions', $data
		)->assertStatus( 401 )
		     ->getContent();
		$this->assertDatabaseMissing( 'actions', $data );
	}

	public function test_cannot_update_actions() {
		$newData = [
			'name' => 'new roar',
			'text' => 'Nuevo rugir'
		];
		$action  = factory( Action::class )->create();

		$this->json( 'put',
			Credentials::API_URL . "/actions/{$action->id}",
			$newData )->assertStatus( 401 );

		$this->assertDatabaseMissing( 'actions', $newData );
		$this->assertDatabaseHas('actions', $action->toArray());
	}

	public function test_cannot_delete_actions() {
		$action = factory( Action::class )->create();

		$this->json( 'delete',
			Credentials::API_URL . "/actions/{$action->id}"
		)->assertStatus( 401 );
		$this->assertDatabaseHas( 'actions', $action->toArray() );
	}
}
