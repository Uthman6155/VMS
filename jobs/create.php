<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$db = db();

// Fetch all vendors for the dropdown
$vendorsResult = $db->query("SELECT id, company_name FROM vendors ORDER BY company_name");
$vendors = [];
if ($vendorsResult) {
    while ($row = $vendorsResult->fetch_assoc()) {
        $vendors[] = $row;
    }
}

// Fetch all users for the dropdown
$usersResult = $db->query("SELECT id, full_name FROM users ORDER BY full_name");
$users = [];
if ($usersResult) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a unique job ID (e.g., JOB-2025-00001)
    $year = date('Y');
    $jobIdPrefix = "JOB-{$year}-";
    
    // Get the latest job ID
    $latestJobIdResult = $db->query("SELECT job_id FROM jobs WHERE job_id LIKE '{$jobIdPrefix}%' ORDER BY id DESC LIMIT 1");
    
    if ($latestJobIdResult && $latestJobIdResult->num_rows > 0) {
        $latestJobId = $latestJobIdResult->fetch_assoc()['job_id'];
        $lastNumber = intval(substr($latestJobId, -5));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    $job_id = $jobIdPrefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    
    // Escape form inputs
    $vendor_id = $db->escape($_POST['vendor_id']);
    $assigned_to = $db->escape($_POST['assigned_to']);
    $title = $db->escape($_POST['title']);
    $description = $db->escape($_POST['description']);
    $start_date = $db->escape($_POST['start_date']);
    $end_date = $db->escape($_POST['end_date']);
    $status = $db->escape($_POST['status']);
    
    // Insert new job
    $sql = "INSERT INTO jobs (job_id, vendor_id, assigned_to, title, description, start_date, due_date, status) 
            VALUES ('$job_id', '$vendor_id', '$assigned_to', '$title', '$description', '$start_date', '$end_date', '$status')";
    
    if ($db->query($sql)) {
        // Set success message and redirect
        $_SESSION['success_message'] = "Job created successfully!";
        header("Location: index.php");
        exit;
    } else {
        $error = "Error creating job: " . $db->error;
    }
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md m-6">
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-semibold">Create New Job</h2>
        <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back to Jobs
        </a>
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
                <input type="text" id="title" name="title" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <select id="vendor_id" name="vendor_id" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Vendor</option>
                    <?php foreach ($vendors as $vendor): ?>
                    <option value="<?= $vendor['id'] ?>"><?= htmlspecialchars($vendor['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Overseen By</label>
                <select id="assigned_to" name="assigned_to" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" id="end_date" name="end_date" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="description" name="description" rows="6" required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>
        
        <div class="flex justify-end">
            <button type="reset" class="bg-gray-500 text-white px-6 py-2 rounded-md mr-4 hover:bg-gray-600">
                Reset
            </button>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Create Job
            </button>
        </div>
    </form>
</div>

<?php
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = "Create New Job";

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer, navbar, or sidebar here as they're included in header.php
?>