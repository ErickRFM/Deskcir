@extends('layouts.app')

@section('title', 'Deskcir AI')

@section('content')
@php
    $prefill = trim((string) request('prompt', ''));
@endphp

<div class="deskcir-ai-page"
     id="deskcirAiPage"
     data-authenticated="{{ auth()->check() ? '1' : '0' }}"
     data-user="{{ auth()->user()->name ?? 'Invitado' }}"
     data-threads-key="deskcir-ai-threads-user-{{ auth()->id() ?? 'guest' }}"
     data-active-key="deskcir-ai-active-user-{{ auth()->id() ?? 'guest' }}"
     data-prefill="{{ $prefill }}">
    <section class="deskcir-ai-page__hero card border-0 overflow-hidden mb-4">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="deskcir-ai-page__glow"></div>
            <div class="row g-4 align-items-center position-relative">
                <div class="col-lg-8">
                    <p class="deskcir-ai-page__eyebrow mb-2">Deskcir AI</p>
                    <h1 class="deskcir-ai-page__title mb-3">Asistente completo para soporte, seguimiento y respuestas mas precisas.</h1>
                    <p class="deskcir-ai-page__subtitle mb-0">
                        {{ auth()->check() ? 'Guarda hasta 5 conversaciones, sigue el hilo activo y decide mejor si te conviene resolver con IA o abrir soporte.' : 'Puedes usar la IA sin iniciar sesion. Si despues quieres guardar conversaciones o enviar ticket, te pediremos crear tu cuenta.' }}
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="deskcir-ai-page__hero-card">
                        <p class="mb-2 fw-bold">Atajos utiles</p>
                        <div class="deskcir-ai-page__hero-links">
                            <a href="/support/create?mode=presencial" class="deskcir-ai-page__mini-link">Solicitar soporte presencial</a>
                            <a href="/support/create" class="deskcir-ai-page__mini-link">Crear ticket o continuar borrador</a>
                            <a href="/store" class="deskcir-ai-page__mini-link">Volver a la tienda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-xl-3">
            <section class="deskcir-ai-side card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <p class="deskcir-ai__eyebrow mb-2">Conversaciones</p>
                    <h3 class="deskcir-ai-side__title">{{ auth()->check() ? 'Tus chats guardados' : 'Modo temporal' }}</h3>
                    <p class="deskcir-ai-page__chat-note mb-3">
                        {{ auth()->check() ? 'Puedes crear, borrar y retomar hasta 5 hilos. Para mas hilos, activa Deskcir AI Plus por 80 MXN al mes.' : 'Sin login el chat no se guarda entre vistas. Si quieres memoria persistente, inicia sesion.' }}
                    </p>

                    @auth
                        <div class="deskcir-ai-threads__actions mb-3">
                            <button type="button" class="btn btn-deskcir w-100" id="deskcirAiNewThread">Nuevo chat</button>
                        </div>
                        <div id="deskcirAiThreadList" class="deskcir-ai-threads"></div>
                        <div class="deskcir-ai-upgrade mt-3" id="deskcirAiUpgradeCard" hidden>
                            <strong>Deskcir AI Plus</strong>
                            <p class="mb-3">Guarda mas de 5 chats y separa casos por cliente, ticket o cotizacion.</p>
                            <button type="button" class="btn btn-outline-deskcir w-100" data-bs-toggle="modal" data-bs-target="#deskcirAiUpgradeModal">Ver plan de 80 MXN</button>
                        </div>
                    @else
                        <div class="deskcir-ai-guest-note">
                            <strong>Chat libre sin login</strong>
                            <p class="mb-0">Puedes consultar, comparar y pedir mensajes mejor redactados. La conversacion se limpia al salir o cambiar de vista.</p>
                        </div>
                    @endauth
                </div>
            </section>

            <section class="deskcir-ai-side card border-0 shadow-sm">
                <div class="card-body p-4">
                    <p class="deskcir-ai__eyebrow mb-2">Flujos de soporte</p>
                    <h3 class="deskcir-ai-side__title">Elige como continuar</h3>
                    <div class="deskcir-ai-side__stack">
                        <a href="{{ route('deskcir.ai', ['prompt' => 'Necesito ayuda para evaluar un caso antes de abrir un ticket.']) }}" class="deskcir-ai-side__action">
                            <span class="material-symbols-outlined">smart_toy</span>
                            <div>
                                <strong>Consultar a la IA</strong>
                                <span>Recibe una guia inicial antes de abrir soporte.</span>
                            </div>
                        </a>
                        <a href="/support/create?mode=presencial" class="deskcir-ai-side__action">
                            <span class="material-symbols-outlined">home_repair_service</span>
                            <div>
                                <strong>Solicitar soporte presencial</strong>
                                <span>Registra una visita o recepcion de equipo.</span>
                            </div>
                        </a>
                        <a href="/support/create" class="deskcir-ai-side__action">
                            <span class="material-symbols-outlined">confirmation_number</span>
                            <div>
                                <strong>Crear ticket</strong>
                                <span>Abre un caso normal para seguimiento tecnico.</span>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-9">
            <section class="deskcir-ai-page__chat card border-0 shadow-sm">
                <div class="deskcir-ai-page__chat-head deskcir-ai-page__chat-head--split">
                    <div>
                        <p class="deskcir-ai__eyebrow mb-1">Vista completa</p>
                        <h2 class="deskcir-ai-page__chat-title mb-1">Chat con Deskcir AI</h2>
                        <p class="deskcir-ai-page__chat-note mb-0">Enter envia el mensaje. Shift + Enter agrega otra linea.</p>
                    </div>
                    <div class="deskcir-ai-page__thread-pill" id="deskcirAiCurrentThreadPill">Chat actual</div>
                </div>

                <div class="deskcir-ai-page__messages" id="deskcirAiPageMessages"></div>

                <form id="deskcirAiPageForm" class="deskcir-ai-page__form">
                    @csrf
                    <label class="deskcir-ai__label" for="deskcirAiPageInput">Tu consulta</label>
                    <textarea id="deskcirAiPageInput" class="deskcir-ai__input deskcir-ai-page__input" rows="5" placeholder="Ej. Mi equipo ya no enciende, ayudame a decidir si primero intento soporte remoto o abro ticket presencial."></textarea>
                    <div class="deskcir-ai-page__actions">
                        <div class="deskcir-ai-page__chips">
                            <button type="button" class="deskcir-ai__chip" data-page-prompt="Resume este caso y dime cual seria la siguiente accion recomendada.">Resumen</button>
                            <button type="button" class="deskcir-ai__chip" data-page-prompt="Compara las opciones que ya mencionamos y recomiendame la mejor con una razon clara.">Comparar</button>
                            <button type="button" class="deskcir-ai__chip" data-page-prompt="Haz la respuesta mas precisa, mas entendible y un poco mas detallada.">Mejorar</button>
                        </div>
                        <button type="submit" class="deskcir-ai__send" id="deskcirAiPageSend">
                            <span class="material-symbols-outlined">send</span>
                            Enviar
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

