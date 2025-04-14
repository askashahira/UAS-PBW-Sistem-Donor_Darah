@extends('layouts.app')

@section('title', 'Cari Pendonor')

@section('content')
<div class="container py-5">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @yield('content')
</main>
    <h2 class="mb-4 text-danger fw-bold">Cari Pendonor</h2>

    @if($donors->isEmpty())
        <div class="alert alert-warning">Belum ada pendonor yang tersedia.</div>
    @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach($donors as $donor)
                <div class="col">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $donor->name }}</h5>
                            <p class="card-text mb-1">Golongan Darah: <span class="fw-bold text-danger">{{ $donor->blood_type }}</span></p>
                            <p class="card-text mb-1">Lokasi: {{ $donor->location }}</p>
                            <p class="card-text mb-1">Telp: {{ $donor->phone ?? '-' }}</p>
                            
                            @php
                                $sudahDitambah = false;
                                $penerima = auth()->user()->penerima;

                                if ($penerima) {
                                    $sudahDitambah = \App\Models\PermintaanDonor::where('penerima_id', $penerima->id)
                                        ->where('pendonor_id', $donor->id)
                                        ->exists();
                                }
                            @endphp

                            <form action="{{ route('penerimas.tambahPendonor', $donor->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="btn btn-sm mt-2 {{ $sudahDitambah ? 'btn-success' : 'btn-danger' }}" 
                                    {{ $sudahDitambah ? 'disabled' : '' }}>
                                    {{ $sudahDitambah ? 'Berhasil Ditambahkan' : 'Tambah Pendonor' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection