<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Notification;

use App\NotificationType;
use App\RollReceive;
use App\User;

use App\Notifications\NotifInputChecker;

class NotifInputCheckerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger notification to check user\' input.';

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
      $notif_types = NotificationType::with('users')->get();

      foreach ($notif_types as $type) {
        $count = 0;
        \Log::info('1 '.$type->type.' printed. Run at '.\Carbon\Carbon::now());

        switch ($type->type) {
          case 'roll-weight':
            \Log::info('1.1 '.$type->type.' query check. Run at '.\Carbon\Carbon::now());
            $count = RollReceive::where('weight',1)->count();
            break;
          case 'roll-price':
            \Log::info('1.2 '.$type->type.' query check. Run at '.\Carbon\Carbon::now());
            $count = RollReceive::where('cost_wgt_local',1)->count();
            break;
        }

        if ($count > 0) {
          foreach ($type->users as $user) {
            \Log::info('2 '.$user->username.' notified. Run at '.\Carbon\Carbon::now());
            $user->notify(new NotifInputChecker($user->department, $user, $count));
          }
        }



      }
    }
}
