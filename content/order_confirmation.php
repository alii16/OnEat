<?php
// Check if user is logged in as a restaurant staff
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'resto') {
    header("Location: login.php");
    exit();
}

// Get the order ID from the query parameter
$order_id = $_GET['order_id'];

// Fetch the updated order details from the database
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$order_id]);
$order = $stmt->fetch();
?>

<body class="bg-gray-100 min-h-screen flex flex-col justify-center items-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4">Order Confirmed</h2>
        <p class="text-gray-700">Order ID: <?php echo htmlspecialchars($order['id']); ?></p>
        <p class="text-gray-700">Status: <?php echo htmlspecialchars($order['status']); ?></p>
        <p class="text-gray-700">Estimated Delivery Time: <?php echo htmlspecialchars($order['estimated_delivery_time']); ?></p>
        <a href="index.php?page=manage_orders" class="mt-4 inline-block bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Back to Manage Orders</a>
    </div>
</body>
