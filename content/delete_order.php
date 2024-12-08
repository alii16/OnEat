<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Hapus entri terkait di tabel order_items
        $stmt = $pdo->prepare('DELETE FROM order_items WHERE order_id = ?');
        $stmt->execute([$order_id]);

        // Hapus entri di tabel orders
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
        $stmt->execute([$order_id]);

        // Commit transaksi
        $pdo->commit();

        // Redirect kembali ke halaman pesanan dengan pesan sukses
        $_SESSION['message'] = "pesanan berhasil dibatalkan";
        echo '<script>window.location.href = "index.php?page=delete_order";</script>';
        exit;

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $pdo->rollBack();
        $_SESSION['error'] = "Gagal membatalkan pesanan: " . $e->getMessage();
        header("Location: index.php?page=my_orders");
        exit;
    }
} else {
    // Jika tidak ada data POST, redirect kembali ke halaman pesanan
    echo '<script>window.location.href = "index.php?page=my_orders";</script>';
    exit;
}
