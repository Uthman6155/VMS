<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../functions/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$vendorId = $db->escape($_GET['id']);
$vendor = $db->query("SELECT v.*, u.full_name as created_by_name 
                     FROM vendors v JOIN users u ON v.created_by = u.id 
                     WHERE v.id = '$vendorId'")->fetch_assoc();

if (!$vendor) {
    header('Location: index.php?error=Vendor not found');
    exit();
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Vendor Details</h2>
        <div>
            <a href="edit.php?id=<?= $vendor['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mr-2">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Company Name</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['company_name']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Contact Person</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['contact_person']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Email</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['email']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Phone</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['phone']) ?></p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Business Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Category</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['category']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Tax ID</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['tax_id']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Status</label>
                    <span class="px-2 py-1 rounded-full 
                        <?= $vendor['status'] === 'active' ? 'bg-green-100 text-green-600' : '' ?>
                        <?= $vendor['status'] === 'pending' ? 'bg-yellow-100 text-yellow-600' : '' ?>
                        <?= $vendor['status'] === 'inactive' ? 'bg-red-100 text-red-600' : '' ?>">
                        <?= ucfirst($vendor['status']) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-gray-600">Registered By</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['created_by_name']) ?></p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Address Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Address</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['address']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">City</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['city']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">State/Region</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['state']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Country</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['country']) ?></p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Bank Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600">Account Name</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['account_name']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Account Number</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['account_number']) ?></p>
                </div>
                <div>
                    <label class="block text-gray-600">Bank Name</label>
                    <p class="font-medium"><?= htmlspecialchars($vendor['bank_name']) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($vendor['certifications_path'])): ?>
        <div class="md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Certifications</h3>
            <a href="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($vendor['certifications_path']) ?>" 
               target="_blank" class="text-blue-500 hover:underline">
                <i class="fas fa-file-pdf mr-2"></i>View Certification
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = "View Vendor: " . $vendor['company_name'];

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer here as it's already included in header.php
?>