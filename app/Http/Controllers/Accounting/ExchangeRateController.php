<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\GeneralTrait;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\ExchangeRate;

class ExchangeRateController extends Controller
{
    use GeneralTrait;

    public function __construct(){
      $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('main.accounting.exchange_rates.index');
    }

    public function getExchangeRateDatatable(Request $request){

      DB::statement(DB::raw('set @rownum=0'));
      $rates = ExchangeRate::select([
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        'id',
        'currency',
        DB::raw('cast(rate_date as date) as rate_date'),
        'selling_rate',
        'buying_rate',
        ])
        ->where('rate_date','>=',$request->date_from)
        ->where('rate_date','<=',$request->date_to)
        ->get();
      $datatables = Datatables::collection($rates)
                    ->addColumn('action', function ($rate) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('exchange_rates.destroy', $rate->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('exchange_rates.edit', $rate->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus kurs ini?\');">
                        <i class="fa fa-trash"></i>
                      </button>
                      </form>';
                    });
      return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('main.accounting.exchange_rates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, array(
        'currency' => 'required',
        'rate_date' => 'required',
        'selling_rate' => 'required|numeric',
        'buying_rate' => 'required|numeric',
      ));

      $check = ExchangeRate::where('currency', $request->currency)->where('rate_date',$request->rate_date)->first();

      if($check){
        return redirect()->back()->with('status-danger','Kurs sudah pernah diinput.');
      }

      $user = Auth::user();

      $rate = new ExchangeRate;
      $rate->currency = $request->currency;
      $rate->rate_date = $request->rate_date;
      $rate->selling_rate = $request->selling_rate;
      $rate->buying_rate = $request->buying_rate;
      $rate->created_by = $user->username;
      $rate->save();

      return redirect()->back()->with('status-success','Kurs berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $rate = ExchangeRate::findOrFail($id);
      return view('main.accounting.exchange_rates.edit')->withData($rate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, array(
        'rate_date' => 'required',
        'selling_rate' => 'required|numeric',
        'buying_rate' => 'required|numeric',
      ));

      $user = Auth::user();

      $rate = ExchangeRate::findOrFail($id);
      $rate->rate_date = $request->rate_date;
      $rate->selling_rate = $request->selling_rate;
      $rate->buying_rate = $request->buying_rate;
      $rate->rstatus = 'AM';
      $rate->updated_by = $user->username;
      $rate->save();

      return redirect()->route('exchange_rates.index')->with('status-success','Kurs berhasil diperbaharui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = Auth::user();

      $rate = ExchangeRate::findOrFail($id);
      $rate->rstatus = 'DL';
      $rate->deleted_by = $user->username;
      $rate->deleted_at = Carbon::now();
      $rate->save();

      return redirect()->back()->with('status-success','Kurs berhasil dihapus.');
    }
}
