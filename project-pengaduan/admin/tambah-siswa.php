<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if ($nama === '' || $email === '' || $username === '' || $password === '' || $role === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } else {
        $nama = mysqli_real_escape_string($koneksi, $nama);
        $email = mysqli_real_escape_string($koneksi, $email);
        $username = mysqli_real_escape_string($koneksi, $username);
        $role = mysqli_real_escape_string($koneksi, $role);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $id_result = mysqli_query($koneksi, "SELECT MAX(id_user) AS max_id FROM users");
        $max_row = mysqli_fetch_assoc($id_result);
        $next_id = intval($max_row['max_id']) + 1;

        $insert = mysqli_query($koneksi, "INSERT INTO users (id_user, email, nama, username, password, role) VALUES ('$next_id', '$email', '$nama', '$username', '$hashed_password', '$role')");
        if ($insert) {
            $success = 'Siswa berhasil ditambahkan.';
            $_POST = [];
        } else {
            $error = 'Gagal menyimpan siswa: ' . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Siswa - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
body {
    background: linear-gradient(135deg, #f8fafc, #fff7ed);
}

.card {
    border-radius: 16px;
}

.btn-primary {
    background: #f97316;
    border: none;
}
.btn-primary:hover {
    background: #ea580c;
}

.progress-bar {
    background: linear-gradient(90deg, #fb923c, #facc15);
}

/* Header Style */
.top-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 40;
}

.top-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
}

.top-header-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.top-header-logo img {
    height: 45px;
}

.top-header-brand {
    display: flex;
    flex-direction: column;
}

.top-header-brand span {
    font-weight: 700;
    background: linear-gradient(to right, #ea580c, #f97316);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.25rem;
}

.top-header-brand small {
    color: #6b7280;
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
</style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800">

<!-- TOP HEADER -->
<div class="top-header">
    <div class="top-header-content">
        <div class="top-header-logo">
            <img src="../img/logostm.png">
            <div class="top-header-brand">
                <span>STM</span>
                <small>AL MADANI GARUT</small>
            </div>
        </div>
        <a href="../auth/logout.php" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full font-semibold shadow-sm transition duration-200">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</div>

<!-- NAVIGATION BAR -->
<div class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="dashboard.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-house-door-fill mr-2"></i>
                    Dashboard
                </a>
                <a href="list_aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-list-check mr-2"></i>
                    List Aspirasi
                </a>
                <a href="histori-aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-clock-history mr-2"></i>
                    Histori Aspirasi
                </a>
                <a href="data-siswa.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
                    <i class="bi bi-people-fill mr-2"></i>
                    Data Siswa
                </a>
                <a href="daftarfeedback.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-chat-dots-fill mr-2"></i>
                    Daftar Feedback
                </a>
                <a href="progresperbaikan.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-graph-up mr-2"></i>
                    Progres Perbaikan
                </a>
            </nav>
            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="md:hidden hidden absolute top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-lg z-50">
                <nav class="px-2 pt-2 pb-3 space-y-1">
                    <a href="dashboard.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Dashboard
                    </a>
                    <a href="list_aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-list-check mr-2"></i>
                        List Aspirasi
                    </a>
                    <a href="histori-aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-clock-history mr-2"></i>
                        Histori Aspirasi
                    </a>
                    <a href="data-siswa.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 bg-orange-50 border-l-4 border-orange-600">
                        <i class="bi bi-people-fill mr-2"></i>
                        Data Siswa
                    </a>
                    <a href="daftarfeedback.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-chat-dots-fill mr-2"></i>
                        Daftar Feedback
                    </a>
                    <a href="progresperbaikan.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-graph-up mr-2"></i>
                        Progres Perbaikan
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold">Tambah Siswa</h1>
                <p class="text-sm text-gray-500">Isi data lengkap siswa baru.</p>
            </div>
            <a href="data-siswa.php" class="text-sm text-orange-600 hover:underline">Kembali ke Data Siswa</a>
        </div>

        <?php if ($success): ?>
            <div class="mb-6 rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
                    <option value="" <?= empty($_POST['role']) ? 'selected' : '' ?>>-- Pilih Role --</option>
                    <option value="siswa" <?= ($_POST['role'] ?? '') === 'siswa' ? 'selected' : '' ?>>Siswa</option>
                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="flex flex-col gap-3 md:flex-row">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-6 py-3 text-white font-semibold hover:bg-orange-600 transition">Tambah Siswa</button>
                <a href="data-siswa.php" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-6 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});
</script>

</body>
</html>