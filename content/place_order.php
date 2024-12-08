<?php
// Initialize total amount
$total_amount = 0;

// Check if a form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lakukan validasi input disini jika diperlukan

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $address = $_POST['delivery_address'];
    $contact = $_POST['contact_information'];
    $payment_method = $_POST['payment_method'];

    try {
        // Membuat pesanan
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, name, delivery_address, contact_information, payment_method, total_amount, restaurant_id) 
                               SELECT ?, ?, ?, ?, ?, 0, restaurant_id FROM menu_items WHERE id = (SELECT item_id FROM cart WHERE user_id = ? LIMIT 1)');
        $stmt->execute([$user_id, $name, $address, $contact, $payment_method, $user_id]);
        $order_id = $pdo->lastInsertId();

        // Memindahkan item keranjang ke order_items
        $stmt = $pdo->prepare('SELECT c.*, m.price FROM cart c JOIN menu_items m ON c.item_id = m.id WHERE c.user_id = ?');
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();

        $total_amount = 0; // Initialize total amount

        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, item_id, quantity) VALUES (?, ?, ?)');
            $stmt->execute([$order_id, $item['item_id'], $item['quantity']]);
            $total_amount += $item['quantity'] * $item['price']; // Menghitung total jumlah
        }

        // Memperbarui total jumlah di tabel orders
        $stmt = $pdo->prepare('UPDATE orders SET total_amount = ? WHERE id = ?');
        $stmt->execute([$total_amount, $order_id]);

        // Mengosongkan keranjang
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);

        $_SESSION['order_placed'] = true; // Menandai bahwa pesanan berhasil ditempatkan

        // Redirect ke halaman my_orders setelah pesanan berhasil
        echo '<script>window.location.href = "index.php?page=my_orders";</script>';
        exit;

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat memproses pesanan: " . $e->getMessage();
    }
}

// Jika tidak ada POST request, maka ini adalah halaman pertama kali diakses atau jika pembayaran dibatalkan
if (isset($_SESSION['order_placed']) && $_SESSION['order_placed'] === true) {
    unset($_SESSION['order_placed']); // Hapus tanda pesanan berhasil ditempatkan
}

// Fetch cart items to calculate total amount for display
$stmt = $pdo->prepare('SELECT c.*, m.price FROM cart c JOIN menu_items m ON c.item_id = m.id WHERE c.user_id = ?');
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total_amount = 0; // Reset total amount
foreach ($cart_items as $item) {
    $total_amount += $item['quantity'] * $item['price'];
}

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
if ($error_message) {
    unset($_SESSION['error_message']); // Hapus pesan kesalahan setelah ditampilkan
}
?>

<section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
        <div class="mx-auto max-w-5xl">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Payment</h2>

            <?php if ($error_message): ?>
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700" role="alert">
                    <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12">
                <form method="post" onsubmit="return confirmPayment();"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6 lg:max-w-xl lg:p-8">
                    <div class="mb-6 grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Full name</label>
                            <input type="text" id="name" name="name"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                placeholder="Ali Polanunu" required />
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label for="delivery_address"
                                class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Address*
                            </label>
                            <input type="text" id="delivery_address" name="delivery_address"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pe-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500  dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                placeholder="xxxx-xxxx-xxxx-xxxx" required />
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label for="contact_information"
                                class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Contact
                                information
                            </label>
                            <input type="text" id="contact_information" name="contact_information"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pe-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500  dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                placeholder="xxxx-xxxx-xxxx-xxxx" required />
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label for="payment_method"
                                class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Payment method
                            </label>
                            <select id="payment_method" name="payment_method" required
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pe-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500  dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                                <option value="Kartu Kredit">Kartu Kredit</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="Tunai/ Bayar di Tempat">Tunai/ Bayar di Tempat</option>
                            </select>
                        </div>

                    </div>

                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4  focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Pay
                        now</button>
                </form>

                <div class="mt-6 grow sm:mt-8 lg:mt-0">
                    <div
                        class="space-y-4 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                        <div class="space-y-2">
                            <dl class="flex items-center justify-between gap-4">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Original price</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">
                                    Rp <?= number_format($total_amount, 0, ',', '.') ?></dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                                <dd class="text-base font-medium text-red-600">-Rp 0</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Store Pickup</dt>
                                <dd class="text-base font-medium text-gray-900">Rp 12.000</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                                <dd class="text-base font-medium text-gray-900">Rp 3.000</dd>
                            </dl>

                            <dl
                                class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-bold text-gray-900 dark:text-white">
                                    Rp <?= number_format($total_amount + 15000, 0, ',', '.') ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function confirmPayment() {
        if (confirm("Apakah Anda yakin ingin melanjutkan?")) {
            alert("Pesanan berhasil diorder!");
            return true; // Lanjutkan dengan submit form
        } else {
            alert("Pesanan dibatalkan.");
            window.location.href = "index.php?page=view_cart"; // Redirect ke halaman sebelumnya
            return false; // Batalkan submit form
        }
    }
</script>