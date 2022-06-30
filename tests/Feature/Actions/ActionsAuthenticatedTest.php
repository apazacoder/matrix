<?php

namespace Tests\Feature\Actions;

use App\Action;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Credentials;

class ActionsAuthenticatedTest extends TestCase {
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

	public function test_can_index_actions() {
		$this->json( 'get',
			Credentials::API_URL . '/actions', [], $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"actions"
		] );
	}

	public function test_can_store_actions() {
		$data     = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/actions', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();
		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseHas( 'actions', $data );
	}

	public function test_can_update_actions() {
		$newData = [
			'name' => 'new roar',
			'text' => 'Nuevo rugir'
		];
		$action  = factory( \App\Action::class )->create();

		$response = $this->json( 'put',
			Credentials::API_URL . "/actions/{$action->id}",
			$newData, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();

		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseHas( 'actions', $newData );
	}

	public function test_can_delete_actions() {
		$data     = [
			'name' => 'roar',
			'text' => 'Rugir'
		];
		$response = $this->json( 'post',
			Credentials::API_URL . '/actions', $data, $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"errors",
			"id",
			"okMessage"
		] )->getOriginalContent();

		$response = $this->json( 'delete',
			Credentials::API_URL . "/actions/{$response['id']}",
			[], $this->authHeaders
		)->assertStatus( 200
		)->getOriginalContent();
		$this->assertEmpty( $response["errors"] );
		$this->assertDatabaseMissing( 'actions', $data );
	}
}
