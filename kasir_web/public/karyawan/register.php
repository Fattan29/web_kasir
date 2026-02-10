<?php
session_start();
require "../../source/routes/auth.php";
require "../../source/routes/db.php";

$no = 1;
$stmt = $conn->prepare("SELECT * FROM users ORDER BY userID DESC");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <header>
        <h2>Toko Kasir - Manajemen User</h2>
        <div>
            <span>Halo, <?php echo $_SESSION['username']; ?> | </span>
            <a href="../../source/controller/logout_process.php" class="logout">Logout</a>
        </div>
    </header>
    
    <div class="sidebar">
        <a href="dashboard.php">Dashboard</a>
        <a href="data_barang.php">Data Barang</a>
        <a href="penjualan.php">Penjualan</a>
        <a href="register.php" class="active">Manajemen User</a>
    </div>
    
    <div class="content">
        <?php if(isset($_GET['success'])): ?>
            <div class="message success">
                <?php 
                if($_GET['success'] == '1') echo 'User berhasil ditambahkan!';
                if($_GET['success'] == 'updated') echo 'User berhasil diupdate!';
                if($_GET['success'] == 'deleted') echo 'User berhasil dihapus!';
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="message error">
                <?php 
                if($_GET['error'] == 'self_delete') echo 'Tidak dapat menghapus akun sendiri!';
                ?>
            </div>
        <?php endif; ?>
        
        <button class="add-btn" onclick="openAddModal()">Tambah User Baru</button>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['role']; ?>">
                                <?php echo strtoupper($row['role']); ?>
                            </span>
                        </td>
                        <td><?php echo substr($row['alamat'], 0, 30) . '...'; ?></td>
                        <td><?php echo $row['nomorTelepon']; ?></td>
                        <td>
                            <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit</button>
                            <button class="btn-delete" onclick="deleteUser(<?php echo $row['userID']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Tambah User Baru</h2>
            <form action="../../source/controller/register_process.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="pelanggan">Pelanggan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="nomorTelepon" required>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit User</h2>
            <form action="../../source/controller/user_process.php" method="POST">
                <input type="hidden" name="userID" id="edit_userID">
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                
                <div class="form-group">
                    <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" name="password" id="edit_password">
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" required>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="pelanggan">Pelanggan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" id="edit_alamat" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="nomorTelepon" id="edit_nomorTelepon" required>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" name="update_user" class="btn-save">Update</button>
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
        
        function openEditModal(user) {
            document.getElementById('edit_userID').value = user.userID;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_alamat').value = user.alamat;
            document.getElementById('edit_nomorTelepon').value = user.nomorTelepon;
            document.getElementById('edit_password').value = '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function deleteUser(id) {
            if (confirm('Yakin ingin menghapus user ini?')) {
                window.location.href = '../../source/controller/user_process.php?delete_user=' + id;
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
