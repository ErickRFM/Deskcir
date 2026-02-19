@extends('layouts.app')

@section('content')

<div class="container py-4">

<div class="card p-4 mb-3">

<h5 class="fw-bold mb-1">{{ $ticket->subject }}</h5>

<small class="text-muted">
Estado:
<span class="badge bg-warning">
{{ $ticket->status }}
</span>
</small>

</div>

{{-- ===== BOTONES NUEVOS (AGREGADOS SIN MOVER TU DISEÑO) ===== --}}
<div class="d-flex gap-2 mb-3">

<button onclick="startScreen()" class="btn btn-outline-secondary px-3">
📺 Compartir MI pantalla
</button>

<button onclick="startCall()" class="btn btn-outline-secondary px-3">
📞 Llamar técnico
</button>

<button onclick="stopStream()" id="stopBtn"
class="btn btn-outline-danger px-3 d-none">
⛔ Detener
</button>

</div>

{{-- ===== VIDEOS OCULTOS ===== --}}
<div id="videoZone" class="row g-3 mb-4 d-none">

<div class="col-md-6">
<video id="local" autoplay muted
class="w-100 rounded border bg-dark"
style="min-height:220px"></video>
</div>

<div class="col-md-6">
<video id="remote" autoplay
class="w-100 rounded border bg-dark"
style="min-height:220px"></video>
</div>

</div>

{{-- CHAT --}}
<div class="card p-3 mb-3 chat-box" id="chatBox">

@foreach($ticket->messages as $m)

<div class="message {{ $m->user_id==auth()->id()?'me':'them' }}">
<div class="bubble">

<div class="fw-bold small mb-1">
{{ $m->user->name }}
</div>

<div>{{ $m->message }}</div>

@if($m->file)
<a href="{{ asset('storage/'.$m->file) }}"
class="file-link">
📎 Ver archivo
</a>
@endif

<div class="time">
{{ $m->created_at->format('H:i') }}
</div>

</div>
</div>

@endforeach

</div>

{{-- FORM --}}
<div class="card p-3">

<form method="POST"
action="/support/{{ $ticket->id }}/message"
enctype="multipart/form-data">

@csrf

<textarea name="message"
class="form-control mb-2"
rows="2"
placeholder="Escribe tu mensaje..."></textarea>

<div class="d-flex gap-2 align-items-center">

<input type="file"
name="file"
class="form-control">

<button class="btn btn-client px-4 fs-5">
➤
</button>

</div>

</form>
</div>

</div>

<style>

.chat-box{
height:60vh;
overflow-y:auto;
}

.message{
display:flex;
margin-bottom:12px;
}

.me{justify-content:flex-end}
.them{justify-content:flex-start}

.bubble{
max-width:70%;
padding:10px 12px;
border-radius:14px;
}

.me .bubble{
background:#00798E;
color:white;
}

.them .bubble{
background:#e5e7eb;
}

.time{
font-size:10px;
opacity:.7;
text-align:right;
}

</style>

{{-- ===== SCRIPT WEBRTC AGREGADO ===== --}}
<script>

let pc, stream
const zone = document.getElementById('videoZone')
const stopBtn = document.getElementById('stopBtn')

const config={ iceServers:[{urls:'stun:stun.l.google.com:19302'}] }

async function startScreen(){
zone.classList.remove('d-none')
stopBtn.classList.remove('d-none')

stream = await navigator.mediaDevices.getDisplayMedia({video:true,audio:true})
local.srcObject = stream
initPeer()

stream.getTracks().forEach(t=>pc.addTrack(t,stream))
createOffer()
}

async function startCall(){
zone.classList.remove('d-none')
stopBtn.classList.remove('d-none')

stream = await navigator.mediaDevices.getUserMedia({audio:true,video:true})
local.srcObject = stream
initPeer()

stream.getTracks().forEach(t=>pc.addTrack(t,stream))
createOffer()
}

function stopStream(){
stream?.getTracks().forEach(t=>t.stop())
zone.classList.add('d-none')
stopBtn.classList.add('d-none')
}

function initPeer(){
pc=new RTCPeerConnection(config)

pc.ontrack=e=>remote.srcObject=e.streams[0]

pc.onicecandidate=e=>{
if(e.candidate){
fetch('/webrtc/ice',{
method:'POST',
headers:{
'X-CSRF-TOKEN':'{{ csrf_token() }}',
'Content-Type':'application/json'
},
body:JSON.stringify({
ticket_id:'{{ $ticket->id }}',
candidate:e.candidate
})
})
}}
}

async function createOffer(){
let offer=await pc.createOffer()
await pc.setLocalDescription(offer)

fetch('/webrtc/offer',{
method:'POST',
headers:{
'X-CSRF-TOKEN':'{{ csrf_token() }}',
'Content-Type':'application/json'
},
body:JSON.stringify({
ticket_id:'{{ $ticket->id }}',
offer
})
})
}

Echo.channel('ticket.{{ $ticket->id }}')
.listen('WebRTCSignal',async e=>{

if(e.user_id == {{ auth()->id() }}) return

if(e.type=='offer'){
initPeer()
await pc.setRemoteDescription(e.data)

let answer=await pc.createAnswer()
await pc.setLocalDescription(answer)

fetch('/webrtc/answer',{
method:'POST',
headers:{
'X-CSRF-TOKEN':'{{ csrf_token() }}',
'Content-Type':'application/json'
},
body:JSON.stringify({
ticket_id:'{{ $ticket->id }}',
answer
})
})
}

if(e.type=='answer')
await pc.setRemoteDescription(e.data)

if(e.type=='ice')
await pc.addIceCandidate(e.data)

})

</script>

@endsection