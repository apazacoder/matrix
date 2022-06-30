<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Action;
use App\Helpers\Helper;
use App\SpatiePermission;

class ActionController extends Controller {
	protected $action;
	protected $nameWithArticle = "de la acción";

	public function __construct( Action $action ) {
		$this->action = $action;
	}

	public function index() {
		return response()->json( [
			"actions" => $this->action->get(),
		] );
	}

	public function store( Request $request ) {
		$errors = Helper::storeErrors( $request->all(), $this->action );
		$id     = 0;
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$action = $this->action->create( $request->all() );
				$id     = $action->id;
				if ( ! SpatiePermission::storeForAction( $action ) ) {
					throw new \Exception( 'No se pudieron crear los permisos' );
				}
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
		]);
	}

	public function update( Request $request, $id ) {
		$errors = [];
		$action = $this->action->find( $id );
		$oldName = $action->name;
		if ( ! $action ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}
		if ( ! $errors ) {
			$errors = Helper::updateErrors( $request->all(), $action );
		}
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$action->update( $request->all() );
				if ( ! SpatiePermission::updateForAction( $oldName, $action ) ) {
					throw new \Exception( 'No se pudieron actualizar los permisos' );
				}

				$id = $action->id;
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
		$action    = $this->action->find( $id );
		$actionName = $action->name;
		if ( ! $action ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}
		if ( ! $errors ) {
			try {
				DB::beginTransaction();
				$processed = $action->delete();
				if ( ! SpatiePermission::deleteForAction( $actionName ) ) {
					throw new \Exception( 'No se pudieron borrar los permisos' );
				}
				DB::commit();
			} catch ( \Exception $e ) {
				DB::rollback();
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
