@extends('layouts.app')

@section('title', 'Data Pendonor')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-danger fw-bold">Daftar Pendonor Anda</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($pendonors->isNotEmpty())
        <div class="mb-4">
            <form action="{{ route('pendonor.updateStatus', $pendonors[0]->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <label for="status" class="form-label">Status Ketersediaan Anda:</label>
                <select name="status" id="status" class="form-select w-auto d-inline mx-2">
                    <option value="Tersedia" {{ $pendonors[0]->status == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Tidak Tersedia" {{ $pendonors[0]->status == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </form>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <a href="{{ route('pendonor.create') }}" class="btn btn-danger mb-4">+ Tambah Pendonor</a>

    {{-- ✅ Notifikasi Permintaan Donor --}}
    <div class="mb-5">
        <h5 class="fw-bold">Notifikasi Permintaan Donor</h5>
        @php
            $notifications = auth()->user()->unreadNotifications;
        @endphp

        @if ($notifications->count() > 0)
            <ul class="list-group">
                @foreach ($notifications as $notification)
                    <li class="list-group-item">
                        <p class="mb-1">{{ $notification->data['pesan'] ?? 'Ada permintaan donor baru' }}</p>
                        <div class="d-flex gap-2 mb-2">
                            <a href="{{ route('pendonor.terima-permintaan', ['id' => $notification->data['permintaan_id']]) }}" class="btn btn-sm btn-success">Terima</a>
                            <a href="{{ route('pendonor.tolak-permintaan', ['id' => $notification->data['permintaan_id']]) }}" class="btn btn-sm btn-danger">Tolak</a>
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">Belum ada notifikasi baru.</p>
        @endif
    </div>

    {{-- ✅ Tabel Data Pendonor --}}
    @if ($pendonors->isEmpty())
        <p class="text-muted">Belum ada data pendonor. Silakan tambahkan terlebih dahulu.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-danger">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No. Telepon</th>
                        <th>Gol. Darah</th>
                        <th>Asal Daerah</th>
                        <th>Riwayat Donor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendonors as $index => $pendonor)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pendonor->nama }}</td>
                            <td>{{ $pendonor->no_telp }}</td>
                            <td>{{ $pendonor->golongan_darah }}</td>
                            <td>{{ $pendonor->asal_daerah }}</td>
                            <td>{{ $pendonor->riwayat_donor ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">{{ $pendonor->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('pendonor.edit', $pendonor->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('pendonor.destroy', $pendonor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
