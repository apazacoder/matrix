<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model {
	protected $fillable = [
		'name',
		'text',
	];

	protected $hidden = [];

	protected $casts = [
    'id' => 'integer',
		'name' => 'string',
		'text' => 'string',
	];

	public function storeRules() {
		return [
			'name' => 'required|max:50|unique:actions',
			'text' => 'required|max:50|unique:actions',
		];
	}

	public function updateRules() {
		return [
			'name' => 'required|max:50|unique:actions,name,' . $this->id,
			'text' => 'required|max:50|unique:actions,text,' . $this->id,
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
}
