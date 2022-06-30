<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\SpatieRole as Role;
use App\SpatiePermission as Permission;
use App\Helpers\Helper;



class RoleController extends Controller {
	protected $role;
	protected $permission;
	protected $nameWithArticle = "del rol";

	public function __construct( Role $role ) {
		$this->role = $role;
		$this->permission = new Permission;

	}

	public function index() {
		return response()->json( [
			"roles"       => $this->role->withPermissionsWithoutAdmin(),
			"permissions" => $this->permission->get()
		] );
	}

	public function store( Request $request ) {
		$errors = Helper::storeErrors( $request->all(), $this->role );
		$id     = 0;
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$role = Role::create( $request->all() );
				$role->syncPermissions( array_column ($request->permissions, "name") );
				$id = $role->id;
				DB::commit();
			} catch ( \Exception $e ) {
				DB::rollback();
				$errors[] = $e->getMessage();
			}
		}

		if ( $errors ) {
			$errors[] = '* No se pudieron guardar los datos ' . $this->nameWithArticle;
		}

		return response()->json( [
			'errors'    => $errors,
			'id'        => $id,
			'okMessage' => ( ! $errors ) ? 'Datos ' . $this->nameWithArticle . ' guardados' : ''
		] );
	}

	public function update( Request $request, $id ) {
		$errors = [];
		$role   = Role::find( $id );
		if ( ! $role ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}
		if ( ! $errors ) {
			$errors = Helper::updateErrors( $request->all(), $role );
		}
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$role->update( $request->all() );
				$role->syncPermissions( array_column( $request->permissions, "name" ) );
				$id = $role->id;
				DB::commit();
			} catch ( \Exception $e ) {
				DB::rollback();
				$errors[] = $e->getMessage();
			}
		}

		if ( $errors ) {
			$errors[] = '* No se pudieron guardar los datos ' . $this->nameWithArticle;
		}

		return response()->json( [
			'errors'    => $errors,
			'id'        => $id,
			'okMessage' => ( ! $errors ) ? 'Datos ' . $this->nameWithArticle . ' actualizados' : ''
		] );
	}

	public function destroy( $id ) {
		$errors    = [];
		$processed = false;
		$role      = Role::find( $id );
		if ( ! $role ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}

		if ( ! $errors ) {
			try {
				$processed = $role->delete();
			} catch ( \Exception $e ) {
				if ( $e->getCode() === "23000" ) {
					$errors[] = '* No puede borrar los datos ' . $this->nameWithArticle . ', antes debe borrar todas las entidades dependientes';
				} else {
					$errors[] = $e->getMessage();
				}
			}
		}

		if ( ! $processed ) {
			$errors[] = '* No se pudieron borrar los datos ' . $this->nameWithArticle;
		}

		return response()->json( [
			'errors'    => $errors,
			'id'        => $id,
			'okMessage' => ( ! $errors ) ? 'Los datos ' . $this->nameWithArticle . ' fueron borrados' : ''
		] );
	}
}
