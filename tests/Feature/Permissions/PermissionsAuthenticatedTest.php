<?php

namespace Tests\Feature\Permissions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\User;
use App\Resource;
use App\Action;
use Tests\Credentials;

class PermissionsAuthenticatedTest extends TestCase{
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

	public function test_can_index_permissions() {
		$this->json( 'get',
			Credentials::API_URL . '/permissions', [], $this->authHeaders
		)->assertStatus( 200
		)->assertJsonStructure( [
			"permissions"
		] );

	}
}
