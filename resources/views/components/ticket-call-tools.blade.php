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
        Deskcir Live funciona como asistencia remota en navegador: compartir pantalla, ver evidencia en vivo y coordinar pasos tecnicos entre cliente, tecnico y admin.
    </p>

    <button type="button" class="btn btn-outline-deskcir btn-sm ticket-call-tools__resume" data-call-action="resume" hidden>
        <span class="material-symbols-outlined">open_in_full</span>
        Volver a la sesion activa
    </button>

    <section class="ticket-call-console" data-call-console hidden>
        <div class="ticket-call-console__topbar">
            <div>
                <p class="ticket-call-console__eyebrow mb-1">Deskcir Live</p>
                <h5 class="mb-1">Centro remoto con {{ $peerLabel }}</h5>
                <p class="ticket-call-console__microcopy mb-0">Vista persistente dentro del ticket para soporte visual y acompanamiento tecnico.</p>
            </div>

            <div class="ticket-call-console__topbar-actions">
                <span class="ticket-call-console__mode" data-call-mode>Listo</span>
                <button type="button" class="btn btn-outline-deskcir btn-sm d-inline-flex align-items-center gap-2" data-call-action="hide">
                    <span class="material-symbols-outlined">visibility_off</span>
                    Ocultar panel
                </button>
                <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2" data-call-action="stop">
                    <span class="material-symbols-outlined">call_end</span>
                    Finalizar
                </button>
            </div>
        </div>

        <div class="ticket-call-console__status" data-call-status>
            Listo para iniciar videollamada o compartir pantalla.
        </div>

        <div class="ticket-call-console__grid">
            <div class="ticket-call-console__main">
                <div class="ticket-call-console__video-shell is-remote">
                    <div class="ticket-call-console__video-label">{{ $peerLabel }}</div>
                    <video autoplay playsinline data-call-remote></video>
                </div>

                <div class="ticket-call-console__video-shell is-local">
                    <div class="ticket-call-console__video-label">Tu camara o pantalla</div>
                    <video autoplay muted playsinline data-call-local></video>
                </div>
            </div>

            <aside class="ticket-call-console__side">
                <div class="ticket-call-console__card">
                    <h6 class="fw-bold mb-2">Flujo recomendado</h6>
                    <ol class="mb-0">
                        <li>Confirma que ambos esten en linea.</li>
                        <li>Inicia videollamada o comparte pantalla.</li>
                        <li>Guia al usuario paso a paso desde el ticket.</li>
                    </ol>
                </div>

                <div class="ticket-call-console__card">
                    <h6 class="fw-bold mb-2">Cobertura actual</h6>
                    <ul class="mb-0">
                        <li>Videollamada en vivo.</li>
                        <li>Compartir pantalla del usuario.</li>
                        <li>Soporte guiado sobre la sesion.</li>
                    </ul>
                </div>

                <div class="ticket-call-console__card is-accent">
                    <h6 class="fw-bold mb-2">Nota tecnica</h6>
                    <p class="mb-0 small">El control total de mouse y teclado requiere un agente nativo instalado en la maquina. Aqui dejamos una experiencia remota web estable y lista para asistencia visual inmediata.</p>
                </div>
            </aside>
        </div>
    </section>
</div>

