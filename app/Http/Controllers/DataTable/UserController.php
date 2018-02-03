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

	// override getUpdatableColumns() in DataTableController
	public function getUpdatableColumns()
	{
		// how does this override getDisplayableColumns()???
		return [
			'name', 'email', 'created_at'
		];
	}

	// override update() in DataTableController abstract/parent class
	public function update($id, Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
			// unique: at users table in the email column
			'email' => 'required|email|unique:users,email',
		]);

		$this->builder()->find($id)->update($request->only($this->getUpdatableColumns()));
	}
}
