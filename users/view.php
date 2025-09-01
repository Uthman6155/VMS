<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../functions/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$userId = $db->escape($_GET['id']);
$user = $db->query("SELECT * FROM users WHERE id = '$userId'")->fetch_assoc();

if (!$user) {
    header('Location: index.php?error=User not found');
    exit();
}

ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">User Details</h2>
        <div>
            <a href="edit.php?id=<?= $user['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mr-2">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-2">Account Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Username</label>
                    <p class="font-medium"><?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Email</label>
                    <p class="font-medium"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Full Name</label>
                    <p class="font-medium"><?= htmlspecialchars($user['full_name']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Role</label>
                    <span class="px-2 py-1 rounded-full 
                        <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Location Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Country</label>
                    <p class="font-medium"><?= htmlspecialchars($user['country']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">State/Region</label>
                    <p class="font-medium"><?= htmlspecialchars($user['state']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">City</label>
                    <p class="font-medium"><?= htmlspecialchars($user['city']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Address</label>
                    <p class="font-medium"><?= htmlspecialchars($user['address']) ?></p>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Account Activity</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <label class="block text-gray-600">Created At</label>
                    <p class="font-medium"><?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <label class="block text-gray-600">Last Updated</label>
                    <p class="font-medium"><?= date('M j, Y g:i A', strtotime($user['updated_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "View User: " . $user['username'];
include __DIR__ . '/../includes/header.php';
?>