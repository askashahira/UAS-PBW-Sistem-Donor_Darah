<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penerima;
use App\Models\Pendonor;
use App\Models\User;
use App\Models\PermintaanDonor;
use App\Notifications\PermintaanBaruNotification;
use Illuminate\Support\Facades\DB;


class PenerimaController extends Controller
{
    public function index()
    {
        $penerimas = Penerima::all();
        return view('dashboard.penerima', compact('penerimas'));
    }

    public function create()
    {
        return view('penerima.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required',
            'golongan_darah' => 'required',
            'asal_daerah' => 'required',
            'riwayat_transfusi' => 'nullable',
        ]);
    
        Penerima::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'golongan_darah' => $request->golongan_darah,
            'asal_daerah' => $request->asal_daerah,
            'riwayat_transfusi' => $request->riwayat_transfusi,
        ]);    

        return redirect()->route('dashboard.index')->with('success', 'Data penerima berhasil disimpan.');
    }

    public function edit($id)
    {
        $penerima = Penerima::findOrFail($id);
        return view('penerima.edit', compact('penerima'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required',
            'golongan_darah' => 'required',
            'asal_daerah' => 'required',
            'riwayat_transfusi' => 'nullable',
        ]);

        $penerima = Penerima::findOrFail($id);
        $penerima->update($request->all());

        return redirect()->route('penerimas.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penerima = Penerima::findOrFail($id);
        $penerima->delete();

        return redirect()->route('penerimas.index')->with('success', 'Data berhasil dihapus!');
    }

    public function cariPendonor()
    {
        $user = auth()->user();
    
        if ($user->role === 'penerima' && !$user->penerima) {
            \App\Models\Penerima::create([
                'user_id' => $user->id,
            ]);
    
            // Refresh user untuk load ulang relasi
            $user->load('penerima');
        }
    
        $donors = User::where('role', 'pendonor')
                    ->orderBy('created_at', 'desc')
                    ->get();
    
        return view('penerima.cari-pendonor', compact('donors', 'user'));
    }
    
    
    public function tambahPendonor(Request $request, $pendonor_id)
{
    \Log::info('Request tambah pendonor diterima', [
        'pendonor_id' => $pendonor_id,
        'user' => auth()->user()->id
    ]);
    
    $user = auth()->user();
    $penerima = $user->penerima;

    if (!$penerima) {
        return redirect()->back()->with('error', 'Data penerima tidak ditemukan untuk akun ini.');
    }

    $pendonor = Pendonor::find($pendonor_id);
    if (!$pendonor) {
        return redirect()->back()->with('error', 'Pendonor tidak ditemukan.');
    }

    $sudahAda = PermintaanDonor::where('penerima_id', $penerima->id)
                                ->where('pendonor_id', $pendonor->id)
                                ->exists();

    if ($sudahAda) {
        return redirect()->back()->with('warning', 'Kamu sudah pernah mengirim permintaan ke pendonor ini.');
    }

    // ✅ Simpan permintaan donor
    $permintaan = PermintaanDonor::create([
        'penerima_id' => $penerima->id,
        'pendonor_id' => $pendonor->id,
    ]);

    // ✅ Kirim notifikasi ke pendonor
    if ($pendonor->user) {
        $pendonor->user->notify(new \App\Notifications\PermintaanBaruNotification([
            'pesan' => 'Ada permintaan donor dari ' . $penerima->nama,
            'permintaan_id' => $permintaan->id,
        ]));
    }

    return redirect()->back()->with('success', 'Permintaan donor berhasil dikirim.');
}
    
    
}
