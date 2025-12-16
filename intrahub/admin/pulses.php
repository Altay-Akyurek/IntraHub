<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Pulse Yönetimi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Standardize Styles -->
<style>
/* Glass Table */
.table {
    --bs-table-bg: transparent;
    --bs-table-color: var(--text-main);
    border-color: rgba(255,255,255,0.05);
}
.table thead th {
    border-bottom: 1px solid rgba(255,255,255,0.1);
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}
.table tbody tr:hover {
    background: rgba(255,255,255,0.02);
}

/* Badges */
.badge-glass {
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 0.4em 0.8em;
    border-radius: 20px;
    font-weight: 500;
}
.badge-success-glass {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
}
.badge-secondary-glass {
    background: rgba(148, 163, 184, 0.2);
    color: #94a3b8;
}

/* Buttons */
.btn-glass-edit {
    background: rgba(59, 130, 246, 0.1);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.2);
}
.btn-glass-edit:hover {
    background: rgba(59, 130, 246, 0.2);
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
}
.btn-glass-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.2);
}
.btn-glass-delete:hover {
    background: rgba(239, 68, 68, 0.2);
    box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);
}
</style>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-heartbeat" style="color: #34d399; filter: drop-shadow(0 0 5px rgba(52, 211, 153, 0.4));"></i> Pulse Yönetimi</h3>
        <button class="btn btn-primary" onclick="openCreateModal()" style="border-radius: 50px; padding-left: 1.5rem; padding-right: 1.5rem;">
            <i class="fas fa-plus"></i> Yeni Pulse
        </button>
    </div>

    <!-- Pulse List -->
    <div class="glass-card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Başlık</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th class="text-end pe-4">İşlem</th>
                    </tr>
                </thead>
                <tbody id="pulseTableBody">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Pulse Modal -->
<div class="modal-overlay" id="pulseModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-card" style="width: 100%; max-width: 600px; margin: 2rem; max-height: 90vh; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1);">
        <h5 class="mb-3" id="modalTitle" style="font-weight: 700; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Yeni Pulse Oluştur</h5>
        <form id="pulseForm">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="pulseId" value="">
            
            <div class="mb-3">
                <label class="form-label">Başlık</label>
                <input name="title" id="pTitle" class="form-control" required style="background: rgba(0,0,0,0.2);">
            </div>
            <div class="mb-3">
                <label class="form-label">Açıklama</label>
                <textarea name="description" id="pDesc" class="form-control" rows="2" style="background: rgba(0,0,0,0.2);"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gönderim Zamanı (Opsiyonel)</label>
                <input type="datetime-local" name="send_at" id="pSendAt" class="form-control" style="background: rgba(0,0,0,0.2);">
            </div>

            <div class="mb-3">
                <label class="form-label">Sorular</label>
                <div id="questionsContainer" class="d-flex flex-column gap-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addQuestion()" style="border-radius: 20px;">
                    <i class="fas fa-plus"></i> Soru Ekle
                </button>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="active" id="activeCheck" class="form-check-input" checked>
                <label class="form-check-label" for="activeCheck">Aktif</label>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" onclick="closeModal()" style="background: transparent; border: 1px solid rgba(255,255,255,0.1); color: var(--text-muted); border-radius: 12px;">İptal</button>
                <button type="submit" class="btn btn-primary" style="min-width: 100px;">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadPulses);

