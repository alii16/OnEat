<?php
// Periksa apakah user sudah login dan merupakan staff restoran
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'resto') {
    header("Location: login.php");
    exit();
}

// Fetch the list of orders associated with the restaurant
$stmt = $pdo->prepare('SELECT o.id, o.status, o.estimated_delivery_time, u.username, o.total_amount, 
                              GROUP_CONCAT(m.name SEPARATOR ", ") as menu_items 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       JOIN order_items oi ON o.id = oi.order_id 
                       JOIN menu_items m ON oi.item_id = m.id 
                       WHERE o.restaurant_id = ? 
                       GROUP BY o.id ORDER BY o.order_date DESC');
$stmt->execute([$_SESSION['restaurant_id']]);
$orders = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $estimated_delivery_time = $_POST['estimated_delivery_time'];

    // Update order status and estimated delivery time
    $updateStmt = $pdo->prepare('UPDATE orders SET status = ?, estimated_delivery_time = ? WHERE id = ?');
    $updateStmt->execute([$status, $estimated_delivery_time, $order_id]);

    // Redirect to refresh the page after updating
    echo '<script>window.location.href = "index.php?page=manage_orders";</script>';
    exit();
}
?>

<div class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto max-w-screen-xl py-6">
        <div class="text-center mb-8">
            <h1 class="text-gray-900 dark:text-white text-3xl font-bold mt-8">Manage Orders</h1>
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
                        <div class="w-full md:w-3/4">
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Order ID: </span>
                                <?= htmlspecialchars($order['id']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Customer:</span>
                                <?= htmlspecialchars($order['username']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Status:</span>
                                <?= htmlspecialchars($order['status']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Estimated Delivery Time:</span>
                                <?= htmlspecialchars($order['estimated_delivery_time']) ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Total Amount:</span>
                                <?= number_format($order['total_amount'] + 15000, 0, ',', '.') ?></div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><span>Items:</span>
                                <?= htmlspecialchars($order['menu_items']) ?></div>

                            <button
                                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                onclick="openUpdateForm('<?php echo htmlspecialchars($order['id']); ?>', '<?php echo htmlspecialchars($order['status']); ?>', '<?php echo htmlspecialchars($order['estimated_delivery_time']); ?>')">
                                <svg class="mr-1 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10.779 17.779 4.36 19.918 6.5 13.5m4.279 4.279 8.364-8.643a3.027 3.027 0 0 0-2.14-5.165 3.03 3.03 0 0 0-2.14.886L6.5 13.5m4.279 4.279L6.499 13.5m2.14 2.14 6.213-6.504M12.75 7.04 17 11.28"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="updateOrderModal" class="hidden sticky inset-0 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-gray-50 dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Update Order</h3>
            <form method="post" class="space-y-4 mt-4">
                <input type="hidden" name="order_id" id="order_id">
                <div>
                    <label for="status"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Status</label>
                    <select name="status" id="status"
                        class="mt-1 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 block w-full border border-gray-300 rounded-md shadow-lg py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="Ditunda">Ditunda</option>
                        <option value="Terkonfirmasi">Terkonfirmasi</option>
                        <option value="Sedang Diantar">Sedang Diantar</option>
                        <option value="Sudah Diantar">Sudah Diantar</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label for="estimated_delivery_time"
                        class="block text-sm font-medium text-gray-700 dark:text-white text-left">Estimasi
                        Waktu Pengantaran</label>
                    <input type="text" name="estimated_delivery_time" id="estimated_delivery_time"
                        class="mt-1 text-gray-900 dark:text-white block w-full border bg-gray-50 dark:bg-gray-700 border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-indigo-600 font-medium text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Update</button>
                </div>
                <div>
                    <button type="button"
                        class="w-full bg-gray-600 font-medium text-white py-2 px-4 rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        onclick="closeUpdateForm()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUpdateForm(orderId, status, estimatedDeliveryTime) {
        document.getElementById('order_id').value = orderId;
        document.getElementById('status').value = status;
        document.getElementById('estimated_delivery_time').value = estimatedDeliveryTime;
        document.getElementById('updateOrderModal').classList.remove('hidden');
    }

    function closeUpdateForm() {
        document.getElementById('updateOrderModal').classList.add('hidden');
    }
</script>