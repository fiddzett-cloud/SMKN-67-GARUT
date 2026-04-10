<?php
session_start();
include "../config/config.php";

// Proteksi halaman
if ($_SESSION['role'] != "admin") {
    header("location:../auth/login.php");
    exit;
}

// Ambil nama user dari session (Pastikan saat login kamu sudah menyimpan $_SESSION['nama'])
// Jika belum ada, kita gunakan fallback 'Admin'
$nama_user = $_SESSION['nama'] ?? 'Admin';

// Fungsi cek tabel (sama seperti dashboard kamu)
function tableExists($conn, $tableName) {
    $tableName = mysqli_real_escape_string($conn, $tableName);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return $result && mysqli_num_rows($result) > 0;
}

$table = tableExists($koneksi, 'data_aspirasi') ? 'data_aspirasi' : 'progres_perbaikan';
$dateField = ($table === 'data_aspirasi') ? 'created_at' : 'tanggal_mulai';

// Query semua data aspirasi milik siswa yang sedang login 
// (Asumsi ada kolom 'id_user' atau 'username' di tabel aspirasi kamu untuk memfilter)
// Jika ingin menampilkan semua untuk testing, hapus bagian WHERE
$query = mysqli_query($koneksi, "SELECT * FROM $table ORDER BY $dateField DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Aspirasi - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #f8fafc, #fff7ed); }
        .nav-item { transition: all 0.2s ease; border-bottom: 2px solid transparent; }
        .nav-item:hover, .nav-item.active { color: #ea580c !important; border-color: #ea580c !important; }
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
    </style>
</head>
<body class="text-gray-800">

<header class="bg-white py-3 px-6 border-b shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="../img/logostm.png" class="h-10">
            <div class="flex flex-col">
                <span class="font-bold text-orange-600 text-lg leading-tight">STM</span>
                <small class="text-gray-500 text-[10px] tracking-widest uppercase">Al Madani Garut</small>
            </div>
        </div>
        <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-600 transition">Logout</a>
    </div>
</header>


<nav class="bg-white border-b border-gray-200 shadow-sm sticky top-[65px] z-40">
    <div class="max-w-7xl mx-auto px-4 overflow-x-auto">
        <div class="flex space-x-6 h-14 items-center whitespace-nowrap">
            <a href="dashboard.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500 hover:text-orange-600">
                <i class="bi bi-house-door mr-2"></i> Dashboard
            </a>
            <a href="list_aspirasi.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500 hover:text-orange-600">
                <i class="bi bi-list-check mr-2"></i> List Aspirasi
            </a>
            <a href="histori-aspirasi.php" class="nav-item border-orange-600 text-orange-600 flex items-center h-full px-2 text-sm font-bold border-b-2">
                <i class="bi bi-clock-history mr-2"></i> Histori Aktivitas
            </a>
            <a href="data-siswa.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500 hover:text-orange-600">
                <i class="bi bi-people mr-2"></i> Data Siswa
            </a>
            <a href="progresperbaikan.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500 hover:text-orange-600">
                <i class="bi bi-graph-up mr-2"></i> Progres
            </a>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto p-6 lg:p-10">
    <div class="mb-10">
        <h1 class="text-2xl font-bold text-gray-800">Histori Aktivitas </h1>
        <p class="text-sm text-gray-500">Jejak aspirasi yang telah Anda sampaikan ke sekolah.</p>
    </div>

    <div class="relative timeline-line">
        <?php if (mysqli_num_rows($query) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                <div class="relative pl-12 mb-8">
                    <div class="absolute left-0 top-1 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center z-10 border-4 border-white shadow-sm">
                        <i class="bi bi-check2-circle text-orange-600"></i>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 mb-3">
                            <span class="text-xs font-bold text-orange-500 uppercase tracking-wider">
                                <i class="bi bi-calendar3 mr-1"></i> 
                                <?= date('d M Y, H:i', strtotime($row[$dateField])) ?>
                            </span>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase 
                                <?= ($row['status'] == 'selesai') ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </div>

                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-bold text-gray-900"><?= htmlspecialchars($nama_user) ?></span> 
                            telah mengajukan aspirasi mengenai 
                            <span class="font-semibold text-orange-600">"<?= htmlspecialchars($row['sarana']) ?>"</span>.
                        </p>
                        
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl text-sm text-gray-500 italic">
                            "<?= htmlspecialchars($row['deskripsi']) ?>"
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="text-center py-20">
                <i class="bi bi-journal-x text-5xl text-gray-200"></i>
                <p class="mt-4 text-gray-400">Belum ada riwayat aspirasi.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>