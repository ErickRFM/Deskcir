<div class="card chat-box p-3 mb-3" id="chatBox">

@foreach($ticket->messages as $m)

<div class="message {{ $m->user_id == auth()->id() ? 'me':'them' }}">

<div class="bubble">

<div class="fw-bold small">
{{ $m->user->name }}
</div>

<div>{{ $m->message }}</div>

@if($m->file)
<a href="{{ asset('storage/'.$m->file) }}"
class="file-link">📎 Archivo</a>
@endif

<div class="time">
{{ $m->created_at->format('H:i') }}
</div>

</div>
</div>

@endforeach

</div>

<div class="card p-3">

<form method="POST"
action="{{ $route }}"
enctype="multipart/form-data">
@csrf

<textarea name="message"
class="form-control input-pro mb-2"></textarea>

<input type="file"
class="form-control input-pro mb-2"
name="file">

<button class="btn btn-client">
Responder
</button>

</form>

</div>

@include('support.partials.chat-style')