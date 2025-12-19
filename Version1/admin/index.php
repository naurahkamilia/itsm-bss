<?php

defined('APP_ACCESS') or die('Direct access not permitted');

session_start();
require_once 'functions.php';
requireAdmin();

$products = getProducts();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="bg-white shadow p-5 flex justify-between">
    <h1 class="text-2xl font-bold text-blue-700">Dashboard Admin</h1>
    <div>
        <a href="../" class="px-4 py-2 bg-blue-600 text-white rounded">Lihat Website</a>
        <a href="../logout.php" class="px-4 py-2 bg-red-600 text-white rounded">Logout</a>
    </div>
</div>

<div class="container mx-auto mt-8">

    <a href="product-add.php"
       class="px-6 py-3 bg-green-600 text-white rounded-lg font-bold inline-block mb-5">
       + Tambah Produk
    </a>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="w-full">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Gambar</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Harga</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$products): ?>
                <tr><td colspan="5" class="p-5 text-center text-gray-500">Belum ada produk</td></tr>
                <?php endif; ?>

                <?php foreach ($products as $p): ?>
                <tr class="border-b">
                    <td class="p-3"><?php echo $p['id']; ?></td>
                    <td class="p-3"><img src="../public/uploads/<?php echo $p['image']; ?>" class="w-16"></td>
                    <td class="p-3 font-bold"><?php echo $p['name']; ?></td>
                    <td class="p-3">Rp <?php echo number_format($p['price'],0,',','.'); ?></td>
                    <td class="p-3 flex gap-2">
                        <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="px-3 py-1 bg-blue-600 text-white rounded">Edit</a>
                        <a onclick="return confirm('Hapus produk ini?')"
                           href="product-delete.php?id=<?php echo $p['id']; ?>"
                           class="px-3 py-1 bg-red-600 text-white rounded">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
