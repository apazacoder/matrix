<?php

namespace Tests\Feature\Auth;

use App\Action;
use App\User;
use Tests\AuthSetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Credentials;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase {
	use RefreshDatabase;

	protected function setUp(): void {
		parent::setUp();

		$this->seed( [
			'UsersTableSeeder',
			'RolesAndPermissionsSeeder',
		] );
	}

	public function test_true_login_with_credentials() {
		$this->assertTrue( User::loginWithEmailAndPass( Credentials::ADMIN_EMAIL, Credentials::ADMIN_PASS ) );
	}

	public function test_true_login_with_incorrect_credentials() {
		$this->assertFalse( User::loginWithEmailAndPass( Credentials::ADMIN_EMAIL . 'bad', Credentials::ADMIN_PASS . 'bad' ) );
	}

	public function test_true_login_with_email() {
		$this->assertTrue( User::loginWithEmail( Credentials::ADMIN_EMAIL ) );
	}

	public function test_false_login_with_incorrect_email() {
		$this->assertFalse( User::loginWithEmail( Credentials::ADMIN_EMAIL . 'incorrect' ) );
	}

	public function test_not_logged_in_by_default() {
		$this->assertFalse( Auth::check() );
	}

	public function test_logged_in_with_email() {
		User::loginWithEmail( Credentials::ADMIN_EMAIL );
		$this->assertTrue( Auth::check() );
	}

}
