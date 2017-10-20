<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;

use Auth;
use DB;
use Carbon\Carbon;

use App\Models\Site;
use App\Models\PaperWidth;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderRealization;

class PurchaseOrderRealizationController extends Controller
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
      $sites = Site::all();

      return view('main.rollstock.realization.index')->withSites($sites);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $sites = Site::all();
      $widths = PaperWidth::all();
      return view('main.rollstock.realization.create')
            ->withSites($sites)
            ->withWidths($widths);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();

      $realization = new PurchaseOrderRealization;
      $realization->purchase_order_id = $request->po_id;
      $realization->paper_key = $request->realization_key;
      $realization->paper_width = $request->realization_width;
      $realization->paper_qty = $request->realization_weight;
      $realization->created_by =  $user->username;
      $realization->save();

      return redirect()->back()->with('status-success','Data berhasil disimpan.');

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

    public function showHistory(Request $request){

      $sites = Site::all();

      $site_id = $request->site;
      $po_num = $request->po_num;

      $purchase_order = PurchaseOrder::where('site_id',$site_id)
                                      ->where('po_num', $po_num)
                                      ->first();

      $purchase_order_id = $purchase_order->id;

      $realizations = PurchaseOrderRealization::select([
                                                'purchase_order_realizations.*',
                                                'receive_rolls.roll_weight'
                                              ])
                                              ->where('purchase_order_id', $purchase_order_id)
                                              ->with(['purchase_order','purchase_order.supplier'])
                                              ->leftJoin(DB::raw("
                                                  (select site_id, po_id, paper_key, paper_width, sum(roll_weight) as roll_weight
                                                  from receive_rolls
                                                  where site_id = '$site_id' and po_id = '$purchase_order_id'
                                                  group by site_id, po_id, paper_key, paper_width)
                                                  receive_rolls
                                                "), function ($join) {
                                                    $join->on('receive_rolls.po_id', 'purchase_order_realizations.purchase_order_id')
                                                         ->on('receive_rolls.paper_key', 'purchase_order_realizations.paper_key')
                                                         ->on('receive_rolls.paper_width', 'purchase_order_realizations.paper_width');
                                                })
                                              ->get();
      if(count($realizations) > 0){
        return view('main.rollstock.realization.index')->withSites($sites)
                ->withPoNum($po_num)
                ->withRealizations($realizations);
      }
      else{
        return redirect()->route('purchase_order_realizations.index')->with('status-danger', 'Data tidak ditemukan.');
      }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $sites = Site::all();
      $widths = PaperWidth::all();
      $realizations = PurchaseOrderRealization::with([
                                                'purchase_order',
                                                'purchase_order.supplier',
                                              ])->findOrFail($id);
      return view('main.rollstock.realization.edit')
            ->withSites($sites)
            ->withWidths($widths)
            ->withData($realizations);
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
      $user = Auth::user();

      $realization = PurchaseOrderRealization::findOrFail($id);
      $realization->paper_key = $request->realization_key;
      $realization->paper_width = $request->realization_width;
      $realization->paper_qty = $request->realization_weight;
      $realization->rstatus = 'AM';
      $realization->updated_by =  $user->username;
      $realization->save();

      if($realization){
        echo "
          <script>
            alert('Data berhasil diperbaharui.');
            window.close();
          </script>
        ";
      }
      else{
        echo "
          <script>
            alert('Data gagal diperbaharui.');
            window.close();
          </script>
        ";
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete($id){
      $user = Auth::user();

      $realization = PurchaseOrderRealization::findOrFail($id);
      $realization->rstatus = 'DL';
      $realization->deleted_by = $user->username;
      $realization->deleted_at = Carbon::now();
      $realization->save();

      if($realization){
        echo "
          <script>
            alert('Data berhasil dihapus.');
            window.close();
          </script>
        ";
      }
      else{
        echo "
          <script>
            alert('Data gagal dihapus.');
            window.close();
          </script>
        ";
      }
    }
}
