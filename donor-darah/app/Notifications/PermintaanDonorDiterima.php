<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PermintaanDonor;

class PermintaanDonorDiterima extends Notification
{
    use Queueable;

    protected $permintaanDonor;

    /**
     * Create a new notification instance.
     */
    public function __construct(PermintaanDonor $permintaanDonor)
    {
        $this->permintaanDonor = $permintaanDonor;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database']; // Kirim ke database saja
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'permintaan_id' => $this->permintaanDonor->id,
            'pendonor_id' => $this->permintaanDonor->pendonor_id,
            'pendonor_nama' => $this->permintaanDonor->pendonor->nama ?? 'Pendonor',
            'pesan' => 'Permintaan donor darah Anda telah diterima',
            'status' => 'diterima',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}