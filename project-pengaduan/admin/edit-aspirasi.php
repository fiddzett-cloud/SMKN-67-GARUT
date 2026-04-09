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

$result = mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE id_aspirasi='$id'");
if (!$result || mysqli_num_rows($result) === 0) {
    die("Data pengaduan tidak ditemukan.");
}

$row = mysqli_fetch_assoc($result);
$error = '';
$success = '';

if (isset($_POST['update'])) {
    $sarana = trim($_POST['sarana']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    $progress_persen = intval($_POST['progress_persen']);
    $tanggal_mulai = trim($_POST['tanggal_mulai']);
    $tanggal_selesai = trim($_POST['tanggal_selesai']);

    if ($sarana === '') {
        $error = "Sarana tidak boleh kosong.";
    } elseif ($deskripsi === '') {
        $error = "Deskripsi tidak boleh kosong.";
    } elseif ($status === '') {
        $error = "Status harus dipilih.";
    } elseif ($progress_persen < 0 || $progress_persen > 100) {
        $error = "Progress harus antara 0 dan 100.";
    } elseif ($tanggal_mulai === '') {
        $error = "Tanggal mulai tidak boleh kosong.";
    } else {
        $sarana = mysqli_real_escape_string($koneksi, $sarana);
        $deskripsi = mysqli_real_escape_string($koneksi, $deskripsi);
        $status = mysqli_real_escape_string($koneksi, $status);
        $tanggal_mulai = mysqli_real_escape_string($koneksi, $tanggal_mulai);
        $tanggal_selesai = $tanggal_selesai === '' ? "NULL" : "'" . mysqli_real_escape_string($koneksi, $tanggal_selesai) . "'";

        $query = "UPDATE data_aspirasi SET 
            sarana='$sarana',
            deskripsi='$deskripsi',
            status='$status',
            progress_persen=$progress_persen,
            tanggal_mulai='$tanggal_mulai',
            tanggal_selesai=$tanggal_selesai
            WHERE id_aspirasi='$id'";

        if (mysqli_query($koneksi, $query)) {
            $success = "Data pengaduan berhasil diperbarui.";
            $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE id_aspirasi='$id'"));
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Pengaduan</title>
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

<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800 pb-24">



<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold">Edit Pengaduan</h1>
                <p class="text-sm text-gray-500">Perbarui data pengaduan sarana sekolah.</p>
            </div>
            <a href="dashboard.php" class="text-sm text-orange-600 hover:underline">Kembali ke Dashboard</a>
        </div>

        <?php if ($success): ?>
            <div class="mb-6 rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sarana</label>
                <input type="text" name="sarana" value="<?= htmlspecialchars($_POST['sarana'] ?? $row['sarana']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="5" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required><?= htmlspecialchars($_POST['deskripsi'] ?? $row['deskripsi']) ?></textarea>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="direncanakan" <?= (($_POST['status'] ?? $row['status']) === 'direncanakan') ? 'selected' : '' ?>>Direncanakan</option>
                        <option value="diproses" <?= (($_POST['status'] ?? $row['status']) === 'diproses') ? 'selected' : '' ?>>Diproses</option>
                        <option value="selesai" <?= (($_POST['status'] ?? $row['status']) === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Progress (%)</label>
                    <input type="number" name="progress_persen" min="0" max="100" value="<?= htmlspecialchars($_POST['progress_persen'] ?? $row['progress_persen']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? $row['tanggal_mulai']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? $row['tanggal_selesai']) ?>" class="w-full rounded-2xl border border-gray-200 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
            </div>

            <div class="flex flex-col gap-3 md:flex-row">
                <button type="submit" name="update" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-6 py-3 text-white font-semibold hover:bg-orange-600 transition">Simpan Perubahan</button>
                <a href="dashboard.php" class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-6 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>


</body>
</html>