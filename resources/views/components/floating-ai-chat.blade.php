<div
    class="deskcir-ai"
    id="deskcirAiWidget"
    data-context="{{ trim($__env->yieldContent('title', 'Deskcir')) }}"
    data-user="{{ auth()->user()->name ?? 'Invitado' }}"
    data-authenticated="{{ auth()->check() ? '1' : '0' }}"
    data-threads-key="deskcir-ai-threads-user-{{ auth()->id() ?? 'guest' }}"
    data-active-key="deskcir-ai-active-user-{{ auth()->id() ?? 'guest' }}"
>
    <button type="button" class="deskcir-ai__trigger" id="deskcirAiTrigger" aria-expanded="false" aria-controls="deskcirAiPanel">
        <span class="material-symbols-outlined">auto_awesome</span>
        <span>Deskcir AI</span>
    </button>

    <div class="deskcir-ai__overlay" id="deskcirAiOverlay" hidden></div>

    <section class="deskcir-ai__panel" id="deskcirAiPanel" hidden>
        <header class="deskcir-ai__header">
            <div>
                <p class="deskcir-ai__eyebrow mb-1">Deskcir AI</p>
                <h3 class="deskcir-ai__title mb-1">{{ auth()->check() ? 'Asistente con memoria' : 'Asistente rapido sin login' }}</h3>
                <p class="deskcir-ai__subtext mb-0">{{ auth()->check() ? 'Recuerda tu hilo activo y lo comparte con la vista completa.' : 'Puedes usarlo al instante. Si quieres guardar chats o abrir tickets, despues te pedira iniciar sesion.' }}</p>
            </div>
            <div class="deskcir-ai__header-actions">
                <a href="{{ route('deskcir.ai') }}" class="deskcir-ai__expand" aria-label="Abrir vista completa">
                    <span class="material-symbols-outlined">open_in_full</span>
                </a>
                <button type="button" class="deskcir-ai__close" id="deskcirAiClose" aria-label="Cerrar asistente">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </header>

        <div class="deskcir-ai__quick">
            <button type="button" class="deskcir-ai__chip" data-ai-prompt="Resume esta pantalla y dime que accion conviene seguir.">
                <span class="material-symbols-outlined">summarize</span>
                Resumen
            </button>
            <button type="button" class="deskcir-ai__chip" data-ai-prompt="Ayudame a responder con un mensaje claro, amable y profesional.">
                <span class="material-symbols-outlined">chat</span>
                Respuesta
            </button>
            <button type="button" class="deskcir-ai__chip" data-ai-prompt="Compara las opciones que ya mencionamos y recomiendame la mejor con una razon clara.">
                <span class="material-symbols-outlined">balance</span>
                Comparar
            </button>
        </div>

        <div class="deskcir-ai__messages" id="deskcirAiMessages"></div>

        <form id="deskcirAiForm" class="deskcir-ai__form">
            @csrf
            <label class="deskcir-ai__label" for="deskcirAiInput">Escribe tu consulta</label>
            <textarea id="deskcirAiInput" class="deskcir-ai__input" rows="4" placeholder="Escribe y presiona Enter para enviar. Shift + Enter agrega salto de linea." required></textarea>
            <div class="deskcir-ai__actions">
                <small class="deskcir-ai__hint">Puedes escribir cosas como mas detallado, comparalo o mejor opcion.</small>
                <button type="submit" class="deskcir-ai__send" id="deskcirAiSend">
                    <span class="material-symbols-outlined">send</span>
                    Enviar
                </button>
            </div>
        </form>
    </section>
</div>

