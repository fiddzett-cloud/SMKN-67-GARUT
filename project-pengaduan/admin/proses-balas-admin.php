<?php
include "../config/config.php";

if (isset($_POST['submit_balas'])) {
    $id = $_POST['id_aspirasi'];
    $balasan = mysqli_real_escape_string($koneksi, $_POST['balasan_admin']);
    $status = $_POST['status'];

    $query = "UPDATE data_aspirasi SET 
              balasan_admin = '$balasan', 
              status = '$status' 
              WHERE id_aspirasi = '$id'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: dashboard-admin.php?pesan=berhasil");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

<form action="" method="POST"> 
    <div class="modal-body">
        <input type="hidden" name="id" id="admin_id_aspirasi"> 
        ...
    </div>
    <div class="modal-footer">
        <button type="submit" name="reply" class="btn btn-success w-100">Kirim & Update</button>
    </div>
</form>
?>