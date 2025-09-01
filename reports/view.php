<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../functions/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$reportId = $db->escape($_GET['id']);
$report = $db->query("SELECT r.*, u.full_name as created_by_name 
                     FROM reports r JOIN users u ON r.created_by = u.id 
                     WHERE r.id = '$reportId'")->fetch_assoc();

if (!$report) {
    header('Location: index.php?error=Report not found');
    exit();
}

// Decode parameters
$parameters = json_decode($report['parameters'], true) ?: [];

ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Report Details</h2>
        <div>
            <?php if ($report['status'] === 'completed' && !empty($report['file_path'])): ?>
            <a href="<?= BASE_URL ?>/uploads/<?= $report['file_path'] ?>" 
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mr-2" download>
                <i class="fas fa-download mr-2"></i>Download
            </a>
            <?php endif; ?>
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-2">Report Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Report Name</label>
                    <p class="font-medium"><?= htmlspecialchars($report['name']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Report Type</label>
                    <p class="font-medium"><?= ucfirst($report['type']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Date Range</label>
                    <p class="font-medium">
                        <?= date('M j, Y', strtotime($report['start_date'])) ?> - 
                        <?= date('M j, Y', strtotime($report['end_date'])) ?>
                    </p>
                </div>
                <div>
                    <label class="block text-gray-600">Status</label>
                    <span class="px-2 py-1 rounded-full 
                        <?= $report['status'] === 'completed' ? 'bg-green-100 text-green-600' : '' ?>
                        <?= $report['status'] === 'processing' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                        <?= $report['status'] === 'pending' ? 'bg-blue-100 text-blue-600' : '' ?>
                        <?= $report['status'] === 'failed' ? 'bg-red-100 text-red-600' : '' ?>">
                        <?= ucfirst(str_replace('_', ' ', $report['status'])) ?>
                    </span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Creation Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Created By</label>
                    <p class="font-medium"><?= htmlspecialchars($report['created_by_name']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Created At</label>
                    <p class="font-medium"><?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Last Updated</label>
                    <p class="font-medium"><?= date('M j, Y g:i A', strtotime($report['updated_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Report Parameters</h3>
            <div class="bg-gray-50 p-4 rounded">
                <?php if (!empty($parameters)): ?>
                    <ul class="list-disc pl-5">
                        <?php foreach ($parameters as $param): ?>
                            <li><?= ucfirst(htmlspecialchars($param)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">No specific parameters were selected for this report.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($report['status'] === 'completed' && !empty($report['file_path'])): ?>
        <div class="md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Generated Report</h3>
            <div class="bg-gray-50 p-4 rounded">
                <p class="mb-2">Report file is ready for download:</p>
                <a href="<?= BASE_URL ?>/uploads/<?= $report['file_path'] ?>" 
                   class="text-blue-500 hover:underline" download>
                    <i class="fas fa-file-pdf mr-2"></i><?= basename($report['file_path']) ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = "View Report: " . $report['name'];
include __DIR__ . '/../includes/header.php';
?>