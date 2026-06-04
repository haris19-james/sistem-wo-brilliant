// resources/js/jadwal-interactive.js

/**
 * Interactive Jadwal (Schedule) Panel
 * Enables real-time rundown detail update without full page reload
 * 
 * Usage: Add data-pesanan-id to clickable elements in pesanan list
 * Example: <div class="cursor-pointer" data-pesanan-id="{{ $pesanan->id }}">
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeJadwalInteractive();
});

function initializeJadwalInteractive() {
    // Get all pesanan list items
    const pesananItems = document.querySelectorAll('[data-pesanan-id]');

    pesananItems.forEach(item => {
        item.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const pesananId = item.dataset.pesananId;
            
            // Add active state
            pesananItems.forEach(el => el.classList.remove('active', 'ring-2', 'ring-blue-400'));
            item.classList.add('active', 'ring-2', 'ring-blue-400');

            // Fetch rundown detail
            await loadRundownDetail(pesananId);
        });
    });

    // Load first pesanan by default
    if (pesananItems.length > 0) {
        pesananItems[0].classList.add('active', 'ring-2', 'ring-blue-400');
        loadRundownDetail(pesananItems[0].dataset.pesananId);
    }
}

/**
 * Load rundown detail via AJAX
 */
async function loadRundownDetail(pesananId) {
    try {
        const response = await fetch(`/lapangan/jadwal/rundown/${pesananId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            updateRundownPanel(data);
            animatePanelUpdate();
        }
    } catch (error) {
        console.error('Error loading rundown detail:', error);
        showErrorNotification('Gagal memuat detail rundown');
    }
}

/**
 * Update the rundown panel on the right side
 */
function updateRundownPanel(data) {
    const panel = document.querySelector('[data-rundown-panel]');
    if (!panel) return;

    const pesanan = data.pesanan;
    const rundowns = data.rundowns;
    const progress = data.progress;

    // Update header
    const header = panel.querySelector('[data-panel-header]');
    if (header) {
        header.innerHTML = `
            <div>
                <h3 class="text-lg font-semibold text-gray-900">${pesanan.nama_pasangan}</h3>
                <p class="text-sm text-gray-600">${pesanan.tanggal_acara}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">${pesanan.lokasi}</p>
                <p class="text-xs text-gray-500">Tema: ${pesanan.tema}</p>
            </div>
        `;
    }

    // Update progress bar
    const progressBar = panel.querySelector('[data-progress-bar]');
    if (progressBar) {
        progressBar.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress Persiapan</span>
                <span class="text-sm font-semibold text-blue-600">${progress}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-300 ease-out"
                     style="width: ${progress}%"></div>
            </div>
        `;
    }

    // Update rundown list
    const rundownList = panel.querySelector('[data-rundown-list]');
    if (rundownList) {
        rundownList.innerHTML = rundowns.map(rundown => `
            <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 last:border-b-0">
                <div class="flex-shrink-0">
                    <div class="${rundown.status_badge_class} p-2 rounded-lg">
                        <svg class="w-4 h-4 ${rundown.icon_class}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${rundown.kegiatan}</p>
                    <p class="text-xs text-gray-600">Kategori: ${rundown.kategori}</p>
                    <div class="mt-2 flex items-center space-x-2">
                        <time class="text-xs font-medium text-gray-700">${rundown.waktu_mulai}</time>
                        ${rundown.waktu_selesai ? `<span class="text-gray-400">→</span><time class="text-xs font-medium text-gray-700">${rundown.waktu_selesai}</time>` : ''}
                    </div>
                    <span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded ${rundown.status_badge_class}">
                        ${rundown.status_label}
                    </span>
                </div>
            </div>
        `).join('');
    }
}

/**
 * Animate panel update with fade-in effect
 */
function animatePanelUpdate() {
    const panel = document.querySelector('[data-rundown-panel]');
    if (!panel) return;

    panel.style.opacity = '0.5';
    panel.style.transition = 'opacity 0.2s ease-in-out';

    setTimeout(() => {
        panel.style.opacity = '1';
    }, 100);
}

/**
 * Show error notification
 */
function showErrorNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
    notification.textContent = message;
    
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

export { initializeJadwalInteractive, loadRundownDetail };
