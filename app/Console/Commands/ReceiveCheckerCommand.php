<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Notification;

use App\RollReceive;
use App\User;

use App\Notifications\NotifInputChecker;

class ReceiveCheckerCommand extends Command
{
  protected $user;
  protected $department;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receive:checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking input user.';

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
        $this->department = ['Accounting','RollStock'];

        for ($i=0; $i < count($this->department) ; $i++) {
          // Notify Accounting for Price = 1;
          switch ($this->department[$i]) {
            case 'Accounting':
              $count = RollReceive::where('cost_wgt_local',1)->count();
              break;
            case 'RollStock':
              $count = RollReceive::where('weight',1)->count();
              break;
          }

          if($count > 0){
            $this->user = User::where('department',$this->department[$i])->get();

            foreach ($this->user as $user) {
              $notify = $user->notify(new NotifInputChecker($this->department[$i], $this->user, $count));
              // if($notify){
              //   \Log::info(''.$user->name.' notify success. Run at '.\Carbon\Carbon::now());
              // }
              // else{
              //   \Log::info(''.$user->name.' notify failed. Run at '.\Carbon\Carbon::now());
              // }
            }
          }

        }

    }
}
