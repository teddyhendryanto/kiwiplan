<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;

use Auth;
use DB;
use Carbon\Carbon;

use App\Models\PaperSupplier;
use App\Models\PaperWidth;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\ReceiveRoll;
use App\Models\Site;
use App\Models\VerifyRoll;

class ReceiveRollController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $sites = Site::all();
      $suppliers = PaperSupplier::all();
      return view('main.rollstock.receiveroll.index')
              ->withSites($sites)
              ->withSuppliers($suppliers);
    }

    public function getPODetail(Request $request){
  		$site_id = $request->site_id;
      $po_num = $request->po_num;

  		$order = PurchaseOrder::with('site','supplier','supplier.keys')
                            ->where('site_id',$site_id)
                            ->where('po_num',$po_num)
                            ->first();

  		if($order){
  			$output = array(
  				'dataset' => $order,
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $sites = Site::all();
      $widths = PaperWidth::all();
      return view('main.rollstock.receiveroll.create')->withSites($sites)->withWidths($widths);
    }

    public function getLastRollCounter($rtype){
      switch ($rtype) {
        case 'AUTO':
          $lastcounter = ReceiveRoll::select(DB::raw('ifnull(max(counter),30999) as counter'))
          ->where('rtype',$rtype)
          ->where('yyyy',date('Y'))
          ->first();;
          break;
        case 'FOX':
          $lastcounter = ReceiveRoll::select(DB::raw('ifnull(max(counter),0) as counter'))
          ->where('rtype',$rtype)
          ->where('yyyy',date('Y'))
          ->first();
          break;
        case 'BOOKED':
          $lastcounter = ReceiveRoll::select(DB::raw('ifnull(max(counter),98999) as counter'))
          ->where('rtype',$rtype)
          ->where('yyyy',date('Y'))
          ->first();
          break;
      }
  		$newcounter = $lastcounter->counter+1;

      return $newcounter;
    }

    public function getFoxRollID(Request $request){
      $paper_key = $request->paper_key;
      $paper_width = sprintf('%03d', $request->paper_width/10);
      $supplier_id = $request->supplier_id;
  		$rtype = $request->rtype;

      $supplier = PaperSupplier::findOrFail($supplier_id);

      if(is_null($supplier)){
        $output = array(
          'status' => false
        );
      }

      $rollid = $supplier->ex_code.substr($paper_key,2,1).substr($paper_key,4,3).$paper_width.date('y').'XXXXX';

      $output = array(
        'rollid' => $rollid,
        'status' => true
      );

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
      $user = Auth::user();

      $rtype = $request->type;
      $site_id = $request->site;
      $receive_date = $request->receive_date;
      $po_id = $request->po_id;
      $po_num = strtoupper($request->po_num);
      $supplier_id = $request->supplier_id;
      $supplier_name = $request->supplier;
      $paper_key = $request->paper_key;
      $paper_width = $request->paper_width;
      $counter = $this->getLastRollCounter($rtype);
      $roll_weight = $request->roll_weight;
      $roll_diameter = $request->roll_diameter;
      $do_num = $this->emptyStringToNull(strtoupper($request->do_num));
      $no_pol = $this->emptyStringToNull(strtoupper($request->no_pol));
      $remarks = $this->emptyStringToNull(strtoupper($request->remarks));

      if($rtype == 'FOX'){
        $roll_id = strtoupper($request->unique_roll_id);
      }
      else{
        $roll_id = $paper_key.sprintf('%03d',$paper_width/10).date('y').sprintf('%05d',$counter);
      }

      if(substr($po_num, -1) == "X"){
				$supplier_roll_id = $roll_id;
			}
			else{
				if($request->supplier_roll_id != ""){
					$supplier_roll_id = strtoupper($request->supplier_roll_id);

					if(strlen($supplier_roll_id > 20)){
						$supplier_roll_id = str_replace('.', '', $supplier_roll_id);
					}

          // check duplicate supplier roll id
          $srollid_check = ReceiveRoll::where('supplier_roll_id',$supplier_roll_id)->first();

          // data exist
          if($srollid_check){
            return redirect()->back()->withInput()->with('status-danger','Supplier Roll ID sudah pernah diinput.');
          }
				}
				else{
				 $supplier_roll_id = null;
				}
			}

      // check duplicate counter
      $duplicate_counter = ReceiveRoll::where('counter',$counter)->first();
      if($duplicate_counter){
        return redirect()->back()->withInput()->with('status-danger','Counter Roll ID Duplikat.');
      }

      // check duplicate roll id
      $duplicate_roll_id = ReceiveRoll::where('unique_roll_id',$roll_id)->first();
      if($duplicate_roll_id){
        return redirect()->back()->withInput()->with('status-danger','Roll ID sudah pernah diinput.');
      }

      // search paper price
      $search_paper_price = PurchaseOrderDetail::select(DB::raw('ifnull(paper_price,1) as paper_price'))
                              ->where('paper_quality',substr($paper_key,2,2))
                              ->where('paper_gramatures','like','%'.substr($paper_key,4,3).'%')
                              ->first();

      if($search_paper_price){
        $paper_price = (int) $search_paper_price->paper_price;
      }
      else{
        $paper_price = 1;
      }

      // check currency type based on supplier id
      $supp = PaperSupplier::findOrFail($supplier_id);
      if($supp->currency != "IDR"){
        $exchange_rates = DB::table('exchange_rates')->select([
          'rate_date', 'selling_rate', 'buying_rate'
        ])->where('rate_date',
          DB::raw("
            (
    					select MAX(rate_date)
    					from exchange_rates
    					where (rate_date <= '$receive_date' and currency = '$supp->currency' and rstatus <> 'DL')
    				)")
        )->first();

        $rate_date = $exchange_rates->rate_date;
        $selling_rate = $exchange_rates->selling_rate;
      }
      else{
        $rate_date = null;
        $selling_rate = null;
      }

      $rcv = new ReceiveRoll;
      $rcv->site_id = $site_id;
      $rcv->po_id = $po_id;
      $rcv->po_num = $po_num;
      $rcv->receive_date = $receive_date;
      $rcv->receive_time = date('H:i:s');
      $rcv->supplier_id = $supplier_id;
      $rcv->paper_key = $paper_key;
      $rcv->paper_width = $paper_width;
      $rcv->paper_price = $paper_price;
      $rcv->yyyy = date('Y');
      $rcv->rtype = $rtype;
      $rcv->counter = $counter;
      $rcv->supplier_roll_id = $supplier_roll_id;
      $rcv->unique_roll_id = $roll_id;
      $rcv->roll_weight = $roll_weight;
      $rcv->roll_diameter = $roll_diameter;
      $rcv->doc_ref = $do_num;
      $rcv->wagon = $no_pol;
      $rcv->remarks = $remarks;
      $rcv->rate_date = $rate_date;
      $rcv->selling_rate = $selling_rate;
      $rcv->created_by = $user->username;
      $rcv->save();

      if($rcv){
        return redirect()->back()->with('status-success','Penerimaan roll berhasil disimpan.');
      }
      else{
        return redirect()->back()->withInput()->with('status-danger','Penerimaan roll gagal disimpan.');
      }
    }

    public function getDetails($site_id, $date_from, $date_to, $rstatus = null, $supplier_id = null){
      $query = ReceiveRoll::with('supplier','verify_roll')
                          ->where('site_id',$site_id)
                          ->where('receive_date','>=',$date_from)
                          ->where('receive_date','<=',$date_to);
      if($rstatus <> 'ALL'){
        $query->where('rstatus',$rstatus);
      }
      if($supplier_id <> ''){
        $query->where('supplier_id',$supplier_id);
      }

      return $query->orderBy('po_num')->get();
    }

    public function getSummary($site_id, $date_from, $date_to, $rstatus = null, $supplier_id = null){
      $query = ReceiveRoll::with('supplier','verify_roll')
                          ->select([
                            'site_id', 'po_num', 'doc_ref', DB::raw('sum(roll_weight) as roll_weight')
                          ])
                          ->where('site_id',$site_id)
                          ->where('receive_date','>=',$date_from)
                          ->where('receive_date','<=',$date_to);
      if($rstatus <> 'ALL'){
        $query->where('rstatus',$rstatus);
      }
      if($supplier_id <> ''){
        $query->where('supplier_id',$supplier_id);
      }

      return $query->groupBy('site_id', 'po_num', 'doc_ref')
                   ->orderBy('po_num')->get();
    }

    public function showHistory(Request $request){
      $site_id = $request->site;
      $rstatus = $request->rstatus;
      $supplier_id = $request->supplier;
      $date_from = $request->date_from;
      $date_to = $request->date_to;

      $details = $this->getDetails($site_id, $date_from, $date_to, $rstatus, $supplier_id);
      $summary = $this->getSummary($site_id, $date_from, $date_to, $rstatus, $supplier_id);

      if(count($details) > 0 && count($summary) > 0){
        $sites = Site::all();
        $suppliers = PaperSupplier::all();
        return view('main.rollstock.receiveroll.index')
                ->withSites($sites)
                ->withSuppliers($suppliers)
                ->withDateFrom($date_from)
                ->withDateTo($date_to)
                ->withDetails($details)
                ->withSummary($summary);
      }
      else{
        return redirect()->back()->with('status-danger','Data tidak ditemukan.');
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
      $widths = PaperWidth::all();
      $data = ReceiveRoll::with([
                            'supplier.keys','verify_roll',
                            'verify_roll.edi_export_details',
                            'verify_roll.edi_export_details.edi_export',
                          ])
                          ->findOrFail($id);

                          // dd($data);

      // dd($data);
      return view('main.rollstock.receiveroll.edit')
              ->withSites($sites)
              ->withWidths($widths)
              ->withData($data);
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

      $receive_date = $request->receive_date;
      $po_id = $request->po_id;
      $po_num = strtoupper($request->po_num);
      $supplier_id = $request->supplier_id;
      $supplier_name = $request->supplier;
      $paper_key = $request->paper_key;
      $paper_width = $request->paper_width;
      $roll_weight = $request->roll_weight;
      $roll_diameter = $request->roll_diameter;
      $do_num = $this->emptyStringToNull(strtoupper($request->do_num));
      $no_pol = $this->emptyStringToNull(strtoupper($request->no_pol));
      $remarks = $this->emptyStringToNull(strtoupper($request->remarks));

      $roll_id = strtoupper($request->unique_roll_id);
      if(substr($po_num, -1) == "X"){
				$supplier_roll_id = $roll_id;
			}
			else{
				if($request->supplier_roll_id != ""){
					$supplier_roll_id = strtoupper($request->supplier_roll_id);

					if(strlen($supplier_roll_id > 20)){
						$supplier_roll_id = str_replace('.', '', $supplier_roll_id);
					}
				}
				else{
				 $supplier_roll_id = null;
				}
			}

      // search paper price
      $search_paper_price = PurchaseOrderDetail::select(DB::raw('ifnull(paper_price,1) as paper_price'))
                              ->where('paper_quality',substr($paper_key,2,2))
                              ->where('paper_gramatures','like','%'.substr($paper_key,4,3).'%')
                              ->first();

      if($search_paper_price){
        $paper_price = (int) $search_paper_price->paper_price;
      }
      else{
        $paper_price = 1;
      }

      // check currency type based on supplier id
      $supp = PaperSupplier::findOrFail($supplier_id);
      if($supp->currency != "IDR"){
        $exchange_rates = DB::table('exchange_rates')->select([
          'rate_date', 'selling_rate', 'buying_rate'
        ])->where('rate_date',
          DB::raw("
            (
    					select MAX(rate_date)
    					from exchange_rates
    					where (rate_date <= '$receive_date' and currency = '$supp->currency' and rstatus <> 'DL')
    				)")
        )->first();

        $rate_date = $exchange_rates->rate_date;
        $selling_rate = $exchange_rates->selling_rate;
      }
      else{
        $rate_date = null;
        $selling_rate = null;
      }

      $rcv = ReceiveRoll::findOrFail($id);
      $rcv->po_id = $po_id;
      $rcv->po_num = $po_num;
      $rcv->receive_date = $receive_date;
      $rcv->supplier_id = $supplier_id;
      $rcv->paper_key = $paper_key;
      $rcv->paper_width = $paper_width;
      $rcv->paper_price = $paper_price;
      $rcv->supplier_roll_id = $supplier_roll_id;
      $rcv->unique_roll_id = $roll_id;
      $rcv->roll_weight = $roll_weight;
      $rcv->roll_diameter = $roll_diameter;
      $rcv->doc_ref = $do_num;
      $rcv->wagon = $no_pol;
      $rcv->remarks = $remarks;
      $rcv->rate_date = $rate_date;
      $rcv->selling_rate = $selling_rate;
      $rcv->rstatus = 'AM';
      $rcv->updated_by = $user->username;
      $rcv->save();

      // update verification datetime
      $verify = VerifyRoll::where('receive_roll_id', $id)->first();
      if(!is_null($verify)){
        $verify->exported = false;
        $verify->rstatus = 'AM';
        $verify->updated_by = $user->username;
        $verify->save();
      }

      if($rcv){
        return redirect()->back()->with('status-success','Penerimaan roll berhasil diperbaharui.');
      }
      else{
        return redirect()->back()->withInput()->with('status-danger','Penerimaan roll gagal diperbaharui.');
      }
    }

    public function delete($id)
    {
      $user = Auth::user();

      $rcv = ReceiveRoll::findOrFail($id);
      $rcv->rstatus = 'DL';
      $rcv->deleted_by = $user->username;
      $rcv->deleted_at = Carbon::now();
      $rcv->save();

      $verify = VerifyRoll::where('receive_roll_id', $id)->first();
      if(!is_null($verify)){
        $verify->rstatus = 'DL';
        $verify->deleted_by = $user->username;
        $verify->deleted_at = Carbon::now();
        $verify->save();
      }

      if($rcv){
        echo "
          <script>
            alert('Penerimaan roll berhasil dihapus.');
            window.close();
          </script>
        ";
      }
      else{
        echo "
          <script>
            alert('Penerimaan roll gagal dihapus.');
            window.close();
          </script>
        ";
      }
    }

    public function editCustom(){
      return view('main.rollstock.receiveroll.edit-custom');
    }

    public function showHistoryCustom(Request $request){
      $search_by = $request->search_by;

      if($search_by == 'unique_roll_id'){
        $filter = $request->unique_roll_id;
        $data = ReceiveRoll::with('supplier','verify_roll')->where('unique_roll_id',$request->unique_roll_id)->get();
      }
      else{
        $filter = $request->doc_ref;
        $data = ReceiveRoll::with('supplier','verify_roll')->where('doc_ref',$request->doc_ref)->get();
      }
      // dd($data);

      if(count($data) > 0){
        return view('main.rollstock.receiveroll.edit-custom')
                ->withDatas($data)
                ->withSearchBy($search_by)
                ->withFilter($filter);
      }
      else{
        return redirect()->route('receiveroll.edit.custom')->with('status-danger', 'Data tidak ditemukan.');
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
      $user = Auth::user();

      $rcv = ReceiveRoll::findOrFail($id);
      $rcv->rstatus = 'DL';
      $rcv->deleted_by = $user->username;
      $rcv->deleted_at = Carbon::now();
      $rcv->save();

      $verify = VerifyRoll::where('receive_roll_id', $id)->first();
      if(!is_null($verify)){
        $verify->rstatus = 'DL';
        $verify->deleted_by = $user->username;
        $verify->deleted_at = Carbon::now();
        $verify->save();
      }

      if($rcv){
        return redirect()->back()->with('status-success','Penerimaan roll berhasil dihapus.');
      }
      else{
        return redirect()->back()->withInput()->with('status-danger','Penerimaan roll gagal dihapus.');
      }
    }
}
