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
use App\Models\PaperGramatur;

class PaperGramaturController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('main.rollstock.setup.gramatures.index');
    }

    public function getGramaturListDatatable(Request $request){
      DB::statement(DB::raw('set @rownum=0'));
      $gramatures = PaperGramatur::select([
        DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        'id',
        'gramatur'
      ]);
      $datatables = Datatables::of($gramatures)
                    ->addColumn('action', function ($gramatur) {
                      return '
                      <form class="form" role="form" method="POST" action="'.route('gramatures.destroy', $gramatur->id).'">
                      <input type="hidden" name="_token" value="'.csrf_token().'">
                      <input type="hidden" name="_method" value="DELETE">
                      <a href="'.route('gramatures.edit', $gramatur->id).'" class="btn btn-xs btn-default">
                        <i class="fa fa-pencil"></i>
                      </a>
                      <button type="submit" class="btn btn-xs btn-default" onclick="return confirm(\'Yakin mau hapus gramatur ini?\');">
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
      return view('main.rollstock.setup.gramatures.create');
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
          'gramatur' => 'required|string|min:3|max:3|unique:paper_gramatures',
        ));

        $user = Auth::user();

        $supp = new PaperGramatur;
        $supp->gramatur = strtoupper($request->gramatur);
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
        $data = PaperGramatur::findOrFail($id);

        return view('main.rollstock.setup.gramatures.edit')->withData($data);
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
        'gramatur' => 'required|string|min:3|max:3|unique:paper_gramatures',
      ));

      $user = Auth::user();

      $supp = PaperGramatur::findOrFail($id);
      $supp->gramatur = strtoupper($request->gramatur);
      $supp->rstatus = 'AM';
      $supp->updated_by = $user->username;
      $supp->save();

      return redirect()->route('gramatures.index')->with('status-success','Edit gramatur berhasil.');
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

      $supp = PaperGramatur::findOrFail($id);
      $supp->rstatus = 'DL';
      $supp->deleted_by = $user->username;
      $supp->deleted_at = Carbon::now();
      $supp->save();

      return redirect()->back()->with('status-success','Hapus gramatur berhasil.');
    }
}
