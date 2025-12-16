<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
$user = current_user();

$pageTitle = 'Etkinlik Takvimi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<div class="container mt-4 fade-in-up">
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-calendar-alt" style="color: var(--secondary-color)"></i> Etkinlik Takvimi</h3>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <button class="btn btn-primary" onclick="openEventModal()" style="width: auto;">
                <i class="fas fa-plus"></i> Etkinlik Ekle
            </button>
            <?php endif; ?>
        </div>
        
        <div id="calendar" style="color: var(--text-main);"></div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal-overlay" id="eventModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-card" style="width: 100%; max-width: 500px; margin: 2rem;">
        <h4 class="mb-4">Yeni Etkinlik</h4>
        <form id="eventForm">
            <input type="hidden" name="action" id="evt_action" value="create">
            <input type="hidden" name="id" id="evt_id" value="">
            <div class="mb-3">
                <label class="form-label">Başlık</label>
                <input name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Başlangıç</label>
                <input type="datetime-local" name="start" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Bitiş (Opsiyonel)</label>
                <input type="datetime-local" name="end" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Konum</label>
                <input name="location" class="form-control">
            </div>
            <div class="d-flex gap-2" style="gap: 10px;">
                <button type="submit" class="btn btn-primary">Kaydet</button>
                <button type="button" class="btn btn-secondary" onclick="closeEventModal()" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); width: 100%; border-radius: 12px;">İptal</button>
            </div>
        </form>
    </div>
</div>

<script>
const IS_ADMIN = <?= json_encode(($user['role'] ?? '') === 'admin') ?>;

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'tr',
        themeSystem: 'standard',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay',
            week: 'Hafta',
            day: 'Gün',
            list: 'Liste'
        },
        events: 'api.php', 
        eventColor: 'var(--primary-color)',
        eventClick: function(info) {
             const ev = info.event;
             const props = ev.extendedProps;
             
             // Base config for Swal
             let swalConfig = {
                icon: 'info',
                title: ev.title,
                html: `
                    <div style="text-align: left">
                        <p><strong>Konum:</strong> ${props.location || '-'}</p>
                        <p><strong>Zaman:</strong> ${ev.start.toLocaleString()} ${ev.end ? '- ' + ev.end.toLocaleString() : ''}</p>
                        <p><strong>Açıklama:</strong> ${props.description || ''}</p>
                    </div>
                `,
                confirmButtonText: 'Tamam'
            };

            if (IS_ADMIN) {
                swalConfig.showDenyButton = true;
                swalConfig.showCancelButton = true;
                swalConfig.confirmButtonText = 'Düzenle'; // Confirm -> Edit
                swalConfig.denyButtonText = 'Sil';       // Deny -> Delete
                swalConfig.cancelButtonText = 'İptal';
                swalConfig.confirmButtonColor = '#3b82f6';
                swalConfig.denyButtonColor = '#ef4444';
            }

            Swal.fire(swalConfig).then((result) => {
                if (IS_ADMIN) {
                    if (result.isConfirmed) {
                        // Edit
                        openEventModal({
                            id: ev.id,
                            title: ev.title,
                            description: props.description,
                            location: props.location,
                            start: ev.startStr.slice(0, 16), // ISO string for input
                            end: ev.endStr ? ev.endStr.slice(0, 16) : ''
                        });
                    } else if (result.isDenied) {
                        // Delete
                        Swal.fire({
                            title: 'Emin misiniz?',
                            text: "Bu etkinlik silinecek!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'Evet, Sil'
                        }).then((delRes) => {
                            if (delRes.isConfirmed) {
                                fetch('api.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ action: 'delete', id: ev.id })
                                }).then(r => r.json()).then(resp => {
                                    if(resp.ok) {
                                        info.event.remove();
                                        Swal.fire('Silindi!', '', 'success');
                                    } else {
                                        Swal.fire('Hata', resp.error || 'Silinemedi', 'error');
                                    }
                                });
                            }
                        });
                    }
                }
            });
        },
        dayMaxEvents: true // allow "more" link when too many events
    });
    calendar.render();
});

