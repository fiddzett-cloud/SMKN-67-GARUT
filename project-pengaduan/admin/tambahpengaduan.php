<?php
session_start();
include "../config/config.php";

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

/* =====================
   HANDLE FORM SUBMISSION
===================== */
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sarana = trim($_POST['sarana']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    $progress_persen = intval($_POST['progress_persen']);
    $tanggal_mulai = trim($_POST['tanggal_mulai']);
    $tanggal_selesai = trim($_POST['tanggal_selesai']);

    // VALIDASI
    if (empty($sarana)) {
        $error = "Sarana tidak boleh kosong";
    } elseif (empty($deskripsi)) {
        $error = "Deskripsi tidak boleh kosong";
    } elseif (empty($status)) {
        $error = "Status harus dipilih";
    } elseif ($progress_persen < 0 || $progress_persen > 100) {
        $error = "Progress harus antara 0-100";
    } elseif (empty($tanggal_mulai)) {
        $error = "Tanggal mulai tidak boleh kosong";
    } else {

        /* =====================
           HANDLE UPLOAD GAMBAR
        ===================== */
        $gambar = '';

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newname = uniqid() . '.' . $ext;
                $destination = '../img/' . $newname;

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $destination)) {
                    $gambar = $newname;
                } else {
                    $error = "Gagal upload gambar";
                }
            } else {
                $error = "Format gambar harus JPG, JPEG, PNG, atau GIF";
            }
        }

        /* =====================
           INSERT DATABASE
        ===================== */
        if (empty($error)) {
            $sarana = mysqli_real_escape_string($koneksi, $sarana);
            $deskripsi = mysqli_real_escape_string($koneksi, $deskripsi);
            $status = mysqli_real_escape_string($koneksi, $status);
            $tanggal_mulai = mysqli_real_escape_string($koneksi, $tanggal_mulai);

            $tanggal_selesai = !empty($tanggal_selesai)
                ? "'" . mysqli_real_escape_string($koneksi, $tanggal_selesai) . "'"
                : "NULL";

            $query = mysqli_query($koneksi, "
                INSERT INTO data_aspirasi 
                (sarana, deskripsi, status, progress_persen, tanggal_mulai, tanggal_selesai, gambar)
                VALUES 
                ('$sarana', '$deskripsi', '$status', $progress_persen, '$tanggal_mulai', $tanggal_selesai, '$gambar')
            ");

            if ($query) {
                $success = "Pengaduan berhasil ditambahkan!";
                $_POST = [];
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengaduan - SuaraSiswa</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #f8fafc, #fff7ed);
        }
    </style>
</head>

<body>

<main class="max-w-3xl mx-auto p-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Tambah Pengaduan</h1>
        <p class="text-gray-500">Tambahkan data pengaduan sarana sekolah</p>
    </div>

    <!-- SUCCESS -->
    <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- ERROR -->
    <?php if ($error): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="bg-white p-6 rounded-xl shadow">
        <form method="POST" enctype="multipart/form-data" class="space-y-4">

            <input type="text" name="sarana" placeholder="Sarana"
                value="<?= htmlspecialchars($_POST['sarana'] ?? '') ?>"
                class="w-full border p-2 rounded">

            <textarea name="deskripsi" placeholder="Deskripsi"
                class="w-full border p-2 rounded"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>

            <select name="status" class="w-full border p-2 rounded">
                <option value="">-- Pilih Status --</option>
                <option value="direncanakan">Direncanakan</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>

            <input type="number" name="progress_persen" min="0" max="100"
                value="<?= htmlspecialchars($_POST['progress_persen'] ?? 0) ?>"
                class="w-full border p-2 rounded">

            <input type="date" name="tanggal_mulai"
                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '') ?>"
                class="w-full border p-2 rounded">

            <input type="date" name="tanggal_selesai"
                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '') ?>"
                class="w-full border p-2 rounded">

            <input type="file" name="gambar" class="w-full">

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-orange-500 text-white py-2 rounded hover:bg-orange-600">
                    Simpan
                </button>

                <a href="dashboard.php"
                    class="flex-1 bg-gray-500 text-white py-2 rounded text-center hover:bg-gray-600">
                    Batal
                </a>
            </div>

        </form>
    </div>

</main>

</body>
</html>