<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Notification;

use App\Models\NotificationType;
use App\Models\ReceiveRoll;
use App\Models\VerifyRoll;
use App\Models\User;

use App\Notifications\NotifUnverifiedRoll;

class NotifUnverifiedRollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:checkUnverifiedRoll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger notification to check roll has not verified.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $notif_types = NotificationType::with('users')->where('type','roll-unverified')->first();

      \Log::info('1 '.$notif_types->type.' printed. Run at '.\Carbon\Carbon::now());
      $count = ReceiveRoll::whereNotIn('id', [DB::raw("select distinct receive_roll_id from verify_rolls where rstatus <> 'DL'")])
                          ->where('rstatus','<>','DL')
                          ->count();

      if($count > 0){
        $type_log = 'Unverified Roll';
        foreach ($notif_types->users as $user) {
          \Log::info('2 '.$user->username.' notified. Run at '.\Carbon\Carbon::now());
          $user->notify(new NotifUnverifiedRoll($user->department, $user, $type_log, $count));
        }
      }
    }
}
