<?php
session_start();
include "../config/config.php"; 

if (!isset($_SESSION['role']) || ($_SESSION['role'] != "user" && $_SESSION['role'] != "siswa")) {
    header("location:../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM aspirasi ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Aspirasi - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800 pb-24">

<!-- MINIMALIST HEADER -->
<header class="bg-gradient-to-r from-white via-orange-50 to-white shadow-lg border-b border-orange-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="../img/logostm.png" class="h-10">
            <div class="hidden sm:block">
                <span class="text-lg font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">STM</span>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">AL MADANI GARUT</p>
            </div>
        </div>
        <a href="../auth/logout.php" class="flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            <i class="bi bi-box-arrow-right"></i>
            <span class="hidden md:inline">Logout</span>
        </a>
    </div>
</header>

<div class="flex min-h-screen">

    <main class="flex-1 p-8">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Histori Aspirasi</h1>
                <p class="text-sm text-gray-500">Rekam jejak seluruh aspirasi dan pengaduan yang telah dikirim.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold">Daftar Aspirasi Anda</h2>
                <div class="flex gap-2">
                    <span class="text-[10px] font-bold px-3 py-1 bg-orange-100 text-orange-600 rounded-full uppercase">Update Terkini</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr class="border-b">
                            <th class="py-4 px-4 text-left">No</th>
                            <th class="py-4 px-4 text-left">Nama</th>
                            <th class="py-4 px-4 text-left">Judul Aspirasi</th>
                            <th class="py-4 px-4 text-left">Kategori/Isi</th>
                            <th class="py-4 px-4 text-left">Tanggal</th>
                            <th class="py-4 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
                            <tr class="border-b hover:bg-orange-50/50 transition duration-200">
                                <td class="py-4 px-4 font-mono text-gray-400"><?= $no++ ?></td>
                                <td class="py-4 px-4 font-bold text-gray-700"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="py-4 px-4 text-gray-600 italic">"<?= htmlspecialchars($row['judul']) ?>"</td>
                                <td class="py-4 px-4">
                                    <div class="max-w-xs truncate text-gray-500" title="<?= htmlspecialchars($row['isi']) ?>">
                                        <?= htmlspecialchars($row['isi']) ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-gray-500">
                                    <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <?php 
                                        $status = $row['status'];
                                        if($status == 'menunggu') {
                                            $style = "bg-yellow-100 text-yellow-700 border-yellow-200";
                                        } elseif($status == 'diproses') {
                                            $style = "bg-blue-100 text-blue-700 border-blue-200";
                                        } else {
                                            $style = "bg-green-100 text-green-700 border-green-200";
                                        }
                                    ?>
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full border uppercase tracking-tighter <?= $style ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <img src="../img/empty.png" class="h-20 mx-auto opacity-20 mb-4" alt="">
                                    Belum ada data aspirasi yang tercatat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            <a href="dashboard.php" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-orange-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </main>
</div>

<!-- BOTTOM NAVIGATION -->
<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t border-gray-200 z-40">
    <div class="max-w-7xl mx-auto flex justify-around items-center h-20 px-4">
        <a href="dashboard.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-house-door-fill text-xl mb-1"></i>
            <span>Dashboard</span>
        </a>
        <a href="datasiswa.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-person-badge text-xl mb-1"></i>
            <span>Profil</span>
        </a>
        <a href="sarana.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-building text-xl mb-1"></i>
            <span>Sarana</span>
        </a>
        <a href="chatsiswa.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-chat-dots-fill text-xl mb-1"></i>
            <span>Chat</span>
        </a>
        <a href="histori-aspirasi.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg bg-orange-100 text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-clock-history text-xl mb-1"></i>
            <span>Histori</span>
        </a>
    </div>
</nav>

</body>
</html>