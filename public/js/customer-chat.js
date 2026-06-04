/**
 * Client booking chat — AJAX send tanpa full-page loading overlay.
 */
(function () {
    const box = document.getElementById('customerChatMessages');
    const form = document.getElementById('customerChatForm');
    const input = document.getElementById('customerChatInput');
    const submitBtn = document.getElementById('customerChatSubmit');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (box) {
        box.scrollTop = box.scrollHeight;
    }

    if (input) {
        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form?.requestSubmit();
            }
        });
    }

    function hideGlobalLoading() {
        if (window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
            window.brilliantNavLoading.hide();
        }
        if (typeof window.hideLoading === 'function') {
            window.hideLoading();
        }
        if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
            window.loadingOverlay.hide();
        }
    }

    function setSending(isSending) {
        if (submitBtn) {
            submitBtn.disabled = isSending;
            submitBtn.textContent = isSending ? '...' : 'Kirim';
        }
        if (input) {
            input.disabled = isSending;
        }
    }

    function appendSentMessage(text, time) {
        if (!box) return;

        const empty = box.querySelector('p.text-center.text-gray-400');
        if (empty) {
            empty.remove();
        }

        const wrap = document.createElement('div');
        wrap.className = 'flex justify-end';

        const inner = document.createElement('div');
        inner.className = 'max-w-[88%] sm:max-w-[75%]';

        const label = document.createElement('p');
        label.className = 'text-[10px] font-semibold mb-1 text-right text-green-700';
        label.textContent = 'Anda';

        const bubble = document.createElement('div');
        bubble.className =
            'px-4 py-2.5 rounded-2xl text-sm shadow-sm bg-green-600 text-white rounded-br-md';

        const body = document.createElement('p');
        body.className = 'whitespace-pre-wrap leading-relaxed';
        body.textContent = text;

        const meta = document.createElement('div');
        meta.className = 'flex items-center justify-end gap-1 mt-1.5 text-green-100';

        const timeSpan = document.createElement('span');
        timeSpan.className = 'text-[10px]';
        timeSpan.dataset.chatTime = '1';
        timeSpan.textContent = time;

        meta.appendChild(timeSpan);
        meta.innerHTML +=
            '<span class="inline-flex" title="Terkirim"><svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span>';

        bubble.appendChild(body);
        bubble.appendChild(meta);
        inner.appendChild(label);
        inner.appendChild(bubble);
        wrap.appendChild(inner);
        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
    }

    function showError(message) {
        let el = document.getElementById('customerChatError');
        if (!el) {
            el = document.createElement('p');
            el.id = 'customerChatError';
            el.className = 'px-4 pb-2 text-xs text-red-600';
            form?.insertAdjacentElement('beforebegin', el);
        }
        el.textContent = message;
        el.classList.remove('hidden');
    }

    function clearError() {
        const el = document.getElementById('customerChatError');
        if (el) {
            el.textContent = '';
            el.classList.add('hidden');
        }
    }

    async function sendMessage(event) {
        event.preventDefault();
        event.stopPropagation();

        hideGlobalLoading();

        if (!window.fetch || !form?.action) return;

        const pesan = input?.value?.trim();
        if (!pesan) return;

        clearError();
        setSending(true);

        const body = new FormData(form);
        const now = new Date();
        const optimisticTime = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

        appendSentMessage(pesan, optimisticTime);
        const saved = pesan;
        if (input) {
            input.value = '';
            input.style.height = 'auto';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                body,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengirim pesan.');
            }

            const lastBubble = box?.lastElementChild?.querySelector('[data-chat-time]');
            if (lastBubble && data.data?.time) {
                lastBubble.textContent = data.data.time;
            }
        } catch (error) {
            if (box?.lastElementChild) {
                box.lastElementChild.remove();
            }
            if (input) {
                input.value = saved;
                input.style.height = 'auto';
            }
            showError(error.message || 'Gagal mengirim pesan. Coba lagi.');
        } finally {
            setSending(false);
            hideGlobalLoading();
        }
    }

    if (form) {
        form.addEventListener('submit', sendMessage, true);
    }
})();
