<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = $_POST['query'];

    // Query untuk mencari restoran dengan JOIN ke tabel users
    $stmt = $pdo->prepare('
        SELECT restaurants.*, users.image_user 
        FROM restaurants
        JOIN users ON restaurants.user_id = users.id
        WHERE restaurants.name LIKE ?
    ');
    $stmt->execute(["%$query%"]);
    $restaurants = $stmt->fetchAll();
} else {
    // Jika tidak ada pencarian, tampilkan semua restoran dengan JOIN ke tabel users
    $stmt = $pdo->query('
        SELECT restaurants.*, users.image_user 
        FROM restaurants
        JOIN users ON restaurants.user_id = users.id
    ');
    $restaurants = $stmt->fetchAll();
}
?>

<section class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto max-w-screen-md px-4 py-6">
        <div class="max-w-xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-8">Daftar Restoran</h1>
            </div>
            <form method="post" class="flex items-center max-w-full mx-auto mb-8">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 12c.263 0 .524-.06.767-.175a2 2 0 0 0 .65-.491c.186-.21.333-.46.433-.734.1-.274.15-.568.15-.864a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 12 9.736a2.4 2.4 0 0 0 .586 1.591c.375.422.884.659 1.414.659.53 0 1.04-.237 1.414-.659A2.4 2.4 0 0 0 16 9.736c0 .295.052.588.152.861s.248.521.434.73a2 2 0 0 0 .649.488 1.809 1.809 0 0 0 1.53 0 2.03 2.03 0 0 0 .65-.488c.185-.209.332-.457.433-.73.1-.273.152-.566.152-.861 0-.974-1.108-3.85-1.618-5.121A.983.983 0 0 0 17.466 4H6.456a.986.986 0 0 0-.93.645C5.045 5.962 4 8.905 4 9.736c.023.59.241 1.148.611 1.567.37.418.865.667 1.389.697Zm0 0c.328 0 .651-.091.94-.266A2.1 2.1 0 0 0 7.66 11h.681a2.1 2.1 0 0 0 .718.734c.29.175.613.266.942.266.328 0 .651-.091.94-.266.29-.174.537-.427.719-.734h.681a2.1 2.1 0 0 0 .719.734c.289.175.612.266.94.266.329 0 .652-.091.942-.266.29-.174.536-.427.718-.734h.681c.183.307.43.56.719.734.29.174.613.266.941.266a1.819 1.819 0 0 0 1.06-.351M6 12a1.766 1.766 0 0 1-1.163-.476M5 12v7a1 1 0 0 0 1 1h2v-5h3v5h7a1 1 0 0 0 1-1v-7m-5 3v2h2v-2h-2Z" />
                        </svg>
                    </div>
                    <input type="text" id="simple-search" name="query"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search restaurant..." required />
                </div>
                <button type="submit"
                    class="p-2.5 ms-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </button>
            </form>

            <?php if (!empty($restaurants)): ?>
                <div class="bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-white rounded-lg shadow-lg p-4">
                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Daftar Restoran:</h3>
                    <ul>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <li class="mb-4 flex items-center">
                                <!-- Tampilkan gambar dari tabel users -->
                                <img src="<?= $restaurant['image_user'] ?>" alt="<?= $restaurant['name'] ?>"
                                    class="w-16 h-16 rounded-full object-cover me-2">

                                <a href="index.php?page=view_menu&restaurant_id=<?= $restaurant['id'] ?>"
                                    class="text-blue-500 hover:underline font-medium text-lg"><?= $restaurant['name'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p class="text-red-500 mt-4">Tidak ada restoran yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>