<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    //
  protected $fillable = [
    'description',
    'scheduled_at',
    'completed_at',
    'status',
    'task_id',
  ];

  protected $casts = [
    'id' => 'integer',
    'description' => 'string',
    'scheduled_at' => 'string',
    'completed_at' => 'string',
    'status' => 'string',
    'task_id' => 'integer',

  ];

  public function storeRules()
  {
    return [
      'description' => 'required',
      'scheduled_at' => 'required',
      'status' => 'required',
      'task_id' => 'required',
    ];
  }

  public function updateRules()
  {
    return [
      'description' => 'required',
      'scheduled_at' => 'required',
      'status' => 'required',
      'task_id' => 'required',
    ];
  }

  public function validationMessages()
  {
    return [
      'description.required' => 'Por favor introduzca la descripción',
      'scheduled_at.required' => 'Por favor defina la fecha límite',
      'status.required' => 'Por favor defina el estado',
      'task_id.required' => 'Debe asociar a una tarea',
    ];
  }

  public function task(){
    return $this->belongsTo('\App\Task', 'task_id', 'id');
  }
}
