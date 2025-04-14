<?php

namespace App\Http\Controllers;

use App\Models\Pendonor;
use Illuminate\Http\Request;
use App\Models\PermintaanDonor;
use App\Models\Penerima;
use App\Models\User;
use App\Models\Chat;


class PendonorController extends Controller
{
    // Tampilkan semua data pendonor milik user login
    public function index()
    {
        $pendonors = Pendonor::where('user_id', auth()->id())->get();
        return view('dashboard.pendonor', compact('pendonors'));
    }

    // Tampilkan form input pendonor
    public function create()
    {
        $existing = Pendonor::where('user_id', auth()->id())->first();

        if ($existing) {
            return redirect()->route('dashboard.pendonor')->with('warning', 'Kamu sudah mengisi data pendonor sebelumnya.');
        }

        return view('pendonor.create');
    }

    // Simpan data pendonor ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'golongan_darah' => 'required|string',
            'asal_daerah' => 'required|string|max:255',
            'riwayat_donor' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'tersedia';

        Pendonor::create($validated);

        return redirect()->route('dashboard.pendonor')->with('success', 'Data pendonor berhasil disimpan.');
    }

    public function edit($id)
{
    $pendonor = Pendonor::findOrFail($id);
    return view('pendonor.edit', compact('pendonor'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'no_telp' => 'required|string|max:20',
        'golongan_darah' => 'required|string',
        'asal_daerah' => 'required|string|max:255',
        'riwayat_donor' => 'nullable|string',
    ]);

    $pendonor = Pendonor::findOrFail($id);
    $pendonor->update($request->all());

    return redirect()->route('dashboard.pendonor')->with('success', 'Data berhasil diperbarui.');
}

public function destroy($id)
{
    $pendonor = Pendonor::findOrFail($id);
    $pendonor->delete();

    return redirect()->route('dashboard.pendonor')->with('success', 'Data berhasil dihapus.');
}
// Tampilkan detail pendonor berdasarkan ID (untuk public lihat profil)
public function show($userId)
{
    // Ambil informasi user yang dimaksud
    $user = User::findOrFail($userId);
    
    // Periksa apakah pengguna adalah pendonor
    $isPendonor = $user->role === 'pendonor';  // Sesuaikan dengan logika role pengguna di database
    
    // Ambil permintaan donor yang terkait (sesuaikan sesuai model dan relasi yang ada)
    $request = PermintaanDonor::where('user_id', $userId)->first(); // Contoh
    
    return view('chat.show', compact('user', 'isPendonor', 'request'));
}


// Tangani pesan yang dikirim ke pendonor
public function kirimPesan(Request $request, $id)
{
    $request->validate([
        'pesan' => 'required|string|max:255',
    ]);

    // Di sini bisa kamu kirim email, simpan ke tabel 'pesan', atau log aja dulu
    \Log::info("Pesan untuk pendonor ID $id: " . $request->pesan);

    return redirect()->back()->with('success', 'Pesan berhasil dikirim.');
}

// tangani status ketersediaan
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Tersedia,Tidak Tersedia',
    ]);

    $pendonor = Pendonor::findOrFail($id);
    
    // Pastikan hanya user yang sesuai yang bisa ubah
    if ($pendonor->user_id !== auth()->id()) {
        abort(403);
    }

    $pendonor->status = $request->status;
    $pendonor->save();

    return redirect()->back()->with('success', 'Status ketersediaan berhasil diperbarui.');
}

// Di dalam class PendonorController

public function terimaPermintaan($id)
{
    $permintaan = PermintaanDonor::findOrFail($id);
    $permintaan->status = 'diterima';
    $permintaan->save();
    
    // Tandai notifikasi sebagai telah dibaca
    auth()->user()->notifications->where('data.permintaan_id', $id)->markAsRead();
    
    // Kirim pemberitahuan ke penerima bahwa permintaan diterima
    $penerima = Penerima::find($permintaan->penerima_id);
    $penerima->user->notify(new \App\Notifications\PermintaanDonorDiterima($permintaan));
    
    // Redirect ke halaman chat dengan peminta
    return redirect()->route('chat.show', ['user' => $penerima->user->id]);
}


public function tolakPermintaan($id)
{
    $permintaan = PermintaanDonor::findOrFail($id);
    $permintaan->status = 'ditolak';
    $permintaan->save();
    
    // Tandai notifikasi sebagai telah dibaca
    auth()->user()->notifications->where('data.permintaan_id', $id)->markAsRead();
    
    // Kirim pemberitahuan ke penerima bahwa permintaan ditolak
    $penerima = Penerima::find($permintaan->penerima_id);
    $penerima->user->notify(new \App\Notifications\PermintaanDonorDitolak($permintaan));
    
    return redirect()->route('dashboard.pendonor')->with('success', 'Permintaan donor telah ditolak');
}

}
