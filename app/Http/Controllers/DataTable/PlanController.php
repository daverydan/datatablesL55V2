<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\Plan;
use Illuminate\Http\Request;

class PlanController extends DataTableController
{
	public function builder()
	{
		return Plan::query();
	}

	public function getUpdatableColumns()
	{
		return [
			'braintree_id', 'price', 'active'
		];
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'braintree_id' => 'required',
			'price' => 'required'
		]);

		if (!$this->allowCreation) {
			return;
		}

		$this->builder->create($request->only($this->getUpdatableColumns()));
	}
}
