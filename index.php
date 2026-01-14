<?php
include 'config.php';

// Pesan sukses/error - INISIALISASI DI SINI
$message = '';

if (isset($_GET['message'])) {
    if ($_GET['message'] == 'delete_success') {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil dihapus!</div>';
    } elseif ($_GET['message'] == 'create_success') {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil ditambahkan!</div>';
    } elseif ($_GET['message'] == 'update_success') {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil diperbarui!</div>';
    } elseif ($_GET['message'] == 'delete_error') {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal menghapus produk. Produk tidak ditemukan!</div>';
    }
}

// Pencarian
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Query data produk
if (!empty($search)) {
    $query = "SELECT * FROM products WHERE 
              name LIKE '%$search%' OR 
              description LIKE '%$search%' 
              ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM products ORDER BY created_at DESC";
}

$result = mysqli_query($conn, $query);

// Simpan data produk dalam array untuk kebutuhan modal
$products_data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $products_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Produk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-boxes"></i> Sistem Manajemen Produk</h1>
            <p>Kelola data produk dengan operasi CRUD (Create, Read, Update, Delete)</p>
        </header>

        <?php 
        // Tampilkan pesan jika ada
        if (!empty($message)) {
            echo $message;
        }
        ?>

        <div class="actions">
            <a href="create.php" class="btn"><i class="fas fa-plus-circle"></i> Tambah Produk Baru</a>
            
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
        </div>

        <div class="table-container">
            <?php if (count($products_data) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga (Rp)</th>
                            <th>Stok</th>
                            <th>Tanggal Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($products_data as $row): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                            <td class="quantity"><?php echo $row['quantity']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-small">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-small delete-btn" 
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-price="<?php echo number_format($row['price'], 0, ',', '.'); ?>"
                                            data-quantity="<?php echo $row['quantity']; ?>">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-box-open fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
                    <p>Tidak ada data produk ditemukan.</p>
                    <?php if (!empty($search)): ?>
                        <p>Hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search); ?>"</strong></p>
                        <a href="index.php" class="btn">Tampilkan Semua Produk</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal/Popup Konfirmasi Hapus -->
        <div id="deleteModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close-modal">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Konfirmasi Hapus Produk</h2>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                    
                    <div class="product-details">
                        <div>
                            <strong>Nama Produk:</strong>
                            <span id="modalProductName"></span>
                        </div>
                        <div>
                            <strong>Harga:</strong>
                            <span id="modalProductPrice"></span>
                        </div>
                        <div>
                            <strong>Stok:</strong>
                            <span id="modalProductQuantity"></span>
                        </div>
                    </div>
                    
                    <p>
                        <i class="fas fa-exclamation-circle warning-icon"></i>
                        Semua data produk ini akan dihapus secara permanen.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn-modal-cancel">
                        <i class="fas fa-times"></i> Batalkan
                    </button>
                    <button id="confirmDelete" class="btn-modal-delete">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Produk
                    </button>
                </div>
            </div>
        </div>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Sistem CRUD Produk. Dibuat dengan PHP dan MySQL.</p>
            <p>Total Produk: <strong><?php echo count($products_data); ?></strong></p>
        </footer>
    </div>

    <script>
        // JavaScript untuk mengontrol modal/popup
        document.addEventListener('DOMContentLoaded', function() {
            let deleteUrl = null;
            
            // Ambil semua tombol hapus
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const modal = document.getElementById('deleteModal');
            const closeModal = document.querySelector('.close-modal');
            const cancelButton = document.querySelector('.btn-modal-cancel');
            const confirmDeleteButton = document.getElementById('confirmDelete');
            
            // Event listener untuk setiap tombol hapus
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productPrice = this.getAttribute('data-price');
                    const productQuantity = this.getAttribute('data-quantity');
                    
                    // Set data produk di modal
                    document.getElementById('modalProductName').textContent = productName;
                    document.getElementById('modalProductPrice').textContent = 'Rp ' + productPrice;
                    document.getElementById('modalProductQuantity').textContent = productQuantity;
                    
                    // Set URL untuk menghapus
                    deleteUrl = 'delete.php?id=' + productId;
                    
                    // Tampilkan modal
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden'; // Mencegah scroll
                });
            });
            
            // Fungsi untuk menyembunyikan modal
            function hideModal() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Mengembalikan scroll
            }
            
            // Event listener untuk tombol tutup
            closeModal.addEventListener('click', hideModal);
            
            // Event listener untuk tombol batalkan
            cancelButton.addEventListener('click', hideModal);
            
            // Event listener untuk klik di luar modal
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    hideModal();
                }
            });
            
            // Event listener untuk tombol konfirmasi hapus
            confirmDeleteButton.addEventListener('click', function() {
                if (deleteUrl) {
                    window.location.href = deleteUrl;
                }
            });
            
            // Tutup modal dengan tombol ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.style.display === 'flex') {
                    hideModal();
                }
            });
        });
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>