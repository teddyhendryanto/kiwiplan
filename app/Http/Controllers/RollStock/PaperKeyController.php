<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Traits\GeneralTrait;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PaperSupplier;
use App\Models\PaperKey;

class PaperKeyController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('main.rollstock.setup.keys.index');
    }

    public function getKeyListDatatable(Request $request){

      $keys = PaperKey::with('supplier')->get();
      $datatables = Datatables::collection($keys)
                    ->addColumn('action', function ($key) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('keys.destroy', $key->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('keys.edit', $key->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus paper key ini?\');">
                        <i class="fa fa-trash"></i>
                      </button>
                      </form>';
                      });
      if ($keyword = $request->get('search')['value']) {
          $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
      }
      return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $suppliers = PaperSupplier::all();
      return view('main.rollstock.setup.keys.create')->withSuppliers($suppliers);
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
          'supplier' => 'required',
          'paper_key' => 'required|string|min:7|max:7|unique:paper_keys',
        ));

        $user = Auth::user();

        $keys = new PaperKey;
        $keys->supplier_id = $request->supplier;
        $keys->paper_key = strtoupper($request->paper_key);
        $keys->created_by = $user->username;
        $keys->save();

        return redirect()->back()->with('status-success','Tambah paper key berhasil.');
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
        $data = PaperKey::findOrFail($id);
        $suppliers = PaperSupplier::all();
        return view('main.rollstock.setup.keys.edit')->withData($data)->withSuppliers($suppliers);
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
        'supplier' => 'required',
        'paper_key' => 'required|string|min:7|max:7|unique:paper_keys',
      ));

      $user = Auth::user();

      $keys = PaperKey::findOrFail($id);
      $keys->supplier_id = $request->supplier;
      $keys->paper_key = strtoupper($request->paper_key);
      $keys->rstatus = 'AM';
      $keys->updated_by = $user->username;
      $keys->save();

      return redirect()->route('keys.index')->with('status-success','Edit paper key berhasil.');
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

      $keys = PaperKey::findOrFail($id);
      $keys->rstatus = 'DL';
      $keys->deleted_by = $user->username;
      $keys->deleted_at = Carbon::now();
      $keys->save();

      return redirect()->back()->with('status-success','Hapus paper key berhasil.');
    }
}
