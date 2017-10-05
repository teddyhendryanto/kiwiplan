<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Traits\GeneralTrait;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PaperSupplier;

class PaperSupplierController extends Controller
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
      return view('main.rollstock.setup.suppliers.index');
    }

    public function getSupplierListDatatable(){
      $supps = PaperSupplier::query();

      return Datatables::of($supps)
            ->addColumn('action', function ($supp) {
              return '
              <form class="form" role="form" method="POST" action="'.route('suppliers.destroy', $supp->id).'">
              <input type="hidden" name="_token" value="'.csrf_token().'">
              <input type="hidden" name="_method" value="DELETE">
              <a href="'.route('suppliers.edit', $supp->id).'" class="btn btn-xs btn-default">
                <i class="fa fa-pencil"></i>
              </a>
              <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus supplier ini?\');">
                <i class="fa fa-trash"></i>
              </button>
              </form>';
              })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('main.rollstock.setup.suppliers.create');
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
          'code' => 'required|string|max:2|unique:paper_suppliers',
          'short_name' => 'required|string',
          'full_name' => 'required|string',
          'lead_time' => 'required|numeric',
          'currency' => 'required|string|max:3',
          'term' => 'required|numeric',
        ));

        $user = Auth::user();

        $supp = new PaperSupplier;
        $supp->code = strtoupper($request->code);
        $supp->ex_code = $this->emptyStringToNull(strtoupper($request->ex_code));
        $supp->short_name = strtoupper($request->short_name);
        $supp->full_name = strtoupper($request->full_name);
        $supp->address = $this->emptyStringToNull(strtoupper($request->address));
        $supp->lead_time = $request->lead_time;
        $supp->currency = strtoupper($request->currency);
        $supp->term = $request->term;
        $supp->contact_person = $this->emptyStringToNull(strtoupper($request->contact_person));
        $supp->phone = $this->emptyStringToNull($request->phone);
        $supp->fax = $this->emptyStringToNull($request->fax);
        $supp->created_by = $user->username;
        $supp->save();

        return redirect()->back()->with('status-success','Tambah supplier berhasil.');
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
        $data = PaperSupplier::findOrFail($id);

        return view('main.rollstock.setup.suppliers.edit')->withData($data);
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
        'short_name' => 'required|string',
        'full_name' => 'required|string',
        'lead_time' => 'required|numeric',
        'currency' => 'required|string|max:3',
        'term' => 'required|numeric',
      ));

      $user = Auth::user();

      $supp = PaperSupplier::findOrFail($id);
      $supp->ex_code = $this->emptyStringToNull(strtoupper($request->ex_code));
      $supp->short_name = strtoupper($request->short_name);
      $supp->full_name = strtoupper($request->full_name);
      $supp->address = $this->emptyStringToNull(strtoupper($request->address));
      $supp->lead_time = $request->lead_time;
      $supp->currency = strtoupper($request->currency);
      $supp->term = $request->term;
      $supp->contact_person = $this->emptyStringToNull(strtoupper($request->contact_person));
      $supp->phone = $this->emptyStringToNull($request->phone);
      $supp->fax = $this->emptyStringToNull($request->fax);
      $supp->rstatus = 'AM';
      $supp->updated_by = $user->username;
      $supp->save();

      return redirect()->route('suppliers.index')->with('status-success','Edit supplier berhasil.');
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

      $supp = PaperSupplier::findOrFail($id);
      $supp->rstatus = 'DL';
      $supp->deleted_by = $user->username;
      $supp->deleted_at = Carbon::now();
      $supp->save();

      return redirect()->back()->with('status-success','Hapus supplier berhasil.');
    }
}
