<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GeneralTrait;

use App\Models\Role;
use App\Models\Permission;
use Auth;

class RoleController extends Controller
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
      return view('main.roles.index');
    }

    public function getRolesDatatable(Request $request){

      $data = Role::orderby('id')
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
      $permissions = Permission::all();
      return view('main.roles.create')->withPermissions($permissions);
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

      $role = new Role;
      $role->name = strtolower($request->name);
      $role->display_name = ucwords($request->display_name);
      $role->description = $this->emptyStringToNull(ucfirst($request->description));
      $role->rstatus = 'NW';
      $role->created_by = $user->username;
      $role->save();

      if(isset($request->permission)){
        // attach
        foreach ($request->input('permission') as $key => $value) {
           $role->attachPermission($value, true);
        }
      }

      return redirect()->back()->with('status-success','Tambah role berhasil.');

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
      $role = Role::find($id);
      $permissions = Permission::all();

      return view('main.roles.edit')->withData($role)->withPermissions($permissions);
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
      $role = Role::findOrFail($id);

      $role->name = strtolower($request->name);
      $role->display_name = ucwords($request->display_name);
      $role->description = $this->emptyStringToNull(ucfirst($request->description));
      $role->rstatus = 'AM';
      $role->updated_by = $user->username;
      $role->save();

      if(isset($request->permission)){
        // detach first
        $role->perms()->sync(array());
        // attach again
        foreach ($request->input('permission') as $key => $value) {
           $role->attachPermission($value, true);
        }
      }
      else{
        // detach
        $role->perms()->sync(array());
      }

      return redirect()->back()->with('status-success','Edit role berhasil.');
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
      $role = Role::findOrFail($id);

      $role->rstatus    = 'DL';
      $role->deleted_by = $user->username;
      $role->deleted_at = date('Y-m-d H:i:s');
      $role->save();

      // delete Permission
      $role->perms()->detach();

      if(count($role)> 0){
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
