<div class="h-full flex flex-col">
    <div class="p-4 flex items-center justify-center border-b border-indigo-700">
        <img src="<?= BASE_URL ?>/assets/images/logo.jpg" alt="Company Logo" class="h-10">
        <h1 class="text-xl font-bold ml-2">VMS</h1>
    </div>
    
    <div class="p-4 flex-1 overflow-y-auto">
        <div class="flex items-center mb-4">
            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                <?= substr($_SESSION['username'] ?? 'U', 0, 1) ?>
            </div>
            <div class="ml-3">
                <p class="font-semibold truncate"><?= $_SESSION['username'] ?? 'User' ?></p>
                <p class="text-xs text-indigo-300"><?= ucfirst($_SESSION['role'] ?? 'user') ?></p>
            </div>
        </div>
        
        <hr class="border-indigo-700 my-4">
        
        <ul class="space-y-1">
            <li>
                <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-menu-item flex items-center p-2 rounded hover:bg-indigo-700 transition-colors duration-200 <?= strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="<?= BASE_URL ?>/vendors/" class="sidebar-menu-item flex items-center p-2 rounded hover:bg-indigo-700 transition-colors duration-200 <?= strpos($_SERVER['PHP_SELF'], 'vendors') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="ml-3">Vendors</span>
                </a>
            </li>
            
            <li>    
                <a href="<?= BASE_URL ?>/jobs/" class="sidebar-menu-item flex items-center p-2 rounded hover:bg-indigo-700 transition-colors duration-200 <?= strpos($_SERVER['PHP_SELF'], 'jobs') !== false ? 'active' : '' ?>">
                    <i class="fas fa-briefcase w-5 text-center"></i>
                    <span class="ml-3">Jobs</span>
                </a>
            </li>
            
            <li>
                <a href="<?= BASE_URL ?>/reports/" class="sidebar-menu-item flex items-center p-2 rounded hover:bg-indigo-700 transition-colors duration-200 <?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="ml-3">Reports</span>
                </a>
            </li>   
            <li>
    <a href="<?= BASE_URL ?>/users/" class="sidebar-menu-item flex items-center p-2 rounded hover:bg-indigo-700 transition-colors duration-200 <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>">
        <i class="fas fa-users-cog w-5 text-center"></i>
        <span class="ml-3">User Management</span>
    </a>
</li>


            <!-- Other menu items -->
        </ul>
    </div>
    
    <div class="p-4 border-t border-indigo-700">
        <a href="<?= BASE_URL ?>/logout.php" class="flex items-center p-2 rounded hover:bg-red-600 text-white transition-colors duration-200">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span class="ml-3">Logout</span>
        </a>
    </div>
</div>