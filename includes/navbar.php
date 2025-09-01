<nav class="px-4 py-2 flex items-center justify-between">
    <div class="flex items-center">
        <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-indigo-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-800 ml-4"><?= $pageTitle ?? 'Vendor Management System' ?></h2>
    </div>
    
    <div class="flex items-center space-x-4">
        
        
        <div class="relative">
            <button class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600" id="user-menu-button">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                    <?= substr($_SESSION['username'] ?? 'U', 0, 1) ?>
                </div>
                <span class="hidden md:inline"><?= $_SESSION['username'] ?? 'User' ?></span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
        </div>
    </div>
</nav>