# Frontend Integration Guide - Lapangan AJAX API

Complete guide for integrating Kanban and reporting features with JavaScript/Fetch API.

## 📌 Table of Contents

1. [Setup & Security](#setup--security)
2. [Kanban Integration](#kanban-integration)
3. [Reporting Integration](#reporting-integration)
4. [Error Handling](#error-handling)
5. [Code Examples](#code-examples)

---

## Setup & Security

### CSRF Token in Fetch Requests

All POST/PATCH requests require CSRF token in header:

```javascript
// Get CSRF token from meta tag
const getCsrfToken = () => {
  return document.querySelector('meta[name="csrf-token"]')?.content || 
         document.querySelector('[name="_token"]')?.value;
};

// Use in fetch
const response = await fetch(url, {
  method: 'PATCH',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': getCsrfToken()
  },
  body: JSON.stringify(data)
});
```

### API Base URL

All endpoints use prefix: `/lapangan/`

Examples:
- `GET /lapangan/tugas/1/detail`
- `PATCH /lapangan/tugas/1/status`
- `GET /lapangan/laporan/metrics`

---

## Kanban Integration

### 1. Toggle Checklist Item

**Endpoint**: `PATCH /lapangan/tugas/{tugas_id}/checklists/{checklist_id}`

**HTML**:
```html
<div class="checklist-item">
  <input type="checkbox" 
         id="checklist-{{ $c->id }}"
         class="toggle-checklist"
         data-tugas-id="{{ $task->id }}"
         data-checklist-id="{{ $c->id }}"
         {{ $c->is_completed ? 'checked' : '' }}>
  <label for="checklist-{{ $c->id }}">
    {{ $c->deskripsi }}
  </label>
</div>
```

**JavaScript**:
```javascript
document.querySelectorAll('.toggle-checklist').forEach(checkbox => {
  checkbox.addEventListener('change', async function() {
    const tugasId = this.dataset.tugasId;
    const checklistId = this.dataset.checklistId;
    const isCompleted = this.checked;
    
    try {
      const response = await fetch(
        `/lapangan/tugas/${tugasId}/checklists/${checklistId}`,
        {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({ is_completed: isCompleted })
        }
      );
      
      const data = await response.json();
      
      if (data.success) {
        // Update progress bar
        updateProgressBar(tugasId, data.progress);
        
        // Show success toast
        showToast(`Progress: ${data.progress}%`, 'success');
        
        // If task auto-completed
        if (data.task_status === 'completed') {
          moveTaskToColumn(tugasId, 'completed');
        }
      } else {
        showToast('Gagal update checklist', 'error');
        this.checked = !isCompleted; // Revert
      }
    } catch (error) {
      console.error('Error:', error);
      showToast('Error updating checklist', 'error');
      this.checked = !isCompleted;
    }
  });
});

function updateProgressBar(tugasId, progress) {
  const progressBar = document.querySelector(
    `[data-tugas-id="${tugasId}"] .progress-bar`
  );
  if (progressBar) {
    progressBar.style.width = progress + '%';
    progressBar.textContent = progress + '%';
  }
}

function showToast(message, type) {
  // Implement your toast notification here
  console.log(`[${type}] ${message}`);
}
```

---

### 2. Update Task Status (Drag & Drop)

**Endpoint**: `PATCH /lapangan/tugas/{tugas_id}/status`

**With Sortable.js / Drag-Drop**:

```javascript
// Example using native drag-drop
const columns = document.querySelectorAll('.kanban-column');

columns.forEach(column => {
  column.addEventListener('drop', async (e) => {
    e.preventDefault();
    
    const tugasId = e.dataTransfer.getData('tugas-id');
    const newStatus = column.dataset.status; // 'pending', 'in_progress', 'completed'
    
    try {
      const response = await fetch(`/lapangan/tugas/${tugasId}/status`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ status: newStatus })
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Element is already moved by dragend, just update progress UI
        const element = document.querySelector(`[data-tugas-id="${tugasId}"]`);
        if (element) {
          element.style.opacity = '1';
          showToast(`Status: ${newStatus}`, 'success');
        }
      }
    } catch (error) {
      console.error('Error:', error);
      showToast('Gagal update status', 'error');
    }
  });
  
  column.addEventListener('dragover', (e) => {
    e.preventDefault();
    column.style.backgroundColor = '#f0f0f0';
  });
  
  column.addEventListener('dragleave', () => {
    column.style.backgroundColor = '';
  });
});

// Add drag handlers to task cards
document.querySelectorAll('.task-card').forEach(card => {
  card.draggable = true;
  
  card.addEventListener('dragstart', (e) => {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('tugas-id', card.dataset.tugasId);
    card.style.opacity = '0.5';
  });
  
  card.addEventListener('dragend', () => {
    card.style.opacity = '1';
  });
});
```

---

### 3. Get Task Detail (Master-Detail)

**Endpoint**: `GET /lapangan/tugas/{tugas_id}/detail`

**HTML**:
```html
<div class="task-list">
  <!-- Left panel: list of tasks -->
  @foreach($tugas as $task)
    <div class="task-item" data-tugas-id="{{ $task->id }}">
      {{ $task->nama_tugas }}
    </div>
  @endforeach
</div>

<div class="task-detail">
  <!-- Right panel: detail of selected task -->
  <div id="detail-container">
    <p>Pilih tugas untuk melihat detail</p>
  </div>
</div>
```

**JavaScript**:
```javascript
document.querySelectorAll('.task-item').forEach(item => {
  item.addEventListener('click', async function() {
    const tugasId = this.dataset.tugasId;
    
    // Highlight selected
    document.querySelectorAll('.task-item').forEach(i => i.classList.remove('active'));
    this.classList.add('active');
    
    // Load detail
    try {
      const response = await fetch(`/lapangan/tugas/${tugasId}/detail`);
      const task = await response.json();
      
      renderTaskDetail(task);
    } catch (error) {
      console.error('Error:', error);
      showToast('Gagal memuat detail tugas', 'error');
    }
  });
});

function renderTaskDetail(task) {
  const html = `
    <div class="detail-header">
      <h3>${task.nama_tugas}</h3>
      <span class="badge badge-${task.status}">
        ${task.status}
      </span>
    </div>
    
    <div class="detail-info">
      <p><strong>Progress:</strong> ${task.progress}%</p>
      <div class="progress-bar" style="width: ${task.progress}%"></div>
      
      <p><strong>Prioritas:</strong> ${task.prioritas}</p>
      <p><strong>Deadline:</strong> ${task.deadline}</p>
      <p><strong>PIC:</strong> ${task.pic_name} (${task.pic_email})</p>
      <p><strong>Event:</strong> ${task.pesanan_nama}</p>
      
      ${task.catatan ? `<p><strong>Catatan:</strong> ${task.catatan}</p>` : ''}
    </div>
    
    <div class="checklists">
      <h4>Sub-Tugas:</h4>
      ${task.checklists.map(c => `
        <label class="checklist-item">
          <input type="checkbox" 
                 class="toggle-checklist"
                 data-tugas-id="${task.id}"
                 data-checklist-id="${c.id}"
                 ${c.is_completed ? 'checked' : ''}>
          <span>${c.deskripsi}</span>
        </label>
      `).join('')}
    </div>
  `;
  
  document.getElementById('detail-container').innerHTML = html;
  
  // Re-attach checkbox listeners
  attachChecklistListeners();
}
```

---

## Reporting Integration

### 1. Get Metrics

**Endpoint**: `GET /lapangan/laporan/metrics?pesanan_id=1` (optional pesanan_id)

**JavaScript**:
```javascript
async function loadMetrics(pesananId = null) {
  try {
    let url = '/lapangan/laporan/metrics';
    if (pesananId) {
      url += `?pesanan_id=${pesananId}`;
    }
    
    const response = await fetch(url);
    const data = await response.json();
    
    // Update UI
    document.querySelector('[data-metric="total"]').textContent = data.total_tasks;
    document.querySelector('[data-metric="completed"]').textContent = data.completed_tasks;
    document.querySelector('[data-metric="progress"]').textContent = data.overall_progress + '%';
    
    // Update status breakdown
    document.querySelector('[data-status="pending"]').textContent = data.by_status.pending;
    document.querySelector('[data-status="in_progress"]').textContent = data.by_status.in_progress;
    document.querySelector('[data-status="completed"]').textContent = data.by_status.completed;
    
    // Update kendala list
    renderKendala(data.kendala);
  } catch (error) {
    console.error('Error loading metrics:', error);
  }
}

// Auto-refresh metrics every 30 seconds
setInterval(() => {
  const pesananId = document.querySelector('[data-pesanan-id]')?.dataset.pesananId;
  loadMetrics(pesananId);
}, 30000);

// Load on page init
document.addEventListener('DOMContentLoaded', () => {
  loadMetrics();
});
```

### 2. Store Challenge Report

**Endpoint**: `POST /lapangan/laporan/kendala`

**HTML**:
```html
<form id="kendala-form" class="kendala-form">
  <select name="pesanan_id" required>
    <option value="">Pilih Event</option>
    @foreach($events as $event)
      <option value="{{ $event->id }}">{{ $event->nama_acara }}</option>
    @endforeach
  </select>
  
  <select name="kondisi" required>
    <option value="">Pilih Kondisi</option>
    <option value="baik">Baik</option>
    <option value="perhatian">Perlu Perhatian</option>
    <option value="kritis">Kritis</option>
  </select>
  
  <textarea name="deskripsi" placeholder="Deskripsi kendala..." required></textarea>
  
  <button type="submit">Kirim Laporan</button>
</form>
```

**JavaScript**:
```javascript
document.getElementById('kendala-form')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const data = {
    pesanan_id: formData.get('pesanan_id'),
    kondisi: formData.get('kondisi'),
    deskripsi: formData.get('deskripsi')
  };
  
  try {
    const response = await fetch('/lapangan/laporan/kendala', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      showToast('Laporan kendala berhasil dikirim', 'success');
      e.target.reset();
      
      // Refresh kendala list
      loadKendalaList(data.pesanan_id);
    } else {
      showToast('Gagal mengirim laporan', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('Error: ' + error.message, 'error');
  }
});

async function loadKendalaList(pesananId) {
  try {
    const response = await fetch(`/lapangan/laporan/kendala/${pesananId}`);
    const result = await response.json();
    
    // Render kendala list
    const kendalaList = document.querySelector('.kendala-list');
    kendalaList.innerHTML = result.data.map(k => `
      <div class="kendala-item kendala-${k.kondisi}">
        <h5>${k.deskripsi}</h5>
        <small>${new Date(k.created_at).toLocaleString('id-ID')}</small>
      </div>
    `).join('');
  } catch (error) {
    console.error('Error:', error);
  }
}
```

---

## Error Handling

### Standard Error Response

API returns HTTP error codes:
- `200` - Success
- `400` - Validation error
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not found
- `422` - Unprocessable (validation)
- `500` - Server error

**Handling**:
```javascript
async function safeApiCall(url, options = {}) {
  try {
    const response = await fetch(url, {
      headers: {
        'X-CSRF-TOKEN': getCsrfToken(),
        ...options.headers
      },
      ...options
    });
    
    // Handle HTTP errors
    if (!response.ok) {
      let message = `HTTP ${response.status}`;
      
      if (response.status === 422) {
        const errors = await response.json();
        message = Object.values(errors.errors).flat().join(', ');
      } else if (response.status === 401) {
        message = 'Session expired - please login again';
        window.location.href = '/lapangan/login';
        return;
      }
      
      throw new Error(message);
    }
    
    return await response.json();
  } catch (error) {
    console.error('API Error:', error);
    showToast(error.message, 'error');
    throw error;
  }
}
```

---

## Code Examples

### Complete Kanban Component

```html
<div class="kanban-board">
  <!-- Columns -->
  <div class="kanban-column" data-status="pending">
    <h3>Belum Dikerjakan</h3>
    <div class="tasks"></div>
  </div>
  
  <div class="kanban-column" data-status="in_progress">
    <h3>Sedang Dikerjakan</h3>
    <div class="tasks"></div>
  </div>
  
  <div class="kanban-column" data-status="completed">
    <h3>Selesai</h3>
    <div class="tasks"></div>
  </div>
</div>

<script>
// Initialize Kanban with drag-drop
function initKanban() {
  // Load tasks
  fetch('/lapangan/tugas')
    .then(r => r.json())
    .then(tasks => {
      renderTasks(tasks);
      attachDragHandlers();
    });
}

function renderTasks(tasks) {
  // Group by status
  const byStatus = {
    pending: [],
    in_progress: [],
    completed: []
  };
  
  tasks.forEach(task => {
    byStatus[task.status]?.push(task);
  });
  
  // Render each column
  Object.entries(byStatus).forEach(([status, taskList]) => {
    const column = document.querySelector(
      `.kanban-column[data-status="${status}"] .tasks`
    );
    column.innerHTML = taskList.map(task => `
      <div class="task-card" data-tugas-id="${task.id}" draggable="true">
        <h4>${task.nama_tugas}</h4>
        <div class="progress-bar" style="width: ${task.progress}%">
          ${task.progress}%
        </div>
        <small>${task.pic_name}</small>
      </div>
    `).join('');
  });
}

function attachDragHandlers() {
  // ... drag-drop implementation from earlier
}

document.addEventListener('DOMContentLoaded', initKanban);
</script>
```

### Complete Reporting Dashboard

```html
<div class="reporting-dashboard">
  <div class="metrics-row">
    <div class="metric">
      <strong>Total Tugas</strong>
      <span data-metric="total">0</span>
    </div>
    <div class="metric">
      <strong>Selesai</strong>
      <span data-metric="completed">0</span> / <span data-metric="total">0</span>
    </div>
    <div class="metric">
      <strong>Progress</strong>
      <span data-metric="progress">0%</span>
    </div>
  </div>
  
  <div class="status-breakdown">
    <p>Pending: <span data-status="pending">0</span></p>
    <p>Sedang: <span data-status="in_progress">0</span></p>
    <p>Selesai: <span data-status="completed">0</span></p>
  </div>
  
  <div class="kendala-section">
    <h3>Laporan Kendala</h3>
    <form id="kendala-form">
      <!-- form fields from earlier -->
    </form>
    <div class="kendala-list"></div>
  </div>
</div>

<script>
// Auto-refresh every 30 seconds
setInterval(async () => {
  const data = await fetch('/lapangan/laporan/metrics').then(r => r.json());
  
  // Update metrics UI
  document.querySelector('[data-metric="total"]').textContent = data.total_tasks;
  document.querySelector('[data-metric="completed"]').textContent = data.completed_tasks;
  document.querySelector('[data-metric="progress"]').textContent = data.overall_progress + '%';
  
  // Update breakdown
  document.querySelector('[data-status="pending"]').textContent = data.by_status.pending;
  document.querySelector('[data-status="in_progress"]').textContent = data.by_status.in_progress;
  document.querySelector('[data-status="completed"]').textContent = data.by_status.completed;
}, 30000);

// Initial load
loadMetrics();
</script>
```

---

**Ready to integrate!** 🚀
