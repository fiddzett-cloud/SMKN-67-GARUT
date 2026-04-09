<?php
session_start();
require "../config/config.php";

/* =====================
   AUTH
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

/* =====================
   DELETE MESSAGE
===================== */
if (isset($_POST['action']) && $_POST['action'] === 'delete_message') {
    $msg_id = (int) $_POST['message_id'];
    $stmt = $koneksi->prepare("DELETE FROM messages WHERE id = ? AND id_user = ?");
    $stmt->bind_param("ii", $msg_id, $id_user);
    $stmt->execute();
    $stmt->close();
    header("Location: chatsiswa.php");
    exit;
}

/* =====================
   SEND MESSAGE
===================== */
if (isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $koneksi->prepare("INSERT INTO messages (id_user, sender, message, created_at) VALUES (?, 'siswa', ?, NOW())");
        $stmt->bind_param("is", $id_user, $msg);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: chatsiswa.php");
    exit;
}

/* =====================
   GET MESSAGES
===================== */
$stmt = $koneksi->prepare("SELECT id, sender, message, created_at FROM messages WHERE id_user = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800 pb-24">

<!-- MINIMALIST HEADER -->
<header class="bg-gradient-to-r from-white via-orange-50 to-white shadow-lg border-b border-orange-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="../img/logostm.png" class="h-10">
            <div class="hidden sm:block">
                <span class="text-lg font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">STM</span>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">AL MADANI GARUT</p>
            </div>
        </div>
        <a href="../auth/logout.php" class="flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            <i class="bi bi-box-arrow-right"></i>
            <span class="hidden md:inline">Logout</span>
        </a>
    </div>
</header>

<div class="flex min-h-screen">

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <div class="bg-white border-b p-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="md:hidden text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Ruang Konsultasi</h1>
                    <p class="text-xs text-green-500 font-semibold flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Admin SuaraSiswa
                    </p>
                </div>
            </div>
        </div>

        <div id="chatBox" class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
            
            <?php if ($res->num_rows == 0): ?>
                <div class="text-center py-10">
                    <div class="bg-white inline-block p-4 rounded-2xl shadow-sm border">
                        <p class="text-sm text-gray-500">Belum ada percakapan. Silahkan kirim pesan untuk memulai konsultasi.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php while ($row = $res->fetch_assoc()): ?>

                <?php if ($row['sender'] === 'siswa'): ?>
                    <div class="flex justify-end group">
                        <div class="max-w-[80%] md:max-w-md">
                            <div class="bg-orange-500 text-white p-4 rounded-2xl rounded-tr-none shadow-md shadow-orange-200">
                                <p class="text-sm leading-relaxed"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                                <div class="text-[10px] text-orange-100 mt-2 flex justify-between items-center italic">
                                    <span><?= date('d M, H:i', strtotime($row['created_at'])) ?></span>
                                    <form method="post" onsubmit="return confirm('Hapus pesan ini?')">
                                        <input type="hidden" name="action" value="delete_message">
                                        <input type="hidden" name="message_id" value="<?= $row['id'] ?>">
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity text-white hover:text-red-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="flex justify-start">
                        <div class="max-w-[80%] md:max-w-md">
                            <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100">
                                <p class="text-sm text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                                <div class="text-[10px] text-gray-400 mt-2 italic">
                                    <?= date('d M, H:i', strtotime($row['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endwhile; ?>
        </div>

        <div class="bg-white p-4 border-t shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <form method="post" class="max-w-4xl mx-auto flex gap-3 items-end">
                <div class="flex-1 bg-gray-100 rounded-2xl px-4 py-2 flex items-center focus-within:ring-2 focus-within:ring-orange-300 transition">
                    <textarea
                        name="message"
                        rows="1"
                        placeholder="Ketik pesan aspirasi Anda..."
                        class="w-full bg-transparent border-none focus:outline-none text-sm py-1 resize-none"
                        required></textarea>
                </div>

                <button class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-xl shadow-lg shadow-orange-200 transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
        </div>

    </main>
</div>

<script>
    const chatBox = document.getElementById("chatBox");
    if(chatBox){
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    
    // Auto-resize textarea
    const tx = document.getElementsByTagName('textarea');
    for (let i = 0; i < tx.length; i++) {
      tx[i].setAttribute('style', 'height:' + (tx[i].scrollHeight) + 'px;overflow-y:hidden;');
      tx[i].addEventListener("input", OnInput, false);
    }

    function OnInput() {
      this.style.height = 'auto';
      this.style.height = (this.scrollHeight) + 'px';
    }
</script>

</body>
</html>