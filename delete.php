<?php
include 'config.php';

// Cek apakah ID produk diberikan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?message=delete_error");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Cek apakah produk ada sebelum menghapus
$check_query = "SELECT * FROM products WHERE id = '$id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    header("Location: index.php?message=delete_error");
    exit();
}

// Hapus data produk
$delete_query = "DELETE FROM products WHERE id = '$id'";

if (mysqli_query($conn, $delete_query)) {
    header("Location: index.php?message=delete_success");
} else {
    header("Location: index.php?message=delete_error");
}

mysqli_close($conn);
?>