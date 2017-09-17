<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller\RollStock\RollStockController;
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

class VerifyRollController extends Controller
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
      $suppliers = PaperSupplier::all();
      return view('main.rollstock.verifyroll.index')
              ->withSites($sites)
              ->withSuppliers($suppliers);
    }

    public function showHistory(Request $request){
      $site_id = $request->site;
      $rstatus = $request->rstatus;
      $supplier_id = $request->supplier;
      $date_from = $request->date_from;
      $date_to = $request->date_to;

      // get receive history from ReceiveRollController
      $details = (new ReceiveRollController)->getDetails($site_id, $date_from, $date_to, $rstatus, $supplier_id);
      $summary = (new ReceiveRollController)->getSummary($site_id, $date_from, $date_to, $rstatus, $supplier_id);

      if(count($details) > 0 && count($summary) > 0){
        $sites = Site::all();
        $suppliers = PaperSupplier::all();
        return view('main.rollstock.verifyroll.create')
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

    public function getDetails($date_from, $date_to){
      $result = ReceiveRoll::with('supplier')
                          ->select([
                            'receive_rolls.*',
                            'verify_rolls.id as verify_id',
                            'verify_rolls.verify_date'
                          ])
                          ->leftJoin('verify_rolls','verify_rolls.receive_roll_id','receive_rolls.id')
                          ->where('verify_rolls.verify_date','>=',$date_from)
                          ->where('verify_rolls.verify_date','<=',$date_to)
                          ->where('receive_rolls.rstatus','<>','DL')
                          ->where('verify_rolls.rstatus','<>','DL')
                          ->orderBy('receive_rolls.po_num')
                          ->get();

      return $result;
    }

    public function getSummary($date_from, $date_to){
      $result = ReceiveRoll::with('supplier')
                          ->select([
                            'site_id', 'po_num', 'doc_ref', DB::raw('sum(roll_weight) as roll_weight')
                          ])
                          ->leftJoin('verify_rolls','verify_rolls.receive_roll_id','receive_rolls.id')
                          ->where('verify_rolls.verify_date','>=',$date_from)
                          ->where('verify_rolls.verify_date','<=',$date_to)
                          ->where('receive_rolls.rstatus','<>','DL')
                          ->where('verify_rolls.rstatus','<>','DL')
                          ->groupBy('receive_rolls.site_id', 'receive_rolls.po_num', 'receive_rolls.doc_ref')
                          ->orderBy('receive_rolls.po_num')
                          ->get();

      return $result;
    }

    public function showVerification(Request $request){
      $date_from = $request->date_from;
      $date_to = $request->date_to;

      $details = $this->getDetails($date_from, $date_to);
      $summary = $this->getSummary($date_from, $date_to);

      if(count($details) > 0 && count($summary) > 0){
        return view('main.rollstock.verifyroll.index')
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $sites = Site::all();
      $suppliers = PaperSupplier::all();
      return view('main.rollstock.verifyroll.create')
              ->withSites($sites)
              ->withSuppliers($suppliers);
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

      if(!isset($request->cb)){
        return redirect()->route('verifyroll.create')->with('status-danger','Verifikasi roll gagal.');
      }

      for ($i=0; $i < count($request->cb) ; $i++) {
        $receive_id = $request->cb[$i];

        $verify = new VerifyRoll;
        $verify->receive_roll_id = $receive_id;
        $verify->verify_date = date('Y-m-d');
        $verify->created_by = $user->username;
        $verify->save();
      }

      return redirect()->route('verifyroll.create')->with('status-success','Verifikasi roll berhasil.');
    }

    public function delete($id)
    {
      $user = Auth::user();

      $verify = VerifyRoll::findOrFail($id);
      $verify->rstatus = 'DL';
      $verify->deleted_by = $user->username;
      $verify->deleted_at = Carbon::now();
      $verify->save();

      if($verify){
        echo "
          <script>
            alert('Verifikasi roll berhasil dihapus.');
            window.close();
          </script>
        ";
      }
      else{
        echo "
          <script>
            alert('Verifikasi roll gagal dihapus.');
            window.close();
          </script>
        ";
      }
    }

}
