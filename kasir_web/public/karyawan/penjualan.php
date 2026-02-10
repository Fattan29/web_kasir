<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

$no = 1;
$stmt = $conn->prepare("SELECT p.*, u.username, u.email 
                        FROM penjualan p 
                        JOIN users u ON p.userID = u.userID 
                        ORDER BY p.tanggalPenjualan DESC, p.penjualanID DESC");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="../css/penjualan.css">
</head>

<body>
    <header>
        <h2>Toko Kasir - Data Penjualan</h2>
        <div>
            <span>Halo, <?php echo $_SESSION['username']; ?> | </span>
            <a href="../../source/controller/logout_process.php" class="logout">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_barang.php">Data Barang</a>
        <a href="penjualan.php" class="active">Penjualan</a>
        <a href="register.php">Tambah Karyawan</a>
    </div>

    <div class="content">
        <h2>Daftar Penjualan</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Pembeli</th>
                    <th>Email</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['penjualanID']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggalPenjualan'])); ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>Rp <?php echo number_format($row['totalHarga'], 0, ',', '.'); ?></td>
                        <td>
                            <button class="btn-detail" onclick="showDetail(<?php echo $row['penjualanID']; ?>)">Detail</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Detail Penjualan</h2>
            <div id="detailContent"></div>
            <button class="btn-detail" onclick="window.print()">Print</button>
        </div>
    </div>

    <script>
        function showDetail(penjualanID) {
            fetch('../../source/controller/get_detail_penjualan.php?id=' + penjualanID)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="detail-table">';
                    html += '<table style="width: 100%;">';
                    html += '<thead><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>';
                    html += '<tbody>';

                    data.forEach(item => {
                        html += `<tr>
                            <td>${item.namaProduk}</td>
                            <td>${item.jumlahProduk}</td>
                            <td>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</td>
                            <td>Rp ${parseFloat(item.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                    });

                    html += '</tbody></table></div>';

                    document.getElementById('detailContent').innerHTML = html;
                    document.getElementById('detailModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Gagal memuat detail penjualan');
                });
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>