<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use DB;

class RollReceiveSummaryController extends Controller
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
      return view('main.accounting.reports.rollreceive.index');
  }

  public function submit(Request $request)
  {
    $query = DB::connection('mysql_kiwidb')
              ->select("call zrollrcvsum('".$request->date_from." 00:00:00','".$request->date_to." 23:59:59')");

    $output = array(
      'data' => $query,
    );

    return response()->json($output);

  }
}
