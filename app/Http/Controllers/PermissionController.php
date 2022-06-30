<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SpatiePermission;

class PermissionController extends Controller {
	protected $permission;
	protected $nameWithArticle = "de la acción";

	public function __construct( SpatiePermission $permission ) {
		$this->permission = $permission;
	}

	public function index() {
		return response()->json( [
			"permissions" => $this->permission->get(),
		] );
	}
}
