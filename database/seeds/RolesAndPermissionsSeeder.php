<?php

use Illuminate\Database\Seeder;

use App\SpatieRole as Role;
use App\SpatiePermission as Permission;
use App\Action;
use App\Resource;

class RolesAndPermissionsSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		// Reset cached roles and permissions
		app()[ \Spatie\Permission\PermissionRegistrar::class ]->forgetCachedPermissions();

		// create actions
		Action::create( [ 'name' => 'index', 'text' => 'Listar' ] );
		Action::create( [ 'name' => 'create', 'text' => 'Crear' ] );
		Action::create( [ 'name' => 'read', 'text' => 'Leer' ] );
		Action::create( [ 'name' => 'update', 'text' => 'Actualizar' ] );
		Action::create( [ 'name' => 'delete', 'text' => 'Borrar' ] );
		Action::create( [ 'name' => 'route', 'text' => 'Enrutar' ] );
    Action::create( [ 'name' => 'graph', 'text' => 'Graficar' ] );

		// create resources
		Resource::create( [ 'name' => 'home', 'text' => 'Inicio' ] );
		Resource::create( [ 'name' => 'users', 'text' => 'Usuarios' ] );
		Resource::create( [ 'name' => 'roles', 'text' => 'Roles' ] );
		Resource::create( [ 'name' => 'permissions', 'text' => 'Permisos' ] );
		Resource::create( [ 'name' => 'actions', 'text' => 'Acciones' ] );
		Resource::create( [ 'name' => 'resources', 'text' => 'Recursos' ] );
		Resource::create( [ 'name' => 'tasks', 'text' => 'Tareas' ] );
		Resource::create( [ 'name' => 'milestones', 'text' => 'Hitos' ] );


    // create permissions
		$actions   = Action::get();
		$resources = Resource::get();
		foreach ( $actions as $action ) {
			foreach ( $resources as $resource ) {
				Permission::create( [
					'name' => $action['name'] . ' ' . $resource['name'],
					'text' => $action['text'] . ' ' . $resource['text'],
				] );
			}
		}

		// main roles
    $role = Role::create( [ 'name' => 'admin', 'text' => 'Administrador' ] ); // all permissions by gate to the admin

    $role = Role::create( [ 'name' => 'sysadmin', 'text' => 'Administrador del sistema' ] ); // Administrador visible del sistema
    $role->givePermissionTo( 'route home' );
    $role->givePermissionTo( 'route tasks' );
    $role->givePermissionTo( 'read tasks' );
    $role->givePermissionTo( 'index tasks' );
    $role->givePermissionTo( 'create tasks' );
    $role->givePermissionTo( 'update tasks' );
    $role->givePermissionTo( 'delete tasks' );
    $role->givePermissionTo( 'graph tasks' );
    $role->givePermissionTo( 'read milestones' );
    $role->givePermissionTo( 'index milestones' );
    $role->givePermissionTo( 'create milestones' );
    $role->givePermissionTo( 'update milestones' );
    $role->givePermissionTo( 'delete milestones' );

    $role = Role::create( [ 'name' => 'director', 'text' => 'Director' ] ); // Director
    $role->givePermissionTo( 'route home' );
    $role->givePermissionTo( 'route tasks' );
    $role->givePermissionTo( 'index tasks' );
    $role->givePermissionTo( 'create tasks' );
    $role->givePermissionTo( 'update tasks' );
    $role->givePermissionTo( 'graph tasks' );
    $role->givePermissionTo( 'read milestones' );
    $role->givePermissionTo( 'index milestones' );
    $role->givePermissionTo( 'create milestones' );
    $role->givePermissionTo( 'update milestones' );
    $role->givePermissionTo( 'delete milestones' );

    $role = Role::create( [ 'name' => 'boss', 'text' => 'Jefe' ] ); // Director
    $role->givePermissionTo( 'route home' );
    $role->givePermissionTo( 'route tasks' );
    $role->givePermissionTo( 'index tasks' );
    $role->givePermissionTo( 'create tasks' );
    $role->givePermissionTo( 'update tasks' );
    $role->givePermissionTo( 'graph tasks' );
    $role->givePermissionTo( 'read milestones' );
    $role->givePermissionTo( 'index milestones' );
    $role->givePermissionTo( 'create milestones' );
    $role->givePermissionTo( 'update milestones' );
    $role->givePermissionTo( 'delete milestones' );

    $role = Role::create( [ 'name' => 'guest', 'text' => 'Invitado' ] ); // VER TODO SIN MODIFICAR
    $role->givePermissionTo( 'route home' );
    $role->givePermissionTo( 'route tasks' );
    $role->givePermissionTo( 'index tasks' );
    $role->givePermissionTo( 'graph tasks' );
    $role->givePermissionTo( 'read milestones' );
    $role->givePermissionTo( 'index milestones' );

    $role = Role::create( [ 'name' => 'suspended', 'text' => 'Suspendido' ] );
		$role->givePermissionTo( 'route home' );

		// assign roles to users
    \App\User::where('email','alcides.apaza@ylb.gob.bo')->first()->assignRole('admin');
    \App\User::where('email','sebastian.mamani@hidrocarburos.gob.bo')->first()->assignRole('sysadmin');
    \App\User::where('email','victor.fuentes@ylb.gob.bo')->first()->assignRole('sysadmin');

    // bosses roles seeder
    \App\User::where('email','ximena.lozano@ylb.gob.bo')->first()->assignRole('boss');
    \App\User::where('email','nelson.carvajal@ylb.gob.bo')->first()->assignRole('boss');
    \App\User::where('email','roger.filipps@ylb.gob.bo')->first()->assignRole('boss');

    // director roles seeder
    \App\User::where('email',"salvador.beltran@ylb.gob.bo")->first()->assignRole('director');
    \App\User::where('email',"celia.mendez@ylb.gob.bo")->first()->assignRole('director');
    \App\User::where('email',"rodney.goitia@ylb.gob.bo")->first()->assignRole('director');
    \App\User::where('email',"saul.cuiza@ylb.gob.bo")->first()->assignRole('director');
    \App\User::where('email',"igor.duran@ylb.gob.bo")->first()->assignRole('director');

    // veedores revisores seeder
    \App\User::where('email',"carlossebastianmc@gmail.com")->first()->assignRole('guest');
	}
}
