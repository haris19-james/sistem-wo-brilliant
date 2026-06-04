/**
 * Input nominal Rupiah (format ID: pemisah ribuan titik).
 * - Tampilan: 1000000 → 1.000.000
 * - Submit: hanya angka ke server (tanpa titik), tanpa pengali ×1000
 */
(function () {
    'use strict';

    function digitsOnly(str) {
        return String(str || '').replace(/\D/g, '');
    }

    function formatId(digits) {
        if (!digits) {
            return '';
        }
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseDigits(formatted) {
        return digitsOnly(formatted);
    }

    function syncDisplayToHidden(display, hidden) {
        const digits = parseDigits(display.value);
        hidden.value = digits;
        display.value = formatId(digits);
        display.setCustomValidity(digits === '' ? 'Nominal wajib diisi.' : '');
    }

    function initGroup(group) {
        const display = group.querySelector('.rupiah-input-display');
        const hidden = group.querySelector('input[type="hidden"][data-rupiah-value]');
        if (!display || !hidden) {
            return;
        }

        if (hidden.value) {
            display.value = formatId(hidden.value);
        }

        display.addEventListener('input', function () {
            const caretFromEnd = display.value.length - (display.selectionStart ?? display.value.length);
            const digits = parseDigits(display.value);
            hidden.value = digits;
            display.value = formatId(digits);
            const pos = Math.max(0, display.value.length - caretFromEnd);
            display.setSelectionRange(pos, pos);
        });

        display.addEventListener('keydown', function (e) {
            const allowed = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
            if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) {
                return;
            }
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });

        display.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const digits = parseDigits(pasted);
            hidden.value = digits;
            display.value = formatId(digits);
        });

        display.addEventListener('blur', function () {
            syncDisplayToHidden(display, hidden);
        });
    }

    function initAll() {
        document.querySelectorAll('[data-rupiah-input]').forEach(initGroup);
    }

    document.addEventListener('DOMContentLoaded', initAll);
    document.addEventListener('turbo:load', initAll);

    window.RupiahInput = {
        formatId,
        parseDigits,
        digitsOnly,
    };
})();
