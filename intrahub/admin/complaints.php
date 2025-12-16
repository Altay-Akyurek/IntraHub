<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle deletion if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM complaints WHERE id = ?");
    $stmt->execute([$deleteId]);
    header("Location: complaints.php?status=deleted");
    exit;
}

$pageTitle = 'Şikayet Yönetimi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Fetch all complaints ordered by date DESC
try {
    $stmt = $pdo->query("
        SELECT c.*, u.username, u.full_name 
        FROM complaints c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC
    ");
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<div class="container mt-4 fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-exclamation-circle" style="color: var(--secondary-color)"></i> Şikayet/Öneri Kutusu</h3>
        <a href="index.php" class="btn btn-sm" style="border: 1px solid var(--border-color); color: var(--text-main);">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Silindi!',
                    text: 'Şikayet başarıyla silindi.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    <?php endif; ?>

    <div class="glass-card">
        <?php if (count($complaints) > 0): ?>
            <div class="table-responsive">
                <table class="table" style="color: var(--text-main); width: 100%; border-collapse: separate; border-spacing: 0 1rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Gönderen</th>
                            <th style="padding: 1rem;">Mesaj</th>
                            <th style="padding: 1rem;">Tarih</th>
                            <th style="padding: 1rem; text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $c): ?>
                            <tr style="background: rgba(255,255,255,0.05);">
                                <td style="padding: 1rem; border-radius: 10px 0 0 10px;">#<?php echo $c['id']; ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($c['is_anonymous']): ?>
                                        <span class="badge" style="background: rgba(236, 72, 153, 0.2); color: #f9a8d4; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                            <i class="fas fa-user-secret"></i> Anonim
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder sm me-2" style="width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-weight: bold;">
                                                <?php echo strtoupper(substr($c['username'] ?? '?', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div><?php echo htmlspecialchars($c['full_name'] ?? 'Bilinmiyor'); ?></div>
                                                <small class="text-muted">@<?php echo htmlspecialchars($c['username'] ?? 'unknown'); ?></small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; max-width: 400px; word-wrap: break-word;">
                                    <?php echo nl2br(htmlspecialchars($c['content'])); ?>
                                </td>
                                <td style="padding: 1rem; color: var(--text-muted);">
                                    <?php echo date('d.m.Y H:i', strtotime($c['created_at'])); ?>
                                </td>
                                <td style="padding: 1rem; border-radius: 0 10px 10px 0; text-align: right;">
                                    <form method="POST" action="" onsubmit="return confirmComplaintDelete(event, this);" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                        <button type="submit" class="btn btn-sm" style="color: #ef4444; background: rgba(239,68,68,0.1); border: none; width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--text-muted); opacity: 0.5;"></i>
                <p class="text-muted">Henüz hiç şikayet veya öneri gönderilmemiş.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmComplaintDelete(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu şikayeti silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal',
        background: 'rgba(255, 255, 255, 0.95)'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>