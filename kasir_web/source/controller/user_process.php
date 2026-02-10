<?php
session_start();
require "../routes/db.php";

if (isset($_POST['update_user'])) {
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $alamat = $_POST['alamat'];
    $nomorTelepon = $_POST['nomorTelepon'];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, alamat = ?, nomorTelepon = ? WHERE userID = ?");
        $stmt->bind_param("ssssssi", $username, $email, $password, $role, $alamat, $nomorTelepon, $userID);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, alamat = ?, nomorTelepon = ? WHERE userID = ?");
        $stmt->bind_param("sssssi", $username, $email, $role, $alamat, $nomorTelepon, $userID);
    }
    
    $stmt->execute();
    
    header("Location: ../../public/karyawan/register.php?success=updated");
    exit();
}

if (isset($_GET['delete_user'])) {
    $userID = $_GET['delete_user'];
    
    if ($userID == $_SESSION['userID']) {
        header("Location: ../../public/karyawan/register.php?error=self_delete");
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    
    header("Location: ../../public/karyawan/register.php?success=deleted");
    exit();
}
?>
