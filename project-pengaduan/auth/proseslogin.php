<?php
session_start();
include "../config/config.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, 
    "SELECT * FROM users WHERE username='$username'"
);

$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($password == $data['password']) {

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['role']    = $data['role'];

        if ($data['role'] == "admin") {
            header("location:../admin/dashboard.php");
        } else {
            header("location:../siswa/dashboard.php");
        }

    } else {
        echo "<script>alert('Password salah');location='login.php';</script>";
    }
} else {
    echo "<script>alert('Username tidak ditemukan');location='login.php';</script>";
}
?>
