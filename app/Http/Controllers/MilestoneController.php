<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    //
  protected $milestone;
  protected $nameWithArticle = "del hito";

  public function __construct( Milestone $milestone ) {
    $this->milestone = $milestone;
  }

  public function index() {
    return response()->json( [
      "milestones" => $this->milestone->get(),
    ] );
  }

  public function store( Request $request ) {
    $requestAll = $request->all();
    $requestAll["user_id"] = Auth::id(); // current user id
    $errors = Helper::storeErrors( $requestAll, $this->milestone );
    $id     = 0;
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $milestone = $this->milestone->create( $requestAll );
        $id       = $milestone->id;
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
    $milestone = $this->milestone->find( $id );
    if ( ! $milestone ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      $errors = Helper::updateErrors( $request->all(), $milestone );
    }
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $milestone->update( $request->all() );
        $id = $milestone->id;
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
    $milestone  = $this->milestone->find( $id );
    if ( ! $milestone ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      try {
        DB::beginTransaction();
        $processed = $milestone->delete();

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
