<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'siswa' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Siswa - SuaraSiswa</title>
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
                <a href="histori-aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-clock-history mr-2"></i>
                    Histori Aspirasi
                </a>
                <a href="data-siswa.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
                    <i class="bi bi-people-fill mr-2"></i>
                    Data Siswa
                </a>
                <a href="daftarfeedback.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-chat-dots-fill mr-2"></i>
                    Daftar Feedback
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
                    <a href="histori-aspirasi.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-clock-history mr-2"></i>
                        Histori Aspirasi
                    </a>
                    <a href="data-siswa.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 bg-orange-50 border-l-4 border-orange-600">
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
                    <h1 class="text-2xl font-bold">Data Siswa</h1>
                    <p class="text-sm text-gray-500">Kelola data siswa</p>
                </div>
            </div>

            <a href="tambah-siswa.php" class="md:hidden lg:flex items-center gap-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2.5 rounded-xl font-semibold shadow-lg shadow-green-200 hover:shadow-xl hover:shadow-green-300 transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="bi bi-plus-circle-fill"></i>
                <span class="hidden md:inline">Tambah</span>
            </a>

        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow p-6 border">

            <h2 class="text-lg font-semibold mb-4">Daftar Siswa</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Username</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="border-t hover:bg-orange-50 transition">

                            <td class="px-4 py-3"><?= $no++ ?></td>
                            <td class="font-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td class="text-gray-500"><?= htmlspecialchars($row['email']) ?></td>

                            <!-- AKSI -->
                            <td class="text-center">
                                <div class="flex justify-center gap-2">

                                    <a href="edit-siswa.php?id=<?= $row['id_user'] ?>"
                                    class="px-3 py-1 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                        Edit
                                    </a>

                                    <a href="hapus-siswa.php?id=<?= $row['id_user'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        Hapus
                                    </a>

                                </div>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Data siswa belum tersedia
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
            class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-xl transition">
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