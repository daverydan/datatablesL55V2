<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends DataTableController
{
	public function builder()
	{
		// I don't understand why we're using ::query()
		// where does it come from
		return User::query(); // returns a builder
	}

	// override getDisplayableColumns() in DataTableController
	public function getDisplayableColumns()
	{
		// how does this override getDisplayableColumns()???
		return [
			'id', 'name', 'email', 'created_at'
		];
	}
}
