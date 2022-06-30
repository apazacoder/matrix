<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthRedirectsTest extends TestCase {
	public function test_non_auth_redirection_to_login_url() {
		$response = $this->get( '/' );
		$response->assertRedirect( route( 'login' ) )
		         ->assertStatus( 302 );
	}

	public function test_login_is_accessible() {
		$response = $this->get( '/connect' );
		$response->assertStatus( 200 );
	}

	public function test_non_auth_invalid_url_redirects_to_login() {
		$response = $this->get( '/xyzabc' );
		$response->assertRedirect( route( 'login' ) )
		         ->assertStatus( 302 );
	}
}
