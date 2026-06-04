// resources/js/kanban-checklist.js

/**
 * Kanban Checklist Handler
 * Handles real-time checklist updates with progress calculation
 * 
 * Usage: Add data-task-id to task cards and data-checklist-checkbox to checkboxes
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeChecklistHandlers();
    initializeHeaderSync();
});

/**
 * Initialize checklist checkbox handlers
 */
function initializeChecklistHandlers() {
    const checkboxes = document.querySelectorAll('[data-checklist-checkbox]');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', async (e) => {
            const checklistId = checkbox.dataset.checklistId;
            const tugasId = checkbox.dataset.tugasId;
            const isCompleted = checkbox.checked;

            // Disable checkbox during update
            checkbox.disabled = true;

            try {
                await updateChecklistStatus(tugasId, checklistId, isCompleted);
                updateProgressDisplay(tugasId);
            } catch (error) {
                console.error('Error updating checklist:', error);
                checkbox.checked = !isCompleted; // Revert on error
                showNotification('Gagal memperbarui checklist', 'error');
            } finally {
                checkbox.disabled = false;
            }
        });
    });
}

/**
 * Update checklist status via AJAX
 */
async function updateChecklistStatus(tugasId, checklistId, isCompleted) {
    const response = await fetch(`/lapangan/tugas/${tugasId}/checklist/${checklistId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            is_completed: isCompleted,
        }),
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (!data.success) {
        throw new Error(data.message || 'Unknown error');
    }

    return data;
}

/**
 * Update progress display for a task card
 */
function updateProgressDisplay(tugasId) {
    const taskCard = document.querySelector(`[data-task-id="${tugasId}"]`);
    if (!taskCard) return;

    // Count total and completed checklists
    const allCheckboxes = taskCard.querySelectorAll('[data-checklist-checkbox]');
    const completedCheckboxes = Array.from(allCheckboxes).filter(cb => cb.checked);

    const progressPercent = allCheckboxes.length > 0
        ? Math.round((completedCheckboxes.length / allCheckboxes.length) * 100)
        : 0;

    // Update progress bar
    const progressBar = taskCard.querySelector('[data-progress-bar]');
    if (progressBar) {
        const barFill = progressBar.querySelector('.progress-fill');
        if (barFill) {
            barFill.style.width = `${progressPercent}%`;
        }
    }

    // Update progress text
    const progressText = taskCard.querySelector('[data-progress-text]');
    if (progressText) {
        progressText.textContent = `${progressPercent}%`;
    }

    // Update status badge if 100%
    if (progressPercent === 100) {
        updateTaskStatus(tugasId, 'completed', taskCard);
    }

    // Show completion animation
    if (completedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
        animateTaskCompletion(taskCard);
    }
}

/**
 * Update task status via AJAX
 */
async function updateTaskStatus(tugasId, status, taskCard) {
    try {
        const response = await fetch(`/lapangan/tugas/${tugasId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status }),
        });

        if (response.ok) {
            const data = await response.json();
            
            // Update status badge
            const statusBadge = taskCard.querySelector('[data-status-badge]');
            if (statusBadge) {
                statusBadge.textContent = 'Selesai';
                statusBadge.className = 'inline-block px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800';
            }

            showNotification('Tugas selesai! 🎉', 'success');
        }
    } catch (error) {
        console.error('Error updating task status:', error);
    }
}

/**
 * Animate task completion
 */
function animateTaskCompletion(taskCard) {
    taskCard.classList.add('ring-2', 'ring-green-400', 'bg-green-50');

    setTimeout(() => {
        taskCard.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
    }, 2000);
}

/**
 * Initialize real-time header sync
 * Syncs user profile from settings updates
 */
function initializeHeaderSync() {
    // Check for profile updates every 10 seconds
    setInterval(async () => {
        try {
            const response = await fetch('/lapangan/api/user-profile');
            if (response.ok) {
                const userProfile = await response.json();
                updateHeaderProfile(userProfile);
            }
        } catch (error) {
            console.error('Error syncing header:', error);
        }
    }, 10000);
}

/**
 * Update header with user profile data
 */
function updateHeaderProfile(userProfile) {
    // Update user name in header
    const nameElements = document.querySelectorAll('[data-header-user-name]');
    nameElements.forEach(el => {
        if (el.textContent !== userProfile.name) {
            el.textContent = userProfile.name;
        }
    });

    // Update user avatar
    const avatarElements = document.querySelectorAll('[data-header-user-avatar]');
    avatarElements.forEach(el => {
        if (el.src !== userProfile.avatar_url) {
            el.src = userProfile.avatar_url;
        }
    });

    // Update email if displayed
    const emailElements = document.querySelectorAll('[data-header-user-email]');
    emailElements.forEach(el => {
        if (el.textContent !== userProfile.email) {
            el.textContent = userProfile.email;
        }
    });
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
                    type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                    'bg-blue-100 border-blue-400 text-blue-700';

    notification.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded shadow-lg`;
    notification.textContent = message;
    
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

export { 
    initializeChecklistHandlers, 
    updateChecklistStatus, 
    updateProgressDisplay,
    initializeHeaderSync 
};
