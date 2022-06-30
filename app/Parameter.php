<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
  protected $fillable = [
    'name',
    'text',
    'value',
  ];

  protected $hidden = [];

  protected $casts = [
    'id' => 'integer',
    'name' => 'string',
    'text' => 'string',
    'value' => 'string',
  ];

  public function storeRules()
  {
    return [
      'name' => 'required|max:128',
      'text' => 'required|max:128',
      'value' => 'required|max:128',
    ];
  }

  public function updateRules()
  {
    return [
      'name' => 'required|max:128',
      'text' => 'required|max:128',
      'value' => 'required|max:128',
    ];
  }

  public function validationMessages()
  {
    return [
      'name.required' => 'Por favor introduzca el nombre del parámetro',
      'name.max' => 'El nombre del parámetro no puede tener más de :max caracteres',
      'text.required' => 'Por favor introduzca el texto visible del parámetro',
      'text.max' => 'El texto visible del parámetro no puede tener más de :max caracteres',
      'value.required' => 'Por favor introduzca el valor del parámetro',
      'value.max' => 'El valor del parámetro no puede tener más de :max caracteres',
    ];
  }
}
