<?php

namespace App\Http\Controllers;

use App\Task;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
  protected $task;
  protected $nameWithArticle = "de la tarea";

  public function __construct( Task $task ) {
    $this->task = $task;
  }

  public function index() {
    $roles = Auth::user()->getRoleTexts();
    $is_director = in_array("Director", $roles);
    $is_boss = in_array("Jefe", $roles);
    $is_guest = in_array("Invitado", $roles);
    if ($is_director || $is_boss){
      // from all the roles the director and the boss is the only restricted to see only his own tasks
      $tasks = Auth::user()->tasks()->with('milestones','user')->get();
    }else{
      // all the other roles: admin, superadmin, guest can see the tasks of all users
      $tasks = $this->task->with('milestones','user')->get();
    }
    return response()->json( [
      "tasks" => $tasks,
      "roles" => Auth::user()->getRoleTexts(),
      "is_director" => $is_director,
      "is_guest" => $is_guest
    ]);
  }

  public function store( Request $request ) {
    $Milestone = new \App\Milestone();
    $requestAll = $request->all();
    $requestAll["user_id"] = Auth::id(); // current user id
    $errors = Helper::storeErrors( $requestAll, $this->task );
    $id     = 0;
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $task = $this->task->create( $requestAll );
        foreach($requestAll["milestones"] as $milestone){
          // validate
          $milestone["task_id"] = $task->id;
          $innerErrors = Helper::storeErrors($milestone, $Milestone );
          if (!$innerErrors){
            $Milestone::create($milestone);
          }else{
            // on any error rollback and stop immediately
            $errors = array_merge($errors, $innerErrors);
            throw new \Exception('* Verifique los datos de los hitos');
          }
        }
        $id       = $task->id;
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
    $Milestone = new \App\Milestone();
    $errors   = [];
    $task = $this->task->find( $id );
    if ( ! $task ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      $errors = Helper::updateErrors( $request->all(), $task );
    }
    if ( ! $errors ) {
      DB::beginTransaction();
      try {
        $task->update( $request->all() );

        $Milestone::where('task_id', '=', $id)->delete();
        foreach($request->all()["milestones"] as $milestone){
          // validate
          $milestone["task_id"] = $task->id;
          $innerErrors = Helper::storeErrors($milestone, $Milestone );
          if (!$innerErrors){
            // if the new status is "completado" store in scheduled_at the current date
            if ($milestone["status"] == "completado"){
              $milestone["completed_at"] = date('Y-m-d H:i:s');
            }
            $Milestone::create($milestone);
          }else{
            // on any error rollback and stop immediately
            $errors = array_merge($errors, $innerErrors);
            throw new \Exception('* Verifique los datos de los hitos');
          }
        }
        $id = $task->id;
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
    $task  = $this->task->find( $id );
    if ( ! $task ) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if ( ! $errors ) {
      try {
        DB::beginTransaction();
        $processed = $task->delete();

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
