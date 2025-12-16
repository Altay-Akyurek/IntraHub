// assets/js/calendar.js
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    var form = document.getElementById('eventForm');
    var deleteBtn = document.getElementById('deleteBtn');
    var attendBtn = document.getElementById('attendBtn');

    function fetchEvents(info, successCallback, failureCallback) {
        fetch('/events/api.php')
            .then(r => r.json())
            .then(data => successCallback(data))
            .catch(err => failureCallback(err));
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        height: 'auto',
        events: fetchEvents,
        select: function (selectionInfo) {
            if (!window.INTRAHUB.isAdmin) return; // sadece admin yeni etkinlik ekleyebilir
            // doldur modal
            document.getElementById('evt_id').value = '';
            document.getElementById('evt_title').value = '';
            document.getElementById('evt_description').value = '';
            document.getElementById('evt_location').value = '';
            // convert to local datetime-local value
            var s = new Date(selectionInfo.start);
            var e = selectionInfo.end ? new Date(selectionInfo.end) : null;
            function toLocalInput(dt) {
                if (!dt) return '';
                dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
                return dt.toISOString().slice(0, 16);
            }
            document.getElementById('evt_start').value = toLocalInput(s);
            document.getElementById('evt_end').value = e ? toLocalInput(e) : '';
            if (deleteBtn) deleteBtn.style.display = 'none';
            eventModal.show();
        },
        eventClick: function (info) {
            var ev = info.event;
            // doldur modal
            document.getElementById('evt_id').value = ev.id;
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
            if (window.INTRAHUB.isAdmin) {
                if (deleteBtn) deleteBtn.style.display = 'inline-block';
                if (attendBtn) attendBtn.style.display = 'none';
            } else {
                if (deleteBtn) deleteBtn.style.display = 'none';
                if (attendBtn) attendBtn.style.display = 'inline-block';
            }
            eventModal.show();
        }
    });

    calendar.render();

    // Form save (create or update)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var id = document.getElementById('evt_id').value;
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