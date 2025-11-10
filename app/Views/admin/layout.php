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
            <div class="row align-items-center">
                <div class="col-auto">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="<?= base_url('admin/dashboard') ?>" class="logo">
                        <img src="<?= base_url('uploads/logo_1762758146.png') ?>" alt="Logo" class="logo-img">
                        Admin Panel
                    </a>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <button class="btn btn-link" title="Notifications">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> <?= session()->get('admin_username') ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('admin/profile') ?>">
                                    <i class="fas fa-user-edit"></i> Profile
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Menu Master JavaScript -->
    <script>
        class MenuMaster {
            constructor(options = {}) {
                this.menuElement = document.getElementById('sidebar');
                this.toggleButton = document.getElementById('menuToggle');
                this.overlay = document.getElementById('menuOverlay');
                this.mainContent = document.getElementById('mainContent');
                this.searchInput = document.getElementById('menuSearch');
                this.sectionsContainer = document.getElementById('menuSections');

                this.isCollapsed = false;
                this.isMobile = window.innerWidth <= 768;
                this.currentUserRole = options.userRole || 'admin';

                this.menuData = options.menuData || this.getDefaultMenuData();
                this.state = {
                    collapsedSections: new Set(),
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
                        roles: ['admin', 'manager', 'user']
                    },
                    {
                        id: 'content',
                        title: 'Content Management',
                        icon: 'fas fa-edit',
                        roles: ['admin', 'manager'],
                        children: [
                            {
                                id: 'pages',
                                title: 'Pages',
                                icon: 'fas fa-file-alt',
                                url: '<?= base_url("admin/pages") ?>',
                                roles: ['admin', 'manager']
                            },
                            {
                                id: 'posts',
                                title: 'Posts',
                                icon: 'fas fa-newspaper',
                                url: '<?= base_url("admin/posts") ?>',
                                roles: ['admin', 'manager']
                            },
                            {
                                id: 'media',
                                title: 'Media Library',
                                icon: 'fas fa-images',
                                url: '<?= base_url("admin/media") ?>',
                                roles: ['admin', 'manager']
                            }
                        ]
                    },
                    {
                        id: 'users',
                        title: 'User Management',
                        icon: 'fas fa-users',
                        roles: ['admin'],
                        children: [
                            {
                                id: 'all-users',
                                title: 'All Users',
                                icon: 'fas fa-users',
                                url: '<?= base_url("admin/users") ?>',
                                roles: ['admin']
                            },
                            {
                                id: 'roles',
                                title: 'Roles & Permissions',
                                icon: 'fas fa-user-shield',
                                url: '<?= base_url("admin/roles") ?>',
                                roles: ['admin']
                            }
                        ]
                    },
                    {
                        id: 'areas',
                        title: 'Manage Areas',
                        icon: 'fas fa-map-marker-alt',
                        roles: ['admin', 'manager'],
                        children: [
                            {
                                id: 'areas-list',
                                title: 'List Areas',
                                icon: 'fas fa-list',
                                url: '<?= base_url("admin/areas") ?>',
                                roles: ['admin', 'manager']
                            },
                            {
                                id: 'areas-create',
                                title: 'Create Area',
                                icon: 'fas fa-plus',
                                url: '<?= base_url("admin/areas/create") ?>',
                                roles: ['admin', 'manager']
                            }
                        ]
                    },
                    {
                        id: 'settings',
                        title: 'Manage Settings',
                        icon: 'fas fa-cog',
                        roles: ['admin'],
                        children: [
                            {
                                id: 'general',
                                title: 'General',
                                icon: 'fas fa-cogs',
                                url: '<?= base_url("admin/settings") ?>',
                                roles: ['admin']
                            },
                            {
                                id: 'system',
                                title: 'System',
                                icon: 'fas fa-server',
                                url: '<?= base_url("admin/settings/system") ?>',
                                roles: ['admin']
                            }
                        ]
                    },
                    {
                        id: 'logs',
                        title: 'Logs',
                        icon: 'fas fa-list-alt',
                        url: '<?= base_url("admin/logs") ?>',
                        roles: ['admin']
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
                const filteredMenu = this.filterMenuByRole(this.menuData);
                const html = filteredMenu.map(section => this.renderMenuSection(section)).join('');
                this.sectionsContainer.innerHTML = html;
            }

            filterMenuByRole(menuData) {
                return menuData.filter(item => {
                    if (item.roles && !item.roles.includes(this.currentUserRole)) {
                        return false;
                    }
                    if (item.children) {
                        item.children = item.children.filter(child =>
                            !child.roles || child.roles.includes(this.currentUserRole)
                        );
                        return item.children.length > 0;
                    }
                    return true;
                });
            }

            renderMenuSection(section) {
                const isCollapsed = this.state.collapsedSections.has(section.id);
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
                        aria-current="${isActive ? 'page' : 'false'}">
                        <i class="${item.icon}"></i>
                        <span>${item.title}</span>
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
                    if (e.target.closest('.menu-item')) {
                        const item = e.target.closest('.menu-item');
                        this.handleItemClick(item.dataset.itemId);
                    }
                });

                // Search
                this.searchInput.addEventListener('input', (e) => {
                    this.state.searchTerm = e.target.value.toLowerCase();
                    this.updateSearchResults();
                });

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
                if (this.state.collapsedSections.has(sectionId)) {
                    this.state.collapsedSections.delete(sectionId);
                } else {
                    this.state.collapsedSections.add(sectionId);
                }
                this.updateSectionVisibility(sectionId);
                this.saveState();
            }

            updateSectionVisibility(sectionId) {
                const section = this.menuElement.querySelector(`[data-section-id="${sectionId}"]`);
                if (section) {
                    const isCollapsed = this.state.collapsedSections.has(sectionId);
                    section.classList.toggle('collapsed', isCollapsed);
                    const header = section.querySelector('.menu-section-header');
                    if (header) {
                        header.setAttribute('aria-expanded', !isCollapsed);
                    }
                }
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
                    collapsedSections: Array.from(this.state.collapsedSections),
                    isCollapsed: this.isCollapsed
                };
                localStorage.setItem('menuMasterState', JSON.stringify(state));
            }

            loadState() {
                const saved = localStorage.getItem('menuMasterState');
                if (saved) {
                    const state = JSON.parse(saved);
                    this.state.collapsedSections = new Set(state.collapsedSections || []);
                    this.isCollapsed = state.isCollapsed || false;
                }
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
            const menuMaster = new MenuMaster({
                userRole: '<?= session()->get('admin_role') ?: 'admin' ?>'
            });

            // Make menuMaster available globally for dynamic updates
            window.menuMaster = menuMaster;
        });
    </script>

    <?= $this->renderSection('scripts', true) ?>
</body>
</html>