@auth
<div class="modal fade" id="deskcirAiUpgradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content deskcir-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Deskcir AI Plus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Activa un plan de 80 MXN al mes para guardar mas de 5 chats y organizar mas casos a la vez.</p>
                <ul class="deskcir-ai-side__tips ps-3 mb-0">
                    <li>Mas conversaciones guardadas por cliente o tema.</li>
                    <li>Mejor continuidad entre casos.</li>
                    <li>Base lista para conectar pago mas adelante.</li>
                </ul>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ahora no</button>
                <button type="button" class="btn btn-deskcir" id="deskcirAiUpgradeButton">Solicitar AI Plus</button>
            </div>
        </div>
    </div>
</div>
@endauth
@endsection

@push('scripts')
<script>
(function () {
    const page = document.getElementById('deskcirAiPage');
    const form = document.getElementById('deskcirAiPageForm');
    if (!page || !form) return;

    const input = document.getElementById('deskcirAiPageInput');
    const messages = document.getElementById('deskcirAiPageMessages');
    const chips = page.querySelectorAll('[data-page-prompt]');
    const sendButton = document.getElementById('deskcirAiPageSend');
    const threadList = document.getElementById('deskcirAiThreadList');
    const newThreadButton = document.getElementById('deskcirAiNewThread');
    const upgradeCard = document.getElementById('deskcirAiUpgradeCard');
    const currentThreadPill = document.getElementById('deskcirAiCurrentThreadPill');
    const upgradeButton = document.getElementById('deskcirAiUpgradeButton');
    const isAuthenticated = page.dataset.authenticated === '1';
    const currentUser = page.dataset.user || 'Invitado';
    const threadsKey = page.dataset.threadsKey;
    const activeKey = page.dataset.activeKey;
    const prefill = page.dataset.prefill || '';
    const context = document.title || 'Deskcir AI';
    const maxFreeThreads = 5;
    let isLoading = false;
    let conversation = [];

    const seedMessage = {
        role: 'model',
        text: isAuthenticated
            ? 'Puedo seguir el hilo activo, comparar respuestas y ayudarte a decidir si conviene abrir soporte.'
            : 'Puedes usar este chat sin iniciar sesion. Si luego quieres guardar conversaciones, te pediremos crear cuenta.'
    };

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

    const titleFromMessages = (messages) => {
        const userMessage = (messages || []).find((item) => item.role === 'user' && item.text);
        if (!userMessage) return 'Nuevo chat';
        return userMessage.text.trim().slice(0, 38) || 'Nuevo chat';
    };

    const readThreads = () => {
        if (!isAuthenticated) return [];
        try {
            const parsed = JSON.parse(localStorage.getItem(threadsKey) || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    };

    const writeThreads = (threads) => {
        if (!isAuthenticated) return;
        localStorage.setItem(threadsKey, JSON.stringify(threads));
    };

    const getActiveThreadId = () => localStorage.getItem(activeKey);
    const setActiveThreadId = (id) => localStorage.setItem(activeKey, id);

    const ensureInitialThread = () => {
        if (!isAuthenticated) {
            conversation = [seedMessage];
            currentThreadPill.textContent = 'Chat temporal';
            return;
        }

        const threads = readThreads();
        let activeId = getActiveThreadId();
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
            setActiveThreadId(activeThread.id);
        }

        conversation = Array.isArray(activeThread.messages) && activeThread.messages.length
            ? activeThread.messages.slice(-12)
            : [seedMessage];
        currentThreadPill.textContent = activeThread.title || 'Chat actual';
    };

    const persistConversation = () => {
        if (!isAuthenticated) return;
        const activeId = getActiveThreadId();
        const threads = readThreads().map((thread) => {
            if (thread.id !== activeId) return thread;
            const nextMessages = conversation.slice(-12);
            return {
                ...thread,
                title: titleFromMessages(nextMessages),
                updatedAt: Date.now(),
                messages: nextMessages,
            };
        });
        writeThreads(threads);
        renderThreadList();
    };

    const appendMessage = (text, type, extraClass = '') => {
        const article = document.createElement('article');
        article.className = `deskcir-ai__message ${type === 'user' ? 'is-user' : 'is-ai'} ${extraClass}`.trim();
        article.innerHTML = `<div class="deskcir-ai__message-body">${type === 'user' ? `<p>${escapeHtml(text)}</p>` : formatAiText(text)}</div>`;
        messages.appendChild(article);
        messages.scrollTop = messages.scrollHeight;
        return article;
    };

    const renderConversation = () => {
        messages.innerHTML = '';
        conversation.forEach((item) => appendMessage(item.text, item.role === 'user' ? 'user' : 'ai'));
    };

    const renderThreadList = () => {
        if (!isAuthenticated || !threadList) return;

        const threads = readThreads().sort((a, b) => (b.updatedAt || 0) - (a.updatedAt || 0));
        const activeId = getActiveThreadId();
        threadList.innerHTML = '';

        threads.forEach((thread) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `deskcir-ai-thread ${thread.id === activeId ? 'is-active' : ''}`;
            button.innerHTML = `
                <span class="deskcir-ai-thread__main">
                    <strong>${escapeHtml(thread.title || 'Chat')}</strong>
                    <small>${new Date(thread.updatedAt || Date.now()).toLocaleString('es-MX', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })}</small>
                </span>
                <span class="deskcir-ai-thread__actions">
                    <span class="material-symbols-outlined" data-thread-delete="${thread.id}">delete</span>
                </span>
            `;

            button.addEventListener('click', (event) => {
                const deleteTarget = event.target.closest('[data-thread-delete]');
                if (deleteTarget) {
                    deleteThread(deleteTarget.dataset.threadDelete);
                    return;
                }
                setActiveThread(thread.id);
            });

            threadList.appendChild(button);
        });

        if (upgradeCard) {
            upgradeCard.hidden = threads.length < maxFreeThreads;
        }
    };

    const setActiveThread = (threadId) => {
        if (!isAuthenticated) return;
        const threads = readThreads();
        const thread = threads.find((item) => item.id === threadId);
        if (!thread) return;
        setActiveThreadId(thread.id);
        conversation = Array.isArray(thread.messages) && thread.messages.length ? thread.messages.slice(-12) : [seedMessage];
        currentThreadPill.textContent = thread.title || 'Chat actual';
        renderConversation();
        renderThreadList();
        input.focus();
    };

    const deleteThread = (threadId) => {
        if (!isAuthenticated) return;
        const threads = readThreads();
        if (threads.length <= 1) {
            conversation = [seedMessage];
            writeThreads([{ id: threads[0].id, title: 'Chat 1', updatedAt: Date.now(), messages: [seedMessage] }]);
            setActiveThreadId(threads[0].id);
            renderConversation();
            renderThreadList();
            return;
        }

        const nextThreads = threads.filter((thread) => thread.id !== threadId);
        writeThreads(nextThreads);

        if (getActiveThreadId() === threadId) {
            setActiveThreadId(nextThreads[0].id);
            conversation = nextThreads[0].messages.length ? nextThreads[0].messages.slice(-12) : [seedMessage];
            currentThreadPill.textContent = nextThreads[0].title || 'Chat actual';
            renderConversation();
        }

        renderThreadList();
    };

    const createThread = () => {
        if (!isAuthenticated) return;
        const threads = readThreads();
        if (threads.length >= maxFreeThreads) {
            if (upgradeCard) upgradeCard.hidden = false;
            const modal = document.getElementById('deskcirAiUpgradeModal');
            if (modal && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
            }
            return;
        }

        const nextNumber = threads.length + 1;
        const newThread = {
            id: makeThreadId(),
            title: `Chat ${nextNumber}`,
            updatedAt: Date.now(),
            messages: [seedMessage],
        };

        threads.unshift(newThread);
        writeThreads(threads);
        setActiveThreadId(newThread.id);
        conversation = [seedMessage];
        currentThreadPill.textContent = newThread.title;
        renderConversation();
        renderThreadList();
        input.focus();
    };

    const setLoading = (loading) => {
        isLoading = loading;
        input.disabled = loading;
        chips.forEach((chip) => chip.disabled = loading);
        if (newThreadButton) newThreadButton.disabled = loading;
        sendButton.disabled = loading;
        sendButton.innerHTML = loading
            ? '<span class="material-symbols-outlined">hourglass_top</span> Pensando'
            : '<span class="material-symbols-outlined">send</span> Enviar';
    };

    ensureInitialThread();
    renderConversation();
    renderThreadList();

    if (prefill) {
        input.value = prefill;
    }

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            input.value = chip.dataset.pagePrompt || '';
            input.focus();
        });
    });

    if (newThreadButton) {
        newThreadButton.addEventListener('click', createThread);
    }

    if (upgradeButton) {
        upgradeButton.addEventListener('click', () => {
            alert('La base del plan ya quedo lista. Falta conectar el cobro real para activar Deskcir AI Plus.');
        });
    }

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
        renderConversation();
        input.value = '';
        setLoading(true);
        const pending = appendMessage('Deskcir AI esta afinando una respuesta mas util...', 'ai', 'is-thinking');

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
                currentThreadPill.textContent = isAuthenticated ? titleFromMessages(conversation) : 'Chat temporal';
            }
        } catch (error) {
            pending.classList.remove('is-thinking');
            pending.classList.add('is-error');
            pending.querySelector('.deskcir-ai__message-body').innerHTML = '<p>No pude conectar con Gemini en este momento.</p>';
        } finally {
            setLoading(false);
            messages.scrollTop = messages.scrollHeight;
            input.focus();
        }
    });
})();
</script>
@endpush

