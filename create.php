<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    
    // Validasi input
    if (empty($name) || empty($price)) {
        $error = "Nama dan harga produk harus diisi!";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Harga harus berupa angka positif!";
    } elseif (!is_numeric($quantity) || $quantity < 0) {
        $error = "Kuantitas harus berupa angka non-negatif!";
    } else {
        // Insert data ke database
        $query = "INSERT INTO products (name, description, price, quantity) 
                  VALUES ('$name', '$description', '$price', '$quantity')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php?message=create_success");
            exit();
        } else {
            $error = "Terjadi kesalahan: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h1>
            <p>Isi form berikut untuk menambahkan produk baru</p>
        </header>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Nama Produk *</label>
                    <input type="text" id="name" name="name" required 
                           placeholder="Contoh: Laptop Gaming" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Deskripsi Produk</label>
                    <textarea id="description" name="description" 
                              placeholder="Deskripsi lengkap produk..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price"><i class="fas fa-money-bill-wave"></i> Harga (Rp) *</label>
                    <input type="number" id="price" name="price" required min="1" step="100"
                           placeholder="Contoh: 12000000" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="quantity"><i class="fas fa-boxes"></i> Kuantitas Stok</label>
                    <input type="number" id="quantity" name="quantity" min="0" step="1"
                           placeholder="Contoh: 10" value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : 0; ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                    <a href="index.php" class="btn btn-danger">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
        
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Sistem CRUD Produk</p>
        </footer>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>