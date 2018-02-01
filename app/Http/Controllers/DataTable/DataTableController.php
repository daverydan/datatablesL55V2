<?php

namespace App\Http\Controllers\DataTable;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class DataTableController extends Controller
{
	protected $builder;

	abstract public function builder();

	public function __construct()
	{
		$builder = $this->builder();

		// dd($builder);
		// dd(get_class($builder));
		// Check if instance of Builder
		if (!$builder instanceof Builder) {
			throw new Exception('Entity builder not instance of Builder');
		}
	}

	public function index()
	{
		// 
	}
}
