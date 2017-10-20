<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;

use Auth;
use App\Models\PaperQuality;
use App\Models\PaperSupplier;
use App\Models\PurchaseOrderFrequent;

class PurchaseOrderFrequentController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('main.accounting.purchase_order_frequents.index');
    }

    public function getPurchaseOrderFrequentDatatable(Request $request){

      $data = PurchaseOrderFrequent::with('supplier')
              ->orderby('supplier_id')
              ->orderby('paper_quality')
              ->get();

      if($data){
        $output = array(
          'dataset' => $data,
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
      $paper_suppliers = PaperSupplier::all();
      $paper_qualities = PaperQuality::all();
      return view('main.accounting.purchase_order_frequents.create')
              ->withPaperSuppliers($paper_suppliers)
              ->withPaperQualities($paper_qualities);
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

      $this->validate($request, array(
        'paper_supplier' => 'required',
        'paper_quality' => 'required',
        'paper_gramatures' => 'required',
      ));

      $freq = new PurchaseOrderFrequent;
      $freq->supplier_id = $request->paper_supplier;
      $freq->paper_quality = $request->paper_quality;
      $freq->paper_gramatures = $request->paper_gramatures;
      $freq->paper_width = $this->emptyStringToNull($request->paper_width);
      $freq->created_by = $user->username;
      $freq->save();

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $data = PurchaseOrderFrequent::findOrFail($id);
      $paper_suppliers = PaperSupplier::all();
      $paper_qualities = PaperQuality::all();
      return view('main.accounting.purchase_order_frequents.edit')
              ->withData($data)
              ->withPaperSuppliers($paper_suppliers)
              ->withPaperQualities($paper_qualities);
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

      $this->validate($request, array(
        'paper_supplier' => 'required',
        'paper_quality' => 'required',
        'paper_gramatures' => 'required',
      ));

      $freq = PurchaseOrderFrequent::findOrFail($id);
      $freq->supplier_id = $request->paper_supplier;
      $freq->paper_quality = $request->paper_quality;
      $freq->paper_gramatures = $request->paper_gramatures;
      $freq->paper_width = $this->emptyStringToNull($request->paper_width);
      $freq->rstatus = 'AM';
      $freq->updated_by = $user->username;
      $freq->save();

      return redirect()->route('purchase_order_frequents.index')->with('status-success','Data berhasil diperbaharui.');
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
      $freq = PurchaseOrderFrequent::findOrFail($id);

      $freq->rstatus    = 'DL';
      $freq->deleted_by = $user->username;
      $freq->deleted_at = date('Y-m-d H:i:s');
      $freq->save();

      if(count($freq)> 0){
        $output = array(
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
}
