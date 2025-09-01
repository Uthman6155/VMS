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

// Fetch all vendors for the dropdown
$vendorsResult = $db->query("SELECT id, company_name FROM vendors ORDER BY company_name");
$vendors = [];
if ($vendorsResult) {
    while ($row = $vendorsResult->fetch_assoc()) {
        $vendors[] = $row;
    }
}

// Fetch all users for the dropdown
$usersResult = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) as full_name FROM users ORDER BY first_name");
$users = [];
if ($usersResult) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch current job data
$jobResult = $db->query("SELECT * FROM jobs WHERE id = '$job_id'");
if (!$jobResult || $jobResult->num_rows === 0) {
    $_SESSION['error_message'] = "Job not found";
    header("Location: index.php");
    exit;
}

$job = $jobResult->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escape form inputs
    $vendor_id = $db->escape($_POST['vendor_id']);
    $assigned_to = $db->escape($_POST['assigned_to']);
    $title = $db->escape($_POST['title']);
    $description = $db->escape($_POST['description']);
    $start_date = $db->escape($_POST['start_date']);
    $end_date = $db->escape($_POST['end_date']);
    $status = $db->escape($_POST['status']);
    
    // Update job
    $sql = "UPDATE jobs SET 
            vendor_id = '$vendor_id',
            assigned_to = '$assigned_to',
            title = '$title',
            description = '$description',
            start_date = '$start_date',
            due_date = '$end_date',
            status = '$status'
            WHERE id = '$job_id'";
    
    if ($db->query($sql)) {
        // Set success message and redirect
        $_SESSION['success_message'] = "Job updated successfully!";
        header("Location: index.php");
        exit;
    } else {
        $error = "Error updating job: " . $db->error;
    }
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md m-6">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Edit Job: <?= htmlspecialchars($job['job_id']) ?></h2>
        <div class="flex space-x-2">
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Back to Jobs
            </a>
            <a href="view.php?id=<?= $job_id ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p><?= $error ?></p>
    </div>
    <?php endif; ?>

    <form method="post" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                <input type="text" id="title" name="title" required value="<?= htmlspecialchars($job['title']) ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <select id="vendor_id" name="vendor_id" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Vendor</option>
                    <?php foreach ($vendors as $vendor): ?>
                    <option value="<?= $vendor['id'] ?>" <?= $vendor['id'] == $job['vendor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vendor['company_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                <select id="assigned_to" name="assigned_to" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $job['assigned_to'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['full_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending" <?= $job['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $job['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="completed" <?= $job['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $job['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" required value="<?= date('Y-m-d', strtotime($job['start_date'])) ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" id="end_date" name="end_date" required value="<?= date('Y-m-d', strtotime($job['due_date'])) ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="6" required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($job['description']) ?></textarea>
        </div>
        
        <div class="flex justify-end">
            <a href="index.php" class="bg-gray-500 text-white px-6 py-2 rounded-md mr-4 hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Update Job
            </button>
        </div>
    </form>
</div>

<?php
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = "Edit Job";

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer, navbar, or sidebar here as they're included in header.php
?>