<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

$no = 1;
$stmt = $conn->prepare("SELECT * FROM produk WHERE status = 'aktif' ORDER BY produkID DESC");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>
    <link rel="stylesheet" href="../css/data_barang.css">
</head>

<body>
    <header>
        <h2>Toko Kasir - Data Barang</h2>
        <div>
            <span>Halo, <?php echo $_SESSION['username']; ?> | </span>
            <a href="../../source/controller/logout_process.php" class="logout">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_barang.php" class="active">Data Barang</a>
        <a href="penjualan.php">Penjualan</a>
        <a href="register.php">Tambah Karyawan</a>
    </div>

    <div class="content">
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">
                <?php
                if ($_GET['success'] == 'added') echo 'Produk berhasil ditambahkan!';
                if ($_GET['success'] == 'updated') echo 'Produk berhasil diupdate!';
                if ($_GET['success'] == 'deleted') echo 'Produk berhasil dihapus!';
                ?>
            </div>
        <?php endif; ?>

        <button class="add-btn" onclick="openAddModal()">Tambah Produk Baru</button>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['namaProduk']; ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['stok']; ?></td>
                        <td>
                            <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit</button>
                            <button class="btn-delete" onclick="deleteProduct(<?php echo $row['produkID']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Tambah Produk</h2>
            <form action="../../source/controller/product_process.php" method="POST">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="namaProduk" required>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="add_product" class="btn-save">Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Produk</h2>
            <form action="../../source/controller/product_process.php" method="POST">
                <input type="hidden" name="produkID" id="edit_produkID">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="namaProduk" id="edit_namaProduk" required>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" id="edit_harga" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" id="edit_stok" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="update_product" class="btn-save">Update</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(product) {
            document.getElementById('edit_produkID').value = product.produkID;
            document.getElementById('edit_namaProduk').value = product.namaProduk;
            document.getElementById('edit_harga').value = product.harga;
            document.getElementById('edit_stok').value = product.stok;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function deleteProduct(id) {
            if (confirm('Yakin ingin menghapus produk ini?')) {
                window.location.href = '../../source/controller/product_process.php?delete_product=' + id;
            }
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>