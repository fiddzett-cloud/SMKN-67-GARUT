<?php
session_start();
include "../config/config.php";
/* =====================
   PROSES REGISTER
===================== */
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = mysqli_query(
        $koneksi,
        "INSERT INTO users (nama, username, password, role) 
         VALUES ('$nama', '$username', '$hashed_password', 'siswa')"
    );
    if ($query) {
        header("Location: login.php?register=success");
        exit;
    } else {
        header("Location: register.php?error=failed");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}   


?>
