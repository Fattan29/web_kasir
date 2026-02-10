<?php
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
}
?>
