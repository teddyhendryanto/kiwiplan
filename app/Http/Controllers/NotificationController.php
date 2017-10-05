<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GeneralTrait;

use Auth;
use App\Models\User;
use App\Models\NotificationType;
use App\Models\NotificationTypeUser;

use Carbon\Carbon;


class NotificationController extends Controller
{
    use GeneralTrait;

    public function markAllAsRead(){
      $user = Auth::user();

      $user->unreadNotifications()->update([
        'read_at' => Carbon::now(),
        'read_by' => $user->username,
      ]);

      return response()->json(array('status' => true));
    }

    public function markAsRead($notification_id){
      $user = Auth::user();

      $notification = $user->notifications()->where('id',$notification_id)->first();
      if ($notification){
        $notification->update([
          'read_at' => Carbon::now(),
          'read_by' => $user->username,
        ]);

        return response()->json(array('status' => true));
      }
    }

    public function index(){
      return view('main.setup.notification_types.index');
    }

    public function getNotificationsDatatable(Request $request){

      $data = NotificationType::orderby('id')
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
      $users = User::get();
      return view('main.setup.notification_types.create')->withUsers($users);
    }

    public function store(Request $request)
    {
      $this->validate($request, array(
        'type' => 'required|min:3',
        'description' => 'required|min:3'
      ));

      $user = Auth::user();

      $type = new NotificationType;
      $type->type = strtolower($request->type);
      $type->description = $this->emptyStringToNull(ucfirst($request->description));
      $type->rstatus = 'NW';
      $type->created_by = $user->username;
      $type->save();

      // auto add to notification_type_user
      if(isset($request->user)){
        // false -> won't overwrite the existing, add the new one.
        // true -> delete all existing, add the new one.
        $type->users()->sync($request->user, false);
      }

      return redirect()->back()->with('status-success','Tambah Tipe Notifikasi berhasil.');
    }

    public function edit($id)
    {
      $type = NotificationType::with('users')->find($id);

      $users = User::get();

      return view('main.setup.notification_types.edit')->withData($type)->withUsers($users);
    }

    public function update(Request $request, $id)
    {
      $this->validate($request, array(
        'type' => 'required|min:3',
        'description' => 'required|min:3'
      ));

      $user = Auth::user();
      $type = NotificationType::findOrFail($id);

      $type->type = strtolower($request->type);
      $type->description = $this->emptyStringToNull(ucfirst($request->description));
      $type->rstatus = 'AM';
      $type->updated_by = $user->username;
      $type->save();

      // auto add to notification_type_user
      if(isset($request->user)){
        // false -> won't overwrite the existing, add the new one.
        // true -> delete all existing, add the new one.
        $type->users()->sync($request->user);
      }
      else{
        $type->users()->sync(array());
      }

      return redirect()->back()->with('status-success','Edit Tipe Notifikasi berhasil.');
    }

    public function destroy($id)
    {
      $user = Auth::user();
      $type = NotificationTypes::findOrFail($id);

      $type->rstatus    = 'DL';
      $type->deleted_by = $user->username;
      $type->deleted_at = date('Y-m-d H:i:s');
      $type->save();

      // delete NotificationTypes
      $type->users()->detach();

      if(count($type)> 0){
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
