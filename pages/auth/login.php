<?php

// Adjust this path according to your actual directory structure
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../config/config.php';

// Rest of the code remains the same
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } elseif (login($username, $password)) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <img src="<?= BASE_URL ?>/assets/images/company-logo.png" alt="GalaxyItt Logo" class="mx-auto h-20 mb-4">
            <h2 class="text-2xl font-bold text-indigo-900">Vendor Portal Login</h2>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="post" class="space-y-4">
            <div>
                <label for="username" class="block mb-2 font-medium">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="password" class="block mb-2 font-medium">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">Forgot password?</a>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Login
            </button>
        </form>
        <div class="mt-4 text-center">
            <p class="text-gray-600">Don't have an account? <a href="#" class="text-indigo-600 hover:text-indigo-800">Contact administrator</a></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>