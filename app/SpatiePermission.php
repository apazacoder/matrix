<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;

class SpatiePermission extends Permission {

	protected $guard_name = "api";

	/**
	 * @param Resource $resource
	 */
	public static function storeForResource( Resource $resource ) {
		$actions = Action::get();
		try {
			DB::beginTransaction();

			foreach ( $actions as $action ) {
				$permission = self::create( [
					'name' => $action->name . ' ' . $resource->name,
					'text' => $action->text . ' ' . $resource->text
				] );
			}
			DB::commit();

			return true;
		} catch ( \Exception $e ) {
			DB::rollBack();

			return false;
		}
	}

	public static function updateForResource( $oldName, Resource $resource ) {
		try {
			DB::beginTransaction();
			$permissions = self::where( 'name', 'like', '% ' . $oldName )->get();
			foreach ( $permissions as $permission ) {
				$updatedPermission = self::find( $permission->id )->update( [
					'name' => explode( " ", $permission->name )[0] . ' ' . $resource->name,
					'text' => explode( " ", $permission->text )[0] . ' ' . $resource->text
				] );
			}
			DB::commit();

			return true;
		} catch ( \Exception $e ) {
			DB::rollBack();

			return false;
		}
	}

	public static function deleteForResource( $resourceName ) {
		return (bool) self::where( 'name', 'like', '% ' . $resourceName )->delete();
	}

	public static function storeForAction( Action $action ) {
		$resources = Resource::get();
		try {
			DB::beginTransaction();

			foreach ( $resources as $resource ) {
				$permission = self::create( [
					'name' => $action->name . ' ' . $resource->name,
					'text' => $action->text . ' ' . $resource->text,
				] );
			}
			DB::commit();

			return true;
		} catch ( \Exception $e ) {
			DB::rollBack();

			return false;
		}
	}

	public static function updateForAction( $oldName, Action $action ) {
		try {
			DB::beginTransaction();
			$permissions = self::where( 'name', 'like', $oldName . ' %' )->get();
			foreach ( $permissions as $permission ) {
				$updatedPermission = self::find( $permission->id )->update( [
					'name' => $action->name . ' ' . explode( " ", $permission->name )[1],
					'text' => $action->text . ' ' . explode( " ", $permission->text )[1]
				] );
			}
			DB::commit();

			return true;
		} catch ( \Exception $e ) {
			\Illuminate\Support\Facades\Log::info( 'Error on update For action' );
			\Illuminate\Support\Facades\Log::info( $e->getMessage() );
			DB::rollBack();

			return false;
		}
	}

	public static function deleteForAction( $actionName ) {
		return (bool) self::where( 'name', 'like', $actionName . ' %' )->delete();
	}
}
