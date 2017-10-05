<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Traits\GeneralTrait;
use App\Traits\MyTcpdf;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\PaperQuality;
use App\Models\PaperSupplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderTransfer;
use App\Models\Site;

class PurchaseOrderController extends Controller
{
    use GeneralTrait;
    use MyTcpdf;

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
      return view('main.accounting.purchase_orders.index');
    }

    public function getPO(Request $request){
      if($request->site != "all"){
        DB::statement(DB::raw('set @rownum=0'));
        $orders = PurchaseOrder::with('site','supplier')->select([
          DB::raw('@rownum  := @rownum  + 1 AS rownum'),
          'id',
          'site_id',
          'supplier_id',
          'po_num',
          DB::raw('cast(po_date as date) as po_date')
          ])->where('po_date','>=',$request->date_from)
          ->where('po_date','<=',$request->date_to)
          ->where('site_id', $request->site)
          ->get();
      }
      else{
        DB::statement(DB::raw('set @rownum=0'));
        $orders = PurchaseOrder::with('site','supplier')->select([
          DB::raw('@rownum  := @rownum  + 1 AS rownum'),
          'id',
          'site_id',
          'supplier_id',
          'po_num',
          DB::raw('cast(po_date as date) as po_date')
          ])->where('po_date','>=',$request->date_from)
          ->where('po_date','<=',$request->date_to)->get();
      }

      $datatables = Datatables::collection($orders)
                    ->addColumn('action', function ($order) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('purchase_orders.destroy', $order->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('purchase_orders.print', $order->id).'" class="btn btn-xs btn-default" target="_blank">
                        <i class="fa fa-print"></i>
                      </a>
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

      // check header on remote
      if($orders->site_id == env('TARGET_SITE_ID')){
        $header = $this->checkPurchaseOrderHeaderOnRemote('update', $id, $orders);
      }

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
              // check detail on remote
              if($orders->site_id == env('TARGET_SITE_ID')){
                $this->checkPurchaseOrderDetailsOnRemote('update', $request->detail_id[$i], $details);
              }
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
              // insert new detail on remote
              if($orders->site_id == env('TARGET_SITE_ID')){
                $this->checkPurchaseOrderDetailsOnRemote('insert', $header, $details);
              }
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

      $detail = PurchaseOrderDetail::with('purchase_order')->findOrFail($id);
      $detail->rstatus = 'DL';
      $detail->deleted_by = $user->username;
      $detail->deleted_at = Carbon::now();
      $detail->save();

      // check detail on remote
      if($detail->purchase_order->site_id == env('TARGET_SITE_ID')){
        $this->checkPurchaseOrderDetailsOnRemote('update', $id, $detail);
      }

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

      // check header on remote
      if($order->site_id == env('TARGET_SITE_ID')){
        $header_id = $this->checkPurchaseOrderHeaderOnRemote('update', $id, $order);
      }

      $detail = PurchaseOrderDetail::where('purchase_order_id',$id)->update([
        'rstatus' => 'DL',
        'deleted_by' => $user->username,
        'deleted_at' => Carbon::now(),
      ]);

      // check detail on remote
      if($order->site_id == env('TARGET_SITE_ID')){
        $this->checkPurchaseOrderDetailsOnRemote('deleteAll', $header_id, $detail);
      }

      // delete transfer log
      $tr = PurchaseOrderTransfer::where('purchase_order_id',$id)->update([
        'rstatus' => 'DL',
        'deleted_by' => $user->username,
        'deleted_at' => Carbon::now(),
      ]);

      return redirect()->back()->with('status-success','Purchase order berhasil dihapus.');
    }

    public function checkPurchaseOrderHeaderOnRemote($flag, $id, $update){
      $check = PurchaseOrder::on(env('REMOTE_TARGET_DB'))->where('transfered_id',$id)->first();
      if($check){
        $check->site_id = $update->site_id;
        $check->yyyymm = $update->yyyymm;
        $check->counter = $update->counter;
        $check->supplier_id = $update->supplier_id;
        $check->po_num = $update->po_num;
        $check->po_num_ex = $update->po_num_ex;
        $check->po_date = $update->po_date;
        $check->po_qty = $update->po_qty;
        $check->due_date = $update->due_date;
        $check->contact_person = $update->contact_person;
        $check->term = $update->term;
        $check->remarks1 = $update->remarks1;
        $check->rstatus = $update->rstatus;
        $check->created_by = $update->created_by;
        $check->updated_by = $update->updated_by;
        $check->deleted_by = $update->deleted_by;
        $check->created_at = $update->created_at;
        $check->updated_at = $update->updated_at;
        $check->deleted_at = $update->deleted_at;
        $check->save();

        return $check->id;
      }
      else{
        return false;
      }
    }

    public function checkPurchaseOrderDetailsOnRemote($flag, $id, $update){
      switch ($flag) {
        case 'update':
          $check = PurchaseOrderDetail::on(env('REMOTE_TARGET_DB'))->where('transfered_id',$id)->first();
          if($check){
            $check->update([
              'paper_quality' => $update->paper_quality,
              'paper_gramatures' => $update->paper_gramatures,
              'paper_width' => $update->paper_width,
              'paper_qty' => $update->paper_qty,
              'um' => $update->um,
              'paper_price' => $update->paper_price,
              'tax' => $update->tax,
              'remarks' => $update->remarks,
              'rstatus' => $update->rstatus,
              'created_by' => $update->created_by,
              'updated_by' => $update->updated_by,
              'deleted_by' => $update->deleted_by,
              'created_at' => $update->created_at,
              'updated_at' => $update->updated_at,
              'deleted_at' => $update->deleted_at,
            ]);
          }
          else{
            return false;
          }
          break;

        case 'deleteAll':
          $user = Auth::user();
          $check = PurchaseOrderDetail::on(env('REMOTE_TARGET_DB'))->where('purchase_order_id',$id)->get();
          if(count($check) > 0){
            foreach ($check as $chk) {
              $chk->rstatus = 'DL';
              $chk->deleted_by = $user->username;
              $chk->deleted_at = Carbon::now();
              $chk->save();
            }
            return true;
          }
          else{
            return false;
          }
          break;

        case 'insert':
          // create new
          $check = PurchaseOrder::on(env('REMOTE_TARGET_DB'))->where('id',$id)->first();
          if($check){
            $insert = PurchaseOrderDetail::on(env('REMOTE_TARGET_DB'))->create([
              'purchase_order_id' => $id,
              'paper_quality' => $update->paper_quality,
              'paper_gramatures' => $update->paper_gramatures,
              'paper_width' => $update->paper_width,
              'paper_qty' => $update->paper_qty,
              'um' => $update->um,
              'paper_price' => $update->paper_price,
              'tax' => $update->tax,
              'remarks' => $update->remarks,
              'transfered_id' => $update->id,
              'rstatus' => 'NW',
              'created_by' => $update->created_by,
              'updated_by' => $update->updated_by,
              'deleted_by' => $update->deleted_by,
              'created_at' => $update->created_at,
              'updated_at' => $update->updated_at,
              'deleted_at' => $update->deleted_at,
            ]);
            return true;
          }
          else{
            return false;
          }
          break;
      }
    }

    public function print_po($id){
      $order = PurchaseOrder::with('purchase_order_details','site','supplier')->findOrFail($id);

      // dd($order);
      $pages = 1;

      $site = $order->site->short_name;
			$poid = $order->id;
			$ponum = $order->po_num;
			$podate = date('d/m/Y',strtotime($order->po_date));
			$acname = $order->supplier->full_name;
			$cp = $order->contact_person;
			$poqty = $order->po_qty;
			$ddate = $order->due_date;
			$payment = $order->term;
			$remarks = $order->remarks1;
			$supphone = $order->supplier->phone;
			$supcurr = $order->supplier->currency;

			// create new PDF document
      $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf::setPageOrientation('P',1);
			$pdf::SetAutoPageBreak(1);
			// set document information
			$pdf::SetCreator(PDF_CREATOR);
			$pdf::SetAuthor('Accounting');
			$pdf::SetTitle('PurchaseOrder#'.$ponum);
			$pdf::SetSubject('PurchaseOrder#'.$ponum);
			$pdf::SetKeywords('PDF');

			// add a page
			$pdf::AddPage();

			$height =3.5;

			// header
			$currY = 5;
			$this->CreateTextBoxHistory('PURCHASE ORDER', 5, $currY, 200, $height, 12, 'B', 'C');
			$currY+=10;

			switch ($site) {
				case 'MBI':
					$this->CreateTextBoxHistory('PT MULTIBOX INDAH', 5, $currY, 125, $height, 8, 'B', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Jl. Raya Cikande - Rangkas Bitung KM. 6 Desa Kareo', 5, $currY, 125, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Kec.Jawilan, Serang 42180 Banten - Indonesia', 5, $currY, 125, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Telp', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('(0254) 404060 (Hunting)', 17.5, $currY, 50, $height, 8, '', 'L');

					$this->CreateTextBoxHistory('Fax', 67.5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 77.5, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('(0254) 404061', 80, $currY, 45, $height, 8, '', 'L');

					$this->CreateTextBoxHistory('PO#', 130, $currY, 10, $height, 8, 'B', 'L');
					$this->CreateTextBoxHistory(':', 140, $currY, 2.5, $height, 8, 'B', 'C');
					$this->CreateTextBoxHistory('M-'.$ponum, 142.5, $currY, 45, $height, 8, 'B', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Email', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('accounting@ptkim.com', 17.5, $currY, 50, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Date', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory($podate, 17.5, $currY, 50, $height, 8, '', 'L');
					$currY+=5;
					break;

				case 'KIM':
					$this->CreateTextBoxHistory('PT KARYA INDAH MULTIGUNA', 5, $currY, 125, $height, 8, 'B', 'L');
					$this->CreateTextBoxHistory('ISO. NO : KIM-FM-PUR-005', 130, $currY, 70, $height, 8, 'B', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Jl. Raya Narogong Km. 12 Cikiwul -- Bantar Gebang', 5, $currY, 125, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Bekasi 17310', 5, $currY, 125, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Telp', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('(021) 82650877', 17.5, $currY, 50, $height, 8, '', 'L');

					$this->CreateTextBoxHistory('Fax', 67.5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 77.5, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('(021) 82650880, 8252680', 80, $currY, 45, $height, 8, '', 'L');

					$this->CreateTextBoxHistory('PO#', 130, $currY, 10, $height, 8, 'B', 'L');
					$this->CreateTextBoxHistory(':', 140, $currY, 2.5, $height, 8, 'B', 'C');
					$this->CreateTextBoxHistory('K-'.$ponum, 142.5, $currY, 45, $height, 8, 'B', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Email', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory('accounting@ptkim.com', 17.5, $currY, 50, $height, 8, '', 'L');
					$currY+=5;
					$this->CreateTextBoxHistory('Date', 5, $currY, 10, $height, 8, '', 'L');
					$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
					$this->CreateTextBoxHistory($podate, 17.5, $currY, 50, $height, 8, '', 'L');
					$currY+=5;
					break;
			}

			// draw a line
			$this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');
			$currY+=2.5;

			// CUSTOMER field
			$this->CreateTextBoxHistory('TO', 5, $currY, 10, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($acname, 17.5, $currY, 42.5, $height, 8, 'B', 'L');
			$currY+=5;

			$this->CreateTextBoxHistory('PHONE/FAX', 80, $currY, 17.5, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 97.5, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($supphone, 100, $currY, 60, $height, 8, 'B', 'L');

			$this->CreateTextBoxHistory('Term Of Payment', 160, $currY, 25, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($payment.' Days', 187.5, $currY, 12.5, $height, 8, 'B', 'L');

			$currY+=5;
			$this->CreateTextBoxHistory('C/PERSON', 80, $currY, 17.5, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 97.5, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($cp, 100, $currY, 60, $height, 8, 'B', 'L');

			$this->CreateTextBoxHistory('Currency', 160, $currY, 25, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($supcurr, 187.5, $currY, 12.5, $height, 8, 'B', 'L');

			$currY+=5;
			$this->CreateTextBoxHistory('Page', 160, $currY, 25, $height, 8, 'B', 'L');
			$this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
			$this->CreateTextBoxHistory($pages, 187.5, $currY, 12.5, $height, 8, 'B', 'L');

			// draw a line
			$currY+=5;
			$this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');
			$currY+=1.5;
			$pdf::SetXY(5,$currY);
			$pdf::SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
			$pdf::Cell(7, 5, 'NO.', '', false, 'C');
			$pdf::Cell(83, 5, 'DESCRIPTION', '', false, 'L');
			$pdf::Cell(20, 5, 'QTY', '', false, 'C');
			$pdf::Cell(15, 5, 'UNIT', '', false, 'C');
			$pdf::Cell(30, 5, 'U.PRICE (KG)', '', false, 'C');
			$pdf::Cell(15, 5, 'DISCOUNT', '', false, 'C');
			$pdf::Cell(30, 5, 'AMOUNT', '', false, 'C');
			$currY+=6.5;
			$this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');

      $currY+=5;
      $i=1;
      $grandamount = 0;
      foreach($order->purchase_order_details as $detail) {
        $pid = $detail->id;
        $pquality = $detail->paper_quality;
        $pgram = $detail->paper_gramatures;
        $pwidth = $detail->paper_width;
        $pum = $detail->um;
        $pqty = $detail->paper_qty;
        $ptax = $detail->tax;
        $pprice = $detail->paper_price;
        $premarks = $detail->remarks;
        $totalamount = $pqty * $pprice;
        $grandamount+= $totalamount;

        $this->CreateTextBoxNoline($i.'.', 5, $currY, 7, 5, 8, '', 'C');
        $this->CreateTextBoxNoline($pquality, 12, $currY, 83, 5, 8, '', 'L');
        if($pqty > 0){
          $this->CreateTextBoxNoline(number_format($pqty,2), 95, $currY, 20, 5, 8, '', 'C');
          $this->CreateTextBoxNoline($pum, 115, $currY, 15, 5, 8, '', 'C');
        }
        if($supcurr != 'IDR'){
          // currency DOLLAR
          $this->CreateTextBoxNoline(number_format($pprice,3), 130, $currY, 30, 5, 8, '', 'C');
          $this->CreateTextBoxNoline('', 160, $currY, 15, 5, 8, '', 'R');
          $this->CreateTextBoxNoline(number_format($totalamount,3), 175, $currY, 30, 5, 8, '', 'R');
        }
        else{
          $this->CreateTextBoxNoline(number_format($pprice), 130, $currY, 30, 5, 8, '', 'C');
          $this->CreateTextBoxNoline('', 160, $currY, 15, 5, 8, '', 'R');
          $this->CreateTextBoxNoline('', 175, $currY, 30, 5, 8, '', 'R');
        }

        $currY+=5;

        $this->CreateTextBoxNoline('GRAMATURE : '.$pgram, 12, $currY, 83, 5, 7, '', 'L');
        $this->CreateTextBoxNoline($ptax, 130, $currY, 30, 5, 7, 'I', 'C');
        $currY+=5;
        if($pwidth != ""){
          $this->CreateTextBoxNoline('WIDTH : '.$pwidth, 12, $currY, 83, 5, 7, '', 'L');
          $currY+=5;
        }
        if($premarks != ""){
          $this->CreateTextBoxNoline('ITEM REMARKS : '.$premarks, 12, $currY, 83, 5, 7, '', 'L');
          $currY+=5;
        }

        $i++;

        if($currY>=275){
          $pdf::AddPage();
          $currY = 5;
          $this->CreateTextBoxHistory('PURCHASE ORDER', 5, $currY, 200, $height, 12, 'B', 'C');
          $currY+=10;

          switch ($site) {
            case 'MBI':
              $this->CreateTextBoxHistory('PT MULTIBOX INDAH', 5, $currY, 125, $height, 8, 'B', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Jl. Raya Cikande - Rangkas Bitung KM. 6 Desa Kareo', 5, $currY, 125, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Kec.Jawilan, Serang 42180 Banten - Indonesia', 5, $currY, 125, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Telp', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('(0254) 404060 (Hunting)', 17.5, $currY, 50, $height, 8, '', 'L');

              $this->CreateTextBoxHistory('Fax', 67.5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 77.5, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('(0254) 404061', 80, $currY, 45, $height, 8, '', 'L');

              $this->CreateTextBoxHistory('PO#', 130, $currY, 10, $height, 8, 'B', 'L');
              $this->CreateTextBoxHistory(':', 140, $currY, 2.5, $height, 8, 'B', 'C');
              $this->CreateTextBoxHistory('M-'.$ponum, 142.5, $currY, 45, $height, 8, 'B', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Email', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('accounting@ptkim.com', 17.5, $currY, 50, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Date', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory($podate, 17.5, $currY, 50, $height, 8, '', 'L');
              $currY+=5;
              break;

            case 'KIM':
              $this->CreateTextBoxHistory('PT KARYA INDAH MULTIGUNA', 5, $currY, 125, $height, 8, 'B', 'L');
              $this->CreateTextBoxHistory('ISO. NO : KIM-FM-PUR-005', 130, $currY, 70, $height, 8, 'B', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Jl. Raya Narogong Km. 12 Cikiwul -- Bantar Gebang', 5, $currY, 125, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Bekasi 17310', 5, $currY, 125, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Telp', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('(021) 82650877', 17.5, $currY, 50, $height, 8, '', 'L');

              $this->CreateTextBoxHistory('Fax', 67.5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 77.5, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('(021) 82650880, 8252680', 80, $currY, 45, $height, 8, '', 'L');

              $this->CreateTextBoxHistory('PO#', 130, $currY, 10, $height, 8, 'B', 'L');
              $this->CreateTextBoxHistory(':', 140, $currY, 2.5, $height, 8, 'B', 'C');
              $this->CreateTextBoxHistory('K-'.$ponum, 142.5, $currY, 45, $height, 8, 'B', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Email', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory('accounting@ptkim.com', 17.5, $currY, 50, $height, 8, '', 'L');
              $currY+=5;
              $this->CreateTextBoxHistory('Date', 5, $currY, 10, $height, 8, '', 'L');
              $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'C', 'L');
              $this->CreateTextBoxHistory($podate, 17.5, $currY, 50, $height, 8, '', 'L');
              $currY+=5;
              break;
          }

          // draw a line
          $this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');
          $currY+=2.5;

          // CUSTOMER field
          $this->CreateTextBoxHistory('TO', 5, $currY, 10, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 15, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($acname, 17.5, $currY, 42.5, $height, 8, 'B', 'L');
          $currY+=5;

          $this->CreateTextBoxHistory('PHONE/FAX', 80, $currY, 17.5, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 97.5, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($supphone, 100, $currY, 60, $height, 8, 'B', 'L');

          $this->CreateTextBoxHistory('Term Of Payment', 160, $currY, 25, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($payment.' Days', 187.5, $currY, 12.5, $height, 8, 'B', 'L');

          $currY+=5;
          $this->CreateTextBoxHistory('C/PERSON', 80, $currY, 17.5, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 97.5, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($cp, 100, $currY, 60, $height, 8, 'B', 'L');

          $this->CreateTextBoxHistory('Currency', 160, $currY, 25, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($supcurr, 187.5, $currY, 12.5, $height, 8, 'B', 'L');

          $currY+=5;
          $this->CreateTextBoxHistory('Page', 160, $currY, 25, $height, 8, 'B', 'L');
          $this->CreateTextBoxHistory(':', 185, $currY, 2.5, $height, 8, 'B', 'C');
          $this->CreateTextBoxHistory($pages, 187.5, $currY, 12.5, $height, 8, 'B', 'L');

          // draw a line
          $currY+=5;
          $this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');
          $currY+=1.5;
          $pdf::SetXY(5,$currY);
          $pdf::SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
          $pdf::Cell(7, 5, 'NO.', '', false, 'C');
          $pdf::Cell(83, 5, 'DESCRIPTION', '', false, 'L');
          $pdf::Cell(20, 5, 'QTY', '', false, 'C');
          $pdf::Cell(15, 5, 'UNIT', '', false, 'C');
          $pdf::Cell(30, 5, 'U.PRICE (KG)', '', false, 'C');
          $pdf::Cell(15, 5, 'DISCOUNT', '', false, 'C');
          $pdf::Cell(30, 5, 'AMOUNT', '', false, 'C');
          $currY+=6.5;
          $this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');
        }
      }
      $totaldata = $i-1;

      $currY=185;
      $this->CreateTextBoxTop('', 5, $currY, 200, $height, 8, 'B', 'C');

      $currY+=5;
      $this->CreateTextBoxNoline('PO REMARKS : ', 5, $currY, 75, 5, 8, 'B', 'L');
      $this->CreateTextBoxNoline('TOTAL TON', 80, $currY, 75, 5, 8, 'B', 'L');
      if($grandamount > 0){
        $this->CreateTextBoxNoline('TOTAL AMOUNT', 155, $currY, 30, 5, 8, 'B', 'L');
        if($supcurr != 'IDR'){
          $this->CreateTextBoxNoline(number_format($grandamount,3), 175, $currY, 30, 5, 8, 'B', 'R');
        }
      }
      $this->CreateTextBoxNoline(number_format($poqty,2), 95, $currY, 20, 5, 8, 'B', 'R');
      $currY+=5;
      $this->CreateTextBoxNoline($remarks, 5, $currY, 72.5, 25, 8, '', 'L');
      $currY+=20;

      // footer
      $this->CreateTextBox('Perhatian:', 5, $currY, 80, 5, 8, '', 'L');
      $this->CreateTextBox('PREPARED BY:', 90, $currY, 25, 5, 8, 'B', 'C');
      $this->CreateTextBox('APPROVED BY:', 150, $currY, 25, 5, 8, 'B', 'C');
      $currY+=5;
      $this->CreateTextBox('- Harap Fax kembali setelah Order Pembelian ini disetujui.', 5, $currY, 80, 5, 8, '', 'L');
      $currY+=3.5;
      $this->CreateTextBox('  Bilamana tidak ada informasi kembali berarti setuju.', 5, $currY, 80, 5, 8, '', 'L');

      $currY+=5;
      $this->CreateTextBox('- Harap mencantumkan Nomor Order Pembelian ini pada', 5, $currY, 80, 5, 8, '', 'L');
      $currY+=3.5;
      $this->CreateTextBox('  Surat Pengantar Barang dan Faktur Penagihan Saudara.', 5, $currY, 80, 5, 8, '', 'L');

      $currY+=10;
      $this->CreateTextBox('DELIVERY TO : ', 5, $currY, 25, 5, 8, 'B', 'L');

      $this->CreateTextBoxTop('ACCOUNTING', 90, $currY, 25, 5, 7, 'B', 'C');
      $this->CreateTextBoxTop('MINTO / MADYO', 120, $currY, 25, 5, 7, 'B', 'C');
      $this->CreateTextBoxTop('HARRY S SIE', 150, $currY, 25, 5, 7, 'B', 'C');
      $this->CreateTextBoxTop('LENNY LS', 180, $currY, 25, 5, 7, 'B', 'C');

      $currY+=5;
      switch ($site) {
        case 'MBI':
          $this->CreateTextBox('GUDANG ROLL MULTIBOX INDAH', 5, $currY, 50, 5, 8, 'B', 'L');
          break;

        case 'KIM':
          $this->CreateTextBox('GUDANG ROLL KARYA INDAH MULTIGUNA', 5, $currY, 50, 5, 8, 'B', 'L');
          break;
      }

      //Close and output PDF document
      $pdf::Output('PurchaseOrder_'.$ponum.'.pdf', 'I');
    }



}
