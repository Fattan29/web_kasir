<?php
session_start();
require "../routes/db.php";

if (isset($_POST['add_product'])) {
    $namaProduk = $_POST['namaProduk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("INSERT INTO produk (namaProduk, harga, stok) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $namaProduk, $harga, $stok);
    $stmt->execute();
    
    header("Location: ../../public/karyawan/data_barang.php?success=added");
    exit();
}

if (isset($_POST['update_product'])) {
    $produkID = $_POST['produkID'];
    $namaProduk = $_POST['namaProduk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("UPDATE produk SET namaProduk = ?, harga = ?, stok = ? WHERE produkID = ?");
    $stmt->bind_param("sdii", $namaProduk, $harga, $stok, $produkID);
    $stmt->execute();
    
    header("Location: ../../public/karyawan/data_barang.php?success=updated");
    exit();
}

if (isset($_GET['delete_product'])) {
    $produkID = $_GET['delete_product'];
    
    $stmt = $conn->prepare("UPDATE produk SET status = 'nonaktif' WHERE produkID = ?");
    $stmt->bind_param("i", $produkID);
    $stmt->execute();
    
    header("Location: ../../public/karyawan/data_barang.php?success=deleted");
    exit();
}

?>
