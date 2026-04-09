<?php
session_start();
include "../config/config.php";

if ($_SESSION['role'] != "siswa") {
    header("location:../auth/login.php");
    exit;
}

function tableExists($conn, $tableName) {
    $tableName = mysqli_real_escape_string($conn, $tableName);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return $result && mysqli_num_rows($result) > 0;
}

// Pilih tabel (fallback ke data_aspirasi atau progres_perbaikan)
$table = tableExists($koneksi, 'data_aspirasi') ? 'data_aspirasi' : 'progres_perbaikan';

// Status awal
$waitingStatus = ($table === 'data_aspirasi') ? 'menunggu' : 'direncanakan';

// Hitung statistik
$menunggu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table WHERE status='$waitingStatus'"))['total'] ?? 0;
$diproses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table WHERE status='diproses'"))['total'] ?? 0;
$selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table WHERE status='selesai'"))['total'] ?? 0;
$total_data = $menunggu + $diproses + $selesai;

// Penyesuaian Field
$dateField = ($table === 'data_aspirasi') ? 'created_at' : 'tanggal_mulai';
$idField = ($table === 'data_aspirasi') ? 'id_aspirasi' : 'id_progress';

// Query data terbaru
$query = mysqli_query($koneksi, "SELECT * FROM $table ORDER BY $dateField DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SuaraSiswa</title>
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

    /* Navigation Bar */
    .nav-item {
        transition: all 0.2s ease;
    }

    .nav-item:hover {
        color: #ea580c !important;
        border-color: #ea580c !important;
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
                <a href="dashboard.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
                    <i class="bi bi-house-door-fill mr-2"></i>
                    Dashboard
                </a>
                <a href="tambahpengaduan.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-person-fill mr-2"></i>
                    Buat Laporan 
                </a>
                <a href="sarana.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-tools mr-2"></i>
                    Sarana
                </a>
                
                <a href="histori-aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-clock-history mr-2"></i>
                    Histori
                </a>
            </nav>
        </div>
    </div>
</div>

<div class="flex min-h-screen">
    <main class="flex-1 p-8 w-full">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, Siswa!</h1>
                <p class="text-sm text-gray-500">Pantau progres aspirasi dan pengaduan Anda di sini.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow border hover:shadow-lg transition">
                <p class="text-sm text-gray-500">Total Aspirasi</p>
                <h2 class="text-3xl font-bold text-orange-500"><?= $total_data ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Menunggu</p>
                <h2 class="text-3xl font-bold text-yellow-500"><?= $menunggu ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Diproses</p>
                <h2 class="text-3xl font-bold text-blue-500"><?= $diproses ?></h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Selesai</p>
                <h2 class="text-3xl font-bold text-green-500"><?= $selesai ?></h2>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border p-6">
            <h2 class="text-lg font-semibold mb-4">Pengaduan Terbaru</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-b">
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="text-left">Sarana</th>
                            <th class="text-left">Deskripsi</th>
                            <th class="text-center">Gambar</th>
                            <th class="text-left">Progres</th>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)) : ?>
                        <tr class="border-b hover:bg-orange-50 transition">
                            <td class="py-4 px-4"><?= $no++ ?></td>
                            <td class="font-bold text-gray-700"><?= htmlspecialchars($row['sarana'] ?? '-') ?></td>
                            <td class="text-gray-500 max-w-xs truncate"><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                            
                            <td class="text-center py-4">
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="../img/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar" class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:opacity-75 transition" onclick="window.open('../img/<?= htmlspecialchars($row['gambar']) ?>', '_blank')">
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>

                            <td class="w-40">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700"
                                         style="width: <?= $row['progress_persen'] ?? 0 ?>%; 
                                                background: linear-gradient(90deg, #f97316, #fb923c);">
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-orange-600"><?= $row['progress_persen'] ?? 0 ?>%</span>
                            </td>

                            <td class="text-gray-500">
                                <?= date('d M Y', strtotime($row['tanggal_mulai'] ?? $row['created_at'] ?? 'now')) ?>
                            </td>

                            <td>
                                <?php
                                $status = $row['status'] ?? '';
                                if ($status == 'direncanakan' || $status == 'menunggu') {
                                    $warna = "bg-yellow-100 text-yellow-700";
                                } elseif ($status == 'diproses') {
                                    $warna = "bg-blue-100 text-blue-700";
                                } else {
                                    $warna = "bg-green-100 text-green-700";
                                }
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $warna ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="edit-aspirasi.php?id=<?= $row[$idField] ?>" 
                                       class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>



</body>
</html>