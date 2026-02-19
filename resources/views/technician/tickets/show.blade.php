@extends('layouts.app')

@section('content')

<div class="container py-4">

{{-- ========== HEADER ========== --}}
<div class="card p-4 mb-4">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>
<h5 class="mb-1">{{ $ticket->subject }}</h5>
<p class="text-muted mb-0">
Cliente: {{ $ticket->user->name }}
</p>
</div>

<div class="d-flex gap-2">

<button onclick="startScreen()"
class="btn btn-sm btn-outline-secondary px-3">
📺 Compartir pantalla
</button>

<button onclick="startCall()"
class="btn btn-sm btn-outline-secondary px-3">
📞 Llamar
</button>

<button onclick="stopStream()"
id="stopBtn"
class="btn btn-sm btn-outline-danger px-3 d-none">
⛔ Detener
</button>

</div>

</div>
</div>

{{-- ========== VIDEOS (OCULTOS) ========== --}}
<div id="videoZone" class="row g-3 mb-4 d-none">

<div class="col-md-6">
<div class="card p-2">
<video id="local" autoplay muted
class="w-100 rounded border bg-dark"
style="min-height:220px"></video>
</div>
</div>

<div class="col-md-6">
<div class="card p-2">
<video id="remote" autoplay
class="w-100 rounded border bg-dark"
style="min-height:220px"></video>
</div>
</div>

</div>

{{-- ========== CHAT ========== --}}
<div class="mb-4">
<x-chat
:ticket="$ticket"
action="/technician/tickets/{{ $ticket->id }}/reply" />
</div>

{{-- ========== CHECKLIST ========== --}}
<div class="card p-4">

<h6 class="mb-3">🧰 Checklist técnico</h6>

<div class="form-check mb-2">
<input class="form-check-input chk" type="checkbox" data-k="diag">
<label class="form-check-label">Diagnóstico realizado</label>
</div>

<div class="form-check mb-2">
<input class="form-check-input chk" type="checkbox" data-k="rep">
<label class="form-check-label">Reparación aplicada</label>
</div>

<div class="form-check">
<input class="form-check-input chk" type="checkbox" data-k="test">
<label class="form-check-label">Pruebas finales</label>
</div>

</div>

</div>

{{-- ========== WEBRTC ========== --}}
<script>

let pc, stream
const zone = document.getElementById('videoZone')
const stopBtn = document.getElementById('stopBtn')

const config = {
iceServers:[{urls:'stun:stun.l.google.com:19302'}]
}

// INICIAR PANTALLA
async function startScreen(){
zone.classList.remove('d-none')
stopBtn.classList.remove('d-none')

stream = await navigator.mediaDevices
.getDisplayMedia({video:true,audio:true})

local.srcObject = stream
initPeer()

stream.getTracks().forEach(t=>{
pc.addTrack(t,stream)
})

createOffer()
}

// INICIAR LLAMADA
async function startCall(){
zone.classList.remove('d-none')
stopBtn.classList.remove('d-none')

stream = await navigator.mediaDevices
.getUserMedia({audio:true,video:true})

local.srcObject = stream
initPeer()

stream.getTracks().forEach(t=>{
pc.addTrack(t,stream)
})

createOffer()
}

// DETENER
function stopStream(){
stream.getTracks().forEach(t=>t.stop())
zone.classList.add('d-none')
stopBtn.classList.add('d-none')
}

// PEER
function initPeer(){
pc = new RTCPeerConnection(config)

pc.ontrack = e=>{
remote.srcObject = e.streams[0]
}

pc.onicecandidate = e=>{
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

// OFERTA
async function createOffer(){
let offer = await pc.createOffer()
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

// CHECKLIST LOCAL
const key='tech_check_{{ $ticket->id }}'
const saved=JSON.parse(localStorage.getItem(key)||'{}')

document.querySelectorAll('.chk').forEach(c=>{
c.checked = saved[c.dataset.k] || false

c.addEventListener('change',()=>{
saved[c.dataset.k]=c.checked
localStorage.setItem(key,JSON.stringify(saved))
})
})

</script>

@endsection