<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

/* =====================
   CHECK & CREATE PHOTO COLUMN IF NOT EXISTS
===================== */
$check_column = mysqli_query($koneksi, "SHOW COLUMNS FROM users LIKE 'photo'");
if (mysqli_num_rows($check_column) == 0) {
    mysqli_query($koneksi, "ALTER TABLE users ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
}

$id_user = $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_user'");
$admin = mysqli_fetch_assoc($query);

/* =====================
   CREATE UPLOAD DIRECTORY
===================== */
$upload_dir = "../img/uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

/* =====================
   HANDLE UPDATE PROFILE
===================== */
$error = '';
$success = '';
$error_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $photo_path = $admin['photo'] ?? '';
    
    // Validasi
    if (empty($nama)) {
        $error = "Nama tidak boleh kosong";
        $error_type = 'edit';
    } elseif (empty($email)) {
        $error = "Email tidak boleh kosong";
        $error_type = 'edit';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid";
        $error_type = 'edit';
    } elseif (empty($username)) {
        $error = "Username tidak boleh kosong";
        $error_type = 'edit';
    } else {
        $nama = mysqli_real_escape_string($koneksi, $nama);
        $email = mysqli_real_escape_string($koneksi, $email);
        $username = mysqli_real_escape_string($koneksi, $username);
        
        // Handle file upload
        if (!empty($_FILES['photo']['name'])) {
            $file = $_FILES['photo'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_type = $file['type'];
            $file_tmp = $file['tmp_name'];
            
            // Validasi file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_type, $allowed_types)) {
                $error = "Tipe file tidak didukung. Gunakan JPG, PNG, atau GIF";
                $error_type = 'edit';
            } elseif ($file_size > 5000000) { // 5MB
                $error = "Ukuran file terlalu besar (maksimal 5MB)";
                $error_type = 'edit';
            } else {
                // Generate unique filename
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = "admin_" . $id_user . "_" . time() . "." . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                // Delete old photo if exists
                if (!empty($admin['photo']) && file_exists("../" . $admin['photo'])) {
                    unlink("../" . $admin['photo']);
                }
                
                // Upload new photo
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $photo_path = "img/uploads/" . $new_file_name;
                } else {
                    $error = "Gagal mengupload foto";
                    $error_type = 'edit';
                }
            }
        }
        
        // If no errors, proceed with update
        if (empty($error)) {
            // Cek email duplikat
            $check_email = mysqli_query($koneksi, "SELECT id_user FROM users WHERE email = '$email' AND id_user != '$id_user'");
            if (mysqli_num_rows($check_email) > 0) {
                $error = "Email sudah digunakan";
                $error_type = 'edit';
            } else {
                // Cek username duplikat
                $check_username = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != '$id_user'");
                if (mysqli_num_rows($check_username) > 0) {
                    $error = "Username sudah digunakan";
                    $error_type = 'edit';
                } else {
                    $photo_update = !empty($photo_path) ? ", photo='$photo_path'" : "";
                    $update = mysqli_query($koneksi, "
                        UPDATE users 
                        SET nama='$nama', email='$email', username='$username'$photo_update
                        WHERE id_user='$id_user'
                    ");
                    
                    if ($update) {
                        $_SESSION['nama'] = $nama;
                        $admin['nama'] = $nama;
                        $admin['email'] = $email;
                        $admin['username'] = $username;
                        if (!empty($photo_path)) {
                            $admin['photo'] = $photo_path;
                        }
                        $success = "Profil berhasil diperbarui!";
                        $error_type = 'success';
                    } else {
                        $error = "Gagal memperbarui profil: " . mysqli_error($koneksi);
                        $error_type = 'edit';
                    }
                }
            }
        }
    }
}

