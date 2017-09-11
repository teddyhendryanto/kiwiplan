<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use DB;

class RollStockController extends Controller
{
  public function index()
  {
      return view('main.rollstock.stock.index');
  }

  public function submit(Request $request)
  {
    $report_by = $request->report_by;

    if($report_by =='detail'){
      $init = DB::connection('mysql_kiwidb')->statement("select zstkat ( '".$request->date." 00:00:00' )");

      if(!$init){
        $output = array(
          'message' => 'Create Temporary Table Failed.',
          'status' => false
        );
        return response()->json($output);
      }
      $query = DB::connection('mysql_kiwidb')->table("ZSTKAT")
                  ->select('ZSTKAT.*',
                  DB::raw('datediff(current_date(),ZSTKAT.received_js) as roll_aging'),
                  DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->get();
      $datatables = Datatables::of($query);

      return $datatables->make(true);
    }
    else{
      $init = DB::connection('mysql_kiwidb')->statement("select zstkbypa ( '".$request->date." 00:00:00' )");

      if(!$init){
        $output = array(
          'message' => 'Create Temporary Table Failed.',
          'status' => false
        );
        return response()->json($output);
      }

      $query = DB::connection('mysql_kiwidb')->table("ZSTKBYPA")
                  ->select('ZSTKBYPA.*',
                  DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->get();
      $datatables = Datatables::of($query);

      return $datatables->make(true);
    }

  }

}
