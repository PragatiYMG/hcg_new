<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= $this->renderSection('title', true) ?: 'Dashboard' ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">

    <style>
        /* Top Header Styles */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .top-header .logo {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            margin-left: 15px;
        }

        .top-header .logo-img {
            height: 30px;
            margin-right: 10px;
        }

        .top-header .page-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .top-header .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .top-header .btn-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .top-header .btn-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .top-header .dropdown-menu {
            margin-top: 8px;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            height: calc(100vh - 60px);
            background-color: #343a40;
            color: white;
            overflow-y: auto;
            z-index: 1020;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-250px);
        }

        .sidebar-header {
            padding: 20px;
            background-color: #495057;
            border-bottom: 1px solid #6c757d;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1031;
            background-color: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .menu-toggle:hover {
            background-color: rgba(255,255,255,0.3);
        }

        .sidebar-search {
            padding: 15px;
            border-bottom: 1px solid #495057;
        }

        .sidebar-search input {
            width: 100%;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: #495057;
            color: white;
        }

        .sidebar-search input::placeholder {
            color: #adb5bd;
        }

        /* Hide sidebar search for now */
        .sidebar-search {
            display: none;
        }

        .menu-section {
            border-bottom: 1px solid #495057;
        }

        .menu-section-header {
            padding: 15px 20px;
            background-color: #495057;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }

        .menu-section-header:hover {
            background-color: #6c757d;
        }

        .menu-section-toggle {
            transition: transform 0.3s ease;
        }

        .menu-section.collapsed .menu-section-toggle {
            transform: rotate(-90deg);
        }

        .menu-items {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 500px;
            overflow-y: auto;
            transition: max-height 0.3s ease;
        }

        .menu-section.collapsed .menu-items {
            max-height: 0;
            overflow: hidden;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: #495057;
            border-left-color: #007bff;
        }

        .menu-item i {
            margin-right: 10px;
            width: 16px;
            text-align: center;
        }

        .menu-item.hidden {
            display: none;
        }

        .menu-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            width: 100%;
            padding: 12px 20px;
            margin: -12px -20px;
        }

        .menu-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 60px);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .menu-overlay {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            width: 100%;
            height: calc(100vh - 60px);
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1010;
        }

        .menu-overlay.active {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .menu-toggle {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .top-header .logo {
                font-size: 1rem;
            }

            .top-header .page-title {
                font-size: 1.2rem;
            }
        }

        /* Accessibility */
        .menu-item[tabindex="0"]:focus,
        .menu-section-header[tabindex="0"]:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <?php
                        $logo = isset($site_logo) ? trim($site_logo) : '';
                        $logoUrl = $logo
                            ? (preg_match('/^https?:\/\//i', $logo) ? $logo : base_url('uploads/' . ltrim($logo, '/')))
                            : base_url('uploads/logo_1762758146.png');
                    ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="logo">
                        <img src="<?= esc($logoUrl) ?>" alt="Logo" class="logo-img">
                        Admin Panel
                    </a>
                </div>
                <div class="header-actions">
                    <button class="btn btn-link" title="Notifications">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            $adminModel = new \App\Models\AdminModel();
                            $admin = $adminModel->find(session()->get('admin_id'));
                            $profilePic = isset($admin['profile_picture']) && $admin['profile_picture']
                                ? base_url('uploads/Admin_Profile/' . $admin['profile_picture'])
                                : null;
                            ?>
                            <?php if ($profilePic): ?>
                                <img src="<?= esc($profilePic) ?>" alt="Profile" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-user-circle mr-2"></i>
                            <?php endif; ?>
                            <span class="d-none d-md-inline"><?= session()->get('admin_username') ?></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="<?= base_url('admin/profile') ?>">
                                <i class="fas fa-user-edit"></i> Profile
                            </a>
                            <a class="dropdown-item" href="#changePasswordModal" data-toggle="modal">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                            <a class="dropdown-item" href="<?= base_url('admin/settings') ?>">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url('admin/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Menu Overlay for Mobile -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
        
        <!-- Menu Sections -->
        <div id="menuSections">
            <!-- Menu items will be dynamically generated here -->
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content" id="mainContent">
        <!-- Page Content -->
        <div class="container-fluid py-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    <!-- Menu Master JavaScript -->
    <script>
        class MenuMaster {
            constructor(options = {}) {
                this.menuElement = document.getElementById('sidebar');
                this.toggleButton = document.getElementById('menuToggle');
                this.overlay = document.getElementById('menuOverlay');
                this.mainContent = document.getElementById('mainContent');
                this.searchInput = document.getElementById('menuSearch'); // May be null if not present
                this.sectionsContainer = document.getElementById('menuSections');

                this.isCollapsed = false;
                this.isMobile = window.innerWidth <= 768;
                this.currentUserRole = options.userRole || 'admin';
                this.userPermissions = options.userPermissions || [];

                this.menuData = options.menuData || this.getDefaultMenuData();
                this.state = {
                    openSection: null, // Only one section can be open at a time
                    activeItem: null,
                    searchTerm: ''
                };

                this.init();
            }

            getDefaultMenuData() {
                return [
                    {
                        id: 'dashboard',
                        title: 'Dashboard',
                        icon: 'fas fa-tachometer-alt',
                        url: '<?= base_url("admin/dashboard") ?>',
                        permission: 'dashboard.view'
                    },
                    {
                        id: 'masters',
                        title: 'Masters',
                        icon: 'fab fa-superpowers',
                        children: [
                            { id: 'areas-list', title: 'Areas', icon: 'fas fa-map-marker-alt', url: '<?= base_url("admin/areas") ?>', permission: 'masters.areas' },
                            { id: 'societies-list', title: 'Societies', icon: 'fas fa-city', url: '<?= base_url("admin/societies") ?>', permission: 'masters.societies' },
                            { id: 'connection-statuses-list', title: 'Connection Statuses', icon: 'fas fa-tasks', url: '<?= base_url("admin/connection-statuses") ?>', permission: 'masters.connection_statuses' },
                            { id: 'meter-contractors-list', title: 'Meter Contractors', icon: 'fas fa-tools', url: '<?= base_url("admin/meter-contractors") ?>', permission: 'masters.meter_contractors' },
                            { id: 'meter-manufacturers-list', title: 'Meter Manufacturers', icon: 'fas fa-industry', url: '<?= base_url("admin/meter-manufacturers") ?>', permission: 'masters.meter_manufacturers' },
                            { id: 'stove-types-list', title: 'Stove Types', icon: 'fas fa-fire', url: '<?= base_url("admin/stove-types") ?>', permission: 'masters.stove_types' },
                            { id: 'burner-counts-list', title: 'Burner Counts', icon: 'fas fa-burn', url: '<?= base_url("admin/burner-counts") ?>', permission: 'masters.burner_counts' },
                            { id: 'connection-fees-list', title: 'Connection Fees', icon: 'fas fa-plug', url: '<?= base_url("admin/connection-fees") ?>', permission: 'masters.connection_fees' },
                            { id: 'rates-list', title: 'Rates', icon: 'fas fa-rupee-sign', url: '<?= base_url("admin/rates") ?>', permission: 'masters.rates' },
                            { id: 'charges', title: 'Charges', icon: 'fas fa-rupee-sign', url: '<?= base_url("admin/charges") ?>', permission: 'masters.charges' },
                            { id: 'taxes-list', title: 'Taxes', icon: 'fas fa-plus', url: '<?= base_url("admin/taxes") ?>', permission: 'masters.taxes' },
                            { id: 'banks-list', title: 'Banks', icon: 'fas fa-university', url: '<?= base_url("admin/banks") ?>', permission: 'masters.banks' },
                            { id: 'images-list', title: 'Images', icon: 'fas fa-images', url: '<?= base_url("admin/images") ?>', permission: 'masters.images' },
                            { id: 'bills-list', title: 'Bill Management', icon: 'fas fa-file-invoice', url: '<?= base_url("admin/bills") ?>', permission: 'bills.view' },
                        ]
                    },
                    {
                        id: 'users',
                        title: 'User Management',
                        icon: 'fas fa-users',
                        permission: 'users.view',
                        children: [
                            {
                                id: 'all-users',
                                title: 'All Users',
                                icon: 'fas fa-users',
                                url: '<?= base_url("admin/users") ?>',
                                permission: 'users.view'
                            }
                        ]
                    },
                    {
                        id: 'employee-management',
                        title: 'Employee Management',
                        icon: 'fas fa-users-cog',
                        permission: 'admin_users.view', // Require admin users permission to show section
                        children: [
                            {
                                id: 'manage-employees',
                                title: 'Manage Employees',
                                icon: 'fas fa-user-friends',
                                url: '<?= base_url("admin/admin-users") ?>',
                                permission: 'admin_users.view'
                            },
                            {
                                id: 'access-management',
                                title: 'Access Management',
                                icon: 'fas fa-shield-alt',
                                url: '<?= base_url("admin/access-management") ?>',
                                permission: 'access.view'
                            },
                            {
                                id: 'employee-reports',
                                title: 'Employee Reports',
                                icon: 'fas fa-chart-bar',
                                url: '<?= base_url("admin/employee-reports") ?>',
                                permission: 'reports.employee'
                            }
                        ]
                    },
                    {
                        id: 'settings',
                        title: 'Manage Settings',
                        icon: 'fas fa-cog',
                        permission: 'settings.view',
                        children: [
                            {
                                id: 'general',
                                title: 'General',
                                icon: 'fas fa-cogs',
                                url: '<?= base_url("admin/settings") ?>',
                                permission: 'settings.view'
                            },
                            {
                                id: 'system',
                                title: 'System',
                                icon: 'fas fa-server',
                                url: '<?= base_url("admin/settings/system") ?>',
                                permission: 'settings.view'
                            }
                        ]
                    },
                    {
                        id: 'logs',
                        title: 'Logs',
                        icon: 'fas fa-list-alt',
                        url: '<?= base_url("admin/logs") ?>',
                        permission: 'logs.view'
                    }
                ];
            }

            init() {
                this.renderMenu();
                this.bindEvents();
                this.setActiveItem();
                this.handleResponsive();
            }

            renderMenu() {
                const filteredMenu = this.filterMenuByPermissions(this.menuData);
                const html = filteredMenu.map(section => this.renderMenuSection(section)).join('');
                this.sectionsContainer.innerHTML = html;
            }

            filterMenuByPermissions(menuData) {
                // Super admin sees everything
                if (this.currentUserRole === 'super_admin') {
                    return menuData;
                }

                return menuData.filter(item => {
                    // Check if user has permission for this menu item
                    if (item.permission) {
                        const hasPermission = this.userPermissions.some(perm => perm.name === item.permission);
                        if (!hasPermission) {
                            return false;
                        }
                    }

                    // Check children permissions
                    if (item.children) {
                        item.children = item.children.filter(child => {
                            if (child.permission) {
                                return this.userPermissions.some(perm => perm.name === child.permission);
                            }
                            return true;
                        });
                        return item.children.length > 0;
                    }

                    return true;
                });
            }

            renderMenuSection(section) {
                const isCollapsed = this.state.openSection !== section.id;
                const hasChildren = section.children && section.children.length > 0;

                let html = `
                    <div class="menu-section ${isCollapsed ? 'collapsed' : ''}" data-section-id="${section.id}">
                `;

                if (hasChildren) {
                    html += `
                        <div class="menu-section-header" tabindex="0" role="button" aria-expanded="${!isCollapsed}" aria-controls="section-${section.id}">
                            <span><i class="${section.icon}"></i> ${section.title}</span>
                            <i class="fas fa-chevron-down menu-section-toggle"></i>
                        </div>
                        <ul class="menu-items" id="section-${section.id}" role="menu">
                            ${section.children.map(child => this.renderMenuItem(child)).join('')}
                        </ul>
                    `;
                } else {
                    html += `
                        <ul class="menu-items">
                            ${this.renderMenuItem(section)}
                        </ul>
                    `;
                }

                html += '</div>';
                return html;
            }

            renderMenuItem(item) {
                const isActive = this.state.activeItem === item.id;
                const isVisible = this.matchesSearch(item);

                return `
                    <li class="menu-item ${isActive ? 'active' : ''} ${isVisible ? '' : 'hidden'}"
                        data-item-id="${item.id}"
                        tabindex="0"
                        role="menuitem"
                        aria-current="${isActive ? 'page' : 'false'}"
                        ${item.url ? `data-url="${item.url}"` : ''}>
                        ${item.url ? `<a href="${item.url}" class="menu-link">` : ''}
                        <i class="${item.icon}"></i>
                        <span>${item.title}</span>
                        ${item.url ? '</a>' : ''}
                    </li>
                `;
            }

            bindEvents() {
                // Menu toggle for mobile
                this.toggleButton.addEventListener('click', () => this.toggleMenu());

                // Overlay click
                this.overlay.addEventListener('click', () => this.closeMenu());

                // Section headers
                this.menuElement.addEventListener('click', (e) => {
                    if (e.target.closest('.menu-section-header')) {
                        const header = e.target.closest('.menu-section-header');
                        const section = header.closest('.menu-section');
                        this.toggleSection(section.dataset.sectionId);
                    }
                });

                // Menu items
                this.menuElement.addEventListener('click', (e) => {
                    const menuItem = e.target.closest('.menu-item');
                    if (menuItem) {
                        e.stopPropagation(); // Prevent event bubbling to parent elements
                        const itemId = menuItem.dataset.itemId;

                        // If there's a direct link, let it handle navigation
                        const link = menuItem.querySelector('.menu-link');
                        if (link) {
                            // Just update active state, let the link handle navigation
                            this.setActiveItem(itemId);
                            return;
                        }

                        // Otherwise handle via JavaScript
                        this.handleItemClick(itemId);
                    }
                });

                // Search (only if search input exists)
                if (this.searchInput) {
                    this.searchInput.addEventListener('input', (e) => {
                        this.state.searchTerm = e.target.value.toLowerCase();
                        this.updateSearchResults();
                    });
                }

                // Keyboard navigation
                this.menuElement.addEventListener('keydown', (e) => this.handleKeydown(e));

                // Window resize
                window.addEventListener('resize', () => this.handleResponsive());
            }

            toggleMenu() {
                if (this.isMobile) {
                    this.menuElement.classList.toggle('mobile-open');
                    this.overlay.classList.toggle('active');
                } else {
                    this.isCollapsed = !this.isCollapsed;
                    this.menuElement.classList.toggle('collapsed');
                    this.mainContent.classList.toggle('expanded');
                    this.saveState();
                }
            }

            closeMenu() {
                if (this.isMobile) {
                    this.menuElement.classList.remove('mobile-open');
                    this.overlay.classList.remove('active');
                }
            }

            toggleSection(sectionId) {
                if (this.state.openSection === sectionId) {
                    // Closing the currently open section
                    this.state.openSection = null;
                } else {
                    // Opening a different section (closes any currently open section)
                    this.state.openSection = sectionId;
                }

                this.updateSectionVisibility();
                this.saveState();
            }

            updateSectionVisibility() {
                // Update all sections based on openSection state
                const allSections = this.menuElement.querySelectorAll('.menu-section[data-section-id]');
                allSections.forEach(section => {
                    const secId = section.dataset.sectionId;
                    const isCollapsed = this.state.openSection !== secId;
                    section.classList.toggle('collapsed', isCollapsed);
                    const header = section.querySelector('.menu-section-header');
                    if (header) {
                        header.setAttribute('aria-expanded', !isCollapsed);
                    }
                });
            }

            handleItemClick(itemId) {
                const item = this.findMenuItem(itemId);
                if (item && item.url) {
                    // Update active state
                    this.setActiveItem(itemId);

                    // Navigate to URL
                    window.location.href = item.url;
                }
            }

            findMenuItem(itemId) {
                for (const section of this.menuData) {
                    if (section.id === itemId) return section;
                    if (section.children) {
                        const child = section.children.find(c => c.id === itemId);
                        if (child) return child;
                    }
                }
                return null;
            }

            setActiveItem(itemId = null) {
                // Remove previous active state
                const prevActive = this.menuElement.querySelector('.menu-item.active');
                if (prevActive) {
                    prevActive.classList.remove('active');
                    prevActive.setAttribute('aria-current', 'false');
                }

                if (itemId) {
                    this.state.activeItem = itemId;
                    const newActive = this.menuElement.querySelector(`[data-item-id="${itemId}"]`);
                    if (newActive) {
                        newActive.classList.add('active');
                        newActive.setAttribute('aria-current', 'true');
                    }
                } else {
                    // Auto-detect active item based on current URL
                    const currentPath = window.location.pathname;
                    for (const section of this.menuData) {
                        if (section.url && currentPath.includes(section.url.replace('<?= base_url("") ?>', ''))) {
                            this.state.activeItem = section.id;
                            break;
                        }
                        if (section.children) {
                            const child = section.children.find(c => c.url && currentPath.includes(c.url.replace('<?= base_url("") ?>', '')));
                            if (child) {
                                this.state.activeItem = child.id;
                                break;
                            }
                        }
                    }
                }

                if (this.state.activeItem) {
                    const activeElement = this.menuElement.querySelector(`[data-item-id="${this.state.activeItem}"]`);
                    if (activeElement) {
                        activeElement.classList.add('active');
                        activeElement.setAttribute('aria-current', 'true');

                        // Auto-expand the section containing the active item
                        const section = activeElement.closest('.menu-section');
                        if (section) {
                            const sectionId = section.dataset.sectionId;
                            this.state.openSection = sectionId;
                            this.updateSectionVisibility();
                        }
                    }
                }
            }

            matchesSearch(item) {
                if (!this.state.searchTerm) return true;
                return item.title.toLowerCase().includes(this.state.searchTerm);
            }

            updateSearchResults() {
                const items = this.menuElement.querySelectorAll('.menu-item');
                items.forEach(item => {
                    const itemData = this.findMenuItem(item.dataset.itemId);
                    const isVisible = itemData && this.matchesSearch(itemData);
                    item.classList.toggle('hidden', !isVisible);
                });
            }

            handleKeydown(e) {
                const focusableElements = this.menuElement.querySelectorAll(
                    '.menu-section-header[tabindex="0"], .menu-item[tabindex="0"]'
                );
                const currentIndex = Array.from(focusableElements).indexOf(document.activeElement);

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        const nextIndex = (currentIndex + 1) % focusableElements.length;
                        focusableElements[nextIndex].focus();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        const prevIndex = currentIndex <= 0 ? focusableElements.length - 1 : currentIndex - 1;
                        focusableElements[prevIndex].focus();
                        break;
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        if (document.activeElement.classList.contains('menu-section-header')) {
                            const section = document.activeElement.closest('.menu-section');
                            this.toggleSection(section.dataset.sectionId);
                        } else if (document.activeElement.classList.contains('menu-item')) {
                            this.handleItemClick(document.activeElement.dataset.itemId);
                        }
                        break;
                    case 'Escape':
                        if (this.isMobile && this.menuElement.classList.contains('mobile-open')) {
                            this.closeMenu();
                        }
                        break;
                }
            }

            handleResponsive() {
                const wasMobile = this.isMobile;
                this.isMobile = window.innerWidth <= 768;

                if (wasMobile !== this.isMobile) {
                    if (this.isMobile) {
                        this.menuElement.classList.remove('collapsed');
                        this.mainContent.classList.remove('expanded');
                        this.closeMenu();
                    } else {
                        this.overlay.classList.remove('active');
                        if (this.isCollapsed) {
                            this.menuElement.classList.add('collapsed');
                            this.mainContent.classList.add('expanded');
                        }
                    }
                }
            }

            saveState() {
                const state = {
                    openSection: this.state.openSection,
                    isCollapsed: this.isCollapsed
                };
                localStorage.setItem('menuMasterState', JSON.stringify(state));
            }

            loadState() {
                const saved = localStorage.getItem('menuMasterState');
                if (saved) {
                    const state = JSON.parse(saved);
                    this.state.openSection = state.openSection || null;
                    this.isCollapsed = state.isCollapsed || false;
                }
                // Default: no section open
            }

            updateMenuData(newMenuData) {
                this.menuData = newMenuData;
                this.renderMenu();
            }

            updateUserRole(newRole) {
                this.currentUserRole = newRole;
                this.renderMenu();
            }
        }

        // Initialize Menu Master when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get user permissions
            const userPermissions = <?= json_encode(getUserPermissions()) ?>;

            const menuMaster = new MenuMaster({
                userRole: '<?= session()->get('admin_role') ?: 'admin' ?>',
                userPermissions: userPermissions
            });

            // Make menuMaster available globally for dynamic updates
            window.menuMaster = menuMaster;
        });
    </script>

    <script>
      // Initialize DataTables on all tables marked with .datatable (skip those with data-skip-auto-init)
      document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $.fn.DataTable) {
          $('.datatable').each(function() {
            if ($(this).data('skip-auto-init')) {
              return; // Skip this table
            }
            $(this).DataTable({
              pageLength: 10,
              lengthChange: true,
              ordering: true,
              searching: true,
              autoWidth: false,
              responsive: true
            });
          });
        }
      });
    </script>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('admin/profile/change-password') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div id="passwordAlert" class="alert" style="display: none;" role="alert"></div>

                        <div class="form-group">
                            <label for="modal_current_password">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="modal_current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="modal_new_password">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="modal_new_password" name="new_password" required>
                            <small class="form-text text-muted">Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</small>
                        </div>

                        <div class="form-group">
                            <label for="modal_confirm_password">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="modal_confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Page-specific scripts -->
    <?= $this->renderSection('page-scripts', true) ?>

    <?= $this->renderSection('scripts', true) ?>

    <script>
        // Handle change password modal
        $('#changePasswordModal').on('show.bs.modal', function () {
            // Clear form when modal is shown
            $('#changePasswordModal form')[0].reset();
            $('#passwordAlert').hide();
        });

        $('#changePasswordModal form').on('submit', function(e) {
            // Optional: Add client-side validation here if needed
        });
    </script>
</body>
</html>