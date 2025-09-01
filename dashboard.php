<?php
require_once __DIR__ . '/functions/auth.php';
requireAuth();
require_once __DIR__ . '/functions/database.php';

$db = db();

// Get stats with safer queries
$totalVendors = $db->query("SELECT COUNT(*) as count FROM vendors")->fetch_assoc()['count'];
$activeJobs = $db->query("SELECT COUNT(*) as count FROM jobs WHERE status IN ('pending', 'in_progress')")->fetch_assoc()['count'];
$pendingApprovals = $db->query("SELECT COUNT(*) as count FROM vendors WHERE status = 'pending'")->fetch_assoc()['count'];

// Recent activity
$recentActivity = [];
$vendorsActivity = $db->query("SELECT company_name, status, created_at FROM vendors ORDER BY created_at DESC LIMIT 5");
while ($row = $vendorsActivity->fetch_assoc()) {
    $recentActivity[] = [
        'activity' => 'New Vendor Registration',
        'name' => $row['company_name'],
        'date' => date('M d, Y', strtotime($row['created_at'])),
        'status' => $row['status']
    ];
}

$jobsActivity = $db->query("SELECT j.title, j.status, j.created_at, v.company_name 
                           FROM jobs j JOIN vendors v ON j.vendor_id = v.id 
                           ORDER BY j.created_at DESC LIMIT 5");
while ($row = $jobsActivity->fetch_assoc()) {
    $recentActivity[] = [
        'activity' => 'Job Assignment',
        'name' => $row['company_name'],
        'date' => date('M d, Y', strtotime($row['created_at'])),
        'status' => $row['status']
    ];
}

// Sort by date
usort($recentActivity, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$recentActivity = array_slice($recentActivity, 0, 5);

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-indigo-900">Dashboard Overview</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Dashboard Cards -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Total Vendors</h3>
                <p class="text-3xl font-bold text-indigo-600">
                    <?= $totalVendors ?>
                </p>
            </div>
            <i class="fas fa-users text-4xl text-indigo-300"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Active Jobs</h3>
                <p class="text-3xl font-bold text-green-600">
                    <?= $activeJobs ?>
                </p>
            </div>
            <i class="fas fa-briefcase text-4xl text-green-300"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Pending Approvals</h3>
                <p class="text-3xl font-bold text-yellow-600">
                    <?= $pendingApprovals ?>
                </p>
            </div>
            <i class="fas fa-hourglass-half text-4xl text-yellow-300"></i>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="mt-6">
    <h2 class="text-2xl font-bold text-indigo-900 mb-4">Recent Activity</h2>
    <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-600">
                    <th class="p-3 text-left">Activity</th>
                    <th class="p-3 text-left">Vendor</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $activity): ?>
                <tr class="border-b">
                    <td class="p-3">
                        <?= htmlspecialchars($activity['activity']) ?>
                    </td>
                    <td class="p-3">
                        <?= htmlspecialchars($activity['name']) ?>
                    </td>
                    <td class="p-3">
                        <?= htmlspecialchars($activity['date']) ?>
                    </td>
                    <td class="p-3">
                        <span
                            class="px-2 py-1 rounded-full 
                            <?= $activity['status'] === 'active' || $activity['status'] === 'completed' ? 'bg-green-100 text-green-600' : '' ?>
                            <?= $activity['status'] === 'pending' || $activity['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                            <?= $activity['status'] === 'inactive' || $activity['status'] === 'cancelled' ? 'bg-red-100 text-red-600' : '' ?>">
                            <?= ucfirst(str_replace('_', ' ', $activity['status'])) ?>
                        </span>
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
$pageTitle = 'Dashboard';

// Include the header which will now use the $content variable
include __DIR__ . '/includes/header.php';

// No need to include footer here as it's already included in header.php
?>