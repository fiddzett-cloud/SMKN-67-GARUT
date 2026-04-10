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
// Handle update from modal
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $sarana = trim($_POST['sarana']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    $progress_persen = intval($_POST['progress_persen']);
    $tanggal_mulai = trim($_POST['tanggal_mulai']);
    $tanggal_selesai = trim($_POST['tanggal_selesai']);

    // Escape strings
    $sarana_esc = mysqli_real_escape_string($koneksi, $sarana);
    $status_esc = mysqli_real_escape_string($koneksi, $status);
    $admin_nama = $_SESSION['nama'] ?? 'Admin'; // Ambil nama admin dari session

    $update_query = "UPDATE data_aspirasi SET 
        sarana='$sarana_esc',
        deskripsi='".mysqli_real_escape_string($koneksi, $deskripsi)."',
        status='$status_esc',
        progress_persen=$progress_persen,
        tanggal_mulai='".mysqli_real_escape_string($koneksi, $tanggal_mulai)."',
        tanggal_selesai=".($tanggal_selesai === '' ? "NULL" : "'" . mysqli_real_escape_string($koneksi, $tanggal_selesai) . "'")."
        WHERE id_aspirasi='$id'";

    if (mysqli_query($koneksi, $update_query)) {
        // --- TAMBAHKAN LOG KE HISTORY ---
        $aksi = "mengubah data sarana menjadi '$sarana' dengan status $status";
        $log_query = "INSERT INTO history_admin (id_aspirasi, aksi, dilakukan_oleh) 
                      VALUES ('$id', '$aksi', '$admin_nama')";
        mysqli_query($koneksi, $log_query);
        // --------------------------------

        header("Location: dashboard.php?msg=updated");
        exit;
    }
}

if (isset($_POST['reply'])) {
    $id = intval($_POST['id']);
    $balasan_admin = trim($_POST['balasan_admin']);
    $status = trim($_POST['status']);
    $admin_nama = $_SESSION['nama'] ?? 'Admin';

    $balasan_esc = mysqli_real_escape_string($koneksi, $balasan_admin);
    $status_esc = mysqli_real_escape_string($koneksi, $status);

    $update_query = "UPDATE data_aspirasi SET 
        balasan_admin='$balasan_esc',
        status='$status_esc'
        WHERE id_aspirasi='$id'";

    if (mysqli_query($koneksi, $update_query)) {
        // --- TAMBAHKAN LOG KE HISTORY ---
        $aksi = "memberikan tanggapan/balasan dan mengubah status menjadi $status";
        $log_query = "INSERT INTO history_admin (id_aspirasi, aksi, dilakukan_oleh) 
                      VALUES ('$id', '$aksi', '$admin_nama')";
        mysqli_query($koneksi, $log_query);
        // --------------------------------

        header("Location: dashboard.php?msg=replied");
        exit;
    }
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <td class="px-3 py-2 text-gray-400"><?= $no++ ?></td>
        <td class="font-bold text-gray-700"><?= htmlspecialchars($row['sarana']) ?></td>
        <td class="text-gray-500 max-w-xs truncate"><?= htmlspecialchars($row['deskripsi']) ?></td>
        
        <td class="text-center py-2">
            <?php if (!empty($row['gambar'])): ?>
                <img src="../img/<?= htmlspecialchars($row['gambar']) ?>" 
                     class="w-12 h-12 object-cover rounded-lg mx-auto shadow-sm cursor-zoom-in" 
                     onclick="window.open('../img/<?= htmlspecialchars($row['gambar']) ?>', '_blank')">
            <?php else: ?>
                <span class="text-gray-300 italic text-xs">No Image</span>
            <?php endif; ?>
        </td>

        <td>
            <?php 
            $s = $row['status'] ?? '';
            $badge = ($s == 'selesai') ? 'bg-green-100 text-green-600' : (($s == 'diproses') ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600');
            ?>
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $badge ?>">
                <?= ucfirst($s) ?>
            </span>
        </td>

        <td class="w-44">
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full" style="width: <?= $row['progress_persen'] ?? 0 ?>%"></div>
                </div>
                <span class="text-[11px] font-bold text-orange-600"><?= $row['progress_persen'] ?? 0 ?>%</span>
            </div>
        </td>

        <td class="text-gray-500 text-xs"><?= $row['tanggal_mulai'] ?></td>
        <td class="text-gray-500 text-xs"><?= $row['tanggal_selesai'] ?? '-' ?></td>

        <td class="px-3 py-2">
            <div class="flex justify-center gap-2">
                <button type="button" 
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalBalasAdmin"
                        data-id="<?= $row['id_aspirasi'] ?>"
                        data-sarana="<?= htmlspecialchars($row['sarana']) ?>"
                        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                        data-balasan="<?= htmlspecialchars($row['balasan_admin'] ?? '') ?>"
                        data-status="<?= $row['status'] ?>"
                        title="Balas Aspirasi">
                    <i class="bi bi-chat-dots"></i>
                </button>

                <button type="button" 
        class="p-2 bg-gray-100 text-gray-500 rounded-lg hover:bg-orange-500 hover:text-white transition"
        data-bs-toggle="modal" 
        data-bs-target="#editModal" 
        data-id="<?= $row['id_aspirasi'] ?>" 
        data-sarana="<?= htmlspecialchars($row['sarana']) ?>" 
        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"  data-status="<?= $row['status'] ?>" data-progress="<?= $row['progress_persen'] ?>"
        data-tanggal_mulai="<?= $row['tanggal_mulai'] ?>" data-tanggal_selesai="<?= $row['tanggal_selesai'] ?>" title="Edit Progres">
    <i class="bi bi-pencil-square"></i>
</button>

                <a href="hapus.php?id=<?= $row['id_aspirasi'] ?>"
                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                   class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition"
                   title="Hapus">
                    <i class="bi bi-trash"></i>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Aspirasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editSarana" class="form-label">Sarana</label>
                        <input type="text" class="form-control" id="editSarana" name="sarana" required>
                    </div>
                   <div class="mb-3">
    <label for="editDeskripsi" class="form-label">Deskripsi</label>
    <textarea class="form-control bg-light" id="editDeskripsi" name="deskripsi" rows="3" readonly></textarea>
</div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="direncanakan">Direncanakan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editProgress" class="form-label">Progress (%)</label>
                        <input type="number" class="form-control" id="editProgress" name="progress_persen" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTanggalMulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="editTanggalMulai" name="tanggal_mulai" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTanggalSelesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="editTanggalSelesai" name="tanggal_selesai">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Aspirasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="detailForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="detailId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sarana:</label>
                        <p id="detailSarana"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Siswa:</label>
                        <p id="detailDeskripsi"></p>
                    </div>
                    <div class="mb-3">
                        <label for="detailBalasan" class="form-label fw-bold">Balasan Admin:</label>
                        <textarea class="form-control" id="detailBalasan" name="balasan_admin" rows="3" placeholder="Masukkan balasan untuk siswa..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="detailStatus" class="form-label fw-bold">Status:</label>
                        <select class="form-select" id="detailStatus" name="status" required>
                            <option value="direncanakan">Direncanakan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="reply" class="btn btn-success">Kirim Balasan & Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

// Edit Modal Logic
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    
    // Ambil semua data dari atribut tombol
    const id = button.getAttribute('data-id');
    const sarana = button.getAttribute('data-sarana');
    const deskripsi = button.getAttribute('data-deskripsi');
    const status = button.getAttribute('data-status');
    const progress = button.getAttribute('data-progress');
    const tglMulai = button.getAttribute('data-tanggal_mulai');
    const tglSelesai = button.getAttribute('data-tanggal_selesai');

    // Masukkan ke dalam input modal
    document.getElementById('editId').value = id;
    document.getElementById('editSarana').value = sarana;
    document.getElementById('editDeskripsi').value = deskripsi; // Sekarang akan muncul isi lamanya
    document.getElementById('editStatus').value = status;
    document.getElementById('editProgress').value = progress;
    document.getElementById('editTanggalMulai').value = tglMulai;
    document.getElementById('editTanggalSelesai').value = tglSelesai;
});

