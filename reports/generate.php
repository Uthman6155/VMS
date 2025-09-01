<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../functions/database.php';

$errors = [];
$report = [
    'name' => '',
    'type' => 'monthly',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'parameters' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    // Validate and sanitize input
    $report['name'] = trim($_POST['name']);
    $report['type'] = $_POST['type'];
    $report['start_date'] = $_POST['start_date'];
    $report['end_date'] = $_POST['end_date'];
    $report['parameters'] = json_encode($_POST['parameters'] ?? []);

    // Validation
    if (empty($report['name'])) {
        $errors['name'] = 'Report name is required';
    }
    
    if (empty($report['start_date']) || empty($report['end_date'])) {
        $errors['date'] = 'Both start and end dates are required';
    } elseif (strtotime($report['start_date']) > strtotime($report['end_date'])) {
        $errors['date'] = 'Start date must be before end date';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO reports (
            name, type, start_date, end_date, 
            status, created_by, parameters
        ) VALUES (
            '" . $db->escape($report['name']) . "',
            '" . $db->escape($report['type']) . "',
            '" . $db->escape($report['start_date']) . "',
            '" . $db->escape($report['end_date']) . "',
            'pending',
            " . $_SESSION['user_id'] . ",
            '" . $db->escape($report['parameters']) . "'
        )";
        
        if ($db->query($sql)) {
            // Get the last inserted ID (fixed the Database class usage)
            $reportId = $db->get_insert_id();
            
            if ($reportId) {
                // In a real application, you would queue a background job here
                // For this example, we'll simulate it by updating the status directly
                sleep(2); // Simulate processing time
                
                // Fixed SQL syntax by ensuring reportId is properly defined
                $updateSql = "UPDATE reports SET status = 'completed' WHERE id = " . intval($reportId);
                $db->query($updateSql);
                
                header('Location: view.php?id=' . $reportId . '&success=Report generated successfully');
                exit();
            } else {
                $errors[] = 'Failed to retrieve report ID. Please try again.';
            }
        } else {
            $errors[] = 'Failed to generate report. Please try again.';
        }
    }
}

ob_start();
?>

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-full">
    <h1 class="text-3xl font-bold text-indigo-900 mb-6">Generate New Report</h1>

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
        <div class="md:col-span-2">
            <label class="block mb-2 font-semibold">Report Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($report['name']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['name']) ? 'border-red-500' : '' ?>" required>
        </div>
        
        <div>
            <label class="block mb-2 font-semibold">Report Type *</label>
            <select name="type" class="w-full px-4 py-2 border rounded-lg" required>
                <option value="monthly" <?= $report['type'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="quarterly" <?= $report['type'] === 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
                <option value="annual" <?= $report['type'] === 'annual' ? 'selected' : '' ?>>Annual</option>
                <option value="custom" <?= $report['type'] === 'custom' ? 'selected' : '' ?>>Custom</option>
            </select>
        </div>
        
        <div>
            <label class="block mb-2 font-semibold">Start Date *</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($report['start_date']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['date']) ? 'border-red-500' : '' ?>" required>
        </div>
        
        <div>
            <label class="block mb-2 font-semibold">End Date *</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($report['end_date']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['date']) ? 'border-red-500' : '' ?>" required>
        </div>
        
        <div class="md:col-span-2">
            <label class="block mb-2 font-semibold">Report Parameters</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="parameters[]" value="vendors" id="param_vendors" class="mr-2">
                    <label for="param_vendors">Vendors</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="parameters[]" value="jobs" id="param_jobs" class="mr-2">
                    <label for="param_jobs">Jobs</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="parameters[]" value="financial" id="param_financial" class="mr-2">
                    <label for="param_financial">Financial</label>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                Generate Report
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Generate New Report';
include __DIR__ . '/../includes/header.php';
?>