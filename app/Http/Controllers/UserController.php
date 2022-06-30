<?php

namespace App\Http\Controllers;

use App\SpatieRole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Helpers\Helper;

class UserController extends Controller
{
  protected $user;
  protected $nameWithArticle = "del usuario";

  public function __construct(User $user)
  {
    $this->user = $user;
    $this->spatieRole = (new SpatieRole());
  }

  public function index()
  {
    // all must be shown but not the admin
    return response()->json([
      "users" => $this->user->withRolesWithoutAdmin(),
      "roles" => $this->spatieRole->withoutAdmin(),
    ]);
  }

  public function getCurrent()
  {
    if (Auth::check()) {
      $user = User::find(Auth::id());

      return response()->json([
        "username" => $user->first_name,
        "email" => $user->email,
        "status" => true,
        "api_token" => $user->api_token,
        "permissions" => $user->allowedPermissions(),
        "routes" => $user->allowedRoutes(),
      ]);
    } else {
      // if the user isn't authenticated the attacker only can see their status
      return response()->json([
        "status" => false
      ]);
    }
  }

  public function getRoles()
  {
    $role = "";
    if (Auth::check()) {
      $user = User::find(Auth::id());
    }

    return response()->json([
      "user" => $user,
      "roles" => implode(", ", $user->getRoleTexts()),
    ]);
  }

  public function store(Request $request)
  {
    $errors = Helper::storeErrors($request->all(), $this->user);
    $id = 0;
    if (!$errors) {
      DB::beginTransaction();
      try {
        // $request->input('warehousesunit_id')

        $user = User::create([
          'first_name' => $request->input('first_name'),
          'second_name' => $request->input('second_name'),
          'first_surname' => $request->input('first_surname'),
          'second_surname' => $request->input('second_surname'),
          'ci' => $request->input('ci'),
          'exp' => $request->input('exp'),
          'position' => $request->input('position'),
          'email' => $request->input('email'),
          'password' => bcrypt($request->input('password')),
        ]);
        $user->syncRoles(array_column($request->roles, "name"));
        $id = $user->id;
        DB::commit();
      } catch (\Exception $e) {
        DB::rollback();
        $errors[] = $e->getMessage();
      }
    }

    if ($errors) {
      $errors[] = '* No se pudieron guardar los datos ' . $this->nameWithArticle;
    }

    return response()->json([
      'errors' => $errors,
      'id' => $id,
      'okMessage' => (!$errors) ? 'Datos ' . $this->nameWithArticle . ' guardados' : ''
    ]);
  }

  public function update(Request $request, $id)
  {
    $errors = [];
    $user = User::find($id);
    if (!$user) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }
    if (!$errors) {
      $errors = Helper::updateErrors($request->all(), $user);
    }
    if (!$errors) {
      DB::beginTransaction();
      try {
        $requestAll = $request->all();
//        $requestAll['warehouse_id'] = \App\Warehousesunit::where('id', '=', $request->input('warehouse_id'))->first()->warehouse_id;
        if (array_key_exists("password", $requestAll) && trim($requestAll["password"])) {
          $requestAll["password"] = bcrypt(trim($requestAll["password"]));
        } else {
          unset($requestAll["password"]);
        }
        $user->update($requestAll);
        $user->syncRoles(array_column($request->roles, "name"));
        $id = $user->id;
        DB::commit();
      } catch (\Exception $e) {
        DB::rollback();
        $errors[] = $e->getMessage();
      }
    }

    if ($errors) {
      $errors[] = '* No se pudieron guardar los datos ' . $this->nameWithArticle;
    }

    return response()->json([
      'errors' => $errors,
      'id' => $id,
      'okMessage' => (!$errors) ? 'Datos ' . $this->nameWithArticle . ' actualizados' : ''
    ]);
  }

  public function destroy($id)
  {
    $errors = [];
    $processed = false;
    $user = User::find($id);
    if (!$user) {
      $errors[] = '* No se encuentran los datos ' . $this->nameWithArticle;
    }

    if (!$errors) {
      try {
        $processed = $user->delete();
      } catch (\Exception $e) {
        if ($e->getCode() === "23000") {
          $errors[] = '* No puede borrar los datos ' . $this->nameWithArticle . ', antes debe borrar todas las entidades dependientes';
        } else {
          $errors[] = $e->getMessage();
        }
      }
    }

    if (!$processed) {
      $errors[] = '* No se pudieron borrar los datos ' . $this->nameWithArticle;
    }

    return response()->json([
      'errors' => $errors,
      'id' => $id,
      'okMessage' => (!$errors) ? 'Los datos ' . $this->nameWithArticle . ' fueron borrados' : ''
    ]);
  }

  public function updatePassword(Request $request, $email)
  {
    $processed = false;
    $requestAll = $request->all();
    $errors = [];
    $oldPassword = array_key_exists("old_password", $requestAll) && !empty($requestAll["old_password"]) ?
      $requestAll["old_password"] : "";
    $newPassword = array_key_exists("new_password", $requestAll) && !empty($requestAll["new_password"]) ?
      $requestAll["new_password"] : "";

    if (!$oldPassword || !$newPassword) {
      $errors[] = "Introduzca las contraseñas";
    }
    $user = null;
    if (!$errors) {
      $user = User::where('email', $email)->get();
      $user = count($user) ? $user->first() : null;
      if (!$user) {
        $errors[] = 'No se encuentran los datos ' . $this->nameWithArticle;
      }
    }

    if (!$errors) {
      $errors = $user->updatePassErrors($requestAll["old_password"]);
    }

    if (!$errors) {
      $processed = $user->update([
        'password' => bcrypt($requestAll['new_password'])
      ]);
    }
    if (!$processed) {
      $errors[] = '* No se pudo actualizar la contraseña';
    }

    return response()->json([
      'errors' => $errors,
      'okMessage' => (!$errors) ? 'Datos ' . $this->nameWithArticle . ' actualizados' : ''
    ]);
  }
}
