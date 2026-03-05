<div class="ticket-chat card border-0 shadow-sm">
    <div class="card-body p-0">

        <div class="ticket-chat__messages" id="chatBox-{{ $ticket->id }}" data-last-id="{{ optional($ticket->messages->last())->id ?? 0 }}">
            @forelse($ticket->messages as $m)
                @php
                    $isMe = $m->user_id == auth()->id();
                    $isSeen = !is_null($m->seen_at);
                @endphp
                <div class="ticket-chat__row {{ $isMe ? 'is-me' : 'is-them' }}" data-message-id="{{ $m->id }}">
                    <article class="ticket-chat__bubble">
                        <div class="ticket-chat__meta">
                            <strong>{{ $m->user->name }}</strong>
                            <span>{{ $m->created_at->format('H:i') }}</span>
                        </div>

                        <p class="ticket-chat__text mb-0">{{ $m->message }}</p>

                        @if($m->file)
                            <a href="{{ asset('storage/'.$m->file) }}" class="ticket-chat__file" target="_blank" rel="noopener">
                                Ver archivo adjunto
                            </a>
                        @endif

                        @if($isMe)
                            <div class="ticket-chat__status {{ $isSeen ? 'is-seen' : '' }}" title="{{ $isSeen ? 'Visto' : 'Enviado' }}" data-status-id="{{ $m->id }}">
                                <span>{!! $isSeen ? '&#10003;&#10003;' : '&#10003;' !!}</span>
                                <small>{{ $isSeen ? 'Visto' : 'Enviado' }}</small>
                            </div>
                        @endif
                    </article>
                </div>
            @empty
                <div class="ticket-chat__empty" id="chatEmpty-{{ $ticket->id }}">
                    Aun no hay mensajes. Inicia la conversacion para este ticket.
                </div>
            @endforelse
        </div>

        <form method="POST" enctype="multipart/form-data" id="chatForm-{{ $ticket->id }}" action="{{ $action }}" class="ticket-chat__form">
            @csrf

            <div class="ticket-chat__composer">
                <textarea name="message" id="msg-{{ $ticket->id }}" placeholder="Escribe un mensaje..." required></textarea>

                <div class="ticket-chat__actions">
                    <label class="ticket-chat__attach" title="Adjuntar archivo">
                        <input type="file" name="file" hidden>
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M16.5 6.5v9.75a4.25 4.25 0 1 1-8.5 0V5.75a2.75 2.75 0 1 1 5.5 0V15a1.25 1.25 0 1 1-2.5 0V7.5h-2V15a3.25 3.25 0 1 0 6.5 0V5.75a4.75 4.75 0 0 0-9.5 0v10.5a6.25 6.25 0 1 0 12.5 0V6.5h-2Z"/>
                        </svg>
                        Adjuntar
                    </label>

                    <button type="submit" class="ticket-chat__send">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m3.4 20.4 17.5-7.5c.7-.3.7-1.3 0-1.6L3.4 3.8c-.7-.3-1.4.4-1.2 1.1l1.7 6.2c.1.4.5.7.9.7h7.2v1.5H4.8c-.4 0-.8.3-.9.7l-1.7 6.2c-.2.7.5 1.4 1.2 1.1Z"/>
                        </svg>
                        Enviar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    const chatBox = document.getElementById('chatBox-{{ $ticket->id }}');
    const chatForm = document.getElementById('chatForm-{{ $ticket->id }}');
    const msgInput = document.getElementById('msg-{{ $ticket->id }}');
    const emptyStateId = 'chatEmpty-{{ $ticket->id }}';
    const pollUrlBase = @json(route('tickets.messages.poll', $ticket->id));

    if (!chatBox || !chatForm || !msgInput) {
        return;
    }

    const meId = {{ (int) auth()->id() }};
    let lastId = Number(chatBox.dataset.lastId || 0);

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    };

    const atBottom = () => chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 80;
    const scrollDown = () => { chatBox.scrollTop = chatBox.scrollHeight; };

    scrollDown();

    msgInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.submit();
        }
    });

    function ensureNoEmptyState() {
        const empty = document.getElementById(emptyStateId);
        if (empty) empty.remove();
    }

    function renderStatus(isSeen, id) {
        const checks = isSeen ? '&#10003;&#10003;' : '&#10003;';
        const label = isSeen ? 'Visto' : 'Enviado';
        const cls = isSeen ? 'ticket-chat__status is-seen' : 'ticket-chat__status';
        return `<div class="${cls}" title="${label}" data-status-id="${id}"><span>${checks}</span><small>${label}</small></div>`;
    }

    function appendMessage(m) {
        const isMe = Number(m.user_id) === meId;
        const rowCls = isMe ? 'ticket-chat__row is-me' : 'ticket-chat__row is-them';
        const fileHtml = m.file_url ? `<a href="${m.file_url}" class="ticket-chat__file" target="_blank" rel="noopener">Ver archivo adjunto</a>` : '';
        const statusHtml = isMe ? renderStatus(!!m.seen, m.id) : '';
        const msgHtml = (m.message || '').replace(/\n/g, '<br>');

        const html = `
            <div class="${rowCls}" data-message-id="${m.id}">
                <article class="ticket-chat__bubble">
                    <div class="ticket-chat__meta">
                        <strong>${escapeHtml(m.user_name || 'Usuario')}</strong>
                        <span>${escapeHtml(m.time || '')}</span>
                    </div>
                    <p class="ticket-chat__text mb-0">${msgHtml}</p>
                    ${fileHtml}
                    ${statusHtml}
                </article>
            </div>
        `;

        chatBox.insertAdjacentHTML('beforeend', html);
    }

    function updateSeenStatus(messages) {
        messages.forEach((m) => {
            if (Number(m.user_id) !== meId) return;
            const statusNode = chatBox.querySelector(`[data-status-id="${m.id}"]`);
            if (!statusNode) return;
            statusNode.classList.toggle('is-seen', !!m.seen);
            statusNode.title = m.seen ? 'Visto' : 'Enviado';
            statusNode.innerHTML = `<span>${m.seen ? '&#10003;&#10003;' : '&#10003;'}</span><small>${m.seen ? 'Visto' : 'Enviado'}</small>`;
        });
    }

    async function pollMessages() {
        try {
            const wasBottom = atBottom();
            const url = `${pollUrlBase}?after_id=${encodeURIComponent(lastId)}`;
            const res = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) return;
            const payload = await res.json();
            const items = Array.isArray(payload.messages) ? payload.messages : [];
            if (!items.length) return;

            ensureNoEmptyState();

            items.forEach((m) => {
                const id = Number(m.id || 0);
                if (!id) return;

                if (!chatBox.querySelector(`[data-message-id="${id}"]`)) {
                    appendMessage(m);
                }

                if (id > lastId) lastId = id;
            });

            updateSeenStatus(items);

            if (wasBottom) {
                scrollDown();
            }
        } catch (err) {
            console.warn('No se pudo actualizar el chat en tiempo real.', err);
        }
    }

    setInterval(pollMessages, 1500);
})();
</script>

<style>
.ticket-chat__messages {
    max-height: 58vh;
    overflow-y: auto;
    padding: 18px;
    background: #f5f7fb;
    border-radius: 14px 14px 0 0;
}

.ticket-chat__row {
    display: flex;
    margin-bottom: 12px;
}

.ticket-chat__row.is-me {
    justify-content: flex-end;
}

.ticket-chat__row.is-them {
    justify-content: flex-start;
}

.ticket-chat__bubble {
    max-width: 78%;
    padding: 12px 14px;
    border-radius: 14px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
}

.ticket-chat__row.is-me .ticket-chat__bubble {
    background: #00798e;
    color: #fff;
}

.ticket-chat__row.is-them .ticket-chat__bubble {
    background: #ffffff;
    color: #111827;
}

.ticket-chat__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 4px;
}

.ticket-chat__text {
    white-space: pre-wrap;
    word-break: break-word;
}

.ticket-chat__file {
    display: inline-block;
    margin-top: 8px;
    font-size: 12px;
    color: inherit;
    text-decoration: underline;
}

.ticket-chat__status {
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    opacity: 0.9;
}

.ticket-chat__status.is-seen {
    color: #d8fbff;
}

.ticket-chat__empty {
    text-align: center;
    color: #6b7280;
    padding: 36px 10px;
    border: 1px dashed #d1d5db;
    border-radius: 12px;
    background: #fff;
}

.ticket-chat__form {
    border-top: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 0 0 14px 14px;
}

.ticket-chat__composer {
    padding: 14px;
}

.ticket-chat__composer textarea {
    width: 100%;
    min-height: 84px;
    resize: vertical;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    padding: 10px 12px;
}

.ticket-chat__actions {
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.ticket-chat__attach {
    font-size: 13px;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 8px 10px;
    cursor: pointer;
    background: #f9fafb;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.ticket-chat__send {
    border: 0;
    border-radius: 10px;
    padding: 8px 16px;
    background: #00798e;
    color: #fff;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.dark .ticket-chat__messages {
    background: #0c1324;
}

.dark .ticket-chat__row.is-them .ticket-chat__bubble {
    background: #1b2336;
    color: #e5e7eb;
}

.dark .ticket-chat__form {
    border-top-color: #263045;
    background: #0f172a;
}

.dark .ticket-chat__composer textarea,
.dark .ticket-chat__attach {
    background: #0b1220;
    border-color: #263045;
    color: #e5e7eb;
}
</style>
