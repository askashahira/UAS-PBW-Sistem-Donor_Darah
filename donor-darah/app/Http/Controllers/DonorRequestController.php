<?php

namespace App\Http\Controllers;

use App\Models\DonorRequest;
use App\Models\Pendonor;
use App\Notifications\DonorRequestNotification;
use App\Notifications\DonorRequestCompletedNotification;
use Illuminate\Http\Request;

class DonorRequestController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'blood_type' => 'required',
            'location' => 'required',
            'message' => 'nullable',
        ]);

        // Simpan permintaan ke database
        $donorRequest = DonorRequest::create([
            'user_id' => auth()->id(), // user yang membuat permintaan
            'blood_type' => $request->blood_type,
            'location' => $request->location,
            'message' => $request->message,
        ]);

        // Cari pendonor yang sesuai
        $matchedDonors = Pendonor::where('golongan_darah', $request->blood_type)
            ->where('asal_daerah', $request->location)
            ->where('status', 'Tersedia') // pastikan hanya pendonor aktif
            ->get();

        // Kirim notifikasi ke user dari pendonor yang cocok
        foreach ($matchedDonors as $pendonor) {
            $user = $pendonor->user; // relasi pendonor ke user
            if ($user) {
                $user->notify(new DonorRequestNotification($donorRequest));
            }
        }

        return redirect()->route('home')->with('success', 'Permintaan donor berhasil dikirim.');
    }

    public function show($id)
    {
        // Ambil permintaan donor beserta data user
        $request = DonorRequest::with('user')->findOrFail($id);
        
        // Cek apakah user yang sedang login adalah pendonor untuk request ini
        $isPendonor = auth()->user()->pendonor && auth()->user()->pendonor->id === $request->pendonor_id;
        
        // Kirim data ke view
        return view('chat.show', compact('request', 'isPendonor'));
    }

    public function accept($id)
    {
        $request = DonorRequest::findOrFail($id);
        
        // Update status request jadi Diterima
        $request->status = 'Diterima';
        $request->save();

        // Update status pendonor jadi Tidak Tersedia
        $pendonor = auth()->user()->pendonor;
        $pendonor->status = 'Tidak Tersedia';
        $pendonor->save();

        return redirect()->route('chat.show', $request->id)
            ->with('success', 'Kamu telah menerima permintaan donor.');
    }

    public function decline($id)
    {
        $request = DonorRequest::findOrFail($id);

        // (Opsional) Kirim notifikasi ke penerima donor kalau ditolak

        return redirect()->route('dashboard')
            ->with('info', 'Permintaan donor telah ditolak.');
    }

    public function complete($id)
    {
        $request = DonorRequest::findOrFail($id);
        $request->status = 'Selesai';
        $request->save();

        // Kirim notifikasi ke pendonor dan penerima bahwa donor telah selesai
        $request->user->notify(new DonorRequestCompletedNotification($request)); // Notifikasi ke penerima
        $pendonor = $request->pendonor;
        $pendonor->user->notify(new DonorRequestCompletedNotification($request)); // Notifikasi ke pendonor

        return redirect()->route('dashboard')->with('success', 'Donor berhasil diselesaikan.');
    }
}
