<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

abstract class DataTableController extends Controller
{
	protected $builder;

	abstract public function builder();

	public function __construct()
	{
		$builder = $this->builder();

		// Check if instance of Builder
		if (!$builder instanceof Builder) {
			throw new Exception('Entity builder not instance of Builder');
		}

		$this->builder = $builder;
	}

	public function index(Request $request)
	{
		// return view('admin.users.index');
		return response()->json([
			'data' => [
				'table' => $this->builder->getModel()->getTable(), // string: table name
				'displayable' => array_values($this->getDisplayableColumns()),
				'updatable' => array_values($this->getUpdatableColumns()),
				'records' => $this->getRecords($request),
			]
		]);
	}

	public function update($id, Request $request)
	{
		$this->builder()->find($id)->update($request->only($this->getUpdatableColumns()));
	}

	public function getDisplayableColumns()
	{
		// remove hidden table columns from display
		// array_diff - Returns an array containing all the entries from array1 that are not present in any of the other arrays.
		return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
	}

	public function getUpdatableColumns()
	{ // can override in sub/derived/child class
		return $this->getDisplayableColumns();
	}

	protected function getDatabaseColumnNames()
	{ // return all the column names ( returns the `users` table )
		return Schema::getColumnListing($this->builder->getModel()->getTable());
	}

	protected function getRecords(Request $request)
	{
		return $this->builder->limit($request->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
	}
}
