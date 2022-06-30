<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Resource;
use App\Helpers\Helper;
use App\SpatiePermission;

class ResourceController extends Controller {
	protected $resource;
	protected $nameWithArticle = "del recurso";

	public function __construct( Resource $resource ) {
		$this->resource = $resource;
	}

	public function index() {
		return response()->json( [
			"resources" => $this->resource->get(),
		] );
	}

	public function store( Request $request ) {
		$errors = Helper::storeErrors( $request->all(), $this->resource );
		$id     = 0;
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$resource = $this->resource->create( $request->all() );
				$id       = $resource->id;
				if ( ! SpatiePermission::storeForResource( $resource ) ) {
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
		] );
	}

	public function update( Request $request, $id ) {
		$errors   = [];
		$resource = $this->resource->find( $id );
		$oldName  = $resource->name;
		if ( ! $resource ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}
		if ( ! $errors ) {
			$errors = Helper::updateErrors( $request->all(), $resource );
		}
		if ( ! $errors ) {
			DB::beginTransaction();
			try {
				$resource->update( $request->all() );

				if ( ! SpatiePermission::updateForResource( $oldName, $resource ) ) {
					throw new \Exception( 'No se pudieron actualizar los permisos' );
				}

				$id = $resource->id;
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
		$resource  = $this->resource->find( $id );
		$resourceName = $resource->name;
		if ( ! $resource ) {
			$errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
		}
		if ( ! $errors ) {
			try {
				DB::beginTransaction();
				$processed = $resource->delete();
				if ( ! SpatiePermission::deleteForResource( $resourceName ) ) {
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
