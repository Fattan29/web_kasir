<?php
session_start();
require "../routes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_products'])) {
    $userID = $_SESSION['userID'];
    $cart = json_decode($_POST['cart'], true);
    $totalHarga = $_POST['totalHarga'];
    
    $conn->begin_transaction();
    
    try {
        $tanggal = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO penjualan (tanggalPenjualan, totalHarga, userID) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $tanggal, $totalHarga, $userID);
        $stmt->execute();
        
        $penjualanID = $conn->insert_id;
        
        foreach ($cart as $item) {
            $produkID = $item['produkID'];
            $jumlah = $item['jumlah'];
            $subtotal = $item['subtotal'];
            
            $stmt = $conn->prepare("INSERT INTO detail_penjualan (penjualanID, produkID, jumlahProduk, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $penjualanID, $produkID, $jumlah, $subtotal);
            $stmt->execute();
            
            $stmt = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE produkID = ?");
            $stmt->bind_param("ii", $jumlah, $produkID);
            $stmt->execute();
        }
        
        $conn->commit();
        header("Location: ../../public/pelanggan/dashboard.php?success=purchase");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../../public/pelanggan/dashboard.php?error=purchase");
        exit();
    }
}
?>
