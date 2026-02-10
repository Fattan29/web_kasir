<?php
session_start();
require "../routes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $nomorTelepon = $_POST['nomorTelepon'];
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, alamat, nomorTelepon) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $password, $role, $alamat, $nomorTelepon);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['role']) && ($_SESSION['role'] == 'petugas' || $_SESSION['role'] == 'admin')) {
            header("Location: ../../public/karyawan/register.php?success=1");
        } else {
            header("Location: ../../public/index.php?registered=1");
        }
    } else {
        header("Location: ../../public/pelanggan/register.php?error=1");
    }
    exit();
}
?>
