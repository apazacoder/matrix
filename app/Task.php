<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  protected $fillable = [
    'issue',
    'description',
    'user_id',
  ];

  protected $casts = [
    'id' => 'integer',
    'issue' => 'string',
    'description' => 'string',
    'user_id' => 'integer',

  ];

  public function storeRules()
  {
    return [
      'issue' => 'required',
      'description' => 'required',
    ];
  }

  public function updateRules()
  {
    return [
      'issue' => 'required',
      'description' => 'required',
    ];
  }

  public function validationMessages()
  {
    return [
      'issue.required' => 'Por favor introduzca el asunto',
      'description.required' => 'Por favor introduzca la descripción',
    ];
  }

  public function user(){
    return $this->belongsTo('\App\User', 'user_id', 'id');
  }

  public function milestones()
  {
    return $this->hasMany('\App\Milestone', 'task_id', 'id');
  }

}
