<?php
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT c.*, m.name, m.price, m.image_url FROM cart c JOIN menu_items m ON c.item_id = m.id WHERE c.user_id = ?');
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $cart_id = $_POST['cart_id'];
    $stmt = $pdo->prepare('DELETE FROM cart WHERE id = ? AND user_id = ?');
    $stmt->execute([$cart_id, $user_id]);
    echo '<script>window.location.href = "index.php?page=view_cart";</script>';
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['quantity'] * $item['price'];
}
?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="bg-gray-50 dark:bg-gray-900 py-10">
    <div class="container mx-auto max-w-5xl px-2">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Shopping Cart</h1>
        <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <li class="flex items-center justify-between p-4">
                            <div class="flex items-center space-x-4">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 object-cover rounded">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['name']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Rp
                                        <?= number_format($item['price'], 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-900 dark:text-white"><?= $item['quantity'] ?></span>
                                <span class="text-gray-800 dark:text-white px-2">x</span>
                                <p class="text-gray-900 dark:text-white font-semibold">Rp
                                    <?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?>
                                </p>
                            </div>
                            <div>
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="p-4 text-center text-gray-600 dark:text-gray-400">Your cart is empty.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-lg mt-6 p-4">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Order Summary</h2>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Original price</span>
                    <span class="text-gray-900 dark:text-white">Rp <?= number_format($total, 0, ',', '.') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Savings</span>
                    <span class="text-red-600">-Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Store Pickup</span>
                    <span class="text-gray-900 dark:text-white">Rp 12.000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tax</span>
                    <span class="text-gray-900 dark:text-white">Rp 3.000</span>
                </div>
            </div>
            <div class="flex justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white">Rp
                    <?= number_format($total + 15000, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="index.php?page=search_restaurants"
                class="bg-gray-50 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-white py-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                Continue Shopping
            </a>

            <button onclick="proceedToCheckout()"
                class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 <?php if (empty($cart_items))
                    echo 'opacity-50 cursor-not-allowed'; ?>"
                <?php if (empty($cart_items))
                    echo 'disabled'; ?>>
                Proceed to Checkout
            </button>
        </div>
    </div>
</div>


<script>
    function proceedToCheckout() {
        <?php if (!empty($cart_items)): ?>
            window.location.href = "index.php?page=place_order";
        <?php endif; ?>
    }
</script>