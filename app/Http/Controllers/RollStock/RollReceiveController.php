<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use App\Models\RollReceive;
use DB;

class RollReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = null, $value = null)
    {
      return view('main.rollstock.rollreceive.index')->withType($type)->withValue($value);
    }

    /**
     * Search data after submiting form
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
      $query = DB::connection('mysql_kiwidb')->table('roll_receives')
                  ->select('roll_receives.*', DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"));
      if($request->type != "" && $request->value != ""){
        switch ($request->type) {
          case 'weight':
            $query->where('roll_receives.weight',$request->value);
            break;
          case 'cost':
            $query->where('roll_receives.cost_wgt_local',$request->value);
            break;
        }
      }
      else{
        $query->whereDate('received_js','>=',$request->date_from)
              ->whereDate('received_js','<=',$request->date_to);
      }

      $datatables = Datatables::of($query->get());

      return $datatables->make(true);

    }
}
