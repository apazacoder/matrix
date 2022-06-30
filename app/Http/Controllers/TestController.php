<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;

class TestController extends Controller
{
	public $user;

	public function __construct(user $user){
		$this->user = $user;
	}
    public function exercisesIndex() {
		return 'hey';
    }

}
