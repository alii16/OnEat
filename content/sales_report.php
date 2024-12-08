<?php
// Fungsi untuk mengambil data penjualan berdasarkan periode
function fetchSalesData($pdo, $restaurant_id, $period)
{
    switch ($period) {
        case 'daily':
            $sql = 'SELECT o.id AS order_id, o.order_date, m.name AS menu_name, m.price, oi.quantity
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN menu_items m ON oi.item_id = m.id
                    WHERE o.restaurant_id = ? AND o.status = "Sudah Diantar" AND m.restaurant_id = ?
                    AND DATE(o.order_date) = CURDATE()';
            $params = [$restaurant_id, $restaurant_id];
            break;

        case 'weekly':
            $currentDate = date('Y-m-d');
            $lastWeek = date('Y-m-d', strtotime('-1 week', strtotime($currentDate)));
            $sql = 'SELECT WEEK(o.order_date) AS week_number, YEAR(o.order_date) AS year_number,
                           SUM(m.price * oi.quantity) AS sales_amount
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN menu_items m ON oi.item_id = m.id
                    WHERE o.restaurant_id = ? AND o.status = "Sudah Diantar" AND m.restaurant_id = ?
                    AND o.order_date >= ? AND o.order_date <= ?
                    GROUP BY WEEK(o.order_date), YEAR(o.order_date)
                    ORDER BY year_number DESC, week_number DESC';
            $params = [$restaurant_id, $restaurant_id, $lastWeek, $currentDate];
            break;

        case 'monthly':
            $currentMonth = date('Y-m-01');
            $lastMonth = date('Y-m-01', strtotime('-1 month', strtotime($currentMonth)));
            $sql = 'SELECT MONTH(o.order_date) AS month_number, YEAR(o.order_date) AS year_number,
                           SUM(m.price * oi.quantity) AS sales_amount
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN menu_items m ON oi.item_id = m.id
                    WHERE o.restaurant_id = ? AND o.status = "Sudah Diantar" AND m.restaurant_id = ?
                    AND o.order_date >= ? AND o.order_date <= ?
                    GROUP BY MONTH(o.order_date), YEAR(o.order_date)
                    ORDER BY year_number DESC, month_number DESC';
            $params = [$restaurant_id, $restaurant_id, $lastMonth, $currentMonth];
            break;

        default:
            return []; // Jika tidak ada pilihan yang sesuai, kembalikan array kosong
    }

    // Eksekusi query dengan parameter yang sesuai
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Proses form submission untuk menangani pilihan pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['period'])) {
        $_SESSION['period'] = $_POST['period']; // Simpan periode yang dipilih di session
    }
}

// Ambil data penjualan berdasarkan periode yang dipilih
if (!isset($_SESSION['period'])) {
    $_SESSION['period'] = 'daily'; // Default periode adalah harian jika tidak ada yang dipilih
}
$restaurant_id = $_SESSION['restaurant_id'];
$period = $_SESSION['period'];
$sales_data = fetchSalesData($pdo, $restaurant_id, $period);

// Hitung total harga
$total_sales = 0;
foreach ($sales_data as $sale) {
    if ($period == 'daily') {
        $total_sales += $sale['price'] * $sale['quantity'];
    } else {
        $total_sales += $sale['sales_amount'];
    }
}
?>

<div class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto py-10">
        <div class="max-w-3xl mx-auto bg-gray-50 dark:bg-gray-900 rounded-lg shadow-2xl p-6">
            <div class="container mt-3 text-center">
                <div
                    class="marquee text-gray-900 dark:text-white text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    <h1>Sales Report</h1>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                <form method="post" class="mb-4">
                    <label for="period" class="text-white block text-md font-medium text-gray-700 mb-2">Pilih
                        Periode:</label>
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="col-span-3">
                            <select id="period" name="period"
                                class=" text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 sm:text-md">
                                <option value="daily" <?php echo ($period == 'daily') ? 'selected' : ''; ?>>Harian
                                </option>
                                <option value="weekly" <?php echo ($period == 'weekly') ? 'selected' : ''; ?>>Mingguan
                                </option>
                                <option value="monthly" <?php echo ($period == 'monthly') ? 'selected' : ''; ?>>Bulanan
                                </option>
                            </select>
                        </div>
                        <div class="col-span-1">
                            <button type="submit"
                                class="inline-block font-medium bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-gray-900 dark:text-gray-300 bg-gray-100 dark:bg-gray-700">
                                <?php if ($period == 'daily'): ?>
                                    <th class="px-4 py-2">Order ID</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Menu Item</th>
                                    <th class="px-4 py-2 text-center">Harga</th>
                                    <th class="px-4 py-2 text-center">Quantity</th>
                                    <th class="px-4 py-2 text-center">Total</th>
                                <?php elseif ($period == 'weekly'): ?>
                                    <th class="px-4 py-2">Minggu</th>
                                    <th class="px-4 py-2">Tahun</th>
                                    <th class="px-4 py-2 text-center">Total Penjualan</th>
                                <?php elseif ($period == 'monthly'): ?>
                                    <th class="px-4 py-2">Bulan</th>
                                    <th class="px-4 py-2">Tahun</th>
                                    <th class="px-4 py-2 text-center">Total Penjualan</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sales_data)): ?>
                                <?php foreach ($sales_data as $sale): ?>
                                    <tr class="text-gray-900 dark:text-gray-300">
                                        <?php if ($period == 'daily'): ?>
                                            <td class="px-4 py-2"><?php echo $sale['order_id']; ?></td>
                                            <td class="px-4 py-2">
                                                <?php echo date('Y-m-d H:i:s', strtotime($sale['order_date'])); ?>
                                            </td>
                                            <td class="px-4 py-2"><?php echo $sale['menu_name']; ?></td>
                                            <td class="px-4 py-2 text-right">
                                                <?php echo 'Rp ' . number_format($sale['price'], 0, ',', '.'); ?>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <?php echo $sale['quantity']; ?>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <?php echo 'Rp ' . number_format($sale['price'] * $sale['quantity'], 0, ',', '.'); ?>
                                            </td>
                                        <?php elseif ($period == 'weekly'): ?>
                                            <td class="px-4 py-2"><?php echo $sale['week_number']; ?></td>
                                            <td class="px-4 py-2"><?php echo $sale['year_number']; ?></td>
                                            <td class="px-4 py-2 text-center">
                                                <?php echo 'Rp ' . number_format($sale['sales_amount'], 0, ',', '.'); ?>
                                            </td>
                                        <?php elseif ($period == 'monthly'): ?>
                                            <td class="px-4 py-2">
                                                <?php echo date('F', mktime(0, 0, 0, $sale['month_number'], 1)); ?>
                                            </td>
                                            <td class="px-4 py-2"><?php echo $sale['year_number']; ?></td>
                                            <td class="px-4 py-2 text-right">
                                                <?php echo 'Rp ' . number_format($sale['sales_amount'], 0, ',', '.'); ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="bg-gray-50 dark:bg-gray-700 font-bold">
                                    <td class="text-white px-4 py-2 text-right"
                                        colspan="<?php echo ($period == 'daily') ? 5 : 2; ?>">Total
                                    </td>
                                    <td class="text-white px-4 py-2 text-right">Rp
                                        <?php echo number_format($total_sales, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-400" colspan="6">Tidak ada data
                                        penjualan yang tersedia untuk periode ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>