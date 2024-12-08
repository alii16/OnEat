<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT orders.*, GROUP_CONCAT(menu_items.name) AS items, GROUP_CONCAT(menu_items.image_url) AS image_urls FROM orders LEFT JOIN order_items ON orders.id = order_items.order_id LEFT JOIN menu_items ON order_items.item_id = menu_items.id WHERE orders.user_id = ? GROUP BY orders.id ORDER BY orders.order_date DESC');
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto max-w-screen-xl py-6">
        <div class="text-center mb-8">
            <h1 class="text-gray-900 dark:text-white text-3xl font-bold mt-8">Pesanan Saya</h1>
        </div>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $_SESSION['message'] ?></span>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($orders as $order): ?>
                <div class="bg-gray-50 dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="flex flex-col md:flex-row items-center p-4">
                        <div class="w-full md:w-1/4 flex justify-center mb-4 md:mb-0">
                            <?php
                            $image_urls = explode(',', $order['image_urls']);
                            $first_image = reset($image_urls);
                            ?>
                            <?php if (!empty($first_image)): ?>
                                <img src="<?= htmlspecialchars($first_image) ?>" alt="Item Image"
                                    class="w-24 h-24 object-cover rounded-md">
                            <?php else: ?>
                                <div class="w-24 h-24 bg-gray-200 rounded-md"></div>
                            <?php endif; ?>
                        </div>
                        <div class="w-full md:w-3/4">
                            <div class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <?= htmlspecialchars($order['items']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Alamat Pengantaran: </span>
                                <?= htmlspecialchars($order['delivery_address']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Metode Pembayaran:</span>
                                <?= htmlspecialchars($order['payment_method']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Tanggal Dipesan:</span>
                                <?= htmlspecialchars($order['order_date']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Status:</span>
                                <?= ucfirst(htmlspecialchars($order['status'])) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Kontak:</span>
                                <?= htmlspecialchars($order['contact_information']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-2"><span>Total:</span> Rp
                                <?= number_format($order['total_amount'] +15000, 0, ',', '.') ?></div>

                            <?php if ($order['status'] == 'Ditunda'): ?>
                                <form method="post" action="index.php?page=delete_order"
                                    onsubmit="return confirm('Yakin ingin membatalkan pesanan?');">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit"
                                        class="flex items-center bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Batal</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled
                                    class="flex items-center bg-gray-600 text-white py-2 px-4 rounded-md shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Batal</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>