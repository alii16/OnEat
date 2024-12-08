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

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex justify-center">
    <div class="container mx-auto max-w-screen-md px-4 py-6">
        <div class="text-center mt-3 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Orders</h1>
        </div>
        <div class="overflow-x-auto">
            <div class="max-w-screen-lg mx-auto">
                <table
                    class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Estimasi Waktu Pengantaran</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Total Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Menu Items</th>
                            <th class="px-6 py-3 text-left text-xs font-large text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['id']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($order['username']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($order['estimated_delivery_time']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo 'Rp ' . number_format($order['total_amount'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 max-w-[10rem] overflow-x-auto whitespace-normal">
                                    <?php echo htmlspecialchars($order['menu_items']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        class="font-medium bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        onclick="openUpdateForm('<?php echo htmlspecialchars($order['id']); ?>', '<?php echo htmlspecialchars($order['status']); ?>', '<?php echo htmlspecialchars($order['estimated_delivery_time']); ?>')">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Update Order Form Modal -->
<div id="updateOrderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Order</h3>
            <form method="post" class="space-y-4 mt-4">
                <input type="hidden" name="order_id" id="order_id">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 text-left">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-lg py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
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
                        class="block text-sm font-medium text-gray-700 text-left">Estimasi
                        Waktu Pengantaran</label>
                    <input type="text" name="estimated_delivery_time" id="estimated_delivery_time"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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