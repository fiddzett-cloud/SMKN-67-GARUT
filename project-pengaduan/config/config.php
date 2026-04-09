<?php
$koneksi = mysqli_connect("localhost", "root", "", "p3-hafidd");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>