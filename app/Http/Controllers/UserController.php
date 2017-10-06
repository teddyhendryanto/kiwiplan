<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\GeneralTrait;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

use Auth;

class UserController extends Controller
{
    use GeneralTrait;

    public function __construct(){
      $this->middleware('auth');
    }

    public function index(Request $request){
      return view('main.users.index');
    }

    public function getUsersDatatable(Request $request){

      $data = User::select('users.*','roles.name as role_name','role_user.user_id')
              ->leftJoin('role_user', 'role_user.user_id', 'users.id')
              ->leftJoin('roles', 'role_user.role_id', 'roles.id')
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

    public function create()
    {
      $roles = Role::all();
      return view('main.users.create')->withRoles($roles);
    }

    public function store(Request $request){
      $this->validate($request, array(
        'username' => 'required|string|min:5|max:5|unique:users',
        'name' => 'required|string|min:3|max:255',
        'email' => 'sometimes|required|email',
        'password' => 'required|string|min:4',
        'role' => 'required',
      ));

      $user = Auth::user();

      $users = User::create([
          'username' => strtolower($request->username),
          'name' => ucwords($request->name),
          'email' => strtolower($request->email),
          'password' => bcrypt($request->password),
          'rstatus' => 'NW',
          'created_by' => $user->username,
      ]);

      if(isset($request->role)){
        // attach
        foreach ($request->input('role') as $key => $value) {
           $users->attachRole($value, true);
        }
      }

      return redirect()->back()->with('status-success','Tambah user berhasil.');
    }

    public function show($id){

    }

    public function edit($id){
      $users = User::findOrFail($id);
      $roles = Role::all();

      return view('main.users.edit')->withData($users)->withRoles($roles);
    }

    public function update(Request $request, $id){
      // dd($request);
      $this->validate($request, array(
        'name' => 'required|string|min:3|max:255',
        'email' => 'sometimes|required|email',
        'role' => 'required',
      ));

      $user = Auth::user();

      $users = User::find($id);
      $users->name= ucwords($request->name);
      $users->email= strtolower($request->email);
      $users->rstatus =  'AM';
      $users->updated_by = $user->username;
      $users->save();

      if(isset($request->role)){
        // detach first
        $users->roles()->sync(array());
        // attach again
        foreach ($request->role as $key => $value) {
           $users->attachRole($value);
        }
      }
      else{
        // detach
        $users->roles()->sync(array());
      }

      return redirect()->back()->with('status-success','Edit user berhasil.');
    }

    public function destroy($id)
    {
      $user = Auth::user();

      $users = User::findOrFail($id);

      $users->rstatus    = 'DL';
      $users->deleted_by = $user->username;
      $users->deleted_at = date('Y-m-d H:i:s');
      $users->save();

      // delete User Role
      $users->roles()->detach();

      if(count($users)> 0){
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
