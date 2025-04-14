<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\DonorRequest;

class DonorRequestNotification extends Notification
{
    use Queueable;

    public $request;

    public function __construct(DonorRequest $request)
    {
        $this->request = $request;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Permintaan Donor Baru',
            'body' => 'Ada permintaan darah ' . $this->request->blood_type . ' dari daerah ' . $this->request->location,
            'request_id' => $this->request->id,
        ];
    }


}
