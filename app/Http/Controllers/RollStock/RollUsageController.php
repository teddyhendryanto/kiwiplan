<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use App\Models\RollUsage;
use DB;

class RollUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('main.rollstock.rollusage.index');
    }

    public function submit(Request $request){

      // Get Detail Transaction
      $query = DB::connection('mysql_kiwidb')->table('roll_usages')
              ->select('roll_usages.*',
                  DB::raw("(roll_usages.weight_before_use - roll_usages.weight_use) as weight_balance"),
                  DB::raw('(@cnt := @cnt + 1) as rownum')
                )
              ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"));
      if ($request->date_from != "" && $request->date_to) {
        $query->whereDate('finish_splice_js','>=',$request->date_from)
              ->whereDate('finish_splice_js','<=',$request->date_to);
      }
      else{
        $query->whereDate('finish_splice_js','>=','2017-08-30')
              ->whereDate('finish_splice_js','<=','2017-08-31');
      }
      $query->get();

      $datatables = Datatables::of($query);

      return $datatables->make(true);
    }
}
