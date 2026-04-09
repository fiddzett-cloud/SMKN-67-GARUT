<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id'");
}

header("Location: data-siswa.php");
exit;
?>