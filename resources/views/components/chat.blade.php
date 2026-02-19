<div class="chat-box" id="chatBox">

@foreach($ticket->messages as $m)

<div class="message {{ $m->user_id==auth()->id() ? 'me':'them' }}">

<div class="bubble">

<div class="d-flex justify-content-between">
<b>{{ $m->user->name }}</b>

<span class="ticks">
✓✓
</span>
</div>

<div class="mt-1">
{{ $m->message }}
</div>

@if($m->file)
<a href="{{ asset('storage/'.$m->file) }}"
   class="file-link">
📎 Archivo
</a>
@endif

<div class="time">
{{ $m->created_at->format('H:i') }}
</div>

</div>
</div>

@endforeach

<div id="typing" class="typing d-none">
Escribiendo...
</div>

</div>

{{-- FORM --}}
<form method="POST"
      enctype="multipart/form-data"
      id="chatForm"
      action="{{ $action }}">

@csrf

<div class="chat-input">

<textarea name="message"
id="msg"
placeholder="Escribe un mensaje..."
required></textarea>

<label class="attach">
<input type="file" name="file" hidden>
📎
</label>

<button class="send">
➤
</button>

</div>

</form>

<script>

chatBox.scrollTop = chatBox.scrollHeight

msg.addEventListener('keydown',e=>{
if(e.key=='Enter'&&!e.shiftKey){
e.preventDefault()
chatForm.submit()
}
})

</script>

<style>

.chat-box{
height:60vh;
overflow:auto;
background:#f1f5f9;
padding:10px;
border-radius:14px;
}

.message{
display:flex;
margin-bottom:10px;
}

.me{justify-content:flex-end}
.them{justify-content:flex-start}

.bubble{
max-width:75%;
padding:10px;
border-radius:14px;
}

.me .bubble{
background:#00798E;
color:white;
}

.them .bubble{
background:#e5e7eb;
}

.chat-input{
display:flex;
gap:6px;
}

.chat-input textarea{
flex:1;
border-radius:12px;
padding:10px;
}

.send{
background:#00798E;
color:white;
border-radius:12px;
width:50px;
}

.ticks{
font-size:10px;
}

.dark .chat-box{
background:#060a15;
}

.dark .them .bubble{
background:#111827;
color:white;
}

</style>