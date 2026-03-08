@props([
    'ticket',
    'screenLabel' => 'Compartir pantalla',
    'callLabel' => 'Iniciar videollamada',
    'peerUserId' => null,
    'peerLabel' => 'Participante',
])

@php
    $uid = $ticket->id;
@endphp

<div class="ticket-call-tools" data-ticket-call="{{ $uid }}">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-call-action="screen">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-6v2h3v2H7v-2h3v-2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 2v9h16V6H4Zm7 1h2v3h3v2h-3v3h-2v-3H8v-2h3V7Z"/>
                </svg>
                {{ $screenLabel }}
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-call-action="call">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18.6 10.8c-1 0-2 .2-2.8.6-.3.1-.7.1-1-.1l-2.6-2.1a15.7 15.7 0 0 0 2.2-3.8c.1-.4 0-.8-.3-1.1L11.5 1.7a1 1 0 0 0-1.3-.1L7.4 3.7c-.3.2-.5.6-.4 1 1.1 7 6.6 12.6 13.7 13.7.4.1.8-.1 1-.4l2.1-2.8c.3-.4.2-1-.1-1.3l-2.6-2.6c-.3-.3-.7-.4-1.1-.3-.7.3-1.6.5-2.4.5Z"/>
                </svg>
                {{ $callLabel }}
            </button>

            <button type="button" class="btn btn-outline-danger btn-sm px-3 d-none" data-call-action="stop">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6h12v12H6z"/>
                </svg>
                Detener
            </button>
        </div>

        @if($peerUserId)
            <div class="ticket-peer-status" data-peer-status title="Estado del participante">
                <span class="ticket-peer-status__dot" data-peer-dot></span>
                <span data-peer-text>{{ $peerLabel }}: comprobando...</span>
            </div>
        @endif
    </div>

    <p class="ticket-call-hint mb-0 mt-2">
        Si compartes la misma pestana, veras efecto espejo. Es normal. Comparte ventana o pantalla completa para una vista mejor.
    </p>

    <div class="ticket-call-alert d-none" data-call-alert></div>

    <section class="row g-3 mt-3 d-none" data-call-zone>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-2">
                <video autoplay muted playsinline class="w-100 rounded ticket-video" data-call-local></video>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-2">
                <video autoplay playsinline class="w-100 rounded ticket-video" data-call-remote></video>
            </div>
        </div>
    </section>

    <div class="ticket-call-modal" data-call-modal aria-hidden="true">
        <div class="ticket-call-modal__backdrop" data-call-reject></div>
        <div class="ticket-call-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="callModalTitle-{{ $uid }}">
            <div class="ticket-call-modal__header">
                <div class="ticket-call-modal__icon" aria-hidden="true">PC</div>
                <div>
                    <h6 id="callModalTitle-{{ $uid }}" class="fw-bold mb-1" data-call-title>Solicitud entrante</h6>
                    <span class="ticket-call-modal__pill" data-call-kind>Pantalla</span>
                </div>
            </div>
            <p class="text-muted mb-3" data-call-description>Te quieren compartir pantalla.</p>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-light btn-sm" data-call-reject>Rechazar</button>
                <button type="button" class="btn btn-deskcir btn-sm" data-call-accept>Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const wrapper = document.querySelector('[data-ticket-call="{{ $uid }}"]');
    if (!wrapper) return;
    if (wrapper.dataset.callInitialized === '1') return;
    wrapper.dataset.callInitialized = '1';

    const zone = wrapper.querySelector('[data-call-zone]');
    const localVideo = wrapper.querySelector('[data-call-local]');
    const remoteVideo = wrapper.querySelector('[data-call-remote]');
    const btnScreen = wrapper.querySelector('[data-call-action="screen"]');
    const btnCall = wrapper.querySelector('[data-call-action="call"]');
    const btnStop = wrapper.querySelector('[data-call-action="stop"]');
    const modal = wrapper.querySelector('[data-call-modal]');
    const modalTitle = wrapper.querySelector('[data-call-title]');
    const modalDescription = wrapper.querySelector('[data-call-description]');
    const modalKind = wrapper.querySelector('[data-call-kind]');
    const modalIcon = wrapper.querySelector('.ticket-call-modal__icon');
    const modalAccept = wrapper.querySelector('[data-call-accept]');
    const modalRejectBtns = wrapper.querySelectorAll('[data-call-reject]');
    const alertBox = wrapper.querySelector('[data-call-alert]');
    const peerStatusEl = wrapper.querySelector('[data-peer-status]');
    const peerDotEl = wrapper.querySelector('[data-peer-dot]');
    const peerTextEl = wrapper.querySelector('[data-peer-text]');

    const peerUserId = {{ $peerUserId ? (int) $peerUserId : 'null' }};
    const peerLabel = @json($peerLabel);

    let pc = null;
    let stream = null;
    let pendingOffer = null;
    let pendingMode = 'call';
    let pendingOfferId = null;
    let lastSignalId = 0;
    const pendingIce = [];
    const pageStartedAt = Date.now();
    const processedSignalIds = new Set();
    const dismissedOfferIds = new Set();

    const config = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };

    function notify(message, level = 'info') {
        const classByLevel = {
            info: 'alert-primary',
            success: 'alert-success',
            warning: 'alert-warning',
            danger: 'alert-danger',
        };

        alertBox.className = `ticket-call-alert alert ${classByLevel[level] || 'alert-primary'}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');

        window.clearTimeout(alertBox._timer);
        alertBox._timer = window.setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 3600);
    }

    function setPeerStatus(online, checking = false) {
        if (!peerStatusEl || !peerDotEl || !peerTextEl) return;

        if (checking) {
            peerStatusEl.classList.remove('is-online', 'is-offline');
            peerTextEl.textContent = `${peerLabel}: comprobando...`;
            return;
        }

        peerStatusEl.classList.toggle('is-online', !!online);
        peerStatusEl.classList.toggle('is-offline', !online);
        peerTextEl.textContent = online ? `${peerLabel}: en linea` : `${peerLabel}: desconectado`;
    }

    async function pingPresence() {
        try {
            await fetch('/presence/ping', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (err) {
            console.warn('No se pudo enviar heartbeat de presencia.', err);
        }
    }

    async function checkPeerPresence() {
        if (!peerUserId) return;
        try {
            const res = await fetch(`/presence/user/${peerUserId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) return;
            const data = await res.json();
            setPeerStatus(!!data.online, false);
        } catch (err) {
            setPeerStatus(false, false);
        }
    }

    function showModal(type, title, description) {
        modalTitle.textContent = title;
        modalDescription.textContent = description;

        if (type === 'screen') {
            modalKind.textContent = 'Pantalla';
            modalIcon.textContent = 'PC';
        } else {
            modalKind.textContent = 'Videollamada';
            modalIcon.textContent = 'CALL';
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function hideModal(reset = true) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (reset) {
            pendingOffer = null;
            pendingMode = 'call';
            pendingOfferId = null;
        }
    }

    function isFreshOffer(signal) {
        if (!signal?.created_at) return true;
        const created = new Date(signal.created_at).getTime();
        if (Number.isNaN(created)) return true;

        const ageMs = Date.now() - created;
        return ageMs <= 30000 && created >= (pageStartedAt - 5000);
    }

    function ensurePeer() {
        if (pc) return pc;

        pc = new RTCPeerConnection(config);

        pc.ontrack = (e) => {
            remoteVideo.srcObject = e.streams[0];
            remoteVideo.play?.().catch(() => {});
        };

        pc.onconnectionstatechange = () => {
            if (!pc) return;
            if (pc.connectionState === 'connected') {
                notify('Conexion establecida.', 'success');
            }
            if (pc.connectionState === 'failed' || pc.connectionState === 'disconnected') {
                notify('La conexion se interrumpio.', 'warning');
            }
        };

        pc.onicecandidate = (e) => {
            if (!e.candidate) return;

            fetch('/webrtc/ice', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: '{{ $ticket->id }}',
                    candidate: e.candidate
                })
            });
        };

        return pc;
    }

    async function getStreamByMode(mode, incoming = false) {
        if (incoming && mode === 'screen') {
            return null;
        }

        if (mode === 'screen') {
            const canShareScreen = !!(navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia);
            if (!canShareScreen) {
                throw new Error('screen-share-not-supported');
            }

            return await navigator.mediaDevices.getDisplayMedia({
                video: { frameRate: { ideal: 24, max: 30 } },
                audio: false
            });
        }

        return await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: { width: { ideal: 1280 }, height: { ideal: 720 }, frameRate: { ideal: 24, max: 30 } }
        });
    }

    async function createOffer(mode) {
        const offer = await ensurePeer().createOffer();
        await pc.setLocalDescription(offer);

        await fetch('/webrtc/offer', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ticket_id: '{{ $ticket->id }}',
                request_mode: mode,
                offer
            })
        });
    }

    async function handleSignal(e) {
        if (!e || e.user_id == {{ auth()->id() }}) return;

        const signalId = Number(e.id || 0);
        if (signalId) {
            if (signalId > lastSignalId) {
                lastSignalId = signalId;
            }

            if (processedSignalIds.has(signalId)) {
                return;
            }
            processedSignalIds.add(signalId);

            if (processedSignalIds.size > 300) {
                const firstId = processedSignalIds.values().next().value;
                if (firstId) {
                    processedSignalIds.delete(firstId);
                }
            }
        }

        if (e.type === 'offer') {
            if (!isFreshOffer(e)) {
                return;
            }
            if (signalId && dismissedOfferIds.has(signalId)) {
                return;
            }
            if (signalId && pendingOfferId === signalId && modal.classList.contains('is-open')) {
                return;
            }

            pendingOffer = e.data;
            pendingMode = e.request_mode || 'call';
            pendingOfferId = signalId || null;

            if (pendingMode === 'screen') {
                showModal('screen', 'Solicitud de pantalla', 'Te quieren compartir pantalla. Aceptar ahora?');
            } else {
                showModal('call', 'Videollamada entrante', 'Tienes una videollamada entrante. Aceptar ahora?');
            }

            notify('Nueva solicitud recibida.', 'info');
            return;
        }

        if (e.type === 'answer') {
            if (!pc) return;
            await pc.setRemoteDescription(e.data);
            notify('La otra persona acepto la conexion.', 'success');
            return;
        }

        if (e.type === 'ice') {
            if (!pc || !pc.remoteDescription) {
                pendingIce.push(e.data);
                return;
            }

            await pc.addIceCandidate(e.data);
        }
    }

    async function pullSignals() {
        try {
            const query = new URLSearchParams({
                ticket_id: '{{ $ticket->id }}',
                after_id: String(lastSignalId || 0)
            });

            const res = await fetch(`/webrtc/poll?${query.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) return;
            const payload = await res.json();
            if (!payload?.signals?.length) return;

            for (const signal of payload.signals) {
                await handleSignal(signal);
            }
        } catch (err) {
            console.warn('Poll de senal WebRTC no disponible.', err);
        }
    }

    async function startSession(mode) {
        closeConnection();
        hideModal();
        zone.classList.remove('d-none');
        btnStop.classList.remove('d-none');

        try {
            stream = await getStreamByMode(mode, false);
            localVideo.srcObject = stream;
            localVideo.play?.().catch(() => {});

            const peer = ensurePeer();
            stream.getTracks().forEach((track) => peer.addTrack(track, stream));

            await createOffer(mode);
            notify(mode === 'screen' ? 'Solicitud de pantalla enviada.' : 'Solicitud de videollamada enviada.', 'success');
        } catch (err) {
            if (mode === 'screen' && err?.message === 'screen-share-not-supported') {
                notify('Este dispositivo o navegador no permite compartir pantalla aqui.', 'warning');
            } else {
                notify('No se pudo iniciar la sesion. Revisa permisos de camara/pantalla.', 'danger');
            }
            closeConnection();
        }
    }

    function closeConnection() {
        if (stream) {
            stream.getTracks().forEach((t) => t.stop());
            stream = null;
        }

        if (pc) {
            pc.close();
            pc = null;
        }

        pendingIce.length = 0;
        localVideo.srcObject = null;
        remoteVideo.srcObject = null;
        zone.classList.add('d-none');
        btnStop.classList.add('d-none');
    }

    async function acceptIncoming() {
        if (!pendingOffer) {
            hideModal();
            return;
        }

        const offerData = pendingOffer;
        const offerMode = pendingMode;
        const offerId = pendingOfferId;
        if (offerId) {
            dismissedOfferIds.add(offerId);
        }

        hideModal();
        zone.classList.remove('d-none');
        btnStop.classList.remove('d-none');

        const peer = ensurePeer();

        if (!stream) {
            try {
                stream = await getStreamByMode(offerMode, true);
                if (stream) {
                    localVideo.srcObject = stream;
                    stream.getTracks().forEach((track) => peer.addTrack(track, stream));
                } else {
                    peer.addTransceiver('video', { direction: 'recvonly' });
                    peer.addTransceiver('audio', { direction: 'recvonly' });
                }
            } catch (err) {
                peer.addTransceiver('video', { direction: 'recvonly' });
                peer.addTransceiver('audio', { direction: 'recvonly' });
                notify('Se respondera solo para recibir video.', 'warning');
            }
        }

        try {
            await peer.setRemoteDescription(offerData);

            while (pendingIce.length) {
                const candidate = pendingIce.shift();
                await peer.addIceCandidate(candidate);
            }

            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);

            await fetch('/webrtc/answer', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: '{{ $ticket->id }}',
                    answer
                })
            });

            notify('Conexion aceptada.', 'success');
        } catch (err) {
            notify('No se pudo completar la conexion.', 'danger');
            closeConnection();
        }
    }

    btnScreen.addEventListener('click', () => startSession('screen'));
    btnCall.addEventListener('click', () => startSession('call'));
    btnStop.addEventListener('click', () => {
        closeConnection();
        hideModal();
        notify('Sesion detenida.', 'warning');
    });

    modalAccept.addEventListener('click', acceptIncoming);
    modalRejectBtns.forEach((btn) => btn.addEventListener('click', () => {
        if (pendingOfferId) {
            dismissedOfferIds.add(pendingOfferId);
        }
        hideModal();
        notify('Solicitud rechazada.', 'warning');
    }));

    if (window.Echo) {
        try {
            window.Echo.channel('ticket.{{ $ticket->id }}').listen('.WebRTCSignal', handleSignal);
        } catch (err) {
            console.warn('Echo activo, pero fallo el listener de WebRTC.', err);
        }
    }

    if (peerUserId) {
        setPeerStatus(false, true);
        pingPresence();
        checkPeerPresence();
        setInterval(pingPresence, 20000);
        setInterval(checkPeerPresence, 7000);
    }

    pullSignals();
    setInterval(pullSignals, 1400);
})();
</script>

<style>
.ticket-call-tools .ticket-video {
    min-height: 220px;
    background: radial-gradient(circle at 30% 30%, #132f55 0%, #071226 55%, #040a16 100%);
}

.ticket-peer-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #d1d5db;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 12px;
    background: #f8fafc;
    color: #334155;
}

.ticket-peer-status__dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #94a3b8;
}

.ticket-peer-status.is-online .ticket-peer-status__dot {
    background: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.22);
}

.ticket-peer-status.is-offline .ticket-peer-status__dot {
    background: #ef4444;
}

.ticket-call-hint {
    font-size: 12px;
    color: #64748b;
}

.ticket-call-alert {
    margin-top: 12px;
    margin-bottom: 0;
    padding: 8px 12px;
    font-size: 13px;
}

.ticket-call-modal {
    position: fixed;
    inset: 0;
    z-index: 5000;
    display: none;
    align-items: center;
    justify-content: center;
}

.ticket-call-modal.is-open {
    display: flex;
}

.ticket-call-modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(1, 9, 22, 0.68);
    backdrop-filter: blur(2px);
}

.ticket-call-modal__dialog {
    position: relative;
    width: min(94vw, 430px);
    border-radius: 16px;
    background: linear-gradient(150deg, #ffffff 0%, #f5f8ff 100%);
    border: 1px solid #d9e2f0;
    padding: 16px;
    box-shadow: 0 20px 48px rgba(2, 8, 23, 0.38);
}

.ticket-call-modal__header {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 8px;
}

.ticket-call-modal__icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #e0f2fe;
    font-size: 12px;
    font-weight: 700;
}

.ticket-call-modal__pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .02em;
    color: #0f766e;
    background: #ccfbf1;
}

.dark .ticket-peer-status {
    background: #0f172a;
    border-color: #2d3a52;
    color: #d1d5db;
}

.dark .ticket-call-hint {
    color: #9ca3af;
}

.dark .ticket-call-modal__dialog {
    background: linear-gradient(160deg, #0e1a30 0%, #0b1527 100%);
    color: #e5e7eb;
    border-color: #26334b;
}

.dark .ticket-call-modal__icon {
    background: #1d2e45;
}

.dark .ticket-call-modal__pill {
    color: #67e8f9;
    background: rgba(8, 145, 178, 0.26);
}

.dark .ticket-call-modal__dialog .text-muted {
    color: #a8b6cc !important;
}
</style>

