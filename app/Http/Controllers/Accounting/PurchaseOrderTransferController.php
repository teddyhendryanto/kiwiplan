<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\PaperQuality;
use App\Models\PaperSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderTransfer;
use App\Models\Site;

class PurchaseOrderTransferController extends Controller
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
      return view('main.accounting.purchase_order_transfers.index')->withSites($sites);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $purchase_orders = PurchaseOrder::with('site','supplier')
                                      ->where('site_id','<>',env('SITE_ID'))
                                      ->where('transfered', false)
                                      ->orderBy('po_date')
                                      ->get();
      return view('main.accounting.purchase_order_transfers.create')->withPurchaseOrders($purchase_orders);
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

      // dd($request->cb);
      for ($i=0; $i < count($request->cb); $i++) {
        $purchase_order = PurchaseOrder::with('purchase_order_details')
                                      ->find($request->cb[$i]);

        // transfer header to REMOTE_TARGET_DB
        $tr_purchase_order = PurchaseOrder::on(env('REMOTE_TARGET_DB'))->updateOrCreate([
          'site_id' => $purchase_order->site_id,
          'yyyymm' => $purchase_order->yyyymm,
          'counter' => $purchase_order->counter,
          'supplier_id' => $purchase_order->supplier_id,
          'po_num' => $purchase_order->po_num,
          'po_num_ex' => $purchase_order->po_num_ex,
          'po_date' => $purchase_order->po_date,
          'po_qty' => $purchase_order->po_qty,
          'due_date' => $purchase_order->due_date,
          'contact_person' => $purchase_order->contact_person,
          'term' => $purchase_order->term,
          'remarks1' => $purchase_order->remarks1,
          'transfered_id' => $purchase_order->id,
          'transfered' => $purchase_order->transfered,
          'transfered_count' => $purchase_order->transfered_count,
          'rstatus' => $purchase_order->rstatus,
          'created_by' => $purchase_order->created_by,
          'updated_by' => $purchase_order->updated_by,
          'deleted_by' => $purchase_order->deleted_by,
          'created_at' => $purchase_order->created_at,
          'updated_at' => $purchase_order->updated_at,
          'deleted_at' => $purchase_order->deleted_at,
        ]);

        foreach ($purchase_order->purchase_order_details as $detail) {
          // transfer detail to REMOTE_TARGET_DB
          $tr_purchase_order_detail = PurchaseOrderDetail::on(env('REMOTE_TARGET_DB'))->updateOrCreate([
            'purchase_order_id' => $tr_purchase_order->id,
            'paper_quality' => $detail->paper_quality,
            'paper_gramatures' => $detail->paper_gramatures,
            'paper_width' => $detail->paper_width,
            'paper_qty' => $detail->paper_qty,
            'um' => $detail->um,
            'paper_price' => $detail->paper_price,
            'tax' => $detail->tax,
            'remarks' => $detail->remarks,
            'transfered_id' => $detail->id,
            'rstatus' => $detail->rstatus,
            'created_by' => $detail->created_by,
            'updated_by' => $detail->updated_by,
            'deleted_by' => $detail->deleted_by,
            'created_at' => $detail->created_at,
            'updated_at' => $detail->updated_at,
            'deleted_at' => $detail->deleted_at,
          ]);
        }

        // save log transfer
        $tr = new PurchaseOrderTransfer;
        $tr->purchase_order_id = $purchase_order->id;
        $tr->transfer_date = date('Y-m-d H:i:s');
        $tr->created_by = $user->username;
        $tr->save();

        // update status transfer
        $purchase_order->transfered = true;
        $purchase_order->transfered_count = ($purchase_order->transfered_count)+1;
        $purchase_order->save();
      }

      return redirect()->back()->with('status-success','Data berhasil di transfer.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $tr = PurchaseOrderTransfer::with('purchase_order','purchase_order.site','purchase_order.supplier')->get();
      // dd($tr);
      return view('main.accounting.purchase_order_transfers.index')->withTransfers($tr);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
