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

      $init = DB::connection('mysql_kiwidb')->statement("select zrolluse ( '".$request->date_from." 00:00:00','".$request->date_to." 23:59:59' )");

      if(!$init){
        $output = array(
          'message' => 'Create Temporary Table Failed.',
          'status' => false
        );
        return response()->json($output);
      }
      $query = DB::connection('mysql_kiwidb')->table("ZTROLLUSE")
                  ->select('ZTROLLUSE.*',
                  DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->get();

      $datatables = Datatables::of($query->get());

      return $datatables->make(true);
    }
}
