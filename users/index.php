<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$db = db();
$search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';
$role = isset($_GET['role']) ? $db->escape($_GET['role']) : '';

$sql = "SELECT * FROM users WHERE 1=1";
$conditions = [];

if (!empty($search)) {
    $conditions[] = "(username LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($role)) {
    $conditions[] = "role = '$role'";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY created_at DESC";
$result = $db->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md w-full max-w-full">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">User Management</h2>
        <a href="create.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i>Add New User
        </a>
    </div>

    <form method="get" class="mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search users..." 
                   value="<?= htmlspecialchars($search) ?>"
                   class="px-3 py-2 border rounded-lg w-64">
            <select name="role" class="px-3 py-2 border rounded-lg">
                <option value="">All Roles</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Manager</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-600">
                    <th class="p-3 text-left">Username</th>
                    <th class="p-3 text-left">Full Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-b">
                    <td class="p-3"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($user['full_name']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full 
                            <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td class="p-3 flex">
                        <a href="view.php?id=<?= $user['id'] ?>" class="text-blue-500 mr-3" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="edit.php?id=<?= $user['id'] ?>" class="text-yellow-500 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <form method="post" action="delete.php" class="inline">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="text-red-500" title="Delete" 
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'User Management';
include __DIR__ . '/../includes/header.php';
?>