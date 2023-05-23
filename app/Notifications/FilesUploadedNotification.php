<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FilesUploadedNotification extends Notification
{
    use Queueable;
    protected $urls;
    protected $user;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($urls, $user)
    {
        $this->urls = $urls;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $downloadLinks = collect($this->urls)->map(function ($url) {
            $fileName = basename($url);
            return '<a href="' . $url . '">' . $fileName . '</a>';
        })->implode('<br>');

        $username = $this->user->name;

        return (new MailMessage)
            ->subject('Files Uploaded')
            ->line('We have uploaded files for you.')
            ->line('Please click the links below to download the files:')
            ->view('emails.files_uploaded', [
                'downloadLinks' => $downloadLinks,
                'username' => $username,
            ]);
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
            //
        ];
    }
}
