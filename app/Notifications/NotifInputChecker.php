<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Carbon\Carbon;
use Auth;

class NotifInputChecker extends Notification
{
    use Queueable;

    protected $department;
    protected $user_notify;
    protected $input_checker;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($department, $user_notify, $input_checker)
    {
      $this->department = $department;
      $this->user_notify = $user_notify;
      $this->input_checker = $input_checker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
      return [
        'department' => $this->department,
        'user_notify' => $this->user_notify,
        'input_checker' => $this->input_checker,
      ];
    }
}
