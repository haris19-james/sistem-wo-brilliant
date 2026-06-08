/**
 * Booking-context chat workspace (Admin & Korlap).
 */
(function () {
    const root = document.getElementById('bookingChatWorkspace');
    if (!root) return;

    const csrf = root.dataset.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
    const sendBase = root.dataset.sendBase || '';

    const messagesBox = document.getElementById('chatMessagesBox');
    if (messagesBox) {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    document.getElementById('chatThreadSearch')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.chat-thread-item').forEach((el) => {
            const hay = el.dataset.search || '';
            el.style.display = hay.includes(term) ? '' : 'none';
        });
    });

    document.getElementById('chatSendForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const pesananId = this.dataset.pesananId;
        const input = document.getElementById('chatMessageInput');
        const pesan = input?.value?.trim();
        const payload = { pesan };
        const btn = this.querySelector('button[type="submit"]');
        console.log('[BookingChat] sending payload', payload, 'pesananId', pesananId);

        if (!pesan || !pesananId) {
            if (btn) btn.disabled = false;
            return alert('Pesan tidak boleh kosong. Silakan isi pesan terlebih dahulu.');
        }

        if (btn) btn.disabled = true;

        try {
            const resp = await fetch(`${sendBase}/${pesananId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });
            const data = await resp.json().catch(() => ({}));
            console.log('[BookingChat] response', resp.status, data);

            if (!resp.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengirim pesan, silakan coba lagi.');
            }

            appendMessage(data.data.text, data.data.time, 'sent');
            input.value = '';
            input.focus();
        } catch (err) {
            const message = err.message && !/pesan/i.test(err.message)
                ? err.message
                : 'Gagal mengirim pesan, silakan coba lagi.';
            alert(message);
        } finally {
            if (btn) btn.disabled = false;
        }
    });

    document.getElementById('internalNoteForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const pesananId = this.dataset.pesananId;
        const textarea = this.querySelector('textarea[name="catatan"]');
        const catatan = textarea?.value?.trim();
        if (!catatan || !pesananId) return;

        const btn = this.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        try {
            const resp = await fetch(`${sendBase}/${pesananId}/internal-note`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ catatan }),
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.message || 'Gagal menyimpan');

            const list = document.getElementById('internalNotesList');
            if (list && data.note) {
                const el = document.createElement('div');
                el.className = 'bg-white/90 rounded-lg p-2 border border-amber-100 text-xs';
                el.innerHTML = `<p class="text-gray-800">${escapeHtml(data.note.catatan)}</p><p class="text-[10px] text-gray-500 mt-1">${escapeHtml(data.note.author)} · ${escapeHtml(data.note.time)}</p>`;
                list.prepend(el);
            }
            textarea.value = '';
        } catch (err) {
            alert(err.message || 'Gagal menyimpan catatan');
        } finally {
            if (btn) btn.disabled = false;
        }
    });

    function appendMessage(text, time, type) {
        if (!messagesBox) return;
        const wrap = document.createElement('div');
        wrap.className = `flex ${type === 'sent' ? 'justify-end' : 'justify-start'}`;
        wrap.innerHTML = `
            <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm ${type === 'sent' ? 'bg-green-600 text-white rounded-br-md' : 'bg-white border border-gray-200 rounded-bl-md'}">
                <p class="whitespace-pre-wrap leading-relaxed">${escapeHtml(text)}</p>
                <p class="text-[10px] mt-1 ${type === 'sent' ? 'text-green-100' : 'text-gray-400'}">${escapeHtml(time)}</p>
            </div>`;
        messagesBox.appendChild(wrap);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
})();
