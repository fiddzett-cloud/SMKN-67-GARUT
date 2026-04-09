<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die("ID tidak ditemukan.");
}

$result = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'");
if (!$result || mysqli_num_rows($result) === 0) {
    die("Data siswa tidak ditemukan.");
}

$row = mysqli_fetch_assoc($result);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if ($nama === '' || $email === '' || $username === '' || $role === '') {
        $error = 'Nama, email, username, dan role wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } else {
        $nama = mysqli_real_escape_string($koneksi, $nama);
        $email = mysqli_real_escape_string($koneksi, $email);
        $username = mysqli_real_escape_string($koneksi, $username);
        $role = mysqli_real_escape_string($koneksi, $role);
        $password_update = '';

        if ($password !== '') {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $password_update = ", password='$hashed_password'";
        }

        $query = "UPDATE users SET email='$email', nama='$nama', username='$username', role='$role' $password_update WHERE id_user='$id'";
        if (mysqli_query($koneksi, $query)) {
            $success = 'Data siswa berhasil diperbarui.';
            $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'"));
        } else {
            $error = 'Gagal memperbarui siswa: ' . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Siswa - Admin</title>
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


<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold">Edit Siswa</h1>
                <p class="text-sm text-gray-500">Perbarui data siswa atau admin.</p>
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
                <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? $row['nama']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $row['email']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? $row['username']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password (biarkan kosong jika tidak diubah)</label>
                <input type="password" name="password" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
                    <option value="siswa" <?= (($_POST['role'] ?? $row['role']) === 'siswa') ? 'selected' : '' ?>>Siswa</option>
                    <option value="admin" <?= (($_POST['role'] ?? $row['role']) === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="flex flex-col gap-3 md:flex-row">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-6 py-3 text-white font-semibold hover:bg-orange-600 transition">Simpan Perubahan</button>
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