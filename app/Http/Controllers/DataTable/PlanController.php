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
}
