<?php

namespace App;

use Spatie\Permission\Models\Role;

class SpatieRole extends Role {

	protected $guard_name = "api";
	/**
	 * Return all roles without admin
	 * @return array
	 */

	protected $casts = [
    'id' => 'string',
		'name' => 'string',
		'text' => 'string'
	];

	public function storeRules() {
		return [
			'name' => 'required|max:110|unique:roles',
			'text' => 'required|max:110|unique:roles',
		];
	}

	public function updateRules() {
		return [
			'name' => 'required|max:110|unique:roles,name,' . $this->id,
			'text' => 'required|max:110|unique:roles,text,' . $this->id,
		];
	}

	public function validationMessages() {
		return [
			'name.required' => 'Por favor introduzca el nombre',
			'name.max'      => 'El nombre no puede tener más de :max caracteres',
			'name.unique'   => 'El nombre ya está registrado, introduzca otro por favor',
			'text.required' => 'Por favor introduzca el texto',
			'text.max'      => 'El texto no puede tener más de :max caracteres',
			'text.unique'   => 'El texto ya está registrado, introduzca otro por favor',
		];
	}


	public function withoutAdmin() {
		$roles = $this->all()->toArray();
		foreach ( $roles as $index => $role ) {
			if ( $role["name"] === "admin" ) {
				array_splice( $roles, $index, 1 );
			}
		}

		return $roles;
	}

//	public function withPermissionsWithoutAdmin() {
//		$roles = \Spatie\Permission\Models\Role::with( 'permissions' )->get();
//		foreach ( $roles as $index => $role ) {
//			if ( $role->name === "admin" ) {
//				unset( $roles[ $index ] );
//			}
//			foreach ( $role->permissions as $permission ) {
//				unset( $permission->pivot );
//			}
//		}
//
//		return $roles->toArray();
//	}

	public function withPermissionsWithoutAdmin() {
		$roles = $this->with( 'permissions' )->get()->toArray();
		foreach ( $roles as $ir => &$role ) {
			if ( $role["name"] === "admin" ) {
				array_splice($roles, $ir, 1);
			}else{
				foreach ( $role["permissions"] as &$permission ) {
					unset( $permission["pivot"] );
				}
			}
		}
		return $roles;
	}
}
