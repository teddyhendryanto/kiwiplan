<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GeneralTrait;

use App\Models\Permission;
use Auth;

class PermissionController extends Controller
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
      return view('main.permissions.index');
    }

    public function getPermissionsDatatable(Request $request){

      $data = Permission::orderby('id')
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
      return view('main.permissions.create');
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
        'name' => 'required|min:3',
        'display_name' => 'required|min:3'
      ));

      $user = Auth::user();

      $permission = new Permission;
      $permission->name = strtolower($request->name);
      $permission->display_name = ucwords($request->display_name);
      $permission->description = $this->emptyStringToNull(ucfirst($request->description));
      $permission->rstatus = 'NW';
      $permission->created_by = $user->username;
      $permission->save();

      return redirect()->back()->with('status-success','Tambah permission berhasil.');
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
      $permission = Permission::find($id);

      return view('main.permissions.edit')->withData($permission);
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
        'name' => 'required|min:3',
        'display_name' => 'required|min:3'
      ));

      $user = Auth::user();
      $permission = Permission::findOrFail($id);

      $permission->name = strtolower($request->name);
      $permission->display_name = ucwords($request->display_name);
      $permission->description = $this->emptyStringToNull(ucfirst($request->description));
      $permission->rstatus = 'AM';
      $permission->updated_by = $user->username;
      $permission->save();

      return redirect()->back()->with('status-success','Edit permission berhasil.');
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
      $permission = Permission::findOrFail($id);

      $permission->rstatus    = 'DL';
      $permission->deleted_by = $user->username;
      $permission->deleted_at = date('Y-m-d H:i:s');
      $permission->save();

      // delete Permission
      $permission->roles()->detach();

      if(count($permission)> 0){
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
