<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$errors = [];
$user = [
    'username' => '',
    'email' => '',
    'full_name' => '',
    'role' => 'manager',
    'country' => '',
    'state' => '',
    'city' => '',
    'address' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    // Validate and sanitize input
    $user['username'] = trim($_POST['username']);
    $user['email'] = trim($_POST['email']);
    $user['full_name'] = trim($_POST['full_name']);
    $user['role'] = $_POST['role'];
    $user['country'] = trim($_POST['country']);
    $user['state'] = trim($_POST['state']);
    $user['city'] = trim($_POST['city']);
    $user['address'] = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($user['username'])) {
        $errors['username'] = 'Username is required';
    }
    
    if (empty($user['email']) || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }
    
    if (empty($user['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // Check if username exists
    $usernameCheck = $db->query("SELECT id FROM users WHERE username = '" . $db->escape($user['username']) . "'");
    if ($usernameCheck && $usernameCheck->num_rows > 0) {
        $errors['username'] = 'Username already exists';
    }
    
    // Check if email exists
    $emailCheck = $db->query("SELECT id FROM users WHERE email = '" . $db->escape($user['email']) . "'");
    if ($emailCheck && $emailCheck->num_rows > 0) {
        $errors['email'] = 'Email already exists';
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (
            username, email, password, full_name, role,
            country, state, city, address
        ) VALUES (
            '" . $db->escape($user['username']) . "',
            '" . $db->escape($user['email']) . "',
            '" . $db->escape($hashed_password) . "',
            '" . $db->escape($user['full_name']) . "',
            '" . $db->escape($user['role']) . "',
            '" . $db->escape($user['country']) . "',
            '" . $db->escape($user['state']) . "',
            '" . $db->escape($user['city']) . "',
            '" . $db->escape($user['address']) . "'
        )";
        
        if ($db->query($sql)) {
            header('Location: index.php?success=User created successfully');
            exit();
        } else {
            $errors[] = 'Failed to create user. Please try again.';
        }
    }
}

ob_start();
?>

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-full">
    <h1 class="text-3xl font-bold text-indigo-900 mb-6">Create New User</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block mb-2 font-semibold">Username *</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['username']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['email']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Full Name *</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['full_name']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Role *</label>
            <select name="role" class="w-full px-4 py-2 border rounded-lg" required>
                <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Password *</label>
            <input type="password" name="password" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['password']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Confirm Password *</label>
            <input type="password" name="confirm_password" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['confirm_password']) ? 'border-red-500' : '' ?>" required>
        </div>
        
        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block mb-2 font-semibold">Country</label>
                <input type="text" name="country" value="<?= htmlspecialchars($user['country']) ?>" 
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block mb-2 font-semibold">State/Region</label>
                <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>" 
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block mb-2 font-semibold">City</label>
                <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" 
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Address</label>
            <textarea name="address" class="w-full px-4 py-2 border rounded-lg" rows="2"><?= 
                htmlspecialchars($user['address']) ?></textarea>
        </div>
        
        <div class="col-span-1 md:col-span-2 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                Create User
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Create New User';
include __DIR__ . '/../includes/header.php';
?>