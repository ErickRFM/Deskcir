@props([
    'ticket',
    'screenLabel' => 'Compartir pantalla',
    'callLabel' => 'Iniciar videollamada',
    'peerUserId' => null,
    'peerLabel' => 'Usuario',
])

@php $uid = 'ticket-call-' . $ticket->id . '-' . substr(md5((string) auth()->id()), 0, 6); @endphp

<div class="ticket-call-tools" id="{{ $uid }}" data-ticket-id="{{ $ticket->id }}" data-peer-id="{{ $peerUserId }}">
    <div class="ticket-call-tools__header">
        <div class="ticket-call-tools__presence">
            <span class="ticket-call-tools__presence-pill is-self">
                <span class="material-symbols-outlined">wifi</span>
                Tu sesion activa
            </span>
            <span class="ticket-call-tools__presence-pill" data-peer-state>
                <span class="ticket-call-tools__dot"></span>
                {{ $peerLabel }}: verificando...
            </span>
        </div>

        <div class="ticket-call-tools__actions">
            <button type="button" class="btn btn-outline-deskcir btn-sm d-inline-flex align-items-center gap-2" data-call-action="screen" {{ $peerUserId ? '' : 'disabled' }}>
                <span class="material-symbols-outlined">present_to_all</span>
                {{ $screenLabel }}
            </button>
            <button type="button" class="btn btn-deskcir btn-sm d-inline-flex align-items-center gap-2" data-call-action="call" {{ $peerUserId ? '' : 'disabled' }}>
                <span class="material-symbols-outlined">video_call</span>
                {{ $callLabel }}
            </button>
        </div>
    </div>

    <p class="ticket-call-tools__hint mb-0">
        Usa ventana o pantalla completa para compartir mejor. Si el otro usuario no esta en linea, la solicitud quedara esperando hasta que abra el ticket.
    </p>

    <div class="ticket-call-modal" data-call-modal hidden>
        <div class="ticket-call-modal__backdrop" data-close-modal></div>
        <div class="ticket-call-modal__dialog">
            <div class="ticket-call-modal__topbar">
                <div>
                    <p class="ticket-call-modal__eyebrow mb-1">Deskcir Live</p>
                    <h5 class="mb-0">Sesion con {{ $peerLabel }}</h5>
                </div>
                <button type="button" class="ticket-call-modal__close" data-close-modal>
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="ticket-call-modal__status" data-call-status>
                Listo para iniciar videollamada o compartir pantalla.
            </div>

            <div class="ticket-call-modal__videos">
                <div class="ticket-call-modal__video-card">
                    <div class="ticket-call-modal__video-label">Tu camara o pantalla</div>
                    <video autoplay muted playsinline data-call-local></video>
                </div>
                <div class="ticket-call-modal__video-card">
                    <div class="ticket-call-modal__video-label">{{ $peerLabel }}</div>
                    <video autoplay playsinline data-call-remote></video>
                </div>
            </div>

            <div class="ticket-call-modal__footer">
                <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2" data-close-modal>
                    <span class="material-symbols-outlined">visibility_off</span>
                    Minimizar
                </button>
                <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2" data-call-action="stop">
                    <span class="material-symbols-outlined">call_end</span>
                    Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const wrapper = document.getElementById(@json($uid));
    if (!wrapper) return;

    const ticketId = wrapper.dataset.ticketId;
    const peerId = wrapper.dataset.peerId;
    const modal = wrapper.querySelector('[data-call-modal]');
    const localVideo = wrapper.querySelector('[data-call-local]');
    const remoteVideo = wrapper.querySelector('[data-call-remote]');
    const statusBox = wrapper.querySelector('[data-call-status]');
    const peerState = wrapper.querySelector('[data-peer-state]');
    const btnScreen = wrapper.querySelector('[data-call-action="screen"]');
    const btnCall = wrapper.querySelector('[data-call-action="call"]');
    const btnStop = wrapper.querySelector('[data-call-action="stop"]');
    const closeButtons = wrapper.querySelectorAll('[data-close-modal]');

    let pc = null;
    let localStream = null;
    let lastSignalId = 0;
    let queuedIce = [];
    let pingTimer = null;
    let pollTimer = null;

    const setStatus = (text) => {
        if (statusBox) {
            statusBox.textContent = text;
        }
    };

    const openModal = () => {
        if (!modal) return;
        modal.hidden = false;
        document.body.classList.add('ticket-call-open');
    };

    const closeModal = () => {
        if (!modal) return;
        modal.hidden = true;
        document.body.classList.remove('ticket-call-open');
    };

    const config = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            {
                urls: 'turn:openrelay.metered.ca:80',
                username: 'openrelayproject',
                credential: 'openrelayproject'
            }
        ]
    };

    async function pingPresence() {
        try {
            await fetch('/presence/ping', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ at: Date.now() })
            });
        } catch (error) {
            console.warn('No se pudo actualizar presencia.', error);
        }
    }

    async function checkPeerPresence() {
        if (!peerId || !peerState) return;

        try {
            const response = await fetch(`/presence/user/${peerId}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const payload = await response.json();
            const online = !!payload.online;
            peerState.classList.toggle('is-online', online);
            peerState.classList.toggle('is-offline', !online);
            peerState.innerHTML = `<span class="ticket-call-tools__dot"></span>{{ $peerLabel }}: ${online ? 'en linea' : 'desconectado'}`;
        } catch (error) {
            console.warn('No se pudo consultar presencia.', error);
        }
    }

    function ensurePeer() {
        if (pc) return pc;

        pc = new RTCPeerConnection(config);

        pc.ontrack = (event) => {
            remoteVideo.srcObject = event.streams[0];
            setStatus('Conexion activa. Ya puedes continuar la llamada.');
        };

        pc.onicecandidate = async (event) => {
            if (!event.candidate) return;

            await fetch('/webrtc/ice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    candidate: event.candidate,
                })
            });
        };

        return pc;
    }

    async function flushQueuedIce() {
        if (!pc || !pc.remoteDescription) return;
        while (queuedIce.length) {
            const candidate = queuedIce.shift();
            await pc.addIceCandidate(new RTCIceCandidate(candidate));
        }
    }

    async function start(mode) {
        if (!peerId) return;

        openModal();
        setStatus(mode === 'screen' ? 'Preparando pantalla compartida...' : 'Preparando videollamada...');

        try {
            localStream = mode === 'screen'
                ? await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true })
                : await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

            localVideo.srcObject = localStream;
            const peer = ensurePeer();

            localStream.getTracks().forEach((track) => {
                peer.addTrack(track, localStream);
            });

            const offer = await peer.createOffer();
            await peer.setLocalDescription(offer);

            await fetch('/webrtc/offer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    offer,
                    request_mode: mode,
                })
            });

            setStatus(mode === 'screen' ? 'Pantalla compartida. Esperando respuesta del otro usuario...' : 'Solicitud enviada. Esperando respuesta del otro usuario...');
        } catch (error) {
            setStatus('No fue posible iniciar la sesion. Revisa permisos de camara o pantalla.');
        }
    }

    async function stopCall() {
        if (localStream) {
            localStream.getTracks().forEach((track) => track.stop());
        }

        if (pc) {
            pc.close();
        }

        pc = null;
        localStream = null;
        queuedIce = [];
        localVideo.srcObject = null;
        remoteVideo.srcObject = null;
        setStatus('Sesion finalizada.');
        closeModal();
    }

    async function handleSignal(signal) {
        lastSignalId = Math.max(lastSignalId, Number(signal.id || 0));

        if (signal.type === 'offer') {
            openModal();
            setStatus(signal.request_mode === 'screen' ? 'Recibiendo pantalla compartida...' : 'Recibiendo videollamada...');

            const peer = ensurePeer();
            await peer.setRemoteDescription(signal.data);

            if (!localStream) {
                localStream = signal.request_mode === 'screen'
                    ? await navigator.mediaDevices.getUserMedia({ audio: true })
                    : await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

                localVideo.srcObject = localStream;
                localStream.getTracks().forEach((track) => peer.addTrack(track, localStream));
            }

            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);

            await fetch('/webrtc/answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    answer,
                })
            });

            await flushQueuedIce();
            return;
        }

        if (signal.type === 'answer' && pc) {
            await pc.setRemoteDescription(signal.data);
            await flushQueuedIce();
            return;
        }

        if (signal.type === 'ice') {
            if (!pc || !pc.remoteDescription) {
                queuedIce.push(signal.data);
                return;
            }

            await pc.addIceCandidate(new RTCIceCandidate(signal.data));
        }
    }

    async function pollSignals() {
        try {
            const response = await fetch(`/webrtc/poll?ticket_id=${ticketId}&after_id=${lastSignalId}`, {
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) return;
            const payload = await response.json();
            const signals = Array.isArray(payload.signals) ? payload.signals : [];
            for (const signal of signals) {
                await handleSignal(signal);
            }
        } catch (error) {
            console.warn('No se pudo actualizar la sesion WebRTC.', error);
        }
    }

    btnScreen?.addEventListener('click', () => start('screen'));
    btnCall?.addEventListener('click', () => start('call'));
    btnStop?.addEventListener('click', stopCall);
    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    pingPresence();
    checkPeerPresence();
    pingTimer = setInterval(pingPresence, 15000);
    pollTimer = setInterval(() => {
        checkPeerPresence();
        pollSignals();
    }, 2000);

    window.addEventListener('beforeunload', () => {
        clearInterval(pingTimer);
        clearInterval(pollTimer);
    });
})();
</script>

<style>
.ticket-call-tools {
    display: grid;
    gap: .65rem;
    min-width: min(100%, 32rem);
}

.ticket-call-tools__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.ticket-call-tools__presence {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
}

.ticket-call-tools__presence-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 999px;
    padding: .42rem .78rem;
    background: #eff7fb;
    border: 1px solid #d9e8ef;
    color: #1c3a4f;
    font-size: .82rem;
    font-weight: 700;
}

.ticket-call-tools__presence-pill.is-self {
    background: #e8fbf5;
    color: #0f766e;
    border-color: #b8ead9;
}

.ticket-call-tools__presence-pill.is-online {
    background: #ecfdf3;
    color: #15803d;
    border-color: #b7e8c7;
}

.ticket-call-tools__presence-pill.is-offline {
    background: #fff4f4;
    color: #b42318;
    border-color: #f6c2c2;
}

.ticket-call-tools__dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: currentColor;
}

.ticket-call-tools__actions {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
}

.ticket-call-tools__hint {
    color: #61778b;
    font-size: .82rem;
}

.ticket-call-modal {
    position: fixed;
    inset: 0;
    z-index: 1055;
}

.ticket-call-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(2, 8, 23, 0.7);
    backdrop-filter: blur(5px);
}

.ticket-call-modal__dialog {
    position: relative;
    width: min(94vw, 980px);
    margin: 4vh auto;
    border-radius: 22px;
    overflow: hidden;
    background: linear-gradient(145deg, #071827 0%, #0c2334 55%, #103046 100%);
    border: 1px solid rgba(103, 207, 230, 0.3);
    box-shadow: 0 28px 60px rgba(2, 8, 23, 0.55);
    color: #ecf8ff;
}

.ticket-call-modal__topbar,
.ticket-call-modal__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.2rem;
}

.ticket-call-modal__eyebrow {
    font-size: .76rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-weight: 800;
    color: #8ed7e8;
}

.ticket-call-modal__close {
    width: 42px;
    height: 42px;
    border: 1px solid rgba(177, 223, 236, 0.24);
    border-radius: 999px;
    background: rgba(7, 24, 39, 0.6);
    color: #ecf8ff;
}

.ticket-call-modal__status {
    padding: 0 1.2rem 1rem;
    color: #b9d8e4;
}

.ticket-call-modal__videos {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    padding: 0 1.2rem 1.2rem;
}

.ticket-call-modal__video-card {
    border: 1px solid rgba(177, 223, 236, 0.16);
    border-radius: 18px;
    padding: .9rem;
    background: rgba(7, 24, 39, 0.5);
}

.ticket-call-modal__video-label {
    margin-bottom: .5rem;
    color: #8fd1e2;
    font-size: .82rem;
    font-weight: 700;
}

.ticket-call-modal__video-card video {
    width: 100%;
    min-height: 260px;
    background: #030712;
    border-radius: 14px;
    object-fit: cover;
}

@media (max-width: 767.98px) {
    .ticket-call-modal__videos {
        grid-template-columns: 1fr;
    }

    .ticket-call-tools {
        min-width: 100%;
    }
}
</style>
