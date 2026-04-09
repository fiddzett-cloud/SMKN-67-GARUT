<?php
session_start();
include "../config/config.php";

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data
$query = "SELECT * FROM data_aspirasi ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);


if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Statistik
$total = mysqli_num_rows($result);
$selesai = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE status='selesai'"));
$proses = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE status='diproses'"));
$rencana = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE status='direncanakan'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
body {
    background: linear-gradient(135deg, #f8fafc, #fff7ed);
    padding-bottom: 120px;
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
        <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill">
            <i class="bi bi-box-arrow-right"></i> Logout
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
                <a href="dashboard.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
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
                <a href="data-siswa.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-people-fill mr-2"></i>
                    Data Siswa
                </a>
               
                <a href="progresperbaikan.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-graph-up mr-2"></i>
                    Progres Perbaikan
                </a>
            </nav>
            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="md:hidden hidden absolute top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-lg z-50">
                <nav class="px-2 pt-2 pb-3 space-y-1">
                    <a href="dashboard.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 bg-orange-50 border-l-4 border-orange-600">
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
                    <a href="data-siswa.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
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

<div class="flex min-h-screen">

    <!-- MAIN -->
    <main class="flex-1 p-8">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">

            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard STM</h1>
                    <p class="text-sm text-gray-500">AL MADANI GARUT</p>
                </div>
            </div>
             <a href="tambahpengaduan.php" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold shadow transition">
                + Tambah Aspirasi
            </a>
        </div>

         

        <!-- STATISTIK -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

            <div class="bg-white p-5 rounded-2xl shadow border hover:shadow-lg transition">
                <p class="text-sm text-gray-500">Total</p>
                <h2 class="text-3xl font-bold text-orange-500"><?= $total ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Selesai</p>
                <h2 class="text-3xl font-bold text-green-500"><?= $selesai ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Diproses</p>
                <h2 class="text-3xl font-bold text-blue-500"><?= $proses ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Direncanakan</p>
                <h2 class="text-3xl font-bold text-yellow-500"><?= $rencana ?></h2>
            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow p-6 border">

            <h2 class="text-lg font-semibold mb-4">Data Progres Perbaikan</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">Sarana</th>
                            <th class="px-3 py-2 text-left">Deskripsi</th>
                            <th class="px-3 py-2 text-center">Gambar</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Progress</th>
                            <th class="px-3 py-2 text-left">Tanggal Mulai</th>
                            <th class="px-3 py-2 text-left">Tanggal Selesai</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="border-t hover:bg-orange-50 transition">

                            <td class="px-3 py-2"><?= $no++ ?></td>
                            <td class="font-semibold"><?= htmlspecialchars($row['sarana']) ?></td>
                            <td class="text-gray-500"><?= htmlspecialchars($row['deskripsi']) ?></td>
                            
                            <td class="text-center py-2">
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="../img/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar" class="w-14 h-14 object-cover rounded-lg cursor-pointer hover:opacity-75 transition" onclick="window.open('../img/<?= htmlspecialchars($row['gambar']) ?>', '_blank')">
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">Tidak ada</span>
                                <?php endif; ?>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <?php if ($row['status'] == 'selesai'): ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Selesai</span>
                                <?php elseif ($row['status'] == 'diproses'): ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Diproses</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Direncanakan</span>
                                <?php endif; ?>
                            </td>

                            <!-- PROGRESS -->
                            <td class="w-40">
                                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700"
                                        style="width: <?= intval($row['progress_persen']) ?>%;
                                        background: linear-gradient(90deg, #f97316, #fb923c);">
                                    </div>
                                </div>
                                <span class="text-xs text-orange-500">
                                    <?= intval($row['progress_persen']) ?>%
                                </span>
                            </td>

                            <td><?= $row['tanggal_mulai'] ?></td>
                            <td><?= $row['tanggal_selesai'] ?? '-' ?></td>
                            <td class="px-3 py-2 space-x-2">
                                <a href="edit-aspirasi.php?id=<?= $row['id_aspirasi'] ?>"
                                   class="inline-block rounded-lg bg-blue-500 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-600 transition">
                                    Edit
                                </a>
                                <a href="hapus.php?id=<?= $row['id_aspirasi'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                                   class="inline-block rounded-lg bg-red-500 px-3 py-1 text-xs font-semibold text-white hover:bg-red-600 transition">
                                    Hapus
                                </a>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>    
            </div>

        </div>

    </main>

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