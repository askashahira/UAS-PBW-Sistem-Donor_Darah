@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Data Penerima</h2>

    <form action="{{ route('penerima.update', $penerima->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" value="{{ old('nama', $penerima->nama) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>No Telp</label>
            <input type="text" name="no_telp" value="{{ old('no_telp', $penerima->no_telp) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Golongan Darah</label>
            <input type="text" name="golongan_darah" value="{{ old('golongan_darah', $penerima->golongan_darah) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Asal Daerah</label>
            <input type="text" name="asal_daerah" value="{{ old('asal_daerah', $penerima->asal_daerah) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ old('keterangan', $penerima->keterangan) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
