<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif'");
$stmt->execute();
$totalProduk = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM penjualan");
$stmt->execute();
$totalPenjualan = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT SUM(totalHarga) as total FROM penjualan");
$stmt->execute();
$totalPendapatan = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'pelanggan' AND status = 'aktif'");
$stmt->execute();
$totalPelanggan = $stmt->get_result()->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <header>
        <h2>Toko Kasir - Dashboard</h2>
        <div>
            <span>Halo, <?php echo $_SESSION['username']; ?> | </span>
            <a href="../../source/controller/logout_process.php" class="logout">Logout</a>
        </div>
    </header>
    
    <div class="sidebar">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="data_barang.php">Data Barang</a>
        <a href="penjualan.php">Penjualan</a>
        <a href="register.php">Tambah Karyawan</a>
    </div>
    
    <div class="content">
        <h2>Statistik</h2>
        <div class="stats">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <div class="number"><?php echo $totalProduk; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Penjualan</h3>
                <div class="number"><?php echo $totalPenjualan; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pendapatan</h3>
                <div class="number">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pelanggan</h3>
                <div class="number"><?php echo $totalPelanggan; ?></div>
            </div>
        </div>
    </div>
</body>
</html>
