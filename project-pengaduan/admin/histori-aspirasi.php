<?php
include "../config/config.php";

// ambil filter
$sarana   = $_GET['sarana'] ?? '';
$status   = $_GET['status'] ?? '';

// amankan input
$sarana = mysqli_real_escape_string($koneksi, $sarana);
$status = mysqli_real_escape_string($koneksi, $status);

// query dasar
$query = "SELECT * FROM data_aspirasi WHERE 1=1";

// filter
if (!empty($sarana)) {
    $query .= " AND sarana LIKE '%$sarana%'";
}

if (!empty($status)) {
    $query .= " AND status = '$status'";
}

$query .= " ORDER BY created_at DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>History Aspirasi - SuaraSiswa</title>
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
                <a href="dashboard.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-house-door-fill mr-2"></i>
                    Dashboard
                </a>
                <a href="list_aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-list-check mr-2"></i>
                    List Aspirasi
                </a>
                <a href="histori-aspirasi.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
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
                    <a href="dashboard.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Dashboard
                    </a>
                    <a href="list_aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-list-check mr-2"></i>
                        List Aspirasi
                    </a>
                    <a href="histori-aspirasi.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 bg-orange-50 border-l-4 border-orange-600">
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
            <div>
                <h1 class="text-2xl font-bold">History Aspirasi</h1>
                <p class="text-sm text-gray-500">Riwayat pengaduan & progres</p>
            </div>

            <a href="histori-aspirasi.php"
            class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-semibold shadow">
                Refresh
            </a>
        </div>

        <!-- FILTER -->
        <form method="GET"
        class="bg-white p-5 rounded-2xl shadow mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

            <input type="text" name="sarana"
                value="<?= htmlspecialchars($sarana) ?>"
                placeholder="Cari sarana..."
                class="px-4 py-2 rounded-xl border focus:ring-2 focus:ring-orange-400">

            <select name="status"
                class="px-4 py-2 rounded-xl border focus:ring-2 focus:ring-orange-400">
                <option value="">Semua Status</option>
                <option value="direncanakan" <?= $status=='direncanakan'?'selected':'' ?>>Direncanakan</option>
                <option value="diproses" <?= $status=='diproses'?'selected':'' ?>>Diproses</option>
                <option value="selesai" <?= $status=='selesai'?'selected':'' ?>>Selesai</option>
            </select>

            <button class="bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-xl font-semibold shadow">
                Filter
            </button>
        </form>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow border">

            <div class="flex justify-between items-center p-5 border-b">
                <h2 class="font-semibold">Data History</h2>
                <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full">
                    Total: <?= mysqli_num_rows($result) ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Sarana</th>
                            <th class="px-4 py-2 text-left">Deskripsi</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Progress</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                    $no = 1;
                    $hasData = false;

                    while ($row = mysqli_fetch_assoc($result)) :
                        $hasData = true;
                    ?>
                        <tr class="border-t hover:bg-orange-50">

                            <td class="px-4 py-2"><?= $no++ ?></td>

                            <td class="font-semibold"><?= htmlspecialchars($row['sarana']) ?></td>

                            <td class="text-gray-500">
                                <?= htmlspecialchars(substr($row['deskripsi'],0,60)) ?>...
                            </td>

                            <td>
                                <?php if ($row['status']=='selesai'): ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Selesai</span>
                                <?php elseif ($row['status']=='diproses'): ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Diproses</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Direncanakan</span>
                                <?php endif; ?>
                            </td>

                            <td class="w-40">
                                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full"
                                        style="width: <?= intval($row['progress_persen']) ?>%;
                                        background: linear-gradient(90deg, #f97316, #fb923c);">
                                    </div>
                                </div>
                                <span class="text-xs text-orange-500">
                                    <?= intval($row['progress_persen']) ?>%
                                </span>
                            </td>

                            <td class="text-gray-500">
                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                    <?php if (!$hasData): ?>
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-400">
                                Tidak ada data
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>

                </table>
            </div>
        </div>

        <!-- BACK -->
        <div class="mt-6">
            <a href="dashboard.php"
            class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl shadow">
                ← Kembali
            </a>
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