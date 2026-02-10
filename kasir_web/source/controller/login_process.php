<?php
session_start();
require "../routes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'petugas' || $user['role'] == 'admin') {
                header("Location: ../../public/karyawan/dashboard.php");
            } else {
                header("Location: ../../public/pelanggan/dashboard.php");
            }
            exit();
        }
    }
    
    header("Location: ../../public/index.php?error=invalid");
    exit();
}
?>