<script>
(function () {
    const wrapper = document.getElementById(@json($uid));
    if (!wrapper) return;

    const ticketId = wrapper.dataset.ticketId;
    const peerId = wrapper.dataset.peerId;
    const storageKey = `deskcir-live-console-${ticketId}-{{ auth()->id() }}`;
    const consolePanel = wrapper.querySelector('[data-call-console]');
    const localVideo = wrapper.querySelector('[data-call-local]');
    const remoteVideo = wrapper.querySelector('[data-call-remote]');
    const statusBox = wrapper.querySelector('[data-call-status]');
    const peerState = wrapper.querySelector('[data-peer-state]');
    const modePill = wrapper.querySelector('[data-call-mode]');
    const btnScreen = wrapper.querySelector('[data-call-action="screen"]');
    const btnCall = wrapper.querySelector('[data-call-action="call"]');
    const btnStop = wrapper.querySelector('[data-call-action="stop"]');
    const btnHide = wrapper.querySelector('[data-call-action="hide"]');
    const btnResume = wrapper.querySelector('[data-call-action="resume"]');

    let pc = null;
    let localStream = null;
    let lastSignalId = 0;
    let queuedIce = [];
    let pingTimer = null;
    let pollTimer = null;
    let currentMode = 'idle';

    const setStatus = (text) => {
        if (statusBox) {
            statusBox.textContent = text;
        }
    };

    const setMode = (mode, text) => {
        currentMode = mode;
        if (!modePill) return;
        modePill.textContent = text;
        modePill.classList.toggle('is-active', mode !== 'idle');
    };

    const openConsole = (persist = true) => {
        if (!consolePanel) return;
        consolePanel.hidden = false;
        if (btnResume) btnResume.hidden = true;
        wrapper.classList.add('is-live-open');
        if (persist) {
            sessionStorage.setItem(storageKey, '1');
        }
    };

    const hideConsole = () => {
        if (!consolePanel) return;
        consolePanel.hidden = true;
        if (btnResume && currentMode !== 'idle') btnResume.hidden = false;
        wrapper.classList.remove('is-live-open');
        sessionStorage.removeItem(storageKey);
    };

    if (sessionStorage.getItem(storageKey) === '1') {
        openConsole(false);
    }

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
            setStatus('Conexion activa. Ya puedes continuar la asistencia remota.');
            setMode(currentMode, currentMode === 'screen' ? 'Pantalla activa' : 'Llamada activa');
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

        pc.onconnectionstatechange = () => {
            if (!pc) return;
            if (['disconnected', 'failed', 'closed'].includes(pc.connectionState)) {
                setStatus('La sesion se desconecto. Puedes volver a iniciarla desde este panel.');
                setMode('idle', 'Desconectado');
            }
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

        openConsole();
        setMode(mode, mode === 'screen' ? 'Preparando pantalla' : 'Preparando llamada');
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

            setStatus(mode === 'screen'
                ? 'Pantalla compartida. Esperando respuesta del otro usuario...'
                : 'Solicitud enviada. Esperando respuesta del otro usuario...');
        } catch (error) {
            setStatus('No fue posible iniciar la sesion. Revisa permisos de camara o pantalla.');
            setMode('idle', 'Error');
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
        setStatus('Sesion finalizada. El panel queda listo para una nueva conexion.');
        setMode('idle', 'Listo');
        hideConsole();
        if (btnResume) btnResume.hidden = true;
    }

    async function handleSignal(signal) {
        lastSignalId = Math.max(lastSignalId, Number(signal.id || 0));

        if (signal.type === 'offer') {
            openConsole();
            setMode(signal.request_mode || 'call', signal.request_mode === 'screen' ? 'Pantalla entrante' : 'Llamada entrante');
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
    btnHide?.addEventListener('click', hideConsole);
    btnResume?.addEventListener('click', () => openConsole());

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
    gap: .85rem;
    min-width: min(100%, 34rem);
}

.ticket-call-tools__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.ticket-call-tools__presence,
.ticket-call-tools__actions {
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
    padding: .45rem .8rem;
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

.ticket-call-tools__hint {
    color: #61778b;
    font-size: .84rem;
}

.ticket-call-tools__resume {
    width: fit-content;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
}

.ticket-call-console {
    border: 1px solid rgba(80, 187, 212, 0.24);
    border-radius: 22px;
    overflow: hidden;
    background: linear-gradient(145deg, #071827 0%, #0c2334 55%, #103046 100%);
    color: #ecf8ff;
    box-shadow: 0 24px 54px rgba(2, 8, 23, 0.24);
}

.ticket-call-console__topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.1rem;
    border-bottom: 1px solid rgba(177, 223, 236, 0.14);
}

.ticket-call-console__topbar-actions {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.ticket-call-console__eyebrow {
    font-size: .76rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-weight: 800;
    color: #8ed7e8;
}

.ticket-call-console__microcopy {
    color: #b7d3df;
    font-size: .84rem;
}

.ticket-call-console__mode {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: .45rem .75rem;
    border-radius: 999px;
    background: rgba(143, 209, 226, 0.12);
    border: 1px solid rgba(143, 209, 226, 0.2);
    color: #cfeef7;
    font-size: .8rem;
    font-weight: 700;
}

.ticket-call-console__mode.is-active {
    background: rgba(19, 214, 164, 0.14);
    border-color: rgba(19, 214, 164, 0.26);
    color: #a8ffde;
}

.ticket-call-console__status {
    padding: .9rem 1.1rem 0;
    color: #b9d8e4;
}

.ticket-call-console__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(280px, .9fr);
    gap: 1rem;
    padding: 1rem 1.1rem 1.1rem;
}

.ticket-call-console__main {
    display: grid;
    gap: .9rem;
}

.ticket-call-console__video-shell {
    border: 1px solid rgba(177, 223, 236, 0.16);
    border-radius: 18px;
    padding: .8rem;
    background: rgba(7, 24, 39, 0.48);
}

.ticket-call-console__video-shell.is-remote video {
    min-height: 360px;
}

.ticket-call-console__video-shell.is-local video {
    min-height: 180px;
}

.ticket-call-console__video-label {
    margin-bottom: .45rem;
    color: #8fd1e2;
    font-size: .82rem;
    font-weight: 700;
}

.ticket-call-console__video-shell video {
    width: 100%;
    background: #030712;
    border-radius: 14px;
    object-fit: cover;
}

.ticket-call-console__side {
    display: grid;
    gap: .85rem;
}

.ticket-call-console__card {
    border: 1px solid rgba(177, 223, 236, 0.16);
    border-radius: 18px;
    padding: .95rem 1rem;
    background: rgba(7, 24, 39, 0.44);
    color: #dbedf4;
}

.ticket-call-console__card.is-accent {
    background: rgba(1, 104, 122, 0.22);
    border-color: rgba(118, 221, 240, 0.26);
}

.ticket-call-console__card ol,
.ticket-call-console__card ul {
    padding-left: 1rem;
    display: grid;
    gap: .45rem;
}

.dark .ticket-call-tools__presence-pill {
    background: #102235;
    border-color: #254159;
    color: #d8f2fb;
}

.dark .ticket-call-tools__hint {
    color: #a6bfd2;
}

@media (max-width: 991.98px) {
    .ticket-call-console__grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    .ticket-call-tools,
    .ticket-call-console {
        min-width: 100%;
    }

    .ticket-call-console__topbar,
    .ticket-call-console__topbar-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .ticket-call-console__video-shell.is-remote video {
        min-height: 240px;
    }

    .ticket-call-console__video-shell.is-local video {
        min-height: 150px;
    }
}
</style>