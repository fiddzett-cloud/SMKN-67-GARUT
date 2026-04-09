<?php
include "../config/config.php";

// ambil filter
$tanggal  = $_GET['tanggal'] ?? '';
$bulan    = $_GET['bulan'] ?? '';
$sarana   = $_GET['sarana'] ?? '';
$status   = $_GET['status'] ?? '';

// amankan input
$tanggal = mysqli_real_escape_string($koneksi, $tanggal);
$bulan   = mysqli_real_escape_string($koneksi, $bulan);
$sarana  = mysqli_real_escape_string($koneksi, $sarana);
$status  = mysqli_real_escape_string($koneksi, $status);

// query dasar
$query = "SELECT * FROM data_aspirasi WHERE 1=1";

// filter
if (!empty($tanggal)) {
    $query .= " AND tanggal_mulai = '$tanggal'";
}

if (!empty($bulan)) {
    $query .= " AND MONTH(tanggal_mulai) = '$bulan'";
}

if (!empty($sarana)) {
    $query .= " AND sarana LIKE '%$sarana%'";
}

if (!empty($status)) {
    $query .= " AND status = '$status'";
}

$query .= " ORDER BY tanggal_mulai DESC";

// 🔥 FIX DI SINI (pakai $koneksi, bukan $conn)
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>List Progres Perbaikan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>


<div class="container mt-5">

    <!-- FILTER -->
    <form method="GET" class="row g-3 mb-4 bg-white p-3 rounded-4 shadow-sm">

        <div class="col-md-3">
            <input type="date" name="tanggal" class="form-control rounded-pill" value="<?= htmlspecialchars($tanggal) ?>">
        </div>

        <div class="col-md-2">
            <select name="bulan" class="form-control rounded-pill">
                <option value="">Bulan</option>
                <?php for ($i=1; $i<=12; $i++) : ?>
                    <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-md-3">
            <input type="text" name="sarana" class="form-control rounded-pill" placeholder="Cari Sarana" value="<?= htmlspecialchars($sarana) ?>">
        </div>

        <div class="col-md-2">
            <select name="status" class="form-control rounded-pill">
                <option value="">Status</option>
                <option value="direncanakan" <?= $status=='direncanakan'?'selected':'' ?>>Direncanakan</option>
                <option value="diproses" <?= $status=='diproses'?'selected':'' ?>>Diproses</option>
                <option value="selesai" <?= $status=='selesai'?'selected':'' ?>>Selesai</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100 rounded-pill">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>

    <!-- TABEL -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold">Hasil Pencarian</h5>
                <span class="badge bg-primary">Total: <?= mysqli_num_rows($result) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Sarana</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th class="text-center">Progress</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                    $no = 1;
                    $hasData = false;

                    while ($row = mysqli_fetch_assoc($result)) :
                        $hasData = true;

                        if ($row['status'] == 'selesai') $badge = 'success';
                        elseif ($row['status'] == 'diproses') $badge = 'warning';
                        else $badge = 'secondary';
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['sarana']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($row['deskripsi']) ?></td>

                            <td>
                                <span class="badge bg-<?= $badge ?> rounded-pill px-3">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>

                            <td>
                                <div class="progress rounded-pill" style="height: 18px;">
                                    <div class="progress-bar"
                                        style="width: <?= intval($row['progress_persen']) ?>%;">
                                        <?= intval($row['progress_persen']) ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if (!$hasData): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada data
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BACK -->
    <div class="mt-4 text-end">
        <a href="dashboard.php" class="btn btn-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

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