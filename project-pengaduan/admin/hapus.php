<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    mysqli_query($koneksi, "DELETE FROM data_aspirasi WHERE id_aspirasi='$id'");
}

header("Location: dashboard.php");
exit;
?>