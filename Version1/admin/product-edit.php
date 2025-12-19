<?php

defined('APP_ACCESS') or die('Direct access not permitted');
session_start();
require_once 'functions.php';
requireAdmin();

$id = $_GET['id'];
$product = getProductById($id);

if (!$product) die("Produk tidak ditemukan!");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $image = $product['image'];

    if ($_FILES['image']['name']) {
        $image = time() . "-" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../public/uploads/" . $image);
    }

    updateProduct($id, $name, $price, $image);

    header("Location: index.php?success=1&message=Produk berhasil diperbarui");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10">

<h1 class="text-2xl font-bold mb-5">Edit Produk</h1>

<form method="POST" enctype="multipart/form-data" class="space-y-5">
    <input type="text" name="name" value="<?php echo $product['name']; ?>" class="w-full p-3 border rounded">
    <input type="number" name="price" value="<?php echo $product['price']; ?>" class="w-full p-3 border rounded">

    <p>Gambar saat ini:</p>
    <img src="../public/uploads/<?php echo $product['image']; ?>" class="w-32 mb-3">

    <input type="file" name="image" class="w-full p-3 border rounded">

    <button class="px-6 py-3 bg-blue-600 text-white rounded">Update</button>
</form>

</body>
</html>
