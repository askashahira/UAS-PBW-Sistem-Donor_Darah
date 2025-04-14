@extends('layouts.app')

@section('title', 'Data Penerima')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-danger fw-bold">Daftar Penerima Darah</h2>

    {{-- ✅ Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Tombol Aksi --}}
    <div class="mb-4">
        <a href="{{ route('penerima.create') }}" class="btn btn-danger me-2">+ Tambah Penerima</a>
        <a href="{{ route('penerimas.cariPendonor') }}" class="btn btn-outline-danger">Cari Pendonor 🔍</a>
    </div>

    {{-- ✅ Notifikasi Status Permintaan Donor --}}
    <div class="mb-5">
        <h5 class="fw-bold">Status Permintaan Donor</h5>

        @php
            $notifications = auth()->user()->notifications;
        @endphp

        @if($notifications->count() > 0)
            <ul class="list-group">
                @foreach($notifications as $notification)
                    @if(isset($notification->data['status']))
                        <li class="list-group-item {{ $notification->data['status'] == 'diterima' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                            <p class="mb-1">{{ $notification->data['pesan'] }}</p>
                            @if($notification->data['status'] == 'diterima')
                            <a href="{{ route('chat.show', ['user' => $notification->data['pendonor_id']]) }}" class="btn btn-light btn-sm mb-2">Mulai Chat</a>
                            @endif
                            <small>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            <p class="text-muted">Belum ada status permintaan donor.</p>
        @endif
    </div>

    {{-- ✅ Tabel Data Penerima --}}
    @if ($penerimas->isEmpty())
        <p class="text-muted">Belum ada data penerima. Silakan tambahkan terlebih dahulu.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-danger">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No. Telepon</th>
                        <th>Gol. Darah Dibutuhkan</th>
                        <th>Asal Daerah</th>
                        <th>Riwayat Transfusi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penerimas as $index => $penerima)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $penerima->nama }}</td>
                            <td>{{ $penerima->no_telp }}</td>
                            <td>{{ $penerima->golongan_darah }}</td>
                            <td>{{ $penerima->asal_daerah }}</td>
                            <td>{{ $penerima->riwayat_transfusi ?? '-' }}</td>
                            <td>
                                <a href="{{ route('penerimas.edit', $penerima->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('penerimas.destroy', $penerima->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
