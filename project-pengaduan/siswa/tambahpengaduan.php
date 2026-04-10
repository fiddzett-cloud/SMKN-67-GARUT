<?php
session_start();
include "../config/config.php";

if ($_SESSION['role'] != "siswa") {
    header("location:../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_siswa = $_SESSION['id_siswa'];
    $sarana = mysqli_real_escape_string($koneksi, $_POST['sarana']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status = 'menunggu'; // Status awal
    $created_at = date('Y-m-d H:i:s');

    // Upload gambar
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../img/";
        $target_file = $target_dir . basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = basename($_FILES['gambar']['name']);
            }
        }
    }

    // Simpan ke database
    $query = "INSERT INTO data_aspirasi (id_siswa, sarana, deskripsi, gambar, status, created_at) VALUES ('$id_siswa', '$sarana', '$deskripsi', '$gambar', '$status', '$created_at')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: dashboard.php?success=1");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Aspirasi - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow">
        <h1 class="text-2xl font-bold mb-4">Tambah Aspirasi</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium">Sarana</label>
                <input type="text" name="sarana" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Deskripsi</label>
                <textarea name="deskripsi" class="w-full p-2 border rounded" rows="4" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Upload Gambar</label>
                <input type="file" name="gambar" accept="image/*" class="w-full p-2 border rounded">
            </div>
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Kirim Aspirasi</button>
            <a href="dashboard.php" class="ml-4 text-gray-500">Kembali</a>
        </form>
    </div>
</body>
</html>