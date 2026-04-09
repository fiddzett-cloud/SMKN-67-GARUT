<?php
session_start();
include "../config/config.php";

/* =====================
   AUTH CHECK
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

/* =====================
   HANDLE UPLOAD FOTO BEFORE/AFTER (ADMIN)
   Update: Menambahkan realpath & Overwrite system
===================== */
$upload_message = '';

// Handle success message from redirect
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $sarana = $_GET['sarana'] ?? '';
    $upload_message = "✅ Berhasil memperbarui foto $sarana.";
}

if (isset($_POST['upload_foto'])) {
    $sarana_upload = $_POST['sarana_upload'] ?? '';

    // Mapping sarana ke nama file (Pastikan ekstensi .png/.jpg sesuai dengan di file sarana.php)
    $map = [
        'Kantin' => ['before' => 'kantinbefore.png', 'after' => 'kantinafter.png'],
        'Masjid' => ['before' => 'masjidbefore.png', 'after' => 'masjidafter.png'],
        'Laboratorium' => ['before' => 'labbefore.png', 'after' => 'labafter.png'],
        'Lapangan Sekolah' => ['before' => 'lapanganbefore.jpg', 'after' => 'lapanganafter.jpg'],
        'Ruang Kelas' => ['before' => 'kelasbefore.jpg', 'after' => 'kelasafter.jpg'],
        'Kamar Mandi dan Toilet' => ['before' => 'wcbefore.jpg', 'after' => 'wcafter.jpg'],
        'UKS' => ['before' => 'wcbefore.jpg', 'after' => 'wcafter.jpg'] // Sesuaikan jika ada file uks sendiri
    ];

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Fungsi Upload yang sudah diperbaiki
    function uploadImage($field, $target, $label, &$message, $allowedTypes)
    {
        if (!isset($_FILES[$field])) return false;

        $file = $_FILES[$field];
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return false;

        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'Ukuran file terlalu besar (PHP limit).',
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas HTML.',
            UPLOAD_ERR_PARTIAL    => 'Upload file terhenti.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk (Cek Izin Folder).',
            UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh ekstensi PHP.'
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message .= " Upload $label gagal: " . ($errors[$file['error']] ?? 'Error tidak diketahui') . ".";
            return false;
        }

        // Cek apakah benar-benar gambar
        $tmp = $file['tmp_name'];
        $info = getimagesize($tmp);
        if ($info === false) {
            $message .= " File $label bukan gambar valid.";
            return false;
        }

        if (!in_array($info['mime'], $allowedTypes, true)) {
            $message .= " Tipe file $label tidak didukung (Gunakan JPG/PNG).";
            return false;
        }

        // --- PERBAIKAN UTAMA: Hapus file lama jika ada agar benar-benar terganti ---
        if (file_exists($target)) {
            @unlink($target); 
        }

        // Simpan file baru
        if (!move_uploaded_file($tmp, $target)) {
            $message .= " Gagal memindahkan file $label ke folder img. Cek permission folder.";
            return false;
        }

        return true;
    }

    if (!isset($map[$sarana_upload])) {
        $upload_message = 'Sarana tidak dikenali.';
    } else {
        // --- PERBAIKAN PATH: Menggunakan realpath agar tidak salah alamat ---
        $baseDir = realpath(__DIR__ . '/../img/');
        if (!$baseDir) {
            $upload_message = "Folder '../img/' tidak ditemukan. Pastikan struktur folder benar.";
        } else {
            $targetBefore = $baseDir . DIRECTORY_SEPARATOR . $map[$sarana_upload]['before'];
            $targetAfter  = $baseDir . DIRECTORY_SEPARATOR . $map[$sarana_upload]['after'];

            $uploadedBefore = uploadImage('foto_before', $targetBefore, 'Before', $upload_message, $allowedTypes);
            $uploadedAfter  = uploadImage('foto_after', $targetAfter, 'After', $upload_message, $allowedTypes);

            if (!$uploadedBefore && !$uploadedAfter && empty($upload_message)) {
                $upload_message = 'Silakan pilih minimal satu file (Before atau After).';
            } elseif (empty($upload_message)) {
                $upload_message = "✅ Berhasil memperbarui foto $sarana_upload.";
                // Redirect untuk menghindari resubmission dan memastikan session tetap aktif
                header("Location: progresperbaikan.php?success=1&sarana=" . urlencode($sarana_upload));
                exit;
            }
        }
    }
}