<script>
(function () {
    const root = document.getElementById('deskcirAiWidget');
    if (!root) return;

    const trigger = document.getElementById('deskcirAiTrigger');
    const overlay = document.getElementById('deskcirAiOverlay');
    const panel = document.getElementById('deskcirAiPanel');
    const closeBtn = document.getElementById('deskcirAiClose');
    const form = document.getElementById('deskcirAiForm');
    const input = document.getElementById('deskcirAiInput');
    const messages = document.getElementById('deskcirAiMessages');
    const chips = root.querySelectorAll('[data-ai-prompt]');
    const sendButton = document.getElementById('deskcirAiSend');
    const context = root.dataset.context || document.title || 'Deskcir';
    const currentUser = root.dataset.user || 'Invitado';
    const isAuthenticated = root.dataset.authenticated === '1';
    const threadsKey = root.dataset.threadsKey;
    const activeKey = root.dataset.activeKey;
    let isLoading = false;
    let conversation = [];

    const seedText = isAuthenticated
        ? 'Puedo recordar tu hilo activo y continuarlo con mejor contexto.'
        : 'Puedes usarme sin iniciar sesion. Este chat es temporal y se borra al cambiar de vista.';

    const seedMessage = { role: 'model', text: seedText };

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const formatInline = (value) => escapeHtml(value)
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/`(.+?)`/g, '<code>$1</code>');

    const formatAiText = (text) => {
        const normalized = String(text || '').replace(/\r/g, '').trim();
        if (!normalized) return '<p>No hubo respuesta util.</p>';

        const lines = normalized.split('\n');
        const blocks = [];
        let paragraph = [];
        let listType = null;
        let listItems = [];

        const flushParagraph = () => {
            if (!paragraph.length) return;
            blocks.push(`<p>${paragraph.map((line) => formatInline(line)).join('<br>')}</p>`);
            paragraph = [];
        };

        const flushList = () => {
            if (!listItems.length || !listType) return;
            const tag = listType === 'ol' ? 'ol' : 'ul';
            blocks.push(`<${tag}>${listItems.map((item) => `<li>${formatInline(item)}</li>`).join('')}</${tag}>`);
            listItems = [];
            listType = null;
        };

        lines.forEach((rawLine) => {
            const line = rawLine.trim();
            if (!line) {
                flushParagraph();
                flushList();
                return;
            }

            const orderedMatch = line.match(/^\d+[.)]\s+(.*)$/);
            const bulletMatch = line.match(/^[-*]\s+(.*)$/);

            if (orderedMatch) {
                flushParagraph();
                if (listType && listType !== 'ol') flushList();
                listType = 'ol';
                listItems.push(orderedMatch[1]);
                return;
            }

            if (bulletMatch) {
                flushParagraph();
                if (listType && listType !== 'ul') flushList();
                listType = 'ul';
                listItems.push(bulletMatch[1]);
                return;
            }

            if (listItems.length) flushList();
            paragraph.push(line);
        });

        flushParagraph();
        flushList();
        return blocks.join('');
    };

    const makeThreadId = () => `thread-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

    const readThreads = () => {
        if (!isAuthenticated) return [];
        try {
            return JSON.parse(localStorage.getItem(threadsKey) || '[]');
        } catch (error) {
            return [];
        }
    };

    const writeThreads = (threads) => {
        if (!isAuthenticated) return;
        localStorage.setItem(threadsKey, JSON.stringify(threads));
    };

    const ensureThread = () => {
        if (!isAuthenticated) {
            conversation = [seedMessage];
            return;
        }

        const threads = readThreads();
        let activeId = localStorage.getItem(activeKey);
        let activeThread = threads.find((thread) => thread.id === activeId);

        if (!activeThread) {
            activeThread = {
                id: makeThreadId(),
                title: 'Chat 1',
                updatedAt: Date.now(),
                messages: [seedMessage],
            };
            threads.unshift(activeThread);
            writeThreads(threads);
            localStorage.setItem(activeKey, activeThread.id);
        }

        conversation = Array.isArray(activeThread.messages) && activeThread.messages.length
            ? activeThread.messages.slice(-12)
            : [seedMessage];
    };

    const persistConversation = () => {
        if (!isAuthenticated) return;
        const threads = readThreads();
        const activeId = localStorage.getItem(activeKey);
        const nextThreads = threads.map((thread) => {
            if (thread.id !== activeId) return thread;
            return {
                ...thread,
                title: thread.title || 'Chat',
                updatedAt: Date.now(),
                messages: conversation.slice(-12),
            };
        });
        writeThreads(nextThreads);
    };

    const appendMessage = (text, type, extraClass = '') => {
        const article = document.createElement('article');
        article.className = `deskcir-ai__message ${type === 'user' ? 'is-user' : 'is-ai'} ${extraClass}`.trim();
        article.innerHTML = `<div class="deskcir-ai__message-body">${type === 'user' ? `<p class="mb-0">${escapeHtml(text)}</p>` : formatAiText(text)}</div>`;
        messages.appendChild(article);
        messages.scrollTop = messages.scrollHeight;
        return article;
    };

    const renderConversation = () => {
        messages.innerHTML = '';
        conversation.forEach((item) => appendMessage(item.text, item.role === 'user' ? 'user' : 'ai'));
    };

    const appendThinking = () => {
        const article = document.createElement('article');
        article.className = 'deskcir-ai__message is-ai is-thinking';
        article.innerHTML = `
            <div class="deskcir-ai__message-body">
                <div class="deskcir-ai__thinking">
                    <span class="deskcir-ai__dot"></span>
                    <span class="deskcir-ai__dot"></span>
                    <span class="deskcir-ai__dot"></span>
                </div>
                <small class="deskcir-ai__thinking-label">Deskcir AI esta afinando una respuesta mas util...</small>
            </div>
        `;
        messages.appendChild(article);
        messages.scrollTop = messages.scrollHeight;
        return article;
    };

    const setOpen = (open) => {
        panel.hidden = !open;
        overlay.hidden = !open;
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        root.classList.toggle('is-open', open);
        if (open) setTimeout(() => input.focus(), 60);
    };

    const setLoading = (loading) => {
        isLoading = loading;
        input.disabled = loading;
        chips.forEach((chip) => chip.disabled = loading);
        sendButton.disabled = loading;
        sendButton.innerHTML = loading
            ? '<span class="material-symbols-outlined">hourglass_top</span> Pensando'
            : '<span class="material-symbols-outlined">send</span> Enviar';
    };

    ensureThread();
    renderConversation();

    trigger.addEventListener('click', () => setOpen(panel.hidden));
    closeBtn.addEventListener('click', () => setOpen(false));
    overlay.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) setOpen(false);
    });

    document.addEventListener('pointerdown', (event) => {
        if (panel.hidden) return;
        if (root.contains(event.target)) return;
        setOpen(false);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            input.value = chip.dataset.aiPrompt || '';
            setOpen(true);
            input.focus();
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (isLoading) return;

        const prompt = input.value.trim();
        if (!prompt) {
            input.focus();
            return;
        }

        const historyForApi = conversation.slice(-10);
        conversation.push({ role: 'user', text: prompt });
        persistConversation();
        appendMessage(prompt, 'user');
        input.value = '';
        setLoading(true);
        const pending = appendThinking();

        try {
            const response = await fetch('/gemini', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    mensaje: prompt,
                    contexto: `${context}. Usuario actual: ${currentUser}`,
                    historial: historyForApi,
                }),
            });

            const payload = await response.json().catch(() => null);
            let text = response.ok
                ? (payload?.message || 'No hubo respuesta util.')
                : (payload?.error || payload?.message || 'No hubo respuesta util.');

            if (response.status === 419) {
                text = 'La sesion expiro. Recarga la pagina para seguir usando Deskcir AI.';
            } else if (response.status === 429) {
                text = payload?.message || 'Llegaste al limite temporal del asistente. Intenta de nuevo en un minuto.';
            }

            pending.classList.remove('is-thinking');
            pending.classList.toggle('is-error', !response.ok);
            pending.querySelector('.deskcir-ai__message-body').innerHTML = formatAiText(text);

            if (response.ok) {
                conversation.push({ role: 'model', text });
                persistConversation();
            }
        } catch (error) {
            pending.classList.remove('is-thinking');
            pending.classList.add('is-error');
            pending.querySelector('.deskcir-ai__message-body').innerHTML = '<p>No pude conectar con Gemini en este momento.</p>';
        } finally {
            setLoading(false);
            input.focus();
            messages.scrollTop = messages.scrollHeight;
        }
    });
})();
</script>
