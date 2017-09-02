<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use App\RollReceive;
use DB;

class RollReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('main.accounting.rollreceive.index');
    }

    /**
     * Search data after submiting form
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
      // $query_1 = DB::connection('mysql_kiwidb')->statement("select zstkbypa ( '2017-08-29 00:00:00' )");
      // if($query_1){
      //   $rollreceive1 = DB::connection('mysql_kiwidb')->table("ZSTKBYPA")->get();
      //
      //   foreach ($rollreceive1 as $data) {
      //     echo $data->paper_type." -- ".$data->paper_code." -- ".$data->supplier_name." -- ".$data->width." -- ".$data->sum_weight." -- ".$data->count_roll."<br/>";
      //   }
      //   // dd($rollreceive1);
      // }
      // else{
      //   dd('2');
      // }
      // $rollreceive = DB::connection('mysql_kiwidb')->table('roll_receives')->get();

      // DB::statement(DB::raw('set @rownum=0'));
      // $query  = RollReceive::select([
      //     DB::raw('@rownum  := @rownum + 1 AS rownum'),
      //     'received_js',
      //     'docket_number',
      //     'order_id',
      //     'paper_code',
      //     'unique_roll_id',
      //     'weight',
      //     'cost_wgt_local'
      //   ])
      //   ->whereDate('received_js','>=',$request->date_from)
      //   ->whereDate('received_js','<=',$request->date_to);
      // $datatables = Datatables::of($query);
      //
      // if ($keyword = $request->get('search')['value']) {
      //     $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
      // }

      $query = DB::connection('mysql_kiwidb')->table('roll_receives')
                  ->select('roll_receives.*', DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->whereDate('received_js','>=',$request->date_from)
                  ->whereDate('received_js','<=',$request->date_to)
                  ->get();
      $datatables = Datatables::of($query);

      return $datatables->make(true);

      // return view('main.accounting.rollreceive.index')->withDatas($rollreceive);

    }
}
