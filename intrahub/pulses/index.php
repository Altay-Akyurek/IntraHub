<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Nabızlar ve Anketler';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$user = current_user();
?>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-heartbeat" style="color: #10b981"></i> Aktif Nabızlar</h3>
    </div>

    <div id="loading" class="text-center text-muted py-5">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
    </div>

    <div id="notesArea" class="row">
        <!-- Pulses will be loaded here -->
    </div>
    
    <div id="noData" class="alert" style="display:none; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-muted);">
        <i class="fas fa-info-circle"></i> Şu an aktif nabız bulunmuyor.
    </div>
</div>

<style>
/* Star Rating Styles */
.rating-group {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 10px;
}
.rating-group:hover .rating-star { color: #ddd; }
.rating-star { cursor: pointer; color: #ddd; transition: color 0.2s; }
.rating-star:hover, .rating-star:hover ~ .rating-star { color: #f59e0b !important; }
input:checked ~ .rating-star { color: #f59e0b !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', loadPulses);

function loadPulses() {
    fetch('/php/company_Chat/intrahub/pulses/api.php?action=list')
        .then(r => r.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            const container = document.getElementById('notesArea');
            container.innerHTML = '';
            
            if (!data.pulses || data.pulses.length === 0) {
                document.getElementById('noData').style.display = 'block';
                return;
            }

            data.pulses.forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-md-6 mb-4';
                
                // Unique ID for radio group
                const radioName = 'rating_' + p.id;

                col.innerHTML = `
                    <div class="glass-card">
                        <h5 class="mb-2" style="color: var(--primary-color)">${e(p.title)}</h5>
                        <p class="text-muted mb-4">${e(p.description)}</p>
                        
                        <form class="pulseForm" data-pulse-id="${p.id}">
                            <div class="mb-3 text-center">
                                <label class="form-label d-block text-muted small mb-2">Puanınız</label>
                                <div class="rating-group">
                                    <input type="radio" name="rating" id="${radioName}_5" value="5" hidden required>
                                    <label for="${radioName}_5" class="fas fa-star fa-2x rating-star" title="Çok İyi"></label>
                                    
                                    <input type="radio" name="rating" id="${radioName}_4" value="4" hidden required>
                                    <label for="${radioName}_4" class="fas fa-star fa-2x rating-star" title="İyi"></label>
                                    
                                    <input type="radio" name="rating" id="${radioName}_3" value="3" hidden required>
                                    <label for="${radioName}_3" class="fas fa-star fa-2x rating-star" title="Orta"></label>
                                    
                                    <input type="radio" name="rating" id="${radioName}_2" value="2" hidden required>
                                    <label for="${radioName}_2" class="fas fa-star fa-2x rating-star" title="Kötü"></label>
                                    
                                    <input type="radio" name="rating" id="${radioName}_1" value="1" hidden required>
                                    <label for="${radioName}_1" class="fas fa-star fa-2x rating-star" title="Çok Kötü"></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Görüşleriniz..."></textarea>
                            </div>

                            <button class="btn btn-primary w-100 mt-2 submitBtn">
                                Gönder <i class="fas fa-paper-plane" style="margin-left:5px"></i>
                            </button>
                        </form>
                    </div>
                `;
                container.appendChild(col);
                
                // Attach event listener immediately
                col.querySelector('.pulseForm').addEventListener('submit', handlePulseSubmit);
            });
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loading').innerHTML = 'Yüklenirken hata oluştu.';
        });
}

function handlePulseSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const pid = form.getAttribute('data-pulse-id');
    const formData = new FormData(form);
    
    // Construct answers object matching generic format
    const answers = {
        rating: formData.get('rating'),
        comment: formData.get('comment')
    };
    
    // Disable button
    const btn = form.querySelector('button');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('/php/company_Chat/intrahub/pulses/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'submit', pulse_id: pid, answers: answers })
    }).then(r => r.json()).then(function (resp) {
        if (resp.ok) {
            const card = form.closest('.glass-card');
            card.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x" style="color: #10b981; margin-bottom: 1rem;"></i>
                    <h4>Teşekkürler!</h4>
                    <p class="text-muted">Geri bildiriminiz alındı.</p>
                </div>
            `;
        } else {
            alert('Hata: ' + (resp.error || 'Bilinmeyen'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }).catch(function (err) { 
        alert('İstek başarısız: ' + err);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function e(str) {
    return str ? str.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
