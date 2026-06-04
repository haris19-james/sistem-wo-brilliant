// resources/js/dokumentasi-upload.js

/**
 * Dokumentasi Upload Handler
 * Handles file uploads for kendala and dokumentasi photos
 * 
 * Usage: Add data-upload-form with pesanan-id
 * Example: <form data-upload-form data-pesanan-id="{{ $pesanan->id }}">
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeUploadHandlers();
});

/**
 * Initialize upload form handlers
 */
function initializeUploadHandlers() {
    const uploadForms = document.querySelectorAll('[data-upload-form]');

    uploadForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formType = form.dataset.formType || 'dokumentasi';
            const pesananId = form.dataset.pesananId;

            if (!pesananId) {
                showNotification('Pesanan ID tidak ditemukan', 'error');
                return;
            }

            await handleUpload(form, pesananId, formType);
        });

        // Preview image on file select
        const fileInput = form.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                previewImage(e.target, form);
            });
        }
    });
}

/**
 * Handle file upload
 */
async function handleUpload(form, pesananId, formType) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const fileInput = form.querySelector('input[type="file"]');

    if (!fileInput || !fileInput.files.length) {
        showNotification('Pilih file terlebih dahulu', 'error');
        return;
    }

    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (fileInput.files[0].size > maxSize) {
        showNotification('Ukuran file terlalu besar (max 5MB)', 'error');
        return;
    }

    // Create form data
    const formData = new FormData();
    formData.append('pesanan_id', pesananId);
    formData.append('foto', fileInput.files[0]);

    if (formType === 'kendala') {
        const ringkasanInput = form.querySelector('textarea[name="ringkasan"]');
        const kondisiInput = form.querySelector('select[name="kondisi"]');
        formData.append('ringkasan', ringkasanInput?.value || 'Kendala lapangan');
        formData.append('kondisi', kondisiInput?.value || 'Perhatian');
    } else {
        const keteranganInput = form.querySelector('textarea[name="keterangan"]');
        formData.append('keterangan', keteranganInput?.value || 'Dokumentasi lapangan');
    }

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="mr-2">⏳</span>Mengunggah...';

    try {
        const endpoint = formType === 'kendala' 
            ? '/lapangan/laporan/kendala'
            : '/lapangan/laporan/dokumentasi';

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        let data = {};
        const ct = response.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
            try {
                data = await response.json();
            } catch {
                throw new Error('Respons server bukan JSON valid.');
            }
        } else {
            throw new Error('Respons server tidak valid (HTTP ' + response.status + ').');
        }

        if (data.success) {
            showNotification(data.message || 'Berhasil disimpan.', 'success');
            form.reset();
            
            // Add photo to gallery if dokumen
            if (formType === 'dokumentasi' && data.photo) {
                addPhotoToGallery(data.photo);
            }
        } else {
            showNotification(data.message || 'Gagal mengunggah file', 'error');
        }
    } catch (error) {
        console.error('Error uploading file:', error);
        showNotification('Terjadi kesalahan saat mengunggah file', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Unggah Foto';
    }
}

/**
 * Preview image before upload
 */
function previewImage(fileInput, form) {
    const preview = form.querySelector('[data-image-preview]');
    if (!preview) return;

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = (e) => {
            preview.style.display = 'block';
            preview.src = e.target.result;
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

/**
 * Add photo to gallery grid
 */
function addPhotoToGallery(photo) {
    const gallery = document.querySelector('[data-photo-gallery]');
    if (!gallery) return;

    const photoElement = document.createElement('div');
    photoElement.className = 'relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow';
    photoElement.innerHTML = `
        <img src="${photo.url}" alt="${photo.title}" class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-end p-3">
            <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity">
                <p class="text-sm font-medium">${photo.title}</p>
                <p class="text-xs">${photo.time}</p>
            </div>
        </div>
    `;

    // Add to beginning of gallery
    gallery.insertBefore(photoElement, gallery.firstChild);

    // Animate entry
    photoElement.style.opacity = '0';
    photoElement.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        photoElement.style.transition = 'all 0.3s ease-out';
        photoElement.style.opacity = '1';
        photoElement.style.transform = 'scale(1)';
    }, 10);
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
                    type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                    'bg-blue-100 border-blue-400 text-blue-700';

    notification.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded shadow-lg z-50`;
    notification.textContent = message;
    
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

export { initializeUploadHandlers, handleUpload, addPhotoToGallery };
