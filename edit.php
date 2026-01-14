<?php
include 'config.php';

$error = '';
$product = null;

// Ambil ID produk dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data produk berdasarkan ID
$query = "SELECT * FROM products WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$product = mysqli_fetch_assoc($result);

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
        // Update data produk
        $update_query = "UPDATE products SET 
                         name = '$name', 
                         description = '$description', 
                         price = '$price', 
                         quantity = '$quantity' 
                         WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            header("Location: index.php?message=update_success");
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
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-edit"></i> Edit Produk</h1>
            <p>Perbarui informasi produk di form berikut</p>
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
                           value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Deskripsi Produk</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price"><i class="fas fa-money-bill-wave"></i> Harga (Rp) *</label>
                    <input type="number" id="price" name="price" required min="1" step="100"
                           value="<?php echo htmlspecialchars($product['price']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="quantity"><i class="fas fa-boxes"></i> Kuantitas Stok</label>
                    <input type="number" id="quantity" name="quantity" min="0" step="1"
                           value="<?php echo htmlspecialchars($product['quantity']); ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Update Produk
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