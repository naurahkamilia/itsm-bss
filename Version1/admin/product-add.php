<?php

defined('APP_ACCESS') or die('Direct access not permitted');
session_start();
require_once 'functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $price = $_POST['price'];

    // Upload
    $filename = time() . "-" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../public/uploads/" . $filename);

    addProduct($name, $price, $filename);

    header("Location: index.php?success=1&message=Produk berhasil ditambahkan");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10">
    <h1 class="text-2xl font-bold mb-5">Tambah Produk</h1>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="text" name="name" placeholder="Nama Produk" required class="w-full p-3 border rounded">
        <input type="number" name="price" placeholder="Harga Produk" required class="w-full p-3 border rounded">
        <input type="file" name="image" required class="w-full p-3 border rounded">

        <button class="px-6 py-3 bg-green-600 text-white rounded">Simpan</button>
    </form>
</body>
</html>
