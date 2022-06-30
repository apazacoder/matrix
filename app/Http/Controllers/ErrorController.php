<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErrorController extends Controller {
	public function handleNotFound() {
		$route = Auth::check() ? 'home' : 'login';
		return redirect()->route( $route )
		                 ->with( 'message',
			                 'Su sesión ha expirado, intente de nuevo por favor' );

	}
}
