<?php
$error_message = '';

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch menu item details from the database
    $stmt = $pdo->prepare('
        SELECT mi.id, mi.name AS item_name, mi.price, mi.stock AS item_stock, mi.image_url, mi.description, 
               r.name AS restaurant_name, u.image_user AS restaurant_image
        FROM menu_items mi
        JOIN restaurants r ON mi.restaurant_id = r.id
        JOIN users u ON r.user_id = u.id
        WHERE mi.id = ?
    ');
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $error_message = "Menu item not found.";
    }
} else {
    $error_message = "No item selected.";
}

$restaurant_id = $_SESSION['restaurant_id'];

// Tangani logika penghapusan item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $item_id = $_POST['item_id'];

    // Fetch the image path before deletion
    $stmt = $pdo->prepare('SELECT image_url FROM menu_items WHERE id = ? AND restaurant_id = ?');
    $stmt->execute([$item_id, $_SESSION['restaurant_id']]);
    $item = $stmt->fetch();

    // Delete the item from the database
    $delete_stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?');
    $delete_stmt->execute([$item_id, $_SESSION['restaurant_id']]);

    // Check if the image file exists and delete it
    if ($item && file_exists($item['image_url'])) {
        unlink($item['image_url']); // Delete the file
    }

    // Redirect untuk mencegah pengiriman ulang form
    echo '<script>window.location.href = "index.php?page=manage_stock";</script>';
    exit;
}
?>


<section class="py-8 bg-gray-50 md:py-16 dark:bg-gray-900 antialiased">
    <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">

        <?php if ($error_message): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <a href="index.php?page=manage_stock" class="inline-flex items-center text-md mt-2 mb-4 font-medium text-gray-900 focus:ring-red-300 dark:text-white">
            <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4"/>
            </svg>
            Back
        </a>
        <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">


            <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                <img class="w-full rounded-lg" src="<?= htmlspecialchars($item['image_url']) ?>"
                    alt="<?= htmlspecialchars($item['item_name']) ?>" />
            </div>

            <div class="mt-6 sm:mt-8 lg:mt-0">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    <?= htmlspecialchars($item['item_name']) ?>
                </h1>
                <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                    <p class="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white me-2">
                        Rp<?= number_format($item['price'], 0, ',', '.') ?>
                    </p>

                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                            </svg>
                            <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium leading-none text-gray-500 dark:text-gray-400">
                            (5.0)
                        </p>
                    </div>

                </div>
                <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Stock:
                    <?= htmlspecialchars($item['item_stock']) ?>
                </p>
                <div class="mt-6 gap-4 items-center flex sm:mt-8">
                    <a href="index.php?page=edit_item&id=<?= htmlspecialchars($item['id']) ?>"
                        class="inline-flex items-center rounded-lg bg-yellow-400 px-3 py-2.5 text-sm font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-400 dark:hover:bg-yellow-500 dark:focus:ring-yellow-600">
                        <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                        </svg>
                        Edit menu
                    </a>

                    <form method="post" onsubmit="return confirmDelete()">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                        <button type="submit" title=""
                            class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2.5 text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                            <svg class="w-5 h-5 -ms-2 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                            </svg>
                            Delete menu
                        </button>
                    </form>


                </div>

                <div class="flex items-center mt-4">
                    <div class="shrink-0 w-10 h-10 mr-3">
                        <img class="w-full" src="<?= htmlspecialchars($item['restaurant_image']) ?>"
                            alt="<?= htmlspecialchars($item['restaurant_name']) ?>" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <?= htmlspecialchars($item['restaurant_name']) ?>
                        </h2>
                    </div>
                </div>

                <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                <p class="mb-6 text-gray-500 dark:text-gray-400">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Blanditiis recusandae adipisci magnam
                    deserunt animi. Maxime, unde. Perspiciatis aut minus, ratione ex alias tempora commodi odio ullam,
                    molestias illo qui eveniet!
                </p>

                <p class="text-gray-500 dark:text-gray-400">
                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit dolores, facere soluta laborum
                    similique odit natus, dicta minus reiciendis, magni ab ipsam quos voluptate fugit ducimus dolor enim
                    sed quisquam.
                </p>
            </div>
        </div>
    </div>
</section>


<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus item ini?");
    }
</script>