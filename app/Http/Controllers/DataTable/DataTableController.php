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

	public function index()
	{
		// return view('admin.users.index');
		return response()->json([
			'data' => [
				'table' => $this->builder->getModel()->getTable(), // string: table name
				'displayable' => array_values($this->getDisplayableColumns()),
				'records' => $this->getRecords(),
			]
		]);
	}

	public function getDisplayableColumns()
	{
		// remove hidden table columns from display
		// array_diff - Returns an array containing all the entries from array1 that are not present in any of the other arrays.
		return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
	}

	protected function getDatabaseColumnNames()
	{ // return all the column names ( returns the `users` table )
		return Schema::getColumnListing($this->builder->getModel()->getTable());
	}

	protected function getRecords()
	{
		return $this->builder->get($this->getDisplayableColumns());
	}
}
