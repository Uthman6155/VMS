<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$db = db();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No job ID specified";
    header("Location: index.php");
    exit;
}

$job_id = $db->escape($_GET['id']);

// Fetch job details
$sql = "SELECT j.*, v.company_name, CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name 
        FROM jobs j 
        JOIN vendors v ON j.vendor_id = v.id 
        JOIN users u ON j.assigned_to = u.id 
        WHERE j.id = '$job_id'";
$result = $db->query($sql);

if (!$result || $result->num_rows === 0) {
    $_SESSION['error_message'] = "Job not found";
    header("Location: index.php");
    exit;
}

$job = $result->fetch_assoc();

// Format dates for display
$start_date = date('M d, Y', strtotime($job['start_date']));
$end_date = date('M d, Y', strtotime($job['due_date']));
$created_at = date('M d, Y h:i A', strtotime($job['created_at']));
$updated_at = date('M d, Y h:i A', strtotime($job['updated_at']));

// Get status class for badge
$statusClass = '';
switch ($job['status']) {
    case 'completed':
        $statusClass = 'bg-green-100 text-green-600';
        break;
    case 'pending':
        $statusClass = 'bg-yellow-100 text-yellow-600';
        break;
    case 'in_progress':
        $statusClass = 'bg-blue-100 text-blue-600';
        break;
    case 'cancelled':
        $statusClass = 'bg-red-100 text-red-600';
        break;
}

$pageTitle = "View Job Details";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md m-6">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Job Details: <?= htmlspecialchars($job['job_id']) ?></h2>
        <div class="flex space-x-2">
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Back to Jobs
            </a>
            <a href="edit.php?id=<?= $job['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                <i class="fas fa-edit mr-2"></i>Edit Job
            </a>
        </div>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
            <div class="px-3 py-1 rounded-full <?= $statusClass ?>">
                <?= ucfirst(str_replace('_', ' ', $job['status'])) ?>
            </div>
            <div><i class="far fa-calendar-alt mr-2"></i>Created: <?= $created_at ?></div>
            <div><i class="far fa-clock mr-2"></i>Last Updated: <?= $updated_at ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Job Title</h3>
                <p class="mt-1 text-lg"><?= htmlspecialchars($job['title']) ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Vendor</h3>
                <p class="mt-1 text-lg"><?= htmlspecialchars($job['company_name']) ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Assigned To</h3>
                <p class="mt-1 text-lg"><?= htmlspecialchars($job['assigned_to_name']) ?></p>
            </div>
        </div>
        
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Job ID</h3>
                <p class="mt-1 text-lg"><?= htmlspecialchars($job['job_id']) ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Start Date</h3>
                <p class="mt-1 text-lg"><?= $start_date ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                <p class="mt-1 text-lg"><?= $end_date ?></p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-md font-medium text-gray-700 mb-2">Description</h3>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="whitespace-pre-line"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        </div>
    </div>
    
    <div class="border-t pt-4 mt-6 flex justify-between">
        <form method="post" action="delete.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this job?')">
            <input type="hidden" name="id" value="<?= $job['id'] ?>">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                <i class="fas fa-trash-alt mr-2"></i>Delete Job
            </button>
        </form>
        
        <button type="button" id="print-btn" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600" onclick="window.print()">
            <i class="fas fa-print mr-2"></i>Print Details
        </button>
    </div>
</div>

<script>
    // Add print-specific styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white, .bg-white * {
                visibility: visible;
            }
            .bg-white {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            #print-btn, form {
                display: none !important;
            }
        }
    `;
    document.head.appendChild(style);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>