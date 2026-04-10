<?php
include "../config/config.php";
$id_user = $_GET['id_user'];

// Query mengambil pesan berdasarkan user tertentu
$query = mysqli_query($koneksi, "SELECT * FROM messages WHERE id_user = '$id_user' ORDER BY created_at ASC");

$chats = [];
while ($row = mysqli_fetch_assoc($query)) {
    $chats[] = $row;
}

header('Content-Type: application/json');
echo json_encode($chats);
?>