/* =====================
   HANDLE DELETE PROGRESS
===================== */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM data_aspirasi WHERE id_aspirasi='$id'");
    header("Location: progresperbaikan.php");
    exit;
}

/* =====================
   GET FILTER
===================== */
$sarana_filter = $_GET['sarana'] ?? '';
$status_filter = $_GET['status'] ?? '';

$where = [];
if ($sarana_filter !== '') {
    $sarana_filter = mysqli_real_escape_string($koneksi, $sarana_filter);
    $where[] = "sarana = '$sarana_filter'";
}
if ($status_filter !== '') {
    $status_filter = mysqli_real_escape_string($koneksi, $status_filter);
    $where[] = "status = '$status_filter'";
}

$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

/* =====================
   QUERY DATA
===================== */
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM data_aspirasi $whereSQL ORDER BY tanggal_mulai DESC"
);

$progres_list = [];
while ($row = mysqli_fetch_assoc($query)) {
    $progres_list[] = $row;
}

// Ambil data untuk edit jika ada parameter
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM data_aspirasi WHERE id_aspirasi='$edit_id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// Statistik
$stat_semua = count($progres_list);
$stat_proses = count(array_filter($progres_list, fn($x) => $x['status'] === 'diproses'));
$stat_selesai = count(array_filter($progres_list, fn($x) => $x['status'] === 'selesai'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progres Perbaikan Sarana - SuaraSiswa</title>
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
                <a href="data-siswa.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
                    <i class="bi bi-people-fill mr-2"></i>
                    Data Siswa
                </a>
                
                <a href="progresperbaikan.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 border-b-2 border-orange-600">
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
                    <a href="data-siswa.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:bg-orange-50 border-l-4 border-transparent">
                        <i class="bi bi-people-fill mr-2"></i>
                        Data Siswa
                    </a>
                    <a href="progresperbaikan.php" class="nav-item active flex items-center px-3 py-2 text-sm font-medium text-orange-600 bg-orange-50 border-l-4 border-orange-600">
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
            <div class="flex items-center gap-3">
                <img src="../img/logostm.png" class="h-12">
                <div>
                    <h1 class="text-2xl font-bold">Progres Perbaikan</h1>
                    <p class="text-sm text-gray-500">STM AL MADANI GARUT</p>
                </div>
            </div>
            <span class="text-sm bg-white px-4 py-2 rounded-xl shadow">
                Admin Panel
            </span>
        </div>

        <!-- STAT -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-5">
                <p class="text-gray-500 text-sm">Total</p>
                <p class="text-3xl font-bold text-orange-500"><?= $stat_semua ?></p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-5">
                <p class="text-gray-500 text-sm">Diproses</p>
                <p class="text-3xl font-bold text-yellow-500"><?= $stat_proses ?></p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-5">
                <p class="text-gray-500 text-sm">Selesai</p>
                <p class="text-3xl font-bold text-green-500"><?= $stat_selesai ?></p>
            </div>
        </div>

        <!-- FORM -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">
                <?= $edit_data ? 'Edit Progres' : 'Tambah Progres' ?>
            </h2>

            <form method="POST" class="space-y-4">

                <div class="grid md:grid-cols-2 gap-4">
                    <select name="sarana" required
                        class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400">
                        <option value="">Pilih Sarana</option>
                        <option value="Kantin">Kantin</option>
                        <option value="Masjid">Masjid</option>
                        <option value="Laboratorium">Laboratorium</option>
                        <option value="Lapangan Sekolah">Lapangan</option>
                    </select>

                    <select name="status_progress" required
                        class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400">
                        <option value="direncanakan">Direncanakan</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <textarea name="deskripsi" rows="3" required
                    class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400"></textarea>

                <div class="grid md:grid-cols-3 gap-4">
                    <input type="number" name="progress_persen"
                        class="border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400">

                    <input type="date" name="tanggal_mulai"
                        class="border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400">

                    <input type="date" name="tanggal_selesai"
                        class="border rounded-xl px-4 py-2 focus:ring-2 focus:ring-orange-400">
                </div>

                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-xl shadow">
                    Simpan
                </button>
            </form>
        </div>

        <!-- UPLOAD FOTO BEFORE/AFTER -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Ubah Foto Before / After untuk Siswa</h2>

            <?php if (!empty($upload_message)): ?>
                <div class="mb-4 p-3 rounded bg-gray-50 text-sm text-gray-700">
                    <?= htmlspecialchars($upload_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Sarana</label>
                    <select name="sarana_upload" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-sky-400">
                        <option value="">-- Pilih Sarana --</option>
                        <option value="Kantin">Kantin Sekolah</option>
                        <option value="Masjid">Masjid Sekolah</option>
                        <option value="Laboratorium">Laboratorium</option>
                        <option value="Lapangan Sekolah">Lapangan Olahraga</option>
                        <option value="Ruang Kelas">Ruang Kelas</option>
                        <option value="Kamar Mandi dan Toilet">Kamar Mandi dan Toilet</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Foto Before (sebelum)</label>
                        <input type="file" name="foto_before" accept="image/*" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Foto After (sesudah)</label>
                        <input type="file" name="foto_after" accept="image/*" class="w-full" />
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" name="upload_foto"
                        class="px-6 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold">
                        Upload Foto
                    </button>
                    <a href="../siswa/sarana.php" target="_blank"
                        class="px-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-semibold">
                        Lihat di Halaman Sarana
                    </a>
                </div>
            </form>
        </div>


        <!-- FILTER -->
        <form method="GET" class="bg-white p-4 rounded-2xl shadow mb-6 grid md:grid-cols-3 gap-4">
            <select name="sarana" class="border rounded-xl px-3 py-2">
                <option value="">Semua Sarana</option>
                <option value="Kantin">Kantin</option>
                <option value="Masjid">Masjid</option>
                <option value="Laboratorium">Laboratorium</option>
                <option value="Lapangan Sekolah">Lapangan</option>
            </select>

            <select name="status" class="border rounded-xl px-3 py-2">
                <option value="">Semua Status</option>
                <option value="direncanakan">Direncanakan</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>

            <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl py-2">
                Filter
            </button>
        </form>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Sarana</th>
                        <th class="px-6 py-3 text-left">Deskripsi</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Progress</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($progres_list as $i => $p): ?>
                    <tr class="border-t hover:bg-orange-50 transition">
                        <td class="px-6 py-3"><?= $i+1 ?></td>
                        <td class="px-6 py-3 font-semibold"><?= $p['sarana'] ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= substr($p['deskripsi'],0,50) ?></td>

                        <td class="px-6 py-3">
                            <span class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-600">
                                <?= $p['status'] ?>
                            </span>
                        </td>

                        <td class="px-6 py-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-orange-400 to-yellow-400"
                                    style="width: <?= $p['progress_persen'] ?>%">
                                </div>
                            </div>
                            <span class="text-xs text-gray-500"><?= $p['progress_persen'] ?>%</span>
                        </td>

                        <td class="px-6 py-3"><?= $p['tanggal_mulai'] ?></td>

                        <td class="px-6 py-3 text-center">
                            <a href="edit-aspirasi.php?id=<?= $p['id_aspirasi'] ?>" class="text-blue-500">Edit</a>
                            <a href="hapus.php?id=<?= $p['id_aspirasi'] ?>" class="text-red-500 ml-3">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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

// Pop up untuk pesan upload
<?php if (!empty($upload_message) && strpos($upload_message, '✅ Berhasil') !== false): ?>
    alert('<?= addslashes($upload_message) ?>\n\nFoto telah berhasil diperbarui dan dapat dilihat di halaman Sarana siswa.');
<?php elseif (!empty($upload_message)): ?>
    alert('<?= addslashes($upload_message) ?>');
<?php endif; ?>
</script>

</body>
</html>
