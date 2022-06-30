<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use Notifiable;
  use HasRoles;

  protected $guard_name = 'api';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'first_name',
    'second_name',
    'first_surname',
    'second_surname',
    'ci',
    'exp',
    'position',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'api_token'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'id' => 'string',
    'email_verified_at' => 'datetime',
    'first_name' => 'string',
    'second_name' => 'string',
    'first_surname' => 'string',
    'second_surname' => 'string',
    'ci' => 'string',
    'exp' => 'string',
    'position' => 'string',
  ];

  public function storeRules()
  {
    return [
      'first_name' => 'required|max:110',
      'first_surname' => 'required|max:110',
      'ci' => 'max:10',
      'exp' => 'max:4',
      'position' => 'required|max:240',
      'email' => 'required|email|max:110|unique:users',
      'password' => 'required',
    ];
  }

  public function updateRules()
  {
    return [
      'first_name' => 'required|max:110',
      'first_surname' => 'required|max:110',
      'ci' => 'max:10',
      'exp' => 'max:4',
      'position' => 'required|max:240',
      'email' => 'required|email|max:110|unique:users,email,' . $this->id,
    ];
  }

  public function validationMessages()
  {
    return [
      'first_name.required' => 'Por favor introduzca el primer nombre',
      'first_name.max' => 'El nombre no puede tener más de :max caracteres',
      'first_surname.required' => 'Por favor introduzca el primer apellido',
      'first_surname.max' => 'El primer apellido no puede tener más de :max caracteres',
      'ci.max' => 'El número de C.I. no puede tener más de :max caracteres',
      'exp.max' => 'El lugar de expedición no puede tener más de :max caracteres',
      'position.required' => 'Debe introducir un cargo',
      'position.max' => 'El cargo no puede tener más de :max caracteres',
      'email.required' => 'Por favor introduzca el email',
      'email.email' => 'El e-mail introducido debe ser válido',
      'email.max' => 'El e-mail no puede tener más de :max caracteres',
      'email.unique' => 'El e-mail ya está registrado, introduzca otro por favor',
      'password.required' => 'Por favor introduzca la contraseña',
      'old_password.required' => 'Debe introducir su contraseña actual para cambiar sus datos',
    ];
  }

  public function allowedRoutes()
  {
    $routePermissions = $this->uniquePermissions();

    $routePermissions->filter(
      function ($val, $index) {
        return strpos($val, 'route') === 0;
      }
    );

    $routes = [];
    $route = '';
    foreach ($routePermissions as $permission) {
      $route = explode(" ", $permission)[1];
      if (array_search($route, $routes) === false) {
        $routes[] = $route;
      }
    }

    return $routes;
  }

  public function uniquePermissions()
  {
    if ($this->isSuspended()) {
      // no permissions for suspended user
      $permissions = collect(['route home']);
    } else if ($this->isAdmin()) {
      // all permissions for admin
      $permissions = SpatiePermission::get()->pluck('name');
    } else {
      // only user permissions
      $permissions = $this->getAllPermissions()->pluck('name');
    }
    array_unique($permissions->toArray());

    return $permissions;
  }

  public function allowedPermissions()
  {
    // get permissions for this user
    $allPermissions = $this->uniquePermissions();

    // all resources
    $resources = Resource::get()->pluck('name');

    $allowedPermissions = [];
    // filter permissions by resource and build an object

    foreach ($resources as $resource) {
      $resourcePermission = [];
      $resourcePermission["subject"] = $resource;
      foreach ($allPermissions as $permission) {
        if (explode(" ", $permission)[1] === $resource) {
          $resourcePermission["actions"][] = explode(" ", $permission)[0];
        }
      }
      if (array_key_exists("actions", $resourcePermission) && count($resourcePermission["actions"]) > 0) {
        array_push($allowedPermissions, $resourcePermission);
      }
    }

    return $allowedPermissions;
  }

  /**
   * Returns an array of roles of the user
   *
   * @return array
   */
  public function getRoleTexts()
  {
    return array_column($this->roles->toArray(), 'text');
  }

  public function withRolesWithoutAdmin()
  {
    $users = $this->with('roles')
      ->where('email', '!=', 'apaza.alcides@gmail.com')->get();
    foreach ($users as $user) {
      foreach ($user->roles as $role) {
        unset ($role->pivot);
      }
    }

    return $users;

  }

  /**
   * @param $email
   *
   * @return bool
   */
  public static function loginWithEmail($email)
  {
    $user = self::where('email', $email)->get();
    $user = count($user) ? $user[0] : null;
    if ($user) {
      Auth::login($user);
    }

    return (bool)$user;
  }

  /**
   * @param $email
   * @param $password
   *
   * @return bool
   */
  public static function loginWithEmailAndPass($email, $password)
  {
    return Auth::attempt(['email' => $email, 'password' => $password]);
  }

  public function isAdmin()
  {
    return $this->hasRole('admin');
  }

  public function isSuspended()
  {
    return $this->hasRole('suspended');
  }


  public function updatePassErrors($oldPassword)
  {
    $errors = [];
    if (!\Illuminate\Support\Facades\Hash::check(
      $oldPassword, $this->password)) {
      $errors[] = "La contraseña actual no coincide con los registros";
    }
    return $errors;
  }

  public function tasks(){
    return $this->hasMany('App\Task', 'user_id', 'id');
  }
}
