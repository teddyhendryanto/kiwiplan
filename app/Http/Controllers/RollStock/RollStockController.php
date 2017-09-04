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

    $init = DB::connection('mysql_kiwidb')->statement("select zstkat ( '".$request->date." 00:00:00' )");
    if($init){
      $query = DB::connection('mysql_kiwidb')->table("ZSTKAT")
                  ->select('ZSTKAT.*',
                  DB::raw('datediff(current_date(),ZSTKAT.received_js) as roll_aging'),
                  DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->get();
      $datatables = Datatables::of($query);

      return $datatables->make(true);

      $rollreceive1 = DB::connection('mysql_kiwidb')->table("ZSTKBYPA")->get();
      foreach ($rollreceive1 as $data) {
        echo $data->paper_type." -- ".$data->paper_code." -- ".$data->supplier_name." -- ".$data->width." -- ".$data->sum_weight." -- ".$data->count_roll."<br/>";
      }
    }
    else{
      dd('2');
    }

  }

}
