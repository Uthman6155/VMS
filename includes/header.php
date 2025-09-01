<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - Vendor Management System' : 'Vendor Management System' ?></title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        z-index: 50;
        transition: transform 0.3s ease;
    }
    
    .content-area {
        margin-left: 260px;
        width: calc(100% - 260px);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.active {
            transform: translateX(0);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        .content-area {
            margin-left: 0;
            width: 100%;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
        }
        .sidebar.active + .sidebar-overlay {
            display: block;
        }
    }
    
    /* Better scroll behavior */
    main {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }
    
    /* Remove excessive whitespace */
    .container {
        max-width: 100%;
        padding: 1rem;
    }
    </style>
   
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="sidebar fixed inset-y-0 left-0 z-30 w-64 bg-indigo-800 text-white">
            <?php include 'sidebar.php'; ?>
        </aside>
        
        <!-- Main Content -->
        <div class="content-area flex flex-col flex-1 ml-64 h-screen overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white shadow-sm">
                <?php include 'navbar.php'; ?>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4">
                <?php if (isset($content)) echo $content; ?>
            </main>
            
            <!-- Footer -->
            <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.content-area').classList.toggle('ml-0');
            document.querySelector('.content-area').classList.toggle('ml-64');
        });
    </script>
</body>
</html>