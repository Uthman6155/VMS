<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$db = db();
$search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';
$type = isset($_GET['type']) ? $db->escape($_GET['type']) : '';
$status = isset($_GET['status']) ? $db->escape($_GET['status']) : '';

$sql = "SELECT r.*, u.full_name as created_by_name FROM reports r
        JOIN users u ON r.created_by = u.id";
$conditions = [];

if (!empty($search)) {
    $conditions[] = "(r.name LIKE '%$search%')";
}
if (!empty($type)) {
    $conditions[] = "r.type = '$type'";
}
if (!empty($status)) {
    $conditions[] = "r.status = '$status'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY r.created_at DESC";
$result = $db->query($sql);
$reports = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md w-full max-w-full">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Report Management</h2>
        <a href="generate.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i>Generate Report
        </a>
    </div>

    <form method="get" class="mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search reports..." 
                   value="<?= htmlspecialchars($search) ?>"
                   class="px-3 py-2 border rounded-lg w-64">
            <select name="type" class="px-3 py-2 border rounded-lg">
                <option value="">All Types</option>
                <option value="monthly" <?= $type === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="quarterly" <?= $type === 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
                <option value="annual" <?= $type === 'annual' ? 'selected' : '' ?>>Annual</option>
                <option value="custom" <?= $type === 'custom' ? 'selected' : '' ?>>Custom</option>
            </select>
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">All Statuses</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Failed</option>
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
                    <th class="p-3 text-left">Report Name</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Date Range</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Created By</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr class="border-b">
                    <td class="p-3"><?= htmlspecialchars($report['name']) ?></td>
                    <td class="p-3"><?= ucfirst($report['type']) ?></td>
                    <td class="p-3">
                        <?= date('M j, Y', strtotime($report['start_date'])) ?> - 
                        <?= date('M j, Y', strtotime($report['end_date'])) ?>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full 
                            <?= $report['status'] === 'completed' ? 'bg-green-100 text-green-600' : '' ?>
                            <?= $report['status'] === 'processing' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                            <?= $report['status'] === 'pending' ? 'bg-blue-100 text-blue-600' : '' ?>
                            <?= $report['status'] === 'failed' ? 'bg-red-100 text-red-600' : '' ?>">
                            <?= ucfirst(str_replace('_', ' ', $report['status'])) ?>
                        </span>
                    </td>
                    <td class="p-3"><?= htmlspecialchars($report['created_by_name']) ?></td>
                    <td class="p-3 flex">
                        <?php if ($report['status'] === 'completed' && !empty($report['file_path'])): ?>
                        <a href="<?= BASE_URL ?>/uploads/<?= $report['file_path'] ?>" 
                           class="text-blue-500 mr-3" title="Download" download>
                            <i class="fas fa-download"></i>
                        </a>
                        <?php endif; ?>
                        <a href="view.php?id=<?= $report['id'] ?>" class="text-blue-500 mr-3" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="post" action="delete.php" class="inline">
                            <input type="hidden" name="id" value="<?= $report['id'] ?>">
                            <button type="submit" class="text-red-500" title="Delete" 
                                    onclick="return confirm('Are you sure you want to delete this report?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Report Management';
include __DIR__ . '/../includes/header.php';
?>