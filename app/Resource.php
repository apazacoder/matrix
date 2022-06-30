<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
	protected $fillable = [
		'name',
		'text',
	];

	protected $hidden = [];

	protected $casts = [
		'name' => 'string',
		'text' => 'string',
	];

	public function storeRules() {
		return [
			'name' => 'required|max:50|unique:resources',
			'text' => 'required|max:50|unique:resources',
		];
	}

	public function updateRules() {
		return [
			'name' => 'required|max:50|unique:resources,name,' . $this->id,
			'text' => 'required|max:50|unique:resources,text,' . $this->id,
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

  // individual storage of permissions and resources
  public function storeWithPermissionNameAndText($name, $text)
  {
    $resource = self::create([
      'name' => $name,
      'text' => $text
    ]);
    return SpatiePermission::storeForResource($resource);
  }
}
