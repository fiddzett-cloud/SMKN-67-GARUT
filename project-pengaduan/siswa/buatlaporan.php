<?php
session_start();
include "../config/config.php";

if ($_SESSION['role'] != "siswa") {
    header("location:../auth/login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sarana = mysqli_real_escape_string($koneksi, $_POST['sarana']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $namaBaru = uniqid() . '.' . $ext;
            $path = "../img/" . $namaBaru;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $path)) {
                $gambar = $namaBaru;
            } else {
                $error = "Upload gambar gagal!";
            }
        } else {
            $error = "Format gambar tidak didukung!";
        }
    }

    if (empty($error)) {
        mysqli_query($koneksi, "
            INSERT INTO data_aspirasi 
            (sarana, deskripsi, status, progress_persen, gambar) 
            VALUES 
            ('$sarana', '$deskripsi', 'direncanakan', 0, '$gambar')
        ");

        $success = "Laporan berhasil dikirim!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buat Laporan</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50">

<div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-xl shadow">

    <h1 class="text-2xl font-bold mb-6 text-orange-500">Buat Laporan</h1>

    <!-- ALERT -->
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">

        <!-- SARANA -->
        <div>
            <label class="block font-semibold">Sarana</label>
            <input type="text" name="sarana" required
                class="w-full border p-2 rounded">
        </div>

        <!-- DESKRIPSI -->
        <div>
            <label class="block font-semibold">Deskripsi</label>
            <textarea name="deskripsi" rows="4" required
                class="w-full border p-2 rounded"></textarea>
        </div>

        <!-- GAMBAR -->
        <div>
            <label class="block font-semibold">Upload Gambar</label>
            <input type="file" name="gambar" class="w-full">
        </div>

        <!-- BUTTON -->
        <div class="flex gap-3">
            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded w-full">
                Kirim Laporan
            </button>

            <a href="dashboard.php"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded w-full text-center">
                Kembali
            </a>
        </div>

    </form>

</div>

</body>
</html>