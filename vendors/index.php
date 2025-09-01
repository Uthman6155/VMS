<?php
require_once __DIR__ . '/../functions/auth.php';
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
$country = isset($_GET['country']) ? $db->escape($_GET['country']) : '';
$city = isset($_GET['city']) ? $db->escape($_GET['city']) : '';
$state = isset($_GET['state']) ? $db->escape($_GET['state']) : '';

$sql = "SELECT v.*, u.full_name as created_by_name FROM vendors v 
        JOIN users u ON v.created_by = u.id";

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(v.company_name LIKE '%$search%' OR v.contact_person LIKE '%$search%')";
}
if (!empty($status)) {
    $conditions[] = "v.status = '$status'";
}
if (!empty($country)) {
    $conditions[] = "v.country = '$country'";
}
if (!empty($city)) {
    $conditions[] = "v.city = '$city'";
}
if (!empty($state)) {
    $conditions[] = "v.state = '$state'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY v.created_at DESC";

$result = $db->query($sql);
$vendors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row;
    }
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md w-full max-w-full">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Registered Vendors</h2>
        <a href="create.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i>Add New Vendor
        </a>
    </div>

    <!-- Search and Filter Form -->
    <form method="get" action="" class="mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search vendors..." 
                value="<?= htmlspecialchars($search) ?>"
                class="px-3 py-2 border rounded-lg w-64">
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">All Statuses</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <select name="country" class="px-3 py-2 border rounded-lg">
                <option value="">All Countries</option>
                <?php 
                $countries = $db->query("SELECT DISTINCT country FROM vendors WHERE country IS NOT NULL ORDER BY country");
                if ($countries):
                    while ($country_row = $countries->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($country_row['country']) ?>" <?= $country === $country_row['country'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country_row['country']) ?>
                        </option>
                    <?php endwhile; 
                endif; ?>
            </select>
            <select name="state" class="px-3 py-2 border rounded-lg">
                <option value="">All States</option>
                <?php 
                $states = $db->query("SELECT DISTINCT state FROM vendors WHERE state IS NOT NULL ORDER BY state");
                if ($states):
                    while ($state_row = $states->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($state_row['state']) ?>" <?= $state === $state_row['state'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($state_row['state']) ?>
                        </option>
                    <?php endwhile;
                endif; ?>
            </select>
            <select name="city" class="px-3 py-2 border rounded-lg">
                <option value="">All Cities</option>
                <?php 
                $cities = $db->query("SELECT DISTINCT city FROM vendors WHERE city IS NOT NULL ORDER BY city");
                if ($cities):
                    while ($city_row = $cities->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($city_row['city']) ?>" <?= $city === $city_row['city'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($city_row['city']) ?>
                        </option>
                    <?php endwhile;
                endif; ?>
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
                    <th class="p-3 text-left">Company Name</th>
                    <th class="p-3 text-left">Contact Person</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Country</th>
                    <th class="p-3 text-left">State</th>
                    <th class="p-3 text-left">City</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                <tr class="border-b">
                    <td class="p-3"><?= htmlspecialchars($vendor['company_name']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($vendor['contact_person']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($vendor['category']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($vendor['country'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($vendor['state'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars($vendor['city'] ?? '') ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full 
                            <?= $vendor['status'] === 'active' ? 'bg-green-100 text-green-600' : '' ?>
                            <?= $vendor['status'] === 'pending' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                            <?= $vendor['status'] === 'inactive' ? 'bg-red-100 text-red-600' : '' ?>">
                            <?= ucfirst($vendor['status']) ?>
                        </span>
                    </td>
                    <td class="p-3 flex">
                        <a href="view.php?id=<?= $vendor['id'] ?>" class="text-blue-500 mr-3" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="edit.php?id=<?= $vendor['id'] ?>" class="text-yellow-500 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="post" action="delete.php" class="inline">
                            <input type="hidden" name="id" value="<?= $vendor['id'] ?>">
                            <button type="submit" class="text-red-500" title="Delete" 
                                    onclick="return confirm('Are you sure you want to delete this vendor?')">
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
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = 'Vendor Management';

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer here as it's already included in header.php
?>