// Modal Balas Admin Logic
const modalBalasAdmin = document.getElementById('modalBalasAdmin');
modalBalasAdmin.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('admin_id_aspirasi').value = button.getAttribute('data-id');
    document.getElementById('admin_show_deskripsi').innerText = button.getAttribute('data-deskripsi');
    document.getElementById('admin_input_balasan').value = button.getAttribute('data-balasan');
    document.getElementById('admin_input_status').value = button.getAttribute('data-status');
});

// Detail Modal
const detailModal = document.getElementById('detailModal');
detailModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const sarana = button.getAttribute('data-sarana');
    const deskripsi = button.getAttribute('data-deskripsi');
    const status = button.getAttribute('data-status');
    const balasan = button.getAttribute('data-balasan');

    document.getElementById('detailId').value = id;
    document.getElementById('detailSarana').textContent = sarana;
    document.getElementById('detailDeskripsi').textContent = deskripsi;
    document.getElementById('detailBalasan').value = balasan;
    document.getElementById('detailStatus').value = status;
});
</script>
<div class="modal fade" id="modalBalasAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Berikan Tanggapan Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses-balas-admin.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_aspirasi" id="admin_id_aspirasi">
                    <div class="mb-3">
                        
                        <p id="admin_show_deskripsi" class="p-3 bg-light rounded text-muted"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Balasan Anda:</label>
                        <textarea class="form-control" name="balasan_admin" id="admin_input_balasan" rows="4" placeholder="Ketik jawaban resmi..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Update Status:</label>
                        <select class="form-select" name="status" id="admin_input_status">
                            <option value="direncanakan">Direncanakan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_balas" class="btn btn-success w-100">Kirim & Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk memasukkan data ke modal admin saat diklik
const modalBalasAdmin = document.getElementById('modalBalasAdmin');
modalBalasAdmin.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('admin_id_aspirasi').value = button.getAttribute('data-id');
    document.getElementById('admin_show_deskripsi').innerText = button.getAttribute('data-deskripsi');
    document.getElementById('admin_input_balasan').value = button.getAttribute('data-balasan');
    document.getElementById('admin_input_status').value = button.getAttribute('data-status');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>