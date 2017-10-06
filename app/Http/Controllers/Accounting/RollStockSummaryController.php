<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use DB;

class RollStockSummaryController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
      return view('main.accounting.reports.stock.index');
  }

  public function submit(Request $request)
  {
    $query = DB::connection('mysql_kiwidb')
              ->select("call zstksum('".$request->date." 23:59:59')");

    $output = array(
      'data' => $query,
    );

    return response()->json($output);

  }
}
