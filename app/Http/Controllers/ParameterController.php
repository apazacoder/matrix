<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Parameter;
use Illuminate\Support\Facades\DB;

class ParameterController extends Controller
{
  protected $parameter;
  protected $nameWithArticle = "del parámetro";

  public function __construct( Parameter $parameter ) {
    $this->parameter = $parameter;
  }

  public function index() {
    return response()->json( [
      "parameters" => $this->parameter->get(),
    ] );
  }

  public function store( Request $request ) {
    $errors = Helper::storeErrors( $request->all(), $this->parameter );
    $id     = 0;
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $parameter = $this->parameter->create( $request->all() );
        $id       = $parameter->id;
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
    $parameter = $this->parameter->find( $id );
    if ( ! $parameter ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      $errors = Helper::updateErrors( $request->all(), $parameter );
    }
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $parameter->update( $request->all() );
        $id = $parameter->id;
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
    $parameter  = $this->parameter->find( $id );
    if ( ! $parameter ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      try {
        DB::beginTransaction();
        $processed = $parameter->delete();

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
