@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Chat dengan {{ $otherUser->name }}</h2>

    <div class="card mb-3" style="max-height: 400px; overflow-y: auto;" id="chat-box">
        <div class="card-body">
            @foreach ($messages as $msg)
                <div class="mb-3 {{ $msg->from_user_id == auth()->id() ? 'text-end' : 'text-start' }}">
                    <div class="d-inline-block p-3 rounded 
                        {{ $msg->from_user_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                        <strong>{{ $msg->from_user_id == auth()->id() ? 'Saya' : $otherUser->name }}</strong><br>
                        {{ $msg->message }}<br>
                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('chat.send', $otherUser->id) }}" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Ketik pesan..." required>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>

    @if($isPendonor && $request->status === 'Diterima')
    <form action="{{ route('donor.requests.complete', $request->id) }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-success">Selesai Donor</button>
    </form>
@endif


    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Kembali</a>
</div>

{{-- Auto-scroll ke bawah --}}
<script>
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection
