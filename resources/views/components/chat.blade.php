<div class="ticket-chat card border-0 shadow-sm">
    <div class="ticket-chat__toolbar">
        <div>
            <p class="ticket-chat__eyebrow mb-1">Conversacion del ticket</p>
            <h6 class="ticket-chat__title mb-0">Seguimiento en tiempo real</h6>
        </div>
        <span class="ticket-chat__badge">{{ $ticket->messages->count() }} mensajes</span>
    </div>

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

                        @if($m->file_url)
                            <a href="{{ $m->file_url }}" class="ticket-chat__file" target="_blank" rel="noopener">
                                <span class="material-symbols-outlined">attach_file</span>
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
                        <span class="material-symbols-outlined">upload_file</span>
                        Adjuntar
                    </label>

                    <button type="submit" class="ticket-chat__send">
                        <span class="material-symbols-outlined">send</span>
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
        const fileHtml = m.file_url ? `<a href="${m.file_url}" class="ticket-chat__file" target="_blank" rel="noopener"><span class="material-symbols-outlined">attach_file</span>Ver archivo adjunto</a>` : '';
        const statusHtml = isMe ? renderStatus(!!m.seen, m.id) : '';
        const msgHtml = escapeHtml(m.message || '').replace(/\n/g, '<br>');

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
.ticket-chat {
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.18) !important;
}

.ticket-chat__toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #e5edf5;
    background: linear-gradient(135deg, #f8fbff, #eef8fc);
}

.ticket-chat__eyebrow {
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #00798e;
}

.ticket-chat__title {
    font-weight: 800;
    color: #10263a;
}

.ticket-chat__badge {
    border-radius: 999px;
    padding: .35rem .7rem;
    background: #dff4f8;
    color: #0a6778;
    font-size: .82rem;
    font-weight: 700;
}

.ticket-chat__messages {
    max-height: 58vh;
    overflow-y: auto;
    padding: 18px;
    background: linear-gradient(180deg, #f5f8fc 0%, #eef4f7 100%);
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
    border-radius: 18px;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
}

.ticket-chat__row.is-me .ticket-chat__bubble {
    background: linear-gradient(135deg, #00798e, #0a93ac);
    color: #fff;
    border-bottom-right-radius: 6px;
}

.ticket-chat__row.is-them .ticket-chat__bubble {
    background: #ffffff;
    color: #111827;
    border: 1px solid #dde7ef;
    border-bottom-left-radius: 6px;
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
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    margin-top: 8px;
    font-size: 12px;
    color: inherit;
    text-decoration: underline;
}

.ticket-chat__file .material-symbols-outlined {
    font-size: 16px;
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
}

.ticket-chat__composer {
    padding: 14px;
}

.ticket-chat__composer textarea {
    width: 100%;
    min-height: 84px;
    resize: vertical;
    border: 1px solid #d1dce5;
    border-radius: 14px;
    padding: 12px 14px;
}

.ticket-chat__actions {
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.ticket-chat__attach,
.ticket-chat__send {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 12px;
    font-weight: 700;
}

.ticket-chat__attach {
    font-size: 13px;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 8px 12px;
    cursor: pointer;
    background: #f9fafb;
}

.ticket-chat__send {
    border: 0;
    padding: 10px 18px;
    background: #00798e;
    color: #fff;
}

.dark .ticket-chat__toolbar {
    background: linear-gradient(135deg, #0f1a2e, #102038);
    border-bottom-color: #223047;
}

.dark .ticket-chat__title,
.dark .ticket-chat__badge {
    color: #e8f2ff;
}

.dark .ticket-chat__badge {
    background: rgba(0, 121, 142, 0.18);
}

.dark .ticket-chat__messages {
    background: linear-gradient(180deg, #0c1324 0%, #0b1729 100%);
}

.dark .ticket-chat__row.is-them .ticket-chat__bubble {
    background: #1b2336;
    color: #e5e7eb;
    border-color: #263045;
}

.dark .ticket-chat__empty,
.dark .ticket-chat__form,
.dark .ticket-chat__composer textarea,
.dark .ticket-chat__attach {
    background: #0f172a;
    border-color: #263045;
    color: #e5e7eb;
}
</style>
