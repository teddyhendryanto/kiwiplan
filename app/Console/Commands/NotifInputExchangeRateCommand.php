<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Notification;

use App\Models\NotificationType;
use App\Models\ExchangeRate;
use App\Models\User;

use App\Notifications\NotifInputExchangeRateChecker;

class NotifInputExchangeRateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:checkExchangeRate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger notification to check user\'s input exchanger rate.';

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
      $notif_types = NotificationType::with('users')->where('type','exchange-rate')->first();

      \Log::info('1 '.$notif_types->type.' printed. Run at '.\Carbon\Carbon::now());
      $check = ExchangeRate::whereDate('rate_date', date('Y-m-d'))->first();

      if(is_null($check)){
        $type_log = "Kurs";
        foreach ($notif_types->users as $user) {
          \Log::info('2 '.$user->username.' notified. Run at '.\Carbon\Carbon::now());
          $user->notify(new NotifInputExchangeRateChecker($user->department, $user, $type_log, date('Y-m-d')));
        }
      }

    }
}
