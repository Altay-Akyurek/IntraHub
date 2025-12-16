// assets/js/calendar.js - geliştirilmiş event modal & attendees gösterimi
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var eventModalEl = document.getElementById('eventModal');
    var eventModal = new bootstrap.Modal(eventModalEl);
    var form = document.getElementById('eventForm');
    var deleteBtn = document.getElementById('deleteBtn');
    var attendBtn = document.getElementById('attendBtn');
    var editFields = document.getElementById('evt_edit_fields');

    function fetchEvents(info, successCallback, failureCallback) {
        fetch('/events/api.php')
            .then(r => r.json())
            .then(data => successCallback(data))
            .catch(err => failureCallback(err));
    }

    function clearReadonly() {
        document.getElementById('evt_title_readonly').textContent = '';
        document.getElementById('evt_time').textContent = '';
        document.getElementById('evt_location_readonly').textContent = '';
        document.getElementById('evt_description_readonly').textContent = '';
        document.getElementById('evt_attendees').innerHTML = '';
        document.getElementById('attendee_count').textContent = '0';
    }

    function loadAttendees(eventId) {
        return fetch('/events/api.php?attendees=1&id=' + encodeURIComponent(eventId))
            .then(r => r.json())
            .then(function (resp) {
                if (!resp.ok) return [];
                return resp.attendees || [];
            }).catch(function () { return []; });
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        height: 'auto',
        events: fetchEvents,
        select: function (selectionInfo) {
            if (!window.INTRAHUB.isAdmin) return; // sadece admin yeni etkinlik ekleyebilir
            // show admin edit fields for create
            clearReadonly();
            document.getElementById('evt_id').value = '';
            editFields.style.display = 'block';
            if (deleteBtn) deleteBtn.style.display = 'none';
            // fill start/end inputs
            var s = selectionInfo.start;
            function toLocalInput(dt) {
                if (!dt) return '';
                dt = new Date(dt);
                dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
                return dt.toISOString().slice(0, 16);
            }
            document.getElementById('evt_start').value = toLocalInput(s);
            document.getElementById('evt_end').value = '';
            document.getElementById('evt_title').value = '';
            document.getElementById('evt_location').value = '';
            document.getElementById('evt_description').value = '';
            eventModal.show();
        },
        eventClick: function (info) {
            var ev = info.event;
            // Hazırlık: Edit/View verilerini doldur
            clearReadonly();
            document.getElementById('evt_id').value = ev.id;
            document.getElementById('evt_title_readonly').textContent = ev.title || '';
            document.getElementById('evt_location_readonly').textContent = ev.extendedProps.location || '';
            document.getElementById('evt_description_readonly').textContent = ev.extendedProps.description || '';
            function fmtLocal(dtStr) {
                if (!dtStr) return '';
                var d = new Date(dtStr);
                return d.toLocaleString();
            }
            var timeStr = fmtLocal(ev.startStr) + (ev.endStr ? ' — ' + fmtLocal(ev.endStr) : '');
            document.getElementById('evt_time').textContent = timeStr;

            // Admin kontrolü ve form doldurma
            if (window.INTRAHUB.isAdmin) {
                editFields.style.display = 'block';
                document.getElementById('evt_title').value = ev.title || '';
                document.getElementById('evt_description').value = ev.extendedProps.description || '';
                document.getElementById('evt_location').value = ev.extendedProps.location || '';
                function toLocalInput(dateStr) {
                    if (!dateStr) return '';
                    var dt = new Date(dateStr);
                    dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
                    return dt.toISOString().slice(0, 16);
                }
                document.getElementById('evt_start').value = toLocalInput(ev.startStr);
                document.getElementById('evt_end').value = toLocalInput(ev.endStr);
                if (deleteBtn) deleteBtn.style.display = 'inline-block';
                if (attendBtn) attendBtn.style.display = 'none';
            } else {
                editFields.style.display = 'none';
                if (deleteBtn) deleteBtn.style.display = 'none';
                if (attendBtn) attendBtn.style.display = 'inline-block';
            }

            // Katılımcıları yükle (arka planda, modal açılırsa hazır olsun)
            loadAttendees(ev.id).then(function (list) {
                var container = document.getElementById('evt_attendees');
                container.innerHTML = '';
                if (!list || list.length === 0) {
                    container.innerHTML = '<div class="p-2 text-muted">Henüz katılımcı yok.</div>';
                } else {
                    list.forEach(function (a) {
                        var div = document.createElement('div');
                        div.className = 'attendee-item';
                        var left = document.createElement('div');
                        left.innerHTML = '<strong>' + (a.full_name || a.username) + '</strong><div class="small text-muted">' + (a.department || '') + '</div>';
                        var right = document.createElement('div');
                        right.innerHTML = '<span class="badge ' + (a.status === 'attending' ? 'bg-success' : 'bg-secondary') + '">' + a.status + '</span><div class="small text-muted">' + (a.responded_at || '') + '</div>';
                        div.appendChild(left);
                        div.appendChild(right);
                        container.appendChild(div);
                    });
                }
                document.getElementById('attendee_count').textContent = list.length;
            });

            // SweetAlert2 ile Bilgi Gösterimi
            Swal.fire({
                icon: 'info',
                title: ev.title || 'Etkinlik Detayı',
                html: `
                    <div style="text-align: left;">
                        <p><strong>Zaman:</strong> ${timeStr}</p>
                        <p><strong>Konum:</strong> ${ev.extendedProps.location || 'Belirtilmemiş'}</p>
                        <p><strong>Açıklama:</strong> ${ev.extendedProps.description || 'Yok'}</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Detaylar / İşlem',
                cancelButtonText: 'Tamam'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kullanıcı detaylara gitmek isterse Bootstrap modalını aç
                    eventModal.show();
                }
            });
        }
    });

    calendar.render();

    // Form save (create or update) - admin
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var id = document.getElementById('evt_id').value;
        if (!window.INTRAHUB.isAdmin) return;
        var payload = {
            action: id ? 'update' : 'create',
            id: id || undefined,
            title: document.getElementById('evt_title').value,
            description: document.getElementById('evt_description').value,
            location: document.getElementById('evt_location').value,
            start: document.getElementById('evt_start').value ? new Date(document.getElementById('evt_start').value).toISOString().slice(0, 19).replace('T', ' ') : null,
            end: document.getElementById('evt_end').value ? new Date(document.getElementById('evt_end').value).toISOString().slice(0, 19).replace('T', ' ') : null
        };

        fetch('/events/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(r => r.json()).then(function (resp) {
            if (resp.ok) {
                calendar.refetchEvents();
                eventModal.hide();
            } else {
                alert('Hata: ' + (resp.error || 'Bilinmeyen hata'));
            }
        }).catch(function (err) {
            alert('İstek başarısız: ' + err);
        });
    });

    // Delete
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            if (!confirm('Etkinliği silmek istediğinize emin misiniz?')) return;
            var id = document.getElementById('evt_id').value;
            fetch('/events/api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            }).then(r => r.json()).then(function (resp) {
                if (resp.ok) {
                    calendar.refetchEvents();
                    eventModal.hide();
                } else {
                    alert('Hata: ' + (resp.error || 'Bilinmeyen hata'));
                }
            }).catch(function (err) {
                alert('İstek başarısız: ' + err);
            });
        });
    }

    // Attend toggle for non-admins
    if (attendBtn) {
        attendBtn.addEventListener('click', function () {
            var id = document.getElementById('evt_id').value;
            fetch('/events/api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'attend', id: id })
            }).then(r => r.json()).then(function (resp) {
                if (resp.ok) {
                    alert('Durum: ' + resp.status);
                    calendar.refetchEvents();
                    eventModal.hide();
                } else {
                    alert('Hata: ' + (resp.error || 'Bilinmeyen hata'));
                }
            }).catch(function (err) {
                alert('İstek başarısız: ' + err);
            });
        });
    }
});