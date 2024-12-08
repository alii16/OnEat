<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil informasi user dari database berdasarkan sesi
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Jika form disubmit untuk update informasi user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Handle file upload untuk gambar user
    $image_user = $user['image_user']; // Default ke gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "upload/users/"; // Direktori upload
        $image_file_type = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid() . '.' . $image_file_type;

        // Validasi tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_user = $target_file; // Simpan path ke gambar baru
            } else {
                echo "<script>alert('Error uploading your file.');</script>";
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        }
    }

    // Update database dengan data baru
    $stmt = $pdo->prepare('UPDATE users SET email = ?, image_user = ? WHERE id = ?');
    $stmt->execute([$email, $image_user, $user_id]);

    // Password Update Logic
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        // Cek apakah password saat ini cocok
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $db_password = $stmt->fetchColumn();

        // Verifikasi password saat ini
        if (password_verify($current_password, $db_password)) {
            if ($new_password === $confirm_password) {
                // Update password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                $stmt->execute([$hashed_new_password, $user_id]);
                echo "<script>alert('Password berhasil diubah!');</script>";
            } else {
                echo "<script>alert('Password baru dan konfirmasi tidak cocok.');</script>";
            }
        } else {
            echo "<script>alert('Password saat ini tidak cocok.');</script>";
        }
    }

    echo "<script>alert('Profile berhasil diupdate!'); window.location.href = 'index.php?page=profile';</script>";
    exit;
}
?>

<!-- HTML untuk Tampilan Profil dan Form Edit -->
<div class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto max-w-screen-lg px-4 py-6">
        <div class="text-center">
            <h1 class="text-gray-900 dark:text-white text-3xl font-bold mt-8 mb-6">Profile User</h1>
        </div>

        <div class="max-w-2xl mx-auto bg-gray-50 dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 text-center">
                <!-- Menampilkan Foto Profil -->
                <div class="mb-2">
                    <img src="<?php echo htmlspecialchars($user['image_user'] ?? 'upload/users/tes.png'); ?>"
                        alt="Profile Picture" class="border-4 border-green-400 h-36 w-36 rounded-full mx-auto">

                </div>

                <!-- Menampilkan Username (Read-only) -->
                <div class="mb-4">
                    <h2 class="text-gray-900 dark:text-white text-xl font-bold"><?php echo htmlspecialchars($user['username']); ?></h2>
                </div>

                <!-- Menampilkan Informasi Profil -->
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <!-- Input untuk mengubah gambar profil -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto
                                Profil</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                    </div>

                    <!-- Kolom untuk Ganti Password -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="current_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                Password</label>
                            <input type="password" id="current_password" name="current_password" placeholder="●●●●●●●●"
                                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                        <div>
                            <label for="new_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                        <div>
                            <label for="confirm_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm
                                Password</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                class="mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center justify-center w-auto px-4 py-2 border bg-indigo-600 text-white font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save change
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>