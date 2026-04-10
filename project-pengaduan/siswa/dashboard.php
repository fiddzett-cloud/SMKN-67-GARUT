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

// Hitung statistik
$menunggu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table WHERE status='direncanakan' OR status='menunggu'"))['total'] ?? 0;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: linear-gradient(135deg, #f8fafc, #fff7ed); }
        .top-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .nav-item { transition: all 0.2s ease; border-bottom: 2px solid transparent; }
        .nav-item:hover, .nav-item.active { color: #ea580c !important; border-color: #ea580c !important; }
        /* Reset modal bootstrap agar tidak konflik dengan tailwind center */
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
    </style>
</head>

<body class="text-gray-800">

<header class="top-header py-3 px-6">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="../img/logostm.png" class="h-10">
            <div class="flex flex-col">
                <span class="font-bold text-orange-600 text-lg leading-tight border-none">STM</span>
                <small class="text-gray-500 text-[10px] tracking-widest uppercase">Al Madani Garut</small>
            </div>
        </div>
        <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-sm flex items-center gap-2 transition">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</header>

<nav class="bg-white border-b border-gray-200 shadow-sm sticky top-[65px] z-30">
    <div class="max-w-7xl mx-auto px-4 flex space-x-8 h-14 items-center">
        <a href="dashboard.php" class="nav-item active flex items-center h-full px-2 text-sm font-medium">
            <i class="bi bi-house-door-fill mr-2"></i> Dashboard
        </a>
        <a href="buatlaporan.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500">
            <i class="bi bi-pencil-square mr-2"></i> Buat Laporan
        </a>
        <a href="sarana.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500">
            <i class="bi bi-tools mr-2"></i> Sarana
        </a>
        <a href="histori-aspirasi.php" class="nav-item flex items-center h-full px-2 text-sm font-medium text-gray-500">
            <i class="bi bi-clock-history mr-2"></i> Histori
        </a>
    </div>
</nav>

<div class="max-w-7xl mx-auto p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, Siswa! 👋</h1>
        <p class="text-sm text-gray-500">Pantau progres aspirasi dan pengaduan Anda secara real-time.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Aspirasi</p>
            <h2 class="text-3xl font-bold text-orange-500 mt-1"><?= $total_data ?></h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-1"><?= $menunggu ?></h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Diproses</p>
            <h2 class="text-3xl font-bold text-blue-500 mt-1"><?= $diproses ?></h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Selesai</p>
            <h2 class="text-3xl font-bold text-green-500 mt-1"><?= $selesai ?></h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Pengaduan Terbaru</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 font-semibold">
                    <tr>
                        <th class="py-4 px-6 text-left">No</th>
                        <th class="text-left">Sarana</th>
                        <th class="text-left">Deskripsi</th>
                        <th class="text-center">Gambar</th>
                        <th class="text-left">Progres</th>
                        <th class="text-left">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($query)) : ?>
                    <tr class="hover:bg-orange-50/50 transition">
                        <td class="py-4 px-6 text-gray-400"><?= $no++ ?></td>
                        <td class="font-bold text-gray-700"><?= htmlspecialchars($row['sarana'] ?? '-') ?></td>
                        <td class="text-gray-500 max-w-xs truncate"><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="../img/<?= htmlspecialchars($row['gambar']) ?>" class="w-12 h-12 object-cover rounded-lg mx-auto shadow-sm cursor-zoom-in" onclick="window.open('../img/<?= htmlspecialchars($row['gambar']) ?>', '_blank')">
                            <?php else: ?>
                                <span class="text-gray-300 italic text-xs">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="w-44">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full bg-orange-500 rounded-full" style="width: <?= $row['progress_persen'] ?? 0 ?>%"></div>
                                </div>
                                <span class="text-[11px] font-bold text-orange-600"><?= $row['progress_persen'] ?? 0 ?>%</span>
                            </div>
                        </td>
                        <td>
                            <?php
                            $s = $row['status'] ?? '';
                            $badge = ($s == 'direncanakan' || $s == 'menunggu') ? 'bg-yellow-100 text-yellow-600' : (($s == 'diproses') ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600');
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $badge ?>">
                                <?= ucfirst($s) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button type="button" 
                                        class="flex items-center gap-1 bg-white border border-orange-200 text-orange-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-orange-500 hover:text-white transition shadow-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalLihatBalasan"
                                        data-sarana="<?= htmlspecialchars($row['sarana']) ?>"
                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                                        data-balasan="<?= htmlspecialchars($row['balasan_admin'] ?? '') ?>">
                                    <i class="bi bi-chat-dots"></i> Balasan
                                </button>
                                
                                <a href="edit-aspirasi.php?id=<?= $row[$idField] ?>" class="p-2 bg-gray-100 text-gray-500 rounded-lg hover:bg-blue-500 hover:text-white transition">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLihatBalasan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
            <div class="modal-header bg-orange-500 text-white border-0 py-4">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i class="bi bi-info-circle"></i> Detail Tanggapan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6 bg-white">
                <div class="mb-5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aspirasi Anda</label>
                    <div class="mt-2 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p id="view_sarana" class="font-bold text-gray-800 text-sm mb-1"></p>
                        <p id="view_deskripsi" class="text-gray-600 text-sm leading-relaxed"></p>
                    </div>
                </div>
                
                <div class="relative">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Tanggapan Admin</label>
                    <div class="mt-2 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p id="view_balasan" class="text-blue-900 text-sm leading-relaxed italic"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 border-0">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalLihatBalasan = document.getElementById('modalLihatBalasan');
    modalLihatBalasan.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        
        // Ambil data dari atribut tombol
        const sarana = button.getAttribute('data-sarana');
        const deskripsi = button.getAttribute('data-deskripsi');
        const balasan = button.getAttribute('data-balasan');
        
        // Masukkan ke elemen modal
        document.getElementById('view_sarana').innerText = sarana;
        document.getElementById('view_deskripsi').innerText = deskripsi;
        document.getElementById('view_balasan').innerText = balasan && balasan !== "" 
            ? balasan 
            : "Mohon tunggu, admin sedang meninjau aspirasi Anda.";
    });
</script>

</body>
</html>