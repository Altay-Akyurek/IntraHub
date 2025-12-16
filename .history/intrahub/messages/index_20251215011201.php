<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
$user = current_user();

// Kullanıcıları çek
$stmt = $pdo->query("SELECT id, username, full_name, email, role FROM users WHERE id != " . $user['id'] . " ORDER BY username ASC");
$users = $stmt->fetchAll();

$pageTitle = 'Mesajlar';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4 fade-in-up">
    <div class="row"
        style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; height: calc(100vh - 150px); min-height: 500px;">
        <!-- Users List -->
        <div class="glass-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div
                style="padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2);">
                <h5
                    style="margin: 0; font-weight: 800; background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Sohbetler:</h5>
                <button class="btn btn-sm btn-primary" onclick="openNewChatModal()">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-inline"> Yeni</span>
                </button>
            </div>
            <div style="padding: 0 1rem 1rem 1rem;">
                <input type="text" id="userSearch" class="form-control mt-3" placeholder="Sohbetlerde Ara..."
                    style="margin-bottom: 0;">
            </div>

            <div class="user-list" style="overflow-y: auto; flex: 1;" id="activeChatsList">
                <?php foreach ($users as $u): ?>
                    <a href="#" class="user-item" data-id="<?= $u['id'] ?>"
                        data-username="<?= e($u['full_name'] ?: $u['username']) ?>">
                        <div class="avatar"><?= strtoupper(substr($u['username'], 0, 1)) ?></div>
                        <div class="user-info">
                            <div class="name"><?= e($u['full_name'] ?: $u['username']) ?></div>
                            <small class="text-muted">
                                <?= e($u['role'] === 'admin' ? 'Yönetici' : 'Çalışan') ?>
                            </small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="glass-card" id="chatContainer"
            style="display: flex; flex-direction: column; position: relative; height: 100%; overflow: hidden;">
            <div class="chat-header" id="chatHeader"
                style="display: none; padding: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); margin-bottom: 0; background: rgba(0,0,0,0.2);">
                <h5 style="margin: 0; font-weight: 700; background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"
                    id="chatTitle">Kullanıcı Seçin</h5>
            </div>

            <div id="messagesArea"
                style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 10px;">
                <div class="text-center text-muted mt-5" id="placholderText">
                    <i class="fas fa-comments fa-3x mb-3"></i><br>
                    Bir kullanıcı seçerek mesajlaşmaya başlayın.
                </div>
            </div>

            <div class="chat-input-area" id="inputArea"
                style="display: none; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <form id="messageForm" style="display: flex; gap: 10px;">
                    <input type="text" name="content" class="form-control" placeholder="Bir mesaj yazın..."
                        style="margin-bottom: 0;" required autocomplete="off">
                    <button type="submit" class="btn btn-primary" style="width: auto; padding: 0 1.5rem;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal-overlay" id="newChatModal"
    style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-card" style="width: 100%; max-width: 400px; margin: 2rem;">
        <h5 class="mb-3"
            style="font-weight: 700; background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Yeni Sohbet Başlat</h5>
        <input type="text" id="modalSearch" class="form-control mb-3" placeholder="Kullanıcı adı ara..."
            style="background: rgba(0,0,0,0.2);">
        <div id="modalUserList" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($users as $u): ?>
                <div class="d-flex align-items-center p-2 border-bottom user-select-item"
                    style="cursor: pointer; border-color: rgba(255,255,255,0.1) !important;"
                    onclick="startChat(<?= $u['id'] ?>, '<?= e($u['full_name'] ?: $u['username']) ?>')">
                    <div class="avatar" style="width: 32px; height: 32px; font-size: 0.8rem; margin-right: 10px;">
                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                    </div>
                    <div><?= e($u['full_name'] ?: $u['username']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-3 text-end">
            <button class="btn btn-secondary btn-sm"
                onclick="document.getElementById('newChatModal').style.display='none'">Kapat</button>
        </div>
    </div>
</div>

<style>
    .user-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: background 0.3s;
        color: var(--text-main);
        text-decoration: none;
    }

    .user-item:hover,
    .user-item.active {
        background: rgba(255, 255, 255, 0.05);
        color: var(--primary-color);
    }

    .avatar {
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 12px;
        position: relative;
        font-size: 0.95rem;
        word-wrap: break-word;
    }

    .message-sent {
        background: var(--primary-color);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }

    .message-received {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-main);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 4px;
        text-align: right;
    }

    .user-select-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>

