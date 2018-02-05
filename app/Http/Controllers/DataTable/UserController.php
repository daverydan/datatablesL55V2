<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends DataTableController
{
	protected $allowCreation = true;

	public function builder()
	{
		// I don't understand why we're using ::query()
		// where does it come from
		return User::query(); // returns a builder
	}

	/**
	 * Get custom column names.
	 * @return array
	 */
	public function getCustomColumnNames()
	{
		return [
			'name' => 'Full name',
			'email' => 'Email adress'
		];
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
			'name', 'email'
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
