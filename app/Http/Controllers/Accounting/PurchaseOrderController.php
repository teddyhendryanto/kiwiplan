<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\GeneralTrait;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\PaperQuality;
use App\Models\PaperSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Site;

class PurchaseOrderController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('main.accounting.purchase_orders.index');
    }

    public function getPO(Request $request, $site){

      DB::statement(DB::raw('set @rownum=0'));
      $orders = PurchaseOrder::with('site','supplier')->select([
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        'id',
        'site_id',
        'supplier_id',
        'po_num',
        DB::raw('cast(po_date as date) as po_date')
        ])->get();
      $datatables = Datatables::collection($orders)
                    ->addColumn('action', function ($order) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('purchase_orders.destroy', $order->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('purchase_orders.edit', $order->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus order ini?\');">
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
        $sites = Site::all();
        $suppliers = PaperSupplier::all();
        $qualities = PaperQuality::all();

        return view('main.accounting.purchase_orders.create')->withSites($sites)
                ->withSuppliers($suppliers)
                ->withQualities($qualities);
    }

    function getLastPONumberBefore(Request $request){

  		$site_id = $request->site_id;
  		$po_num_ex = $request->po_num_ex;

  		$get_po_num = PurchaseOrder::where('site_id',$site_id)->where('po_num',$po_num_ex)->first();

  		if($get_po_num){
  			$output = array(
  				'dataset' => $get_po_num,
  				'status' => true
  			);
  		}
  		else{
  			$output = array(
  				'status' => false
  			);
  		}

  		return response()->json($output);
  	}

    function getLastPONumber(Request $request){
  		$getdate = date('Ym');

  		$site_id = $request->site_id;

      $lastcounter = PurchaseOrder::select(DB::raw('ifnull(max(counter),20000) as counter'))
                    ->where('yyyymm',$getdate)
                    ->where('site_id',$site_id)->first();

  		$newcounter = $lastcounter->counter+1;
  		$ponum = date('m').'-'.date('Y').'-'.$newcounter;

  		$output = array(
  			'newcounter' => $newcounter,
  			'ponum' => $ponum,
  			'status' => true
  		);

  		return response()->json($output);
  	}

    public function getSupplierDetail(Request $request){
      $supplier_id = $request->supplier_id;
      $supplier = PaperSupplier::with('purchase_order_frequents')->findOrFail($supplier_id);

      if($supplier){
  			$output = array(
  				'dataset' => $supplier,
  				'status' => true
  			);
  		}
  		else{
  			$output = array(
  				'status' => false
  			);
  		}

  		return response()->json($output);
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
        'type' => 'required',
        'site' => 'required',
        'po_num' => 'required',
        'supplier' => 'required',
        'po_date' => 'required',
        'po_qty' => 'required|numeric',
      ));

      $user = Auth::user();

      $type = $request->type;

      $orders = new PurchaseOrder;
      $orders->site_id = $request->site;
      $orders->yyyymm = date('Ym');
      $orders->counter = $request->po_num_counter;
      $orders->supplier_id = $request->supplier;
      $orders->po_num = strtoupper($request->po_num);
      if($type == 'KHUSUS'){
        $orders->po_num_ex = strtoupper($request->po_num_ex);
      }
      else if($type == 'OVERRIDE'){
        $orders->po_num = strtoupper($request->po_num_ex);
        $orders->po_num_ex = strtoupper($request->po_num);
      }
      $orders->po_date = $request->po_date;
      $orders->po_qty = $request->po_qty;
      $orders->due_date = $this->emptyStringToNull(strtoupper($request->due_date));
      $orders->contact_person = $this->emptyStringToNull(strtoupper($request->contact_person));
      $orders->term = $this->emptyStringToNull($request->term);
      $orders->remarks1 = $this->emptyStringToNull(strtoupper($request->remarks));
      $orders->created_by = $user->username;
      $orders->save();

      if($orders){
        $count = 0;
        for ($i=0; $i < 10; $i++) {

          // skip for null value
          if($request->pquality[$i] == ""){
            break;
          }

          $details = new PurchaseOrderDetail;
          $details->purchase_order_id = $orders->id;
          $details->paper_quality = $request->pquality[$i];
          $details->paper_gramatures = $request->pgram[$i];
          $details->paper_width = $this->emptyStringToNull($request->pwidth[$i]);
          $details->paper_qty = $this->emptyStringToNull($request->pqty[$i]);
          $details->um = $this->emptyStringToNull($request->pum[$i]);
          $details->paper_price = $request->pprice[$i];
          $details->tax = $request->ptax[$i];
          $details->remarks = $this->emptyStringToNull($request->premarks[$i]);
          $details->created_by = $user->username;
          $details->save();

          if($details){
            $count++;
          }

        } // end loop
      }

      if($count > 0){
        return redirect()->back()->with('status-success','Purchase order berhasil disimpan.');
      }
      else{
        return redirect()->back()->with('status-data','Purchase order gagal disimpan.');
      }

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
      $sites = Site::all();
      $suppliers = PaperSupplier::all();
      $qualities = PaperQuality::all();
      $data = PurchaseOrder::with('site','supplier','purchase_order_details')->findOrFail($id);

      return view('main.accounting.purchase_orders.edit')->withData($data)
              ->withSites($sites)
              ->withSuppliers($suppliers)
              ->withQualities($qualities);

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
        'po_date' => 'required',
        'po_qty' => 'required|numeric',
      ));

      $user = Auth::user();

      $orders = PurchaseOrder::findOrFail($id);
      $orders->po_date = $request->po_date;
      $orders->po_qty = $request->po_qty;
      $orders->due_date = $this->emptyStringToNull(strtoupper($request->due_date));
      $orders->contact_person = $this->emptyStringToNull(strtoupper($request->contact_person));
      $orders->term = $this->emptyStringToNull($request->term);
      $orders->remarks1 = $this->emptyStringToNull(strtoupper($request->remarks));
      $orders->updated_by = $user->username;
      $orders->save();

      if($orders){
        $count = 0;
        for ($i=0; $i < 10; $i++) {

          if(isset($request->detail_id[$i]) && $request->detail_id[$i] != ""){
            // skip for null paper_quality
            if($request->pquality[$i] == ""){
              break;
            }
            // detail id -> update record
            $details = PurchaseOrderDetail::findOrFail($request->detail_id[$i]);
            $details->paper_quality = $request->pquality[$i];
            $details->paper_gramatures = $request->pgram[$i];
            $details->paper_width = $this->emptyStringToNull($request->pwidth[$i]);
            $details->paper_qty = $this->emptyStringToNull($request->pqty[$i]);
            $details->um = $this->emptyStringToNull($request->pum[$i]);
            $details->paper_price = $request->pprice[$i];
            $details->tax = $request->ptax[$i];
            $details->remarks = $this->emptyStringToNull($request->premarks[$i]);
            $details->rstatus = 'AM';
            $details->updated_by = $user->username;
            $details->save();

            if($details){
              $count++;
            }
          }
          else{
            // skip for null paper_quality
            if($request->pquality[$i] == ""){
              break;
            }
            // no detail id -> new record
            $details = new PurchaseOrderDetail;
            $details->purchase_order_id = $orders->id;
            $details->paper_quality = $request->pquality[$i];
            $details->paper_gramatures = $request->pgram[$i];
            $details->paper_width = $this->emptyStringToNull($request->pwidth[$i]);
            $details->paper_qty = $this->emptyStringToNull($request->pqty[$i]);
            $details->um = $this->emptyStringToNull($request->pum[$i]);
            $details->paper_price = $request->pprice[$i];
            $details->tax = $request->ptax[$i];
            $details->remarks = $this->emptyStringToNull($request->premarks[$i]);
            $details->created_by = $user->username;
            $details->save();

            if($details){
              $count++;
            }
          }

        } // end loop
      }

      if($count > 0){
        return redirect()->back()->with('status-success','Purchase order berhasil diperbaharui.');
      }
      else{
        return redirect()->back()->with('status-data','Purchase order gagal diperbaharui.');
      }
    }

    function deleteDetailSingle(Request $request){

  		$id = $request->id;

      $user = Auth::user();

      $detail = PurchaseOrderDetail::findOrFail($id);
      $detail->rstatus = 'DL';
      $detail->deleted_by = $user->username;
      $detail->deleted_at = Carbon::now();
      $detail->save();

      $output = array(
        'status' => true
      );

  		return response()->json($output);
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

      $order = PurchaseOrder::findOrFail($id);
      $order->rstatus = 'DL';
      $order->deleted_by = $user->username;
      $order->deleted_at = Carbon::now();
      $order->save();

      $detail = PurchaseOrderDetail::where('purchase_order_id',$id)->update([
        'rstatus' => 'DL',
        'deleted_by' => $user->username,
        'deleted_at' => Carbon::now(),
      ]);
      return redirect()->back()->with('status-success','Purchase order berhasil dihapus.');
    }
}