<script>
    let currentChatId = 0;
    let lastMessageId = 0;
    let oldestMessageId = 0;
    let isLoadingHistory = false;

    const messagesArea = document.getElementById('messagesArea');
    const inputArea = document.getElementById('inputArea');
    const chatHeader = document.getElementById('chatHeader');
    const chatTitle = document.getElementById('chatTitle');
    const placeholderText = document.getElementById('placholderText');
    const messageForm = document.getElementById('messageForm');

    // Setup standard user list clicks
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            startChat(item.dataset.id, item.dataset.username);
        });
    });

    // Modal Search
    document.getElementById('modalSearch').addEventListener('input', (e) => {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('.user-select-item').forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(val) ? 'flex' : 'none';
        });
    });

    // Main Search
    document.getElementById('userSearch').addEventListener('input', (e) => {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.dataset.username.toLowerCase();
            item.style.display = name.includes(val) ? 'flex' : 'none';
        });
    });

    // Scroll listener for history (threshold 50px)
    messagesArea.addEventListener('scroll', () => {
        if (messagesArea.scrollTop < 50 && currentChatId > 0 && !isLoadingHistory) {
            fetchHistory();
        }
    });

    // Wheel listener
    messagesArea.addEventListener('wheel', (e) => {
        if (e.deltaY < 0 && messagesArea.scrollTop < 10 && currentChatId > 0 && !isLoadingHistory) {
            fetchHistory();
        }
    });

    // Add Load More Button dynamically
    function showLoadMoreButton() {
        if (document.getElementById('loadMoreBtn')) return;

        const btn = document.createElement('button');
        btn.id = 'loadMoreBtn';
        btn.className = 'btn btn-sm btn-secondary w-100 mb-3';
        btn.style.background = 'rgba(255,255,255,0.05)';
        btn.style.border = 'none';
        btn.style.color = 'var(--text-muted)';
        btn.innerText = 'Daha Eski Mesajları Yükle';
        btn.onclick = fetchHistory;

        messagesArea.insertBefore(btn, messagesArea.firstChild);
    }

    function openNewChatModal() {
        document.getElementById('newChatModal').style.display = 'flex';
    }

    function startChat(id, name) {
        if (currentChatId === id) return; // Prevent re-rendering same chat
        currentChatId = id;
        lastMessageId = 0; // Reset for new chat
        oldestMessageId = 0;
        chatTitle.textContent = name;

        // Highlight active user in the list
        document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
        const activeItem = document.querySelector(`.user-item[data-id="${id}"]`);
        if (activeItem) activeItem.classList.add('active');

        // Close modal if open
        document.getElementById('newChatModal').style.display = 'none';

        // UI Reset
        inputArea.style.display = 'block';
        chatHeader.style.display = 'block';
        placeholderText.style.display = 'none';
        messagesArea.innerHTML = ''; // Clear for new chat

        fetchMessages(true); // Initial load
    }

    messageForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = messageForm.querySelector('input[name="content"]');
        const content = input.value.trim();

        if (!content || currentChatId === 0) return;

        const btn = messageForm.querySelector('button');
        input.disabled = true;
        btn.disabled = true;

        fetch('post_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ to: currentChatId, content: content })
        })
            .then(r => r.json())
            .then(data => {
                input.disabled = false;
                btn.disabled = false;

                if (data.ok) {
                    input.value = '';
                    input.focus();
                    fetchMessages(); // Refresh immediately
                } else {
                    alert('Hata: ' + (data.error || 'Mesaj gönderilemedi'));
                }
            })
            .catch(err => {
                input.disabled = false;
                btn.disabled = false;
                console.error(err);
                alert('Bağlantı hatası!');
            });
    });

    // Poll
    setInterval(() => {
        if (currentChatId > 0) fetchMessages();
    }, 3000);

    function fetchHistory() {
        if (oldestMessageId === 0) return;
        isLoadingHistory = true;

        // Show loader
        let loader = document.getElementById('historyLoader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'historyLoader';
            loader.className = 'text-center text-muted col-12 my-2';
            loader.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
            messagesArea.insertBefore(loader, messagesArea.firstChild);
        }

        // Save scroll height
        const beforeScrollHeight = messagesArea.scrollHeight;

        fetch(`fetch_message.php?user=${currentChatId}&before_id=${oldestMessageId}&limit=10`)
            .then(r => r.json())
            .then(msgs => {
                isLoadingHistory = false;
                if (loader) loader.remove();

                if (!msgs || msgs.length === 0) {
                    // No more messages
                    // Only show "End of history" if we actually have some messages
                    if (messagesArea.children.length > 0) {
                        const info = document.createElement('div');
                        info.className = 'text-center text-muted small my-2';
                        info.innerText = 'Mesaj geçmişinin sonu.';
                        messagesArea.insertBefore(info, messagesArea.firstChild);

                        // Auto remove after 2s to not clutter
                        setTimeout(() => info.remove(), 2000);
                    }

                    oldestMessageId = 0;
                    return;
                }

                const fragment = document.createDocumentFragment();
                msgs.forEach(m => {
                    const div = createMessageDiv(m);
                    fragment.appendChild(div);
                    if (oldestMessageId === 0 || parseInt(m.id) < oldestMessageId) {
                        oldestMessageId = parseInt(m.id);
                    }
                });

                // Insert at top
                messagesArea.insertBefore(fragment, messagesArea.firstChild);

                // Restore scroll position
                messagesArea.scrollTop = messagesArea.scrollHeight - beforeScrollHeight;
            })
            .catch(() => {
                isLoadingHistory = false;
                if (loader) loader.remove();
            });
    }

    function fetchMessages(isInitial = false) {
        if (currentChatId === 0) return;

        // Use since_id to only get new messages
        // For initial load, we send NO params to get default 6
        let url = `fetch_message.php?user=${currentChatId}`;
        if (!isInitial) {
            url += `&since_id=${lastMessageId}`;
        } else {
            // Initial load: logic in PHP will handle LIMIT 6
        }

        fetch(url)
            .then(r => r.json())
            .then(msgs => {
                if (!msgs || msgs.length === 0) {
                    if (isInitial && lastMessageId === 0) {
                        messagesArea.innerHTML = '<div class="text-center text-muted mt-5" id="noMsgInfo">Henüz mesaj yok.</div>';
                    }
                    return;
                }

                // Remove "no messages" placeholder
                const noMsg = document.getElementById('noMsgInfo');
                if (noMsg) noMsg.remove();

                const isAtBottom = messagesArea.scrollHeight - messagesArea.scrollTop <= messagesArea.clientHeight + 100;

                msgs.forEach(m => {
                    if (parseInt(m.id) <= lastMessageId) return; // duplicate check

                    const div = createMessageDiv(m);
                    messagesArea.appendChild(div);

                    lastMessageId = Math.max(lastMessageId, parseInt(m.id));

                    // Track oldest separately for history
                    if (oldestMessageId === 0 || parseInt(m.id) < oldestMessageId) {
                        oldestMessageId = parseInt(m.id);
                    }
                });

                if (isInitial || isAtBottom) {
                    scrollToBottom();
                }
            })
            .catch(err => console.error("Fetch error:", err));
    }

    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function createMessageDiv(m) {
        m.is_me = (m.sender_id == <?= $user['id'] ?>);
        const div = document.createElement('div');
        div.className = `message-bubble ${m.is_me ? 'message-sent' : 'message-received'}`;
        div.dataset.id = m.id;

        div.innerHTML = `
            ${e(m.content)}
            <div class="message-time">${new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
        `;
        return div;
    }

    function e(str) {
        return str.replace(/[\u00A0-\u9999<>&]/gim, function (i) {
            return '&#' + i.charCodeAt(0) + ';';
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>