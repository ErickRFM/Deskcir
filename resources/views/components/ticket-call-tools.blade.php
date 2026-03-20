@props([
    'ticket',
    'screenLabel' => 'Compartir pantalla',
    'callLabel' => 'Iniciar videollamada',
    'peerUserId' => null,
    'peerLabel' => 'Usuario',
    'screenFlow' => 'share-self',
])

@php
    $uid = 'ticket-call-' . $ticket->id . '-' . substr(md5((string) auth()->id()), 0, 6);
    $lastSignalId = \App\Models\RtcSignal::query()->where('ticket_id', $ticket->id)->max('id') ?? 0;
    $screenFlow = $screenFlow === 'request-peer' ? 'request-peer' : 'share-self';
@endphp

<div
    class="ticket-call-tools"
    id="{{ $uid }}"
    data-ticket-id="{{ $ticket->id }}"
    data-peer-id="{{ $peerUserId }}"
    data-peer-label="{{ $peerLabel }}"
    data-screen-flow="{{ $screenFlow }}"
    data-last-signal-id="{{ $lastSignalId }}"
>
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
        Deskcir Live solicita permisos solo cuando aceptas o inicias una sesion. Para compartir pantalla, el navegador te dejara elegir pantalla completa, ventana o pestana.
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
                <p class="ticket-call-console__microcopy mb-0">Acepta permisos desde aqui, monitorea el estado de la conexion y finaliza la sesion sin salir del ticket.</p>
            </div>

            <div class="ticket-call-console__topbar-actions">
                <span class="ticket-call-console__mode" data-call-mode>Listo</span>
                <button type="button" class="btn btn-outline-deskcir btn-sm d-inline-flex align-items-center gap-2" data-call-action="hide">
                    <span class="material-symbols-outlined">visibility_off</span>
                    Ocultar panel
                </button>
                <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2" data-call-action="stop" disabled>
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
                    <div class="ticket-call-console__video-stage">
                        <div class="ticket-call-console__placeholder" data-video-placeholder="remote">
                            <span class="material-symbols-outlined">desktop_windows</span>
                            <strong>Esperando la sesion de {{ $peerLabel }}</strong>
                            <p>Cuando la otra parte acepte, aqui veras su camara o la pantalla compartida.</p>
                        </div>
                        <video autoplay playsinline data-call-remote></video>
                    </div>
                </div>

                <div class="ticket-call-console__video-shell is-local">
                    <div class="ticket-call-console__video-label">Tu camara o pantalla</div>
                    <div class="ticket-call-console__video-stage is-local-preview">
                        <div class="ticket-call-console__placeholder" data-video-placeholder="local">
                            <span class="material-symbols-outlined">videocam</span>
                            <strong>Tu vista previa aparecera aqui</strong>
                            <p>Solo se mostrara cuando compartas tu camara o tu pantalla en la sesion actual.</p>
                        </div>
                        <video autoplay muted playsinline data-call-local></video>
                    </div>
                </div>
            </div>

            <aside class="ticket-call-console__side">
                <div class="ticket-call-console__card">
                    <h6 class="fw-bold mb-2">Flujo recomendado</h6>
                    <ol class="mb-0">
                        <li>Confirma que ambos esten en linea.</li>
                        <li>Acepta permisos solo cuando vayas a usar camara o pantalla.</li>
                        <li>Finaliza la sesion desde este panel para cerrarla en ambos lados.</li>
                    </ol>
                </div>

                <div class="ticket-call-console__card">
                    <h6 class="fw-bold mb-2">Permisos del navegador</h6>
                    <ul class="mb-0">
                        <li>Videollamada: camara y microfono.</li>
                        <li>Compartir pantalla: pantalla, ventana o pestana.</li>
                        <li>Puedes cancelar desde el selector nativo si no quieres compartir.</li>
                    </ul>
                </div>

                <div class="ticket-call-console__card is-accent">
                    <h6 class="fw-bold mb-2">Nota tecnica</h6>
                    <p class="mb-0 small">El control remoto total sigue requiriendo software adicional. Este modulo queda enfocado en llamada, evidencia en vivo y pantalla compartida estable dentro del ticket.</p>
                </div>
            </aside>
        </div>
    </section>

    <div class="ticket-call-modal" data-call-modal hidden>
        <div class="ticket-call-modal__backdrop" data-modal-backdrop></div>
        <div class="ticket-call-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $uid }}-modal-title">
            <span class="ticket-call-modal__badge" data-modal-badge>Deskcir Live</span>
            <h5 class="ticket-call-modal__title" id="{{ $uid }}-modal-title" data-modal-title>Permisos de sesion</h5>
            <p class="ticket-call-modal__body" data-modal-body>Confirma para continuar.</p>
            <div class="ticket-call-modal__actions">
                <button type="button" class="btn btn-outline-deskcir" data-modal-cancel>Cancelar</button>
                <button type="button" class="btn btn-deskcir" data-modal-confirm>Continuar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const wrapper = document.getElementById(@json($uid));
    if (!wrapper) return;

    const ticketId = Number(wrapper.dataset.ticketId || 0);
    const peerId = Number(wrapper.dataset.peerId || 0);
    const peerLabel = wrapper.dataset.peerLabel || 'Usuario';
    const screenFlow = wrapper.dataset.screenFlow === 'request-peer' ? 'request-peer' : 'share-self';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());

    const consolePanel = wrapper.querySelector('[data-call-console]');
    const localVideo = wrapper.querySelector('[data-call-local]');
    const remoteVideo = wrapper.querySelector('[data-call-remote]');
    const localPlaceholder = wrapper.querySelector('[data-video-placeholder="local"]');
    const remotePlaceholder = wrapper.querySelector('[data-video-placeholder="remote"]');
    const statusBox = wrapper.querySelector('[data-call-status]');
    const peerState = wrapper.querySelector('[data-peer-state]');
    const modePill = wrapper.querySelector('[data-call-mode]');
    const btnScreen = wrapper.querySelector('[data-call-action="screen"]');
    const btnCall = wrapper.querySelector('[data-call-action="call"]');
    const btnStop = wrapper.querySelector('[data-call-action="stop"]');
    const btnHide = wrapper.querySelector('[data-call-action="hide"]');
    const btnResume = wrapper.querySelector('[data-call-action="resume"]');
    const modal = wrapper.querySelector('[data-call-modal]');
    const modalBackdrop = wrapper.querySelector('[data-modal-backdrop]');
    const modalBadge = wrapper.querySelector('[data-modal-badge]');
    const modalTitle = wrapper.querySelector('[data-modal-title]');
    const modalBody = wrapper.querySelector('[data-modal-body]');
    const modalCancel = wrapper.querySelector('[data-modal-cancel]');
    const modalConfirm = wrapper.querySelector('[data-modal-confirm]');

    const supportsRtc = typeof window.RTCPeerConnection === 'function' && !!navigator.mediaDevices;
    const config = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'turn:openrelay.metered.ca:80', username: 'openrelayproject', credential: 'openrelayproject' }
        ]
    };

    let pc = null;
    let localStream = null;
    let queuedIce = [];
    let pingTimer = null;
    let pollTimer = null;
    let pollBusy = false;
    let peerOnline = false;
    let actionLock = false;
    let unloading = false;
    let pendingOffer = null;
    let modalState = null;
    let currentMode = 'idle';
    let lastSignalId = Number(wrapper.dataset.lastSignalId || 0);

    const setStatus = (text) => statusBox && (statusBox.textContent = text);
    const setMode = (mode, text) => {
        currentMode = mode;
        if (!modePill) return;
        modePill.textContent = text || 'Listo';
        modePill.classList.toggle('is-active', mode !== 'idle');
    };
    const hasLiveTracks = (stream) => !!stream && typeof stream.getTracks === 'function' && stream.getTracks().some((track) => track.readyState === 'live');
    const syncVideoState = () => {
        if (localPlaceholder) localPlaceholder.hidden = hasLiveTracks(localVideo?.srcObject);
        if (remotePlaceholder) remotePlaceholder.hidden = hasLiveTracks(remoteVideo?.srcObject);
    };
    const openConsole = () => {
        if (!consolePanel) return;
        consolePanel.hidden = false;
        wrapper.classList.add('is-live-open');
        if (btnResume) btnResume.hidden = true;
    };
    const hideConsole = () => {
        if (!consolePanel) return;
        consolePanel.hidden = true;
        wrapper.classList.remove('is-live-open');
        if (btnResume) btnResume.hidden = !(currentMode !== 'idle' || !!pendingOffer || !!pc);
    };
    const closeModal = () => {
        if (!modal) return;
        modal.hidden = true;
        modalState = null;
    };
    const showModal = (options) => {
        if (!modal || !modalTitle || !modalBody || !modalConfirm || !modalCancel) return;
        modalState = options || {};
        modal.hidden = false;
        modalBadge.textContent = options.badge || 'Deskcir Live';
        modalTitle.textContent = options.title || 'Permisos de sesion';
        modalBody.textContent = options.body || 'Confirma para continuar.';
        modalConfirm.textContent = options.confirmLabel || 'Continuar';
        modalCancel.textContent = options.cancelLabel || 'Cancelar';
        modalCancel.hidden = !!options.hideCancel;
    };
    const updateButtons = () => {
        const canStart = !!peerId && supportsRtc && peerOnline && !actionLock && currentMode === 'idle' && !pendingOffer;
        const hasSession = currentMode !== 'idle' || !!pendingOffer || !!pc;
        if (btnCall) {
            btnCall.disabled = !canStart;
            btnCall.title = !supportsRtc ? 'Tu navegador no soporta Deskcir Live.' : !peerId ? 'No hay otro usuario disponible en este ticket.' : !peerOnline ? `${peerLabel} no esta en linea ahora mismo.` : '';
        }
        if (btnScreen) {
            btnScreen.disabled = !canStart;
            btnScreen.title = btnCall?.title || '';
        }
        if (btnStop) btnStop.disabled = !hasSession;
        if (btnResume) btnResume.hidden = consolePanel && !consolePanel.hidden ? true : !hasSession;
    };
    const setPeerOnline = (online) => {
        peerOnline = !!online;
        if (peerState) {
            peerState.classList.toggle('is-online', peerOnline);
            peerState.classList.toggle('is-offline', !peerOnline);
            peerState.innerHTML = `<span class="ticket-call-tools__dot"></span>${peerLabel}: ${peerOnline ? 'en linea' : 'desconectado'}`;
        }
        updateButtons();
    };
    const signalMode = (mode) => mode === 'screen-request' ? 'screen-request' : mode === 'screen-share' ? 'screen-share' : 'call';
    const errorText = (error, mode) => {
        const name = error?.name || '';
        if (!supportsRtc) return 'Tu navegador no soporta videollamada ni compartir pantalla en este modulo.';
        if (name === 'NotAllowedError' || name === 'PermissionDeniedError') return mode === 'call' ? 'No se concedieron permisos para camara o microfono.' : 'No se concedio permiso para compartir pantalla.';
        if (name === 'NotReadableError') return mode === 'call' ? 'La camara o el microfono ya estan siendo usados por otra app.' : 'No fue posible leer la pantalla seleccionada.';
        if (name === 'NotFoundError' || name === 'DevicesNotFoundError') return mode === 'call' ? 'No se encontro una camara o microfono disponible.' : 'No se encontro una fuente de pantalla para compartir.';
        if (name === 'AbortError') return 'La operacion fue cancelada antes de completarse.';
        return 'No fue posible completar la sesion. Revisa permisos, conectividad y vuelve a intentarlo.';
    };
    const hangupText = (reason) => {
        switch (reason) {
            case 'declined': return `${peerLabel} rechazo la solicitud.`;
            case 'denied': return `${peerLabel} no concedio el permiso solicitado.`;
            case 'busy': return `${peerLabel} ya tiene otra sesion activa.`;
            case 'reload': return `${peerLabel} recargo o abandono la pagina.`;
            case 'disconnected': return `La sesion con ${peerLabel} se perdio por conectividad.`;
            default: return `La sesion con ${peerLabel} finalizo.`;
        }
    };

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });
        if (!response.ok) throw new Error(`Request failed with status ${response.status}`);
        return response.json().catch(() => ({}));
    }

    function clearPeerConnection() {
        const peer = pc;
        pc = null;
        if (!peer) return;
        peer.ontrack = null;
        peer.onicecandidate = null;
        peer.onconnectionstatechange = null;
        try { peer.close(); } catch (error) { console.warn('No se pudo cerrar la conexion WebRTC.', error); }
    }

    function clearLocalStream() {
        if (localStream) {
            localStream.getTracks().forEach((track) => {
                track.onended = null;
                track.stop();
            });
        }
        localStream = null;
        if (localVideo) localVideo.srcObject = null;
        syncVideoState();
    }

    function clearRemotePreview() {
        if (remoteVideo) remoteVideo.srcObject = null;
        syncVideoState();
    }

    function bindLocalStream(stream, mode) {
        localStream = stream;
        if (localVideo) localVideo.srcObject = stream;
        const primaryTrack = stream.getVideoTracks()[0] || stream.getTracks()[0];
        if (primaryTrack) {
            primaryTrack.onended = () => {
                if (!['screen-share', 'screen-request'].includes(mode) || unloading) return;
                stopCall({ notify: true, reason: 'ended', keepConsole: true, statusText: 'Se dejo de compartir la pantalla desde el selector del navegador.' });
            };
        }
        syncVideoState();
    }

    function createPeerConnection() {
        const peer = new RTCPeerConnection(config);
        pc = peer;
        queuedIce = [];
        peer.ontrack = (event) => {
            if (pc !== peer) return;
            remoteVideo.srcObject = event.streams?.[0] || new MediaStream([event.track]);
            syncVideoState();
            setStatus('Conexion activa. Ya puedes continuar la asistencia remota.');
            setMode(currentMode, currentMode === 'call' ? 'Llamada activa' : 'Pantalla activa');
            updateButtons();
        };
        peer.onicecandidate = async (event) => {
            if (pc !== peer || !event.candidate || unloading) return;
            try {
                await postJson('/webrtc/ice', { ticket_id: ticketId, candidate: event.candidate });
            } catch (error) {
                console.warn('No se pudo enviar el candidato ICE.', error);
            }
        };
        peer.onconnectionstatechange = () => {
            if (pc !== peer) return;
            if (peer.connectionState === 'connecting') setStatus('Conectando la sesion remota...');
            if (peer.connectionState === 'connected') {
                setStatus('Conexion activa. Ya puedes continuar la asistencia remota.');
                setMode(currentMode, currentMode === 'call' ? 'Llamada activa' : 'Pantalla activa');
            }
            if (peer.connectionState === 'disconnected') setStatus('La sesion se desconecto. Estamos esperando a que vuelva la conectividad.');
            if (peer.connectionState === 'failed') {
                stopCall({ notify: true, reason: 'disconnected', keepConsole: true, statusText: 'La sesion fallo por conectividad. Puedes volver a intentarlo desde este panel.' });
            }
        };
        return peer;
    }

    async function flushQueuedIce() {
        if (!pc || !pc.remoteDescription) return;
        while (queuedIce.length) {
            const candidate = queuedIce.shift();
            await pc.addIceCandidate(new RTCIceCandidate(candidate));
        }
    }

    async function pingPresence() {
        try { await postJson('/presence/ping', { at: Date.now() }); } catch (error) { console.warn('No se pudo actualizar presencia.', error); }
    }

    async function checkPeerPresence() {
        if (!peerId || !peerState) return;
        try {
            const response = await fetch(`/presence/user/${peerId}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const payload = await response.json();
            setPeerOnline(!!payload.online);
        } catch (error) {
            console.warn('No se pudo consultar presencia.', error);
        }
    }

    async function sendHangup(reason = 'ended') {
        if (!peerId || unloading) return;
        try {
            await postJson('/webrtc/hangup', { ticket_id: ticketId, reason });
        } catch (error) {
            console.warn('No se pudo enviar el cierre de sesion.', error);
        }
    }

    function beaconHangup(reason = 'reload') {
        if (!peerId || !csrfToken || !navigator.sendBeacon) return;
        const payload = new FormData();
        payload.append('_token', csrfToken);
        payload.append('ticket_id', String(ticketId));
        payload.append('reason', reason);
        navigator.sendBeacon('/webrtc/hangup', payload);
    }

    async function stopCall(options = {}) {
        const { notify = false, reason = 'ended', keepConsole = true, statusText = 'Sesion finalizada. El panel queda listo para una nueva conexion.' } = options;
        closeModal();
        pendingOffer = null;
        clearPeerConnection();
        clearLocalStream();
        clearRemotePreview();
        queuedIce = [];
        setMode('idle', 'Listo');
        setStatus(statusText);
        updateButtons();
        if (keepConsole) openConsole(); else hideConsole();
        if (notify) await sendHangup(reason);
    }

    const incomingCopy = (mode) => mode === 'screen-request'
        ? { badge: 'Compartir pantalla', title: 'Solicitud para compartir pantalla', body: `${peerLabel} solicita ver tu pantalla. Al aceptar, el navegador te dejara elegir pantalla, ventana o pestana.`, confirmLabel: 'Aceptar y elegir pantalla' }
        : mode === 'screen-share'
            ? { badge: 'Compartir pantalla', title: 'Pantalla compartida entrante', body: `${peerLabel} quiere mostrarte su pantalla. Acepta para abrir la sesion remota.`, confirmLabel: 'Aceptar' }
            : { badge: 'Videollamada', title: 'Videollamada entrante', body: `${peerLabel} quiere iniciar una videollamada contigo. Al aceptar, el navegador pedira acceso a camara y microfono.`, confirmLabel: 'Aceptar llamada' };
    const outgoingCopy = (mode) => mode === 'screen-request'
        ? { badge: 'Solicitud remota', title: 'Solicitar pantalla', body: `Se enviara una solicitud a ${peerLabel}. Si acepta, podra elegir que pantalla, ventana o pestana compartir.`, confirmLabel: 'Enviar solicitud' }
        : mode === 'screen-share'
            ? { badge: 'Compartir pantalla', title: 'Compartir tu pantalla', body: 'El navegador abrira su selector para que elijas pantalla completa, ventana o pestana.', confirmLabel: 'Continuar' }
            : { badge: 'Videollamada', title: 'Iniciar videollamada', body: 'El navegador pedira permisos para camara y microfono antes de crear la sesion.', confirmLabel: 'Continuar' };

    async function startOutgoing(mode) {
        if (!supportsRtc) return setStatus('Tu navegador no soporta videollamada ni compartir pantalla en este modulo.');
        if (!peerId) return setStatus('No hay otro usuario disponible en este ticket.');
        if (!peerOnline) return setStatus(`${peerLabel} no esta en linea en este momento.`);

        await stopCall({ notify: false, keepConsole: true, statusText: 'Preparando una nueva sesion remota...' });
        openConsole();
        actionLock = true;
        updateButtons();
        setMode(mode, mode === 'call' ? 'Preparando llamada' : 'Preparando sesion');

        try {
            const peer = createPeerConnection();
            if (mode === 'call') {
                setStatus('Solicitando acceso a camara y microfono...');
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                bindLocalStream(stream, mode);
                stream.getTracks().forEach((track) => peer.addTrack(track, stream));
            } else if (mode === 'screen-share') {
                setStatus('Abriendo selector del navegador para elegir pantalla, ventana o pestana...');
                const stream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
                bindLocalStream(stream, mode);
                stream.getTracks().forEach((track) => peer.addTrack(track, stream));
            } else {
                setStatus(`Solicitud enviada a ${peerLabel}. Cuando acepte podra elegir pantalla, ventana o pestana.`);
                peer.addTransceiver('video', { direction: 'recvonly' });
                peer.addTransceiver('audio', { direction: 'recvonly' });
            }

            const offer = await peer.createOffer();
            await peer.setLocalDescription(offer);
            await postJson('/webrtc/offer', { ticket_id: ticketId, offer, request_mode: mode });
            if (mode === 'call') {
                setStatus(`Esperando que ${peerLabel} acepte la videollamada...`);
                setMode(mode, 'Llamada solicitada');
            } else if (mode === 'screen-share') {
                setStatus(`Esperando que ${peerLabel} acepte ver tu pantalla...`);
                setMode(mode, 'Pantalla solicitada');
            } else {
                setMode(mode, 'Solicitud enviada');
            }
        } catch (error) {
            await stopCall({ notify: false, keepConsole: true, statusText: errorText(error, mode) });
            setMode('idle', 'Error');
        } finally {
            actionLock = false;
            updateButtons();
        }
    }

    async function acceptIncomingOffer() {
        if (!pendingOffer) return;
        const signal = pendingOffer;
        pendingOffer = null;
        closeModal();
        openConsole();
        actionLock = true;
        updateButtons();
        const mode = signalMode(signal.request_mode);

        try {
            const peer = createPeerConnection();
            await peer.setRemoteDescription(signal.data);

            if (mode === 'call') {
                setStatus('Solicitando acceso a camara y microfono para responder la videollamada...');
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                bindLocalStream(stream, mode);
                stream.getTracks().forEach((track) => peer.addTrack(track, stream));
            } else if (mode === 'screen-request') {
                setStatus('Elige pantalla, ventana o pestana en el selector del navegador para compartir.');
                const stream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
                bindLocalStream(stream, mode);
                stream.getTracks().forEach((track) => peer.addTrack(track, stream));
            } else {
                clearLocalStream();
                setStatus('Aceptando la pantalla compartida entrante...');
            }

            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);
            await postJson('/webrtc/answer', { ticket_id: ticketId, answer });
            await flushQueuedIce();
            setMode(mode, mode === 'call' ? 'Conectando llamada' : 'Conectando sesion');
            setStatus(mode === 'call' ? 'Videollamada aceptada. Conectando...' : mode === 'screen-request' ? 'Pantalla seleccionada. Conectando la sesion remota...' : 'Pantalla remota aceptada. Conectando...');
        } catch (error) {
            await stopCall({ notify: true, reason: error?.name === 'NotAllowedError' ? 'denied' : 'declined', keepConsole: true, statusText: errorText(error, mode) });
        } finally {
            actionLock = false;
            updateButtons();
        }
    }

    async function declineIncomingOffer(reason = 'declined') {
        pendingOffer = null;
        closeModal();
        setMode('idle', 'Listo');
        setStatus('La solicitud fue cancelada antes de iniciar la sesion.');
        updateButtons();
        await sendHangup(reason);
    }

    async function handleSignal(signal) {
        lastSignalId = Math.max(lastSignalId, Number(signal.id || 0));

        if (signal.type === 'offer') {
            const mode = signalMode(signal.request_mode);
            if (pendingOffer || currentMode !== 'idle' || pc) {
                await sendHangup('busy');
                return;
            }
            pendingOffer = signal;
            openConsole();
            setMode('incoming', mode === 'call' ? 'Llamada entrante' : 'Solicitud entrante');
            setStatus(mode === 'call' ? `${peerLabel} quiere iniciar una videollamada contigo.` : mode === 'screen-request' ? `${peerLabel} solicita que compartas tu pantalla.` : `${peerLabel} quiere mostrarte su pantalla.`);
            showModal({ ...incomingCopy(mode), onConfirm: acceptIncomingOffer, onCancel: () => declineIncomingOffer('declined') });
            updateButtons();
            return;
        }

        if (signal.type === 'answer' && pc) {
            await pc.setRemoteDescription(signal.data);
            await flushQueuedIce();
            setStatus('Respuesta recibida. Terminando de conectar la sesion...');
            return;
        }

        if (signal.type === 'ice') {
            if (!pc || !pc.remoteDescription) {
                queuedIce.push(signal.data);
                return;
            }
            await pc.addIceCandidate(new RTCIceCandidate(signal.data));
            return;
        }

        if (signal.type === 'hangup') {
            await stopCall({ notify: false, keepConsole: true, statusText: hangupText(signal.data?.reason || 'ended') });
        }
    }

    async function pollSignals() {
        if (pollBusy || !peerId) return;
        pollBusy = true;
        try {
            const response = await fetch(`/webrtc/poll?ticket_id=${ticketId}&after_id=${lastSignalId}`, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const payload = await response.json();
            const signals = Array.isArray(payload.signals) ? payload.signals : [];
            for (const signal of signals) {
                await handleSignal(signal);
            }
        } catch (error) {
            console.warn('No se pudo actualizar la sesion WebRTC.', error);
        } finally {
            pollBusy = false;
        }
    }

    btnScreen?.addEventListener('click', () => {
        const mode = screenFlow === 'request-peer' ? 'screen-request' : 'screen-share';
        showModal({ ...outgoingCopy(mode), onConfirm: () => startOutgoing(mode) });
    });
    btnCall?.addEventListener('click', () => showModal({ ...outgoingCopy('call'), onConfirm: () => startOutgoing('call') }));
    btnStop?.addEventListener('click', () => stopCall({ notify: true, reason: 'ended', keepConsole: true, statusText: 'Sesion finalizada. El panel queda listo para volver a iniciar otra conexion.' }));
    btnHide?.addEventListener('click', hideConsole);
    btnResume?.addEventListener('click', openConsole);
    modalBackdrop?.addEventListener('click', () => modalState?.onCancel ? modalState.onCancel() : closeModal());
    modalCancel?.addEventListener('click', () => modalState?.onCancel ? modalState.onCancel() : closeModal());
    modalConfirm?.addEventListener('click', () => modalState?.onConfirm ? modalState.onConfirm() : closeModal());
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || modal?.hidden) return;
        modalState?.onCancel ? modalState.onCancel() : closeModal();
    });

    if (!supportsRtc) setStatus('Tu navegador no soporta videollamada ni compartir pantalla en este modulo.');
    updateButtons();
    syncVideoState();
    pingPresence();
    checkPeerPresence();
    pingTimer = setInterval(pingPresence, 15000);
    pollTimer = setInterval(() => {
        checkPeerPresence();
        pollSignals();
    }, 2000);

    window.addEventListener('beforeunload', () => {
        unloading = true;
        clearInterval(pingTimer);
        clearInterval(pollTimer);
        if (currentMode !== 'idle' || pendingOffer || pc) beaconHangup('reload');
        clearPeerConnection();
        clearLocalStream();
        clearRemotePreview();
    });
})();
</script>

<style>
.ticket-call-tools { display: grid; gap: .85rem; min-width: min(100%, 34rem); }
.ticket-call-tools__header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.ticket-call-tools__presence, .ticket-call-tools__actions { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; }
.ticket-call-tools__presence-pill { display: inline-flex; align-items: center; gap: .35rem; border-radius: 999px; padding: .45rem .8rem; background: #eff7fb; border: 1px solid #d9e8ef; color: #1c3a4f; font-size: .82rem; font-weight: 700; }
.ticket-call-tools__presence-pill.is-self { background: #e8fbf5; color: #0f766e; border-color: #b8ead9; }
.ticket-call-tools__presence-pill.is-online { background: #ecfdf3; color: #15803d; border-color: #b7e8c7; }
.ticket-call-tools__presence-pill.is-offline { background: #fff4f4; color: #b42318; border-color: #f6c2c2; }
.ticket-call-tools__dot { width: 8px; height: 8px; border-radius: 999px; background: currentColor; }
.ticket-call-tools__hint { color: #61778b; font-size: .84rem; }
.ticket-call-tools__resume { width: fit-content; display: inline-flex; align-items: center; gap: .4rem; }
.ticket-call-console { border: 1px solid rgba(80, 187, 212, 0.24); border-radius: 22px; overflow: hidden; background: linear-gradient(145deg, #071827 0%, #0c2334 55%, #103046 100%); color: #ecf8ff; box-shadow: 0 24px 54px rgba(2, 8, 23, 0.24); }
.ticket-call-console__topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1rem 1.1rem; border-bottom: 1px solid rgba(177, 223, 236, 0.14); }
.ticket-call-console__topbar-actions { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; justify-content: flex-end; }
.ticket-call-console__eyebrow { font-size: .76rem; letter-spacing: .12em; text-transform: uppercase; font-weight: 800; color: #8ed7e8; }
.ticket-call-console__microcopy { color: #b7d3df; font-size: .84rem; }
.ticket-call-console__mode { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: .45rem .75rem; border-radius: 999px; background: rgba(143, 209, 226, 0.12); border: 1px solid rgba(143, 209, 226, 0.2); color: #cfeef7; font-size: .8rem; font-weight: 700; }
.ticket-call-console__mode.is-active { background: rgba(19, 214, 164, 0.14); border-color: rgba(19, 214, 164, 0.26); color: #a8ffde; }
.ticket-call-console__status { padding: .9rem 1.1rem 0; color: #b9d8e4; min-height: 56px; }
.ticket-call-console__grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(260px, .9fr); gap: 1rem; padding: 1rem 1.1rem 1.1rem; }
.ticket-call-console__main { display: grid; gap: .9rem; }
.ticket-call-console__video-shell { border: 1px solid rgba(177, 223, 236, 0.16); border-radius: 18px; padding: .8rem; background: rgba(7, 24, 39, 0.48); }
.ticket-call-console__video-label { margin-bottom: .45rem; color: #8fd1e2; font-size: .82rem; font-weight: 700; }
.ticket-call-console__video-stage { position: relative; border-radius: 14px; overflow: hidden; background: linear-gradient(160deg, rgba(3, 7, 18, 0.98), rgba(6, 21, 34, 0.94)); aspect-ratio: 16 / 9; min-height: 220px; }
.ticket-call-console__video-stage.is-local-preview { aspect-ratio: 16 / 10; min-height: 170px; }
.ticket-call-console__video-stage video { width: 100%; height: 100%; object-fit: cover; display: block; background: transparent; }
.ticket-call-console__placeholder { position: absolute; inset: 0; display: grid; place-content: center; gap: .45rem; text-align: center; padding: 1.2rem; color: #dceefa; background: radial-gradient(circle at top, rgba(34, 211, 238, 0.12), transparent 38%), linear-gradient(145deg, rgba(9, 20, 31, 0.82), rgba(3, 7, 18, 0.94)); }
.ticket-call-console__placeholder .material-symbols-outlined { font-size: 2rem; color: #8fd1e2; }
.ticket-call-console__placeholder strong { font-size: 1rem; }
.ticket-call-console__placeholder p { margin: 0; color: #9fc8d8; font-size: .88rem; }
.ticket-call-console__side { display: grid; gap: .85rem; }
.ticket-call-console__card { border: 1px solid rgba(177, 223, 236, 0.16); border-radius: 18px; padding: .95rem 1rem; background: rgba(7, 24, 39, 0.44); color: #dbedf4; }
.ticket-call-console__card.is-accent { background: rgba(1, 104, 122, 0.22); border-color: rgba(118, 221, 240, 0.26); }
.ticket-call-console__card ol, .ticket-call-console__card ul { padding-left: 1rem; display: grid; gap: .45rem; }
.ticket-call-modal { position: fixed; inset: 0; z-index: 1080; display: grid; place-items: center; padding: 1rem; }
.ticket-call-modal__backdrop { position: absolute; inset: 0; background: rgba(2, 8, 23, 0.72); backdrop-filter: blur(6px); }
.ticket-call-modal__dialog { position: relative; width: min(100%, 32rem); border-radius: 24px; padding: 1.25rem 1.25rem 1.1rem; background: #f8fcff; border: 1px solid rgba(99, 179, 203, 0.28); box-shadow: 0 30px 70px rgba(2, 8, 23, 0.3); color: #12344a; }
.ticket-call-modal__badge { display: inline-flex; align-items: center; width: fit-content; border-radius: 999px; padding: .35rem .75rem; background: #e9f9fd; border: 1px solid #cfeff6; color: #0a7184; font-size: .78rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.ticket-call-modal__title { margin: .95rem 0 .5rem; font-weight: 800; }
.ticket-call-modal__body { margin: 0; color: #4d6678; line-height: 1.6; }
.ticket-call-modal__actions { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.15rem; flex-wrap: wrap; }
.dark .ticket-call-tools__presence-pill { background: #102235; border-color: #254159; color: #d8f2fb; }
.dark .ticket-call-tools__hint { color: #a6bfd2; }
.dark .ticket-call-modal__dialog { background: #071827; border-color: #1f4960; color: #ecf8ff; }
.dark .ticket-call-modal__badge { background: rgba(0, 183, 224, 0.12); border-color: rgba(88, 200, 230, 0.24); color: #99eafe; }
.dark .ticket-call-modal__body { color: #b8d7e3; }
@media (max-width: 991.98px) { .ticket-call-console__grid { grid-template-columns: 1fr; } }
@media (max-width: 767.98px) {
    .ticket-call-tools, .ticket-call-console { min-width: 100%; }
    .ticket-call-console__topbar, .ticket-call-console__topbar-actions, .ticket-call-modal__actions { flex-direction: column; align-items: stretch; }
    .ticket-call-console__status { min-height: 0; }
    .ticket-call-console__video-stage { min-height: 200px; }
    .ticket-call-console__video-stage.is-local-preview { min-height: 150px; }
}
</style>
