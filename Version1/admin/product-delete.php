<?php
session_start();
require_once 'functions.php';
requireAdmin();

$id = $_GET['id'];

deleteProduct($id);

header("Location: index.php?success=1&message=Produk berhasil dihapus");
exit;
