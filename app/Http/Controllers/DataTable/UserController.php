<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends DataTableController
{
	public function builder()
	{
		return User::query(); // returns a builder
	}
}
