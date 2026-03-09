@props([
'ticket',
'screenLabel'=>'Compartir pantalla',
'callLabel'=>'Iniciar videollamada',
])

@php $uid=$ticket->id; @endphp

<div class="ticket-call-tools" data-ticket-call="{{ $uid }}">

<div class="d-flex gap-2 mb-2">
<button class="btn btn-outline-secondary btn-sm" data-call-action="screen">{{ $screenLabel }}</button>
<button class="btn btn-outline-secondary btn-sm" data-call-action="call">{{ $callLabel }}</button>
<button class="btn btn-outline-danger btn-sm d-none" data-call-action="stop">Detener</button>

<input type="file" class="form-control form-control-sm" data-send-file style="max-width:200px">
</div>

<section class="row g-3 mt-2 d-none" data-call-zone>

<div class="col-md-6">
<video autoplay muted playsinline class="w-100 rounded" data-call-local></video>
</div>

<div class="col-md-6">
<video autoplay playsinline class="w-100 rounded border" data-call-remote></video>
</div>

</section>

</div>

<script>
(function(){

const wrapper=document.querySelector('[data-ticket-call="{{ $uid }}"]')
if(!wrapper) return

const btnScreen=wrapper.querySelector('[data-call-action="screen"]')
const btnCall=wrapper.querySelector('[data-call-action="call"]')
const btnStop=wrapper.querySelector('[data-call-action="stop"]')
const sendFileInput=wrapper.querySelector('[data-send-file]')

const localVideo=wrapper.querySelector('[data-call-local]')
const remoteVideo=wrapper.querySelector('[data-call-remote]')
const zone=wrapper.querySelector('[data-call-zone]')

let pc=null
let stream=null
let dataChannel=null
let lastSignalId=0
let pendingIce=[]

const config={
iceServers:[
{urls:"stun:stun.l.google.com:19302"},
{
urls:"turn:openrelay.metered.ca:80",
username:"openrelayproject",
credential:"openrelayproject"
}
]
}

function ensurePeer(){

if(pc) return pc

pc=new RTCPeerConnection(config)

dataChannel=pc.createDataChannel("control")

setupDataChannel()

pc.ondatachannel=e=>{
dataChannel=e.channel
setupDataChannel()
}

pc.ontrack=e=>{
remoteVideo.srcObject=e.streams[0]
}

pc.onicecandidate=e=>{
if(!e.candidate) return

fetch("/webrtc/ice",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},
body:JSON.stringify({
ticket_id:"{{ $ticket->id }}",
candidate:e.candidate
})
})

}

return pc
}

function setupDataChannel(){

if(!dataChannel) return

dataChannel.onmessage=e=>{
const msg=JSON.parse(e.data)

if(msg.type==="mouse_move"){
console.log("mouse move",msg)
}

if(msg.type==="mouse_click"){
console.log("mouse click")
}

if(msg.type==="key_down"){
console.log("key",msg.key)
}

if(msg.type==="file_meta"){
receiveFile(msg)
}

if(msg.type==="file_chunk"){
receiveChunk(msg)
}

}

}

async function start(mode){

zone.classList.remove("d-none")
btnStop.classList.remove("d-none")

try{

if(mode==="screen"){
stream=await navigator.mediaDevices.getDisplayMedia({
video:{frameRate:{ideal:60,max:60}}
})
}else{
stream=await navigator.mediaDevices.getUserMedia({video:true,audio:true})
}

localVideo.srcObject=stream

const peer=ensurePeer()

stream.getTracks().forEach(t=>peer.addTrack(t,stream))

const offer=await peer.createOffer()
await peer.setLocalDescription(offer)

await fetch("/webrtc/offer",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},
body:JSON.stringify({
ticket_id:"{{ $ticket->id }}",
offer,
request_mode:mode
})
})

}catch(e){
console.error(e)
}

}

async function handleSignal(s){

if(s.id>lastSignalId) lastSignalId=s.id

if(s.type==="offer"){

const peer=ensurePeer()

await peer.setRemoteDescription(s.data)

peer.addTransceiver("video",{direction:"recvonly"})

const answer=await peer.createAnswer()
await peer.setLocalDescription(answer)

await fetch("/webrtc/answer",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},
body:JSON.stringify({
ticket_id:"{{ $ticket->id }}",
answer
})
})

}

if(s.type==="answer"){
await pc.setRemoteDescription(s.data)
}

if(s.type==="ice"){

if(!pc||!pc.remoteDescription){
pendingIce.push(s.data)
return
}

await pc.addIceCandidate(new RTCIceCandidate(s.data))

}

}

async function poll(){

const res=await fetch(`/webrtc/poll?ticket_id={{ $ticket->id }}&after_id=${lastSignalId}`)
if(!res.ok) return

const data=await res.json()

for(const s of data.signals){
await handleSignal(s)
}

}

btnScreen.onclick=()=>start("screen")
btnCall.onclick=()=>start("call")

btnStop.onclick=()=>{
if(stream) stream.getTracks().forEach(t=>t.stop())
if(pc) pc.close()
pc=null
stream=null
}

setInterval(poll,1500)

/* -------- CONTROL REMOTO -------- */

remoteVideo.addEventListener("mousemove",e=>{

if(!dataChannel) return

dataChannel.send(JSON.stringify({
type:"mouse_move",
x:e.offsetX,
y:e.offsetY
}))

})

remoteVideo.addEventListener("click",()=>{

dataChannel.send(JSON.stringify({
type:"mouse_click"
}))

})

window.addEventListener("keydown",e=>{

if(!dataChannel) return

dataChannel.send(JSON.stringify({
type:"key_down",
key:e.key
}))

})

/* -------- FILE TRANSFER -------- */

sendFileInput.addEventListener("change",()=>{

const file=sendFileInput.files[0]

if(!file||!dataChannel) return

dataChannel.send(JSON.stringify({
type:"file_meta",
name:file.name,
size:file.size
}))

const chunkSize=16000
let offset=0

const reader=new FileReader()

reader.onload=e=>{
dataChannel.send(JSON.stringify({
type:"file_chunk",
data:Array.from(new Uint8Array(e.target.result))
}))
offset+=chunkSize
readSlice()
}

function readSlice(){
const slice=file.slice(offset,offset+chunkSize)
reader.readAsArrayBuffer(slice)
}

readSlice()

})

})();
</script>