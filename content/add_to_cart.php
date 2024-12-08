<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    try {
        $stmt = $pdo->prepare('INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $item_id, $quantity]);

        // Gunakan JavaScript untuk menampilkan alert sukses dan redirect
        echo "<script>
                alert('Item berhasil ditambahkan!');
                window.location.href = 'index.php?page=view_cart';
              </script>";
        exit;
    } catch (PDOException $e) {
        // Menangkap kesalahan dan menampilkan pesan
        echo "<script>
                alert('Terjadi kesalahan saat menambahkan item: " . $e->getMessage() . "');
                window.location.href = 'index.php?page=add_to_cart';
              </script>";
    }
}
