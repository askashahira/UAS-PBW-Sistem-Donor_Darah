@extends('layouts.app')

@section('title', 'Edit Data Pendonor')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-warning fw-bold">Edit Data Pendonor</h2>
    <form action="{{ route('pendonor.update', $pendonor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $pendonor->nama }}" required>
        </div>

        <div class="mb-3">
            <label for="no_telp" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $pendonor->no_telp }}" required>
        </div>

        <div class="mb-3">
            <label for="golongan_darah" class="form-label">Golongan Darah</label>
            ion value="B+" {{ $pendonor->golongan_darah == 'B+' ? 'selected' : '' }}>B+</option>
                <opt<select class="form-select" id="golongan_darah" name="golongan_darah" required>
                <option value="A+" {{ $pendonor->golongan_darah == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ $pendonor->golongan_darah == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B-" {{ $pendonor->golongan_darah == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ $pendonor->golongan_darah == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ $pendonor->golongan_darah == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ $pendonor->golongan_darah == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ $pendonor->golongan_darah == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="asal_daerah" class="form-label">Asal Daerah</label>
            <input type="text" class="form-control" id="asal_daerah" name="asal_daerah" value="{{ $pendonor->asal_daerah }}" required>
        </div>

        <div class="mb-3">
            <label for="riwayat_donor" class="form-label">Riwayat Donor/Transfusi (opsional)</label>
            <textarea class="form-control" id="riwayat_donor" name="riwayat_donor" rows="3">{{ $pendonor->riwayat_donor }}</textarea>
        </div>

        <button type="submit" class="btn btn-warning">Update Data</button>
    </form>
</div>
@endsection
