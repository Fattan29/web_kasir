<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

$stmt = $conn->prepare("SELECT * FROM produk WHERE stok > 0 AND status = 'aktif'");
$stmt->execute();
$products = $stmt->get_result();

$userID = $_SESSION['userID'];
$stmt = $conn->prepare("SELECT p.*, u.username FROM penjualan p 
                        JOIN users u ON p.userID = u.userID 
                        WHERE p.userID = ? 
                        ORDER BY p.tanggalPenjualan DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$purchases = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan</title>
    <link rel="stylesheet" href="../css/toko.css">
</head>
<body>
    <header>
        <h2>Toko Kasir</h2>
        <div>
            <span>Halo, <?php echo $_SESSION['username']; ?> | </span>
            <a href="../../source/controller/logout_process.php" class="logout">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="message success">Pembelian berhasil!</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="message error">Pembelian gagal!</div>
        <?php endif; ?>
        
        <h2>Produk Tersedia</h2>
        <div class="products">
            <?php while($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <h3><?php echo $product['namaProduk']; ?></h3>
                    <p>Harga: Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                    <p>Stok: <?php echo $product['stok']; ?></p>
                    <input type="number" min="1" max="<?php echo $product['stok']; ?>" 
                           value="1" id="qty-<?php echo $product['produkID']; ?>">
                    <button onclick="addToCart(<?php echo $product['produkID']; ?>, 
                                              '<?php echo $product['namaProduk']; ?>', 
                                              <?php echo $product['harga']; ?>, 
                                              <?php echo $product['stok']; ?>)">
                        Tambah ke Keranjang
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="cart">
            <h2>Keranjang Belanja</h2>
            <div id="cartItems"></div>
            <div class="cart-total">Total: Rp <span id="totalPrice">0</span></div>
            <button id="buyBtn" onclick="checkout()">Beli Sekarang</button>
        </div>
        
        <h2>Riwayat Pembelian</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php while($purchase = $purchases->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $purchase['penjualanID']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($purchase['tanggalPenjualan'])); ?></td>
                        <td>Rp <?php echo number_format($purchase['totalHarga'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        let cart = [];
        
        function addToCart(id, name, price, maxStock) {
            const qty = parseInt(document.getElementById('qty-' + id).value);
            
            if (qty > maxStock) {
                alert('Jumlah melebihi stok!');
                return;
            }
            
            const existing = cart.find(item => item.produkID === id);
            if (existing) {
                if (existing.jumlah + qty > maxStock) {
                    alert('Jumlah total melebihi stok!');
                    return;
                }
                existing.jumlah += qty;
                existing.subtotal = existing.jumlah * price;
            } else {
                cart.push({
                    produkID: id,
                    namaProduk: name,
                    harga: price,
                    jumlah: qty,
                    subtotal: price * qty
                });
            }
            
            updateCart();
        }
        
        function removeFromCart(id) {
            cart = cart.filter(item => item.produkID !== id);
            updateCart();
        }
        
        function updateCart() {
            const cartDiv = document.getElementById('cartItems');
            const totalPrice = document.getElementById('totalPrice');
            
            if (cart.length === 0) {
                cartDiv.innerHTML = '<p>Keranjang kosong</p>';
                totalPrice.textContent = '0';
                return;
            }
            
            let html = '';
            let total = 0;
            
            cart.forEach(item => {
                html += `
                    <div class="cart-item">
                        <div>
                            <strong>${item.namaProduk}</strong><br>
                            ${item.jumlah} x Rp ${item.harga.toLocaleString('id-ID')}
                        </div>
                        <div>
                            Rp ${item.subtotal.toLocaleString('id-ID')}
                            <button onclick="removeFromCart(${item.produkID})" 
                                    style="margin-left: 10px; background: #dc3545;">Hapus</button>
                        </div>
                    </div>
                `;
                total += item.subtotal;
            });
            
            cartDiv.innerHTML = html;
            totalPrice.textContent = total.toLocaleString('id-ID');
        }
        
        function checkout() {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            
            const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../source/controller/purchase_process.php';
            
            const cartInput = document.createElement('input');
            cartInput.type = 'hidden';
            cartInput.name = 'cart';
            cartInput.value = JSON.stringify(cart);
            form.appendChild(cartInput);
            
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'totalHarga';
            totalInput.value = total;
            form.appendChild(totalInput);
            
            const buyInput = document.createElement('input');
            buyInput.type = 'hidden';
            buyInput.name = 'buy_products';
            buyInput.value = '1';
            form.appendChild(buyInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
