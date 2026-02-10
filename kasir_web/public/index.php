<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Kasir</title>
    <link rel="stylesheet" href="../public/css/pelanggan.css">
</head>
<body>
    <div class="container">
        <h2>Login Toko Kasir</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="message error">Email atau password salah!</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['registered'])): ?>
            <div class="message success">Registrasi berhasil! Silakan login.</div>
        <?php endif; ?>
        
        <form action="../source/controller/login_process.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="link">
            Belum punya akun? <a href="pelanggan/register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
