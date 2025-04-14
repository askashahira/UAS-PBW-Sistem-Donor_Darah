<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PermintaanBaruNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'pesan' => $this->data['pesan'] ?? 'Ada permintaan donor baru.',
            'permintaan_id' => $this->data['permintaan_id'],
        ];
    }
}
