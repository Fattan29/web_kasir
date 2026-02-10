<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

if (isset($_GET['id'])) {
    $penjualanID = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT dp.*, p.namaProduk, p.harga 
                            FROM detail_penjualan dp 
                            JOIN produk p ON dp.produkID = p.produkID 
                            WHERE dp.penjualanID = ?");
    $stmt->bind_param("i", $penjualanID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $details = [];
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($details);
}
?>
