<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

// Function to require admin privileges
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}

// Check if user has permission to access admin pages
$currentPage = basename($_SERVER['PHP_SELF']);
$adminPages = ['create.php', 'edit.php', 'delete.php', 'generate.php'];

if (in_array($currentPage, $adminPages) && !isAdmin()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$db = db();
$search = isset($_GET['search']) ? $db->escape($_GET['search']) : '';
$status = isset($_GET['status']) ? $db->escape($_GET['status']) : '';

$sql = "SELECT j.*, v.company_name FROM jobs j 
        JOIN vendors v ON j.vendor_id = v.id";

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(j.title LIKE '%$search%' OR v.company_name LIKE '%$search%')";
}
if (!empty($status)) {
    $conditions[] = "j.status = '$status'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY j.created_at DESC";

$result = $db->query($sql);
$jobs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md m-6 overflow-x-auto">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Job Assignments</h2>
        <a href="create.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i>Add New Job
        </a>
    </div>

    <!-- Search and Filter Form -->
    <form method="get" class="mb-6">
        <div class="flex gap-4">
            <input type="text" name="search" placeholder="Search jobs..." 
                   value="<?= htmlspecialchars($search) ?>"
                   class="px-3 py-2 border rounded-lg w-64">
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">All Statuses</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>

    <table class="w-full">
        <thead>
            <tr class="bg-gray-100 text-gray-600">
                <th class="p-3 text-left">Title</th>
                <th class="p-3 text-left">Vendor</th>
                <th class="p-3 text-left">Due Date</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
            <tr class="border-b">
                <td class="p-3"><?= htmlspecialchars($job['title']) ?></td>
                <td class="p-3"><?= htmlspecialchars($job['company_name']) ?></td>
                <td class="p-3"><?= date('M d, Y', strtotime($job['due_date'])) ?></td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full 
                        <?= $job['status'] === 'completed' ? 'bg-green-100 text-green-600' : '' ?>
                        <?= $job['status'] === 'pending' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                        <?= $job['status'] === 'in_progress' ? 'bg-blue-100 text-blue-600' : '' ?>
                        <?= $job['status'] === 'cancelled' ? 'bg-red-100 text-red-600' : '' ?>">
                        <?= ucfirst(str_replace('_', ' ', $job['status'])) ?>
                    </span>
                </td>
                <td class="p-3 flex">
                    <a href="view.php?id=<?= $job['id'] ?>" class="text-blue-500 mr-3" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="edit.php?id=<?= $job['id'] ?>" class="text-yellow-500 mr-3" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="post" action="delete.php" class="inline">
                        <input type="hidden" name="id" value="<?= $job['id'] ?>">
                        <button type="submit" class="text-red-500" title="Delete" 
                                onclick="return confirm('Are you sure you want to delete this job?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = "Job Management";

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer here as it's already included in header.php
?>