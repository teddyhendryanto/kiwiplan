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
use App\Models\PaperQuality;

class PaperQualityController extends Controller
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
      return view('main.rollstock.setup.qualities.index');
    }

    public function getQualityListDatatable(Request $request){
      DB::statement(DB::raw('set @rownum=0'));
      $qualities = PaperQuality::select([
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        'id',
        'quality'
      ]);
      $datatables = Datatables::of($qualities)
                    ->addColumn('action', function ($quality) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('qualities.destroy', $quality->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('qualities.edit', $quality->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus kualitas ini?\');">
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
      return view('main.rollstock.setup.qualities.create');
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
          'quality' => 'required|string|min:2|unique:paper_qualities',
        ));

        $user = Auth::user();

        $supp = new PaperQuality;
        $supp->quality = strtoupper($request->quality);
        $supp->created_by = $user->username;
        $supp->save();

        return redirect()->back()->with('status-success','Tambah Kualitas berhasil.');
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
        $data = PaperQuality::findOrFail($id);

        return view('main.rollstock.setup.qualities.edit')->withData($data);
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
        'quality' => 'required|string|min:2|unique:paper_qualities',
      ));

      $user = Auth::user();

      $supp = PaperQuality::findOrFail($id);
      $supp->quality = strtoupper($request->quality);
      $supp->rstatus = 'AM';
      $supp->updated_by = $user->username;
      $supp->save();

      return redirect()->route('qualities.index')->with('status-success','Edit kualitas berhasil.');
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

      $supp = PaperQuality::findOrFail($id);
      $supp->rstatus = 'DL';
      $supp->deleted_by = $user->username;
      $supp->deleted_at = Carbon::now();
      $supp->save();

      return redirect()->back()->with('status-success','Hapus kualitas berhasil.');
    }
}