function openEventModal(data = null) {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    
    // Reset form first
    form.reset();
    
    if (data) {
        // Edit Mode
        document.getElementById('evt_action').value = 'update';
        document.getElementById('evt_id').value = data.id;
        form.elements['title'].value = data.title || '';
        form.elements['description'].value = data.description || '';
        form.elements['location'].value = data.location || '';
        form.elements['start'].value = data.start || '';
        form.elements['end'].value = data.end || '';
        modal.querySelector('h4').textContent = 'Etkinliği Düzenle';
    } else {
        // Create Mode
        document.getElementById('evt_action').value = 'create';
        document.getElementById('evt_id').value = '';
        modal.querySelector('h4').textContent = 'Yeni Etkinlik';
    }

    modal.style.display = 'flex';
}
function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

document.getElementById('eventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
        if(resp.ok) {
            closeEventModal();
            location.reload(); 
        } else {
            alert('Hata: ' + (resp.error || 'Bilinmeyen'));
        }
    });
});
</script>

<style>
/* FullCalendar Premium Glass Design */
.fc {
    --fc-page-bg-color: transparent;
    --fc-neutral-bg-color: rgba(255,255,255,0.02);
    --fc-border-color: rgba(255, 255, 255, 0.06);
    --fc-list-event-hover-bg-color: rgba(255,255,255,0.08);
    --fc-today-bg-color: rgba(99, 102, 241, 0.15) !important;
    --fc-event-bg-color: var(--primary-color);
    --fc-event-border-color: transparent;
    color: var(--text-main);
    font-family: 'Outfit', sans-serif;
}

/* Header Toolbar */
.fc-header-toolbar {
    margin-bottom: 2rem !important;
    padding: 0 0.5rem;
}

.fc-toolbar-title {
    font-size: 1.75rem !important;
    font-weight: 800;
    letter-spacing: -0.5px;
    background: linear-gradient(to right, #fff, #94a3b8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Premium Buttons */
.fc-button-primary {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: var(--text-main) !important;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50px !important; /* Pill shape */
    padding: 0.6em 1.2em !important;
    font-weight: 500;
    letter-spacing: 0.5px;
}
.fc-button-primary:hover {
    background: rgba(255,255,255,0.1) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.1);
    border-color: rgba(255,255,255,0.2) !important;
}
.fc-button-primary:not(:disabled).fc-button-active {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    border-color: transparent !important;
    color: white !important;
    box-shadow: 0 0 15px var(--primary-color); /* Glow effect */
}

/* Day Grid */
.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--fc-border-color);
}
.fc-daygrid-day {
    transition: background 0.2s;
}
.fc-daygrid-day:hover {
    background: rgba(255,255,255,0.02);
}
.fc-daygrid-day-number {
    padding: 10px;
    font-weight: 600;
    color: var(--text-muted);
}
.fc-col-header-cell-cushion {
    color: var(--text-main);
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
    padding: 12px 0;
}

/* Today Cell Highlight */
.fc-day-today {
    background: var(--fc-today-bg-color) !important;
    position: relative;
    overflow: hidden;
}
.fc-day-today::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    box-shadow: inset 0 0 20px rgba(99, 102, 241, 0.2);
    pointer-events: none;
}

/* Event Cards */
.fc-event {
    cursor: pointer;
    border-radius: 8px;
    margin: 2px 4px;
    padding: 4px 8px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.8), rgba(236, 72, 153, 0.8));
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.fc-event:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 15px rgba(99, 102, 241, 0.3);
    z-index: 5;
}
.fc-event-title, .fc-event-time {
    font-weight: 500;
    font-size: 0.9em;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.fc-daygrid-event-dot {
    border-color: white !important;
}

/* List View Polish */
.fc-list {
    border: none;
    background: transparent;
}
.fc-list-day-cushion {
    background: rgba(255,255,255,0.03) !important;
    font-weight: 600;
    text-transform: capitalize;
}
.fc-list-event td {
    border-color: var(--fc-border-color);
}
.fc-list-event:hover td {
    background: rgba(255,255,255,0.05) !important;
}
.fc-list-event-dot {
    border-color: var(--secondary-color);
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
