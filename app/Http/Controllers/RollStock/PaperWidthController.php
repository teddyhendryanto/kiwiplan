<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Traits\GeneralTrait;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PaperWidth;

class PaperWidthController extends Controller
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
      return view('main.rollstock.setup.widths.index');
    }

    public function getWidthListDatatable(Request $request){
      DB::statement(DB::raw('set @rownum=0'));
      $widths = PaperWidth::select([
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        'id',
        'width'
      ]);
      $datatables = Datatables::of($widths)
                    ->addColumn('action', function ($width) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('widths.destroy', $width->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('widths.edit', $width->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus lebar ini?\');">
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
      return view('main.rollstock.setup.widths.create');
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
          'width' => 'required|string|min:3|max:4|unique:paper_widths',
        ));

        $user = Auth::user();

        $supp = new PaperWidth;
        $supp->width = strtoupper($request->width);
        $supp->created_by = $user->username;
        $supp->save();

        return redirect()->back()->with('status-success','Tambah lebar berhasil.');
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
        $data = PaperWidth::findOrFail($id);

        return view('main.rollstock.setup.widths.edit')->withData($data);
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
        'width' => 'required|string|min:3|max:4|unique:paper_widths',
      ));

      $user = Auth::user();

      $supp = PaperWidth::findOrFail($id);
      $supp->width = strtoupper($request->width);
      $supp->rstatus = 'AM';
      $supp->updated_by = $user->username;
      $supp->save();

      return redirect()->route('widths.index')->with('status-success','Edit lebar berhasil.');
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

      $supp = PaperWidth::findOrFail($id);
      $supp->rstatus = 'DL';
      $supp->deleted_by = $user->username;
      $supp->deleted_at = Carbon::now();
      $supp->save();

      return redirect()->back()->with('status-success','Hapus lebar berhasil.');
    }
}