function loadPulses() {
    fetch('/php/company_Chat/intrahub/pulses/api.php?all=1')
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('pulseTableBody');
            tbody.innerHTML = '';
            if(data.pulses) {
                if(data.pulses.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Henüz pulse yok.</td></tr>';
                    return;
                }
                data.pulses.forEach(p => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="ps-4">
                            <strong style="color: var(--text-main); font-weight: 600;">${e(p.title)}</strong><br>
                            <small style="color: var(--text-muted); font-size: 0.8em;">${e(p.description || '')}</small>
                        </td>
                        <td>
                            ${p.active == 1 
                                ? '<span class="badge badge-glass badge-success-glass">Aktif</span>' 
                                : '<span class="badge badge-glass badge-secondary-glass">Pasif</span>'}
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.9em;">
                            ${new Date(p.created_at).toLocaleDateString('tr-TR')}
                        </td>
                        <td class="text-end pe-4">
                            <a href="pulse_responses.php?id=${p.id}" class="btn btn-sm btn-info text-white me-1" title="Yanıtlar" style="border-radius: 8px;"><i class="fas fa-chart-bar"></i></a>
                            <button class="btn btn-sm btn-glass-edit me-1" onclick="editPulse(${p.id})" title="Düzenle" style="border-radius: 8px;"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-glass-delete" onclick="deletePulse(${p.id})" title="Sil" style="border-radius: 8px;"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
}

function openCreateModal() {
    document.getElementById('pulseForm').reset();
    document.getElementById('modalTitle').innerText = 'Yeni Pulse Oluştur';
    document.getElementById('formAction').value = 'create';
    document.getElementById('pulseId').value = '';
    document.getElementById('questionsContainer').innerHTML = '';
    addQuestion(); // Add one empty
    document.getElementById('pulseModal').style.display = 'flex';
}

function editPulse(id) {
    // 1. Fetch details
    fetch('/php/company_Chat/intrahub/pulses/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'get', id: id })
    })
    .then(r => r.json())
    .then(resp => {
        if(resp.ok && resp.pulse) {
            const p = resp.pulse;
            // 2. Populate Form
            document.getElementById('formAction').value = 'update';
            document.getElementById('pulseId').value = p.id;
            document.getElementById('modalTitle').innerText = 'Pulse Düzenle';
            
            document.getElementById('pTitle').value = p.title;
            document.getElementById('pDesc').value = p.description;
            // datetime-local expects YYYY-MM-DDTHH:MM format
            if(p.send_at) {
                document.getElementById('pSendAt').value = p.send_at.replace(' ', 'T').slice(0, 16);
            } else {
                document.getElementById('pSendAt').value = '';
            }
            document.getElementById('activeCheck').checked = (p.active == 1);
            
            // 3. Populate Questions
            const container = document.getElementById('questionsContainer');
            container.innerHTML = '';
            if(p.questions && Array.isArray(p.questions)) {
                p.questions.forEach(q => addQuestion(q.text, q.type));
            } else {
                addQuestion();
            }
            
            // 4. Show Modal
            document.getElementById('pulseModal').style.display = 'flex';
        } else {
            Swal.fire('Hata', resp.error || 'Veri çekilemedi', 'error');
        }
    });
}

function closeModal() {
    document.getElementById('pulseModal').style.display = 'none';
}

function addQuestion(text = '', type = 'text') {
    const div = document.createElement('div');
    div.className = 'card p-3 question-item mb-2';
    div.style.background = 'rgba(255,255,255,0.03)';
    div.style.border = '1px solid rgba(255,255,255,0.05)';
    div.style.borderRadius = '12px';
    div.innerHTML = `
        <div class="mb-2">
            <input type="text" class="form-control form-control-sm q-text" placeholder="Soru metni" value="${e(text)}" required style="background: rgba(0,0,0,0.2);">
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm q-type" style="background: rgba(0,0,0,0.2); color: var(--text-main); border-color: var(--border-color);">
                <option value="text" ${type === 'text' ? 'selected' : ''}>Metin Cevap</option>
                <option value="scale" ${type === 'scale' ? 'selected' : ''}>1-5 Ölçek</option>
            </select>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.question-item').remove()" style="border-radius: 8px;"><i class="fas fa-times"></i></button>
        </div>
    `;
    document.getElementById('questionsContainer').appendChild(div);
}

document.getElementById('pulseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const questions = [];
    document.querySelectorAll('.question-item').forEach(item => {
        questions.push({
            text: item.querySelector('.q-text').value,
            type: item.querySelector('.q-type').value
        });
    });

    const payload = {
        action: document.getElementById('formAction').value,
        id: document.getElementById('pulseId').value,
        title: formData.get('title'),
        description: formData.get('description'),
        send_at: formData.get('send_at'),
        active: document.getElementById('activeCheck').checked ? 1 : 0,
        questions: questions
    };

    fetch('/php/company_Chat/intrahub/pulses/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(resp => {
        if(resp.ok) {
            closeModal();
            loadPulses();
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: 'İşlem başarıyla tamamlandı.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Hata', resp.error || 'Bilinmeyen', 'error');
        }
    });
});

function deletePulse(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu pulse kaydını silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal',
        background: 'rgba(255, 255, 255, 0.95)'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/php/company_Chat/intrahub/pulses/api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            })
            .then(r => r.json())
            .then(resp => {
                if(resp.ok) {
                    loadPulses();
                    Swal.fire(
                        'Silindi!',
                        'Pulse başarıyla silindi.',
                        'success'
                    );
                } else {
                    Swal.fire('Hata', resp.error, 'error');
                }
            });
        }
    });
}

function e(str) {
    return str ? str.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
