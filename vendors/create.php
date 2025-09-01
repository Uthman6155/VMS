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
$errors = [];
$vendor = [
    'company_name' => '',
    'contact_person' => '',
    'email' => '',
    'phone' => '',
    'category' => '',
    'tax_id' => '',
    'account_name' => '',
    'account_number' => '',
    'bank_name' => '',
    'country' => '',
    'state' => '',
    'city' => '',
    'address' => '',
    'status' => 'pending'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    
    // Validate and sanitize input
    $vendor['company_name'] = trim($_POST['company_name']);
    $vendor['contact_person'] = trim($_POST['contact_person']);
    $vendor['email'] = trim($_POST['email']);
    $vendor['phone'] = trim($_POST['phone']);
    $vendor['category'] = trim($_POST['category']);
    $vendor['tax_id'] = trim($_POST['tax_id']);
    $vendor['account_name'] = trim($_POST['account_name']);
    $vendor['account_number'] = trim($_POST['account_number']);
    $vendor['bank_name'] = trim($_POST['bank_name']);
    $vendor['country'] = trim($_POST['country']);
    $vendor['state'] = trim($_POST['state']);
    $vendor['city'] = trim($_POST['city']);
    $vendor['address'] = trim($_POST['address']);
    $vendor['status'] = $_POST['status'];
    
    // Validation
    if (empty($vendor['company_name'])) {
        $errors['company_name'] = 'Company name is required';
    }
    
    if (empty($vendor['email']) || !filter_var($vendor['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }
    
    // Check if email exists
    $emailCheck = $db->query("SELECT id FROM vendors WHERE email = '" . $db->escape($vendor['email']) . "'");
    if ($emailCheck && $emailCheck->num_rows > 0) {
        $errors['email'] = 'Email already exists';
    }
    
    // File upload
    $certifications_path = '';
    if (isset($_FILES['certifications']) && $_FILES['certifications']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['certifications']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = UPLOAD_PATH . 'vendor_certifications/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['certifications']['tmp_name'], $upload_dir . $filename)) {
                $certifications_path = 'vendor_certifications/' . $filename;
            } else {
                $errors['certifications'] = 'Failed to upload file';
            }
        } else {
            $errors['certifications'] = 'Invalid file type. Only PDF, JPG, PNG allowed';
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO vendors (
            company_name, contact_person, email, phone, category, 
            tax_id, account_name, account_number, bank_name, country, state, city, address,
            certifications_path, status, created_by
        ) VALUES (
            '" . $db->escape($vendor['company_name']) . "',
            '" . $db->escape($vendor['contact_person']) . "',
            '" . $db->escape($vendor['email']) . "',
            '" . $db->escape($vendor['phone']) . "',
            '" . $db->escape($vendor['category']) . "',
            '" . $db->escape($vendor['tax_id']) . "',
            '" . $db->escape($vendor['account_name']) . "',
            '" . $db->escape($vendor['account_number']) . "',
            '" . $db->escape($vendor['bank_name']) . "',
            '" . $db->escape($vendor['country']) . "',
            '" . $db->escape($vendor['state']) . "',
            '" . $db->escape($vendor['city']) . "',
            '" . $db->escape($vendor['address']) . "',
            '" . $db->escape($certifications_path) . "',
            '" . $db->escape($vendor['status']) . "',
            " . $_SESSION['user_id'] . "
        )";
        
        if ($db->query($sql)) {
            header('Location: index.php?success=Vendor created successfully');
            exit();
        } else {
            $errors[] = 'Failed to create vendor. Please try again.';
        }
    }
}

// Store the content in a variable instead of directly outputting it
ob_start();
?>

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-full">
    <h1 class="text-3xl font-bold text-indigo-900 mb-6">New Vendor Registration</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block mb-2 font-semibold">Company Name *</label>
            <input type="text" name="company_name" value="<?= htmlspecialchars($vendor['company_name']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['company_name']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Contact Person *</label>
            <input type="text" name="contact_person" value="<?= htmlspecialchars($vendor['contact_person']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($vendor['email']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg <?= isset($errors['email']) ? 'border-red-500' : '' ?>" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Phone *</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($vendor['phone']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Business Category *</label>
            <input type="text" name="category" value="<?= htmlspecialchars($vendor['category']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
            <small class="text-gray-500">Enter the vendor's business category or specialization</small>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Tax ID / Registration Number *</label>
            <input type="text" name="tax_id" value="<?= htmlspecialchars($vendor['tax_id']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block mb-2 font-semibold">Country *</label>
                <input type="text" name="country" value="<?= htmlspecialchars($vendor['country']) ?>" 
                    class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">State/Region *</label>
                <input type="text" name="state" value="<?= htmlspecialchars($vendor['state']) ?>" 
                    class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold">City *</label>
                <input type="text" name="city" value="<?= htmlspecialchars($vendor['city']) ?>" 
                    class="w-full px-4 py-2 border rounded-lg" required>
            </div>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Address *</label>
            <textarea name="address" class="w-full px-4 py-2 border rounded-lg" rows="2" required><?= 
                htmlspecialchars($vendor['address']) ?></textarea>
        </div>
        
        <!-- Bank details split into separate fields -->
        <div class="col-span-1 md:col-span-2">
            <h3 class="text-lg font-semibold mb-3">Bank Details</h3>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Account Name *</label>
            <input type="text" name="account_name" value="<?= htmlspecialchars($vendor['account_name']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div>
            <label class="block mb-2 font-semibold">Account Number *</label>
            <input type="text" name="account_number" value="<?= htmlspecialchars($vendor['account_number']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Bank Name *</label>
            <input type="text" name="bank_name" value="<?= htmlspecialchars($vendor['bank_name']) ?>" 
                   class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Upload Certifications</label>
            <input type="file" name="certifications" class="w-full px-4 py-2 border rounded-lg">
            <?php if (isset($errors['certifications'])): ?>
            <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['certifications']) ?></p>
            <?php endif; ?>
        </div>
        <div class="col-span-1 md:col-span-2">
            <label class="block mb-2 font-semibold">Status</label>
            <select name="status" class="w-full px-4 py-2 border rounded-lg">
                <option value="pending" <?= $vendor['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="active" <?= $vendor['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $vendor['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-span-1 md:col-span-2 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                Register Vendor
            </button>
        </div>
    </form>
</div>

<?php
// Get the content from buffer and store it in the $content variable
$content = ob_get_clean();

// Set page title
$pageTitle = 'New Vendor Registration';

// Include the header which will now use the $content variable
include __DIR__ . '/../includes/header.php';

// No need to include footer here as it's already included in header.php
?>