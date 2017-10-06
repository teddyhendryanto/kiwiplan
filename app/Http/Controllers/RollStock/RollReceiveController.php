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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
      $init = DB::connection('mysql_kiwidb')->statement("select zrollrcv ( '".$request->date_from." 00:00:00','".$request->date_to." 23:59:59' )");

      if(!$init){
        $output = array(
          'message' => 'Create Temporary Table Failed.',
          'status' => false
        );
        return response()->json($output);
      }
      $query = DB::connection('mysql_kiwidb')->table("ZTROLLRCV")
                  ->select('ZTROLLRCV.*',
                  DB::raw('(@cnt := @cnt + 1) as rownum'))
                  ->crossJoin(DB::raw("(SELECT @cnt := 0) as dummy"))
                  ->get();

      $datatables = Datatables::of($query);

      return $datatables->make(true);

    }
}
