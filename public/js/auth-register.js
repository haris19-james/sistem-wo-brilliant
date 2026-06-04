/**
 * Pendaftaran akun client — tanpa overlay berat, redirect cepat setelah sukses.
 */
(function () {
    const form = document.getElementById('register-form');
    if (!form) return;

    const submitBtn = document.getElementById('register-submit');
    const submitText = document.getElementById('register-submit-text');
    const submitSpinner = document.getElementById('register-submit-spinner');
    const alertBox = document.getElementById('register-alert');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const dashboardUrl = form.dataset.redirect || '/client/dashboard';

    const fields = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        password: document.getElementById('password'),
        password_confirmation: document.getElementById('password_confirmation'),
    };

    let isLoading = false;

    function hidePageLoaders() {
        if (typeof window.hideLoading === 'function') {
            window.hideLoading();
        }
        if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
            window.loadingOverlay.hide();
        }
        if (window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
            window.brilliantNavLoading.hide();
        }
    }

    function setLoading(loading) {
        isLoading = loading;
        if (submitBtn) submitBtn.disabled = loading;
        if (submitText) submitText.textContent = loading ? 'Memproses...' : 'Daftar';
        if (submitSpinner) submitSpinner.classList.toggle('hidden', !loading);
    }

    function showAlert(message, type = 'error') {
        if (!alertBox) return;
        alertBox.textContent = message;
        alertBox.classList.remove('hidden', 'bg-red-50', 'border-red-100', 'text-red-700', 'bg-green-50', 'border-green-100', 'text-green-800');
        alertBox.classList.add(type === 'success' ? 'bg-green-50 border-green-100 text-green-800' : 'bg-red-50 border-red-100 text-red-700');
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideAlert() {
        if (alertBox) {
            alertBox.classList.add('hidden');
            alertBox.textContent = '';
        }
    }

    function setFieldError(fieldName, message) {
        const input = fields[fieldName];
        const errorEl = document.getElementById(`error-${fieldName}`);
        if (input) {
            input.classList.toggle('border-red-400', Boolean(message));
            input.classList.toggle('ring-1', Boolean(message));
            input.classList.toggle('ring-red-200', Boolean(message));
        }
        if (errorEl) {
            if (message) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            } else {
                errorEl.textContent = '';
                errorEl.classList.add('hidden');
            }
        }
    }

    function clearFieldErrors() {
        Object.keys(fields).forEach((key) => setFieldError(key, ''));
    }

    function isValidEmail(email) {
        const value = (email || '').trim();
        return value.includes('@') && value.includes('.') && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function validateClient() {
        clearFieldErrors();
        hideAlert();

        let valid = true;
        const name = fields.name?.value?.trim() || '';
        const email = fields.email?.value?.trim() || '';
        const password = fields.password?.value || '';
        const passwordConfirmation = fields.password_confirmation?.value || '';

        if (!name) {
            setFieldError('name', 'Nama lengkap wajib diisi.');
            valid = false;
        }

        if (!email) {
            setFieldError('email', 'Email wajib diisi.');
            valid = false;
        } else if (!email.includes('@') || !email.includes('.')) {
            setFieldError('email', 'Format email tidak valid.');
            valid = false;
        } else if (!isValidEmail(email)) {
            setFieldError('email', 'Format email tidak valid.');
            valid = false;
        }

        if (!password) {
            setFieldError('password', 'Kata sandi wajib diisi.');
            valid = false;
        } else if (password.length < 8) {
            setFieldError('password', 'Kata sandi minimal 8 karakter.');
            valid = false;
        }

        if (!passwordConfirmation) {
            setFieldError('password_confirmation', 'Konfirmasi kata sandi wajib diisi.');
            valid = false;
        } else if (password !== passwordConfirmation) {
            setFieldError('password_confirmation', 'Password tidak cocok.');
            valid = false;
        }

        return valid;
    }

    function applyServerErrors(errors) {
        if (!errors || typeof errors !== 'object') return;
        Object.entries(errors).forEach(([field, messages]) => {
            const msg = Array.isArray(messages) ? messages[0] : messages;
            if (fields[field]) setFieldError(field, msg);
        });
    }

    async function parseResponseBody(response) {
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            try {
                return await response.json();
            } catch {
                return {};
            }
        }

        if (response.status === 419) {
            return { success: false, message: 'Sesi habis. Muat ulang halaman lalu coba lagi.' };
        }

        return { success: false, message: `Server mengembalikan status ${response.status}.` };
    }

    function extractErrorMessage(data, status) {
        if (data?.message) return data.message;

        if (data?.errors && typeof data.errors === 'object') {
            const first = Object.values(data.errors).flat()[0];
            if (first) return first;
        }

        if (status === 422) return 'Data tidak valid. Periksa email dan kata sandi.';
        if (status >= 500) return 'Terjadi kesalahan server. Coba lagi nanti.';

        return 'Gagal mendaftar. Silakan coba lagi.';
    }

    function handleSuccess(data) {
        hidePageLoaders();
        sessionStorage.setItem('brilliantFastEntry', '1');
        window.location.replace(data.redirect || dashboardUrl);
    }

    async function submitRegister(event) {
        event.preventDefault();
        if (isLoading) return;

        if (!validateClient()) {
            showAlert('Periksa kembali data yang Anda isi.');
            return;
        }

        hidePageLoaders();
        setLoading(true);
        hideAlert();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: new FormData(form),
            });

            const data = await parseResponseBody(response);

            if (!response.ok || data.success === false) {
                if (data.errors) applyServerErrors(data.errors);
                showAlert(extractErrorMessage(data, response.status));
                setLoading(false);
                return;
            }

            if (data.success === true || response.ok) {
                handleSuccess(data);
                return;
            }

            showAlert('Respons server tidak dikenali.');
            setLoading(false);
        } catch {
            showAlert('Koneksi gagal. Coba lagi.');
            setLoading(false);
        }
    }

    form.addEventListener('submit', submitRegister);

    Object.values(fields).forEach((input) => {
        input?.addEventListener('input', () => {
            if (input.id) setFieldError(input.id, '');
        });
    });
})();
