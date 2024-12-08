<?php
if ($_SESSION['user_level'] !== 'resto') {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload
    $image = $_FILES['image'];
    $target_dir = "upload/"; // Directory to save uploaded images
    $target_file = $target_dir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the image file is a actual image or fake image
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        $error_message = 'File is not an image.';
        $uploadOk = 0;
    }

    // Check file size (e.g., limit to 2MB)
    if ($image["size"] > 2000000) {
        $error_message = 'Sorry, your file is too large.';
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $error_message = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare('INSERT INTO menu_items (name, stock, price, image_url, description, restaurant_id) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $stock, $price, $target_file, $description, $_SESSION['restaurant_id']]);

            echo "<script>alert('Menu berhasil ditambah!'); window.location.href = 'index.php?page=manage_stock';</script>";
            exit;
        } else {
            $error_message = 'Sorry, there was an error uploading your file.';
        }
    }
}
?>

<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto max-w-screen-xl px-4 py-6 flex justify-center items-center min-h-screen">
        <div class="relative w-full max-w-2xl h-full md:h-auto">
            <div class="relative p-4 bg-gray-50 rounded-lg shadow dark:bg-gray-800 sm:p-5">

                <!-- Display error message if exists -->
                <?php if ($error_message): ?>
                    <div class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div
                    class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Add New Product
                    </h3>
                    <a href="index.php?page=manage_stock">
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </a>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                            <input type="text" name="name" id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Menu name..." required>
                        </div>
                        <div>
                            <label for="stock"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock</label>
                            <input type="number" name="stock" id="stock"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Add stock..." required>
                        </div>
                        <div>
                            <label for="price"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price</label>
                            <input type="number" name="price" id="price"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Price amount..." required>
                        </div>
                        <div>
                            <label for="image"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        </div>
                    </div>
                    <div class="sm:col-span-2 mb-4">
                        <label for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea id="description" rows="5" name="description"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Write a description..."></textarea>
                    </div>
                    <!-- Image preview -->
                    <div id="imagePreview" class="mb-4 hidden">
                        <label for="imagepreview"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Image Selected</label>
                        <img id="preview" class="hidden max-w-xs h-auto rounded-lg" />
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add new product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Hide the image preview on small screens (mobile) */
        @media (max-width: 640px) {
            #imagePreview {
                display: none;
                /* Hide the preview on mobile devices */
            }
        }

        /* Show the image preview on larger screens */
        @media (min-width: 641px) {
            #imagePreview {
                display: block;
                /* Show the preview on larger devices */
            }
        }
    </style>

    <script>
        // Preview the image when selected
        document.getElementById('image').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden'); // Show the preview
                    imagePreview.classList.remove('hidden'); // Show the imagePreview div
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden'); // Hide the preview if no file is selected
                imagePreview.classList.add('hidden'); // Hide the imagePreview div
            }
        });
    </script>

</body>