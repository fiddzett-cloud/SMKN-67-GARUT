<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

/* =====================
   HANDLE FORM SUBMISSION
===================== */
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sarana = trim($_POST['sarana']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    $progress_persen = intval($_POST['progress_persen']);
    $tanggal_mulai = trim($_POST['tanggal_mulai']);
    $tanggal_selesai = trim($_POST['tanggal_selesai']);
    
    // Validasi
    if (empty($sarana)) {
        $error = "Sarana tidak boleh kosong";
    } elseif (empty($deskripsi)) {
        $error = "Deskripsi tidak boleh kosong";
    } elseif (empty($status)) {
        $error = "Status harus dipilih";
    } elseif ($progress_persen < 0 || $progress_persen > 100) {
        $error = "Progress harus antara 0-100";
    } elseif (empty($tanggal_mulai)) {
        $error = "Tanggal mulai tidak boleh kosong";
    } else {
        // Handle file upload
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $newname = uniqid() . '.' . $ext;
                $destination = '../img/' . $newname;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $destination)) {
                    $gambar = $newname;
                } else {
                    $error = "Gagal upload gambar";
                }
            } else {
                $error = "Format gambar tidak didukung (hanya jpg, jpeg, png, gif)";
            }
        }
        
        if (empty($error)) {
            // Sanitize input
            $sarana = mysqli_real_escape_string($koneksi, $sarana);
            $deskripsi = mysqli_real_escape_string($koneksi, $deskripsi);
            $status = mysqli_real_escape_string($koneksi, $status);
            $tanggal_mulai = mysqli_real_escape_string($koneksi, $tanggal_mulai);
            $tanggal_selesai = !empty($tanggal_selesai) ? "'" . mysqli_real_escape_string($koneksi, $tanggal_selesai) . "'" : "NULL";
            
            $query = mysqli_query($koneksi, "
                INSERT INTO data_aspirasi (sarana, deskripsi, status, progress_persen, tanggal_mulai, tanggal_selesai, gambar)
                VALUES ('$sarana', '$deskripsi', '$status', $progress_persen, '$tanggal_mulai', $tanggal_selesai, '$gambar')
            ");
            
            if ($query) {
                $success = "Pengaduan berhasil ditambahkan!";
                // Reset form
                $_POST = [];
            } else {
                $error = "Gagal menyimpan pengaduan: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengaduan - SuaraSiswa</title>
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



<main class="max-w-7xl mx-auto">
            <!-- DESKTOP NAVIGATION REMOVED -->

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Tambah Pengaduan</h1>
            <p class="text-gray-500 mt-1">Tambahkan data pengaduan dan perbaikan sarana sekolah</p>
        </div>
            </div>
            <span class="text-sm text-slate-500">Admin | SuaraSiswa</span>
        </div>

        <!-- SUCCESS MESSAGE -->
        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                <div>
                    <p class="font-semibold">Sukses!</p>
                    <p><?= $success ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- ERROR MESSAGE -->
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                <div>
                    <p class="font-semibold">Kesalahan!</p>
                    <p><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- FORM CONTAINER -->
        <div class="max-w-2xl bg-white rounded-xl shadow-sm p-8">
            
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">

                <!-- SARANA -->
                <div>
                    <label for="sarana" class="block text-sm font-semibold text-gray-700 mb-2">
                        Sarana <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="sarana" 
                        name="sarana" 
                        value="<?= htmlspecialchars($_POST['sarana'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="Contoh: Lapangan Sekolah"
                        required
                    >
                </div>

                <!-- DESKRIPSI -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                        placeholder="Jelaskan detail pengaduan dan perbaikan..."
                        required
                    ><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>

                <!-- STATUS -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        required
                    >
                        <option value="">-- Pilih Status --</option>
                        <option value="direncanakan" <?= ($_POST['status'] ?? '') == 'direncanakan' ? 'selected' : '' ?>>Direncanakan</option>
                        <option value="diproses" <?= ($_POST['status'] ?? '') == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="selesai" <?= ($_POST['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <!-- PROGRESS -->
                <div>
                    <label for="progress_persen" class="block text-sm font-semibold text-gray-700 mb-2">
                        Progress (%) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="progress_persen" 
                        name="progress_persen" 
                        value="<?= htmlspecialchars($_POST['progress_persen'] ?? '0') ?>"
                        min="0" 
                        max="100"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        required
                    >
                </div>

                <!-- TANGGAL MULAI -->
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="tanggal_mulai" 
                        name="tanggal_mulai" 
                        value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        required
                    >
                </div>

                <!-- TANGGAL SELESAI -->
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Selesai (Opsional)
                    </label>
                    <input 
                        type="date" 
                        id="tanggal_selesai" 
                        name="tanggal_selesai" 
                        value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                </div>

                <!-- GAMBAR UPLOAD -->
                <div>
                    <label for="gambar" class="block text-sm font-semibold text-gray-700 mb-2">
                        Upload Gambar (Opsional)
                    </label>
                    <input 
                        type="file" 
                        id="gambar" 
                        name="gambar" 
                        accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB</p>
                </div>

                <!-- BUTTONS -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8m0 8l-4-2m4 2l4-2"/>
                        </svg>
                        Tambah Pengaduan
                    </button>
                    <a 
                        href="dashboard.php" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg transition text-center"
                    >
                        Batal
                    </a>
                </div>

            </form>

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