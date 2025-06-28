<?php
require_once 'db_config.php';
if ($conn) {
    echo "Koneksi database berhasil!";
} else {
    echo "Koneksi database gagal.";
}
$conn->close();
?>