/* =====================
   HANDLE CHANGE PASSWORD
===================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($old_password)) {
        $error = "Password lama tidak boleh kosong";
        $error_type = 'password';
    } elseif ($old_password != $admin['password']) {
        $error = "Password lama salah";
        $error_type = 'password';
    } elseif (empty($new_password)) {
        $error = "Password baru tidak boleh kosong";
        $error_type = 'password';
    } elseif (strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter";
        $error_type = 'password';
    } elseif ($new_password != $confirm_password) {
        $error = "Konfirmasi password tidak sesuai";
        $error_type = 'password';
    } else {
        $new_password = mysqli_real_escape_string($koneksi, $new_password);
        
        $update = mysqli_query($koneksi, "
            UPDATE users 
            SET password='$new_password'
            WHERE id_user='$id_user'
        ");
        
        if ($update) {
            $admin['password'] = $new_password;
            $success = "Password berhasil diubah!";
            $error_type = 'success';
        } else {
            $error = "Gagal mengubah password: " . mysqli_error($koneksi);
            $error_type = 'password';
        }
    }
}

// Get total aspirasi data untuk stats
$total_aspirasi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM aspirasi"))['total'];
$total_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role='siswa'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - SuaraSiswa</title>
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
                <a href="data-siswa.php" class="nav-item flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-orange-600 hover:border-orange-600 border-b-2 border-transparent transition">
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

<div class="max-w-6xl mx-auto px-4 py-8">

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

        <!-- PROFILE CARD - LEBAR -->
        <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- AVATAR & BASIC INFO -->
                <div class="col-span-1 text-center">
                    <!-- AVATAR -->
                    <div class="mb-6 flex justify-center relative group">
                        <?php if (!empty($admin['photo']) && file_exists("../" . $admin['photo'])): ?>
                            <img src="../<?= htmlspecialchars($admin['photo']) ?>" alt="Profile Photo" class="w-28 h-28 rounded-full shadow-lg object-cover">
                        <?php else: ?>
                            <div class="w-28 h-28 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Upload button overlay -->
                        <button 
                            type="button"
                            onclick="openModal('editModal')"
                            class="absolute bottom-0 right-0 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full shadow-lg transition"
                            title="Ubah foto profil"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>

                    <!-- NAME & ROLE -->
                    <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($admin['nama']) ?></h2>
                    <p class="text-blue-600 font-semibold mt-2">
                        <span class="inline-block px-4 py-1 bg-blue-100 rounded-full text-sm">
                            Administrator
                        </span>
                    </p>
                </div>

                <!-- BIODATA - TENGAH -->
                <div class="col-span-1">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Biodata Akun
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest">ID User</p>
                            <p class="text-gray-900 font-semibold"><?= htmlspecialchars($admin['id_user']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Username</p>
                            <p class="text-gray-900 font-semibold"><?= htmlspecialchars($admin['username']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Email</p>
                            <p class="text-gray-900 font-semibold break-all"><?= htmlspecialchars($admin['email']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Role</p>
                            <p class="text-gray-900 font-semibold"><?= ucfirst($admin['role']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- STATS -->
                <div class="col-span-1">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        Statistik Admin
                    </h3>
                    <div class="space-y-3">
                        <div class="bg-blue-50 p-3 rounded">
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Total Aspirasi</p>
                            <p class="text-2xl font-bold text-blue-600"><?= $total_aspirasi ?></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded">
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Total Siswa</p>
                            <p class="text-2xl font-bold text-green-600"><?= $total_siswa ?></p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded">
                            <p class="text-xs text-gray-500 uppercase tracking-widest">Status Akun</p>
                            <p class="text-lg font-bold text-yellow-600">✓ Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="mt-8 pt-8 border-t border-gray-200 flex gap-3">
                <button 
                    onclick="openModal('editModal')" 
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profil
                </button>
                <button 
                    onclick="openModal('passwordModal')" 
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Ubah Password
                </button>
            </div>
        </div>

        <!-- INFO SECURITY -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h4 class="font-semibold text-yellow-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                </svg>
                Tips Keamanan Akun
            </h4>
            <ul class="text-sm text-yellow-800 space-y-1">
                <li>✓ Jangan bagikan password dengan orang lain</li>
                <li>✓ Ubah password secara berkala (minimal 3 bulan)</li>
                <li>✓ Gunakan password yang kuat dan unik</li>
                <li>✓ Logout ketika selesai menggunakan komputer bersama</li>
            </ul>
        </div>

        <!-- BACK BUTTON -->
        <div class="mt-6">
            <a href="dashboard.php"
               class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                ← Kembali ke Dashboard
            </a>
        </div>

    </main>
</div>

<!-- MODAL EDIT PROFIL -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Edit Profil</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- ERROR MESSAGE IN MODAL -->
        <?php if ($error && $error_type === 'edit'): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <!-- PHOTO UPLOAD -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Profil</label>
                <div class="relative">
                    <input 
                        type="file" 
                        name="photo" 
                        id="photoInput"
                        accept="image/jpeg,image/png,image/gif"
                        class="hidden"
                        onchange="previewPhoto(this)"
                    >
                    <div 
                        onclick="document.getElementById('photoInput').click()"
                        class="w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-lg text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition"
                        id="photoDropZone"
                    >
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-600">Klik untuk upload atau drag & drop</p>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF (Maks 5MB)</p>
                    </div>
                    <!-- Photo preview -->
                    <div id="photoPreview" class="mt-3"></div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="nama" 
                    value="<?= htmlspecialchars($admin['nama']) ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    value="<?= htmlspecialchars($admin['username']) ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($admin['email']) ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    required
                >
            </div>

            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    name="update_profile"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition"
                >
                    Simpan
                </button>
                <button 
                    type="button"
                    onclick="closeModal('editModal')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 rounded-lg transition"
                >
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL UBAH PASSWORD -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Ubah Password</h3>
            <button onclick="closeModal('passwordModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- ERROR MESSAGE IN MODAL -->
        <?php if ($error && $error_type === 'password'): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Lama</label>
                <input 
                    type="password" 
                    name="old_password" 
                    placeholder="Masukkan password lama Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <input 
                    type="password" 
                    name="new_password" 
                    placeholder="Minimal 6 karakter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    placeholder="Ulangi password baru Anda"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition"
                    required
                >
            </div>

            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    name="change_password"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition"
                >
                    Ubah Password
                </button>
                <button 
                    type="button"
                    onclick="closeModal('passwordModal')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 rounded-lg transition"
                >
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT -->
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const editModal = document.getElementById('editModal');
        const passwordModal = document.getElementById('passwordModal');
        
        if (e.target === editModal) closeModal('editModal');
        if (e.target === passwordModal) closeModal('passwordModal');
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('editModal');
            closeModal('passwordModal');
        }
    });

    // Auto-close modals when success
    <?php if ($success): ?>
        setTimeout(() => {
            closeModal('editModal');
            closeModal('passwordModal');
        }, 500);
    <?php endif; ?>

    // Preview photo function
    function previewPhoto(input) {
        const preview = document.getElementById('photoPreview');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="relative">
                        <img src="${e.target.result}" alt="Preview" class="w-full h-40 rounded-lg object-cover">
                        <button 
                            type="button"
                            onclick="clearPhotoInput()"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                            </svg>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    }

    // Clear photo input
    function clearPhotoInput() {
        const photoInput = document.getElementById('photoInput');
        photoInput.value = '';
        document.getElementById('photoPreview').innerHTML = '';
    }

    // Drag and drop functionality
    const dropZone = document.getElementById('photoDropZone');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('photoInput').files = files;
            previewPhoto(document.getElementById('photoInput'));
        }
    }
</script>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});
</script>

</body>
</html>
