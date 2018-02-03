<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
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
		$builder = $this->builder;

		if ($this->hasSearchQuery($request)) {
			$builder = $this->buildSearch($builder, $request);
		}

		try {
			return $this->builder->limit($request->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
		} catch(QueryException $e) {
			return [];
		}
	}

	protected function hasSearchQuery(Request $request)
	{
		// array_filter = only get the elements that aren't NULL
		// count(check if not NULL([check only these fields])) === 3
		return count(array_filter($request->only(['column', 'operator', 'value']))) === 3;
	}

	protected function buildSearch(Builder $builder, Request $request)
	{
		$queryParts = $this->resolveQueryParts($request->operator, $request->value);
		
		return $builder->where($request->column, $queryParts['operator'], $queryParts['value']);
	}

	protected function resolveQueryParts($operator, $value)
	{
		// https://laravel.com/docs/5.5/helpers#method-array-get
		return array_get([
			'equals' => [
				'operator' => '=',
				'value' => $value
			],
			'contains' => [
				'operator' => 'LIKE',
				'value' => "%{$value}%"
			],
			'starts_with' => [
				'operator' => 'LIKE',
				'value' => "{$value}%"
			],
			'ends_with' => [
				'operator' => 'LIKE',
				'value' => "%{$value}"
			],
			'greater_than' => [
				'operator' => '>',
				'value' => $value
			],
			'less_than' => [
				'operator' => '<',
				'value' => $value
			],
			'greater_than_or_equal_to' => [
				'operator' => '>=',
				'value' => $value
			],
			'less_than_or_equal_to' => [
				'operator' => '<=',
				'value' => $value
			]
		], $operator);
	}
}
