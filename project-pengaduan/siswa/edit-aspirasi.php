<?php
session_start();
include "../config/config.php";

if ($_SESSION['role'] != "siswa") {
    header("location:../auth/login.php");
    exit;
}

// Cek ID yang akan diedit
if (!isset($_GET['id'])) {
    header("location:dashboard.php");
    exit;
}

$id = $_GET['id'];

// Ambil data aspirasi berdasarkan ID
// Karena kamu menggunakan fallback tabel di dashboard, kita pastikan tabel yang benar di sini
$query = mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE id_aspirasi = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

// Proses Update Data
if (isset($_POST['update'])) {
    $sarana = mysqli_real_escape_string($koneksi, $_POST['sarana']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];

    // Cek apakah ada file gambar baru yang diunggah
    if ($_FILES['gambar']['name'] != "") {
        $filename = $_FILES['gambar']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $new_name = "ASP-" . time() . "." . $ext;
        $tmp_name = $_FILES['gambar']['tmp_name'];

        if (move_uploaded_file($tmp_name, "../img/" . $new_name)) {
            // Hapus gambar lama jika ada
            if ($gambar_lama != "" && file_exists("../img/" . $gambar_lama)) {
                unlink("../img/" . $gambar_lama);
            }
            $file_db = $new_name;
        }
    } else {
        $file_db = $gambar_lama;
    }

    $update = mysqli_query($koneksi, "UPDATE data_aspirasi SET 
                sarana = '$sarana', 
                deskripsi = '$deskripsi', 
                gambar = '$file_db' 
                WHERE id_aspirasi = '$id'");

    if ($update) {
        echo "<script>alert('Aspirasi berhasil diperbarui!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aspirasi - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #f8fafc, #fff7ed); }
    </style>
</head>
<body class="text-gray-800">

<div class="max-w-4xl mx-auto p-6 lg:p-12">
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="dashboard.php" class="text-gray-500 hover:text-orange-600 text-sm font-medium">Dashboard</a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-400 text-sm font-medium">Edit Aspirasi</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-orange-500 p-8 text-white">
            <h2 class="text-2xl font-bold flex items-center gap-3">
                <i class="bi bi-pencil-square"></i> Edit Aspirasi Anda
            </h2>
            <p class="text-orange-100 text-sm mt-1">Perbarui detail laporan atau sarana yang ingin diperbaiki.</p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            <input type="hidden" name="gambar_lama" value="<?= $data['gambar'] ?>">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sarana / Fasilitas</label>
                <input type="text" name="sarana" 
                       value="<?= htmlspecialchars($data['sarana']) ?>" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" 
                       placeholder="Contoh: Toilet Gedung A, Kursi Kelas..." required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Kerusakan / Keluhan</label>
                <textarea name="deskripsi" rows="5" 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" 
                          placeholder="Jelaskan secara detail..." required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Kondisi (Opsional)</label>
                <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="shrink-0 text-center">
                        <?php if (!empty($data['gambar'])): ?>
                            <img src="../img/<?= $data['gambar'] ?>" class="h-24 w-24 object-cover rounded-xl shadow-md border-2 border-white">
                            <p class="text-[10px] text-gray-400 mt-2 italic">Gambar Saat Ini</p>
                        <?php else: ?>
                            <div class="h-24 w-24 bg-gray-200 rounded-xl flex items-center justify-center">
                                <i class="bi bi-image text-3xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="gambar" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-orange-100 file:text-orange-700 hover:file:bg-orange-200 transition">
                        <p class="mt-2 text-xs text-gray-400 italic">*Pilih file baru jika ingin mengganti foto.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="dashboard.php" class="flex-1 text-center py-3 rounded-xl border border-gray-200 text-gray-500 font-bold hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" name="update" class="flex-[2] bg-orange-500 text-white py-3 rounded-xl font-bold hover:bg-orange-600 shadow-lg shadow-orange-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>