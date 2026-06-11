/* ============================================================
   DiabetesRisk — main.js
   Global JS utilities: toast, sidebar, tooltips
   ============================================================ */

// ── Toast Notification ─────────────────────────────────────
function showToast(message, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        document.body.appendChild(container);
    }

    const iconMap = {
        success: 'check-circle',
        error:   'alert-circle',
        info:    'info',
        warning: 'alert-triangle',
    };

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `
        <i data-feather="${iconMap[type] || 'info'}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);
    if (typeof feather !== 'undefined') feather.replace();

    // Auto dismiss after 3.5 seconds
    setTimeout(() => {
        toast.classList.add('hiding');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }, 3500);
}

// ── Form validation helper ─────────────────────────────────
function highlightErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    let valid = true;
    form.querySelectorAll('input[required]').forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('input-error');
            valid = false;
        } else {
            input.classList.remove('input-error');
        }
    });
    return valid;
}

// ── Sidebar overlay close (mobile) ────────────────────────
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    if (
        sidebar &&
        sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) &&
        toggle && !toggle.contains(e.target)
    ) {
        sidebar.classList.remove('open');
    }
});

// ── Remove input-error on typing ──────────────────────────
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-error') && e.target.value.trim()) {
        e.target.classList.remove('input-error');
    }
});
