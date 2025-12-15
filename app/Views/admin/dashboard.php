<?php $this->extend('admin/layout') ?>

<?php $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title" id="greeting">Welcome, <?= esc($admin['first_name'] . ' ' . $admin['last_name']) ?>!</h2>
                    <p class="card-text">You are now logged in to the admin panel. Here's an overview of your system.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview Metrics -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-chart-line mr-2"></i>System Overview</h4>
        </div>
    </div>

    <!-- Primary Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Admin Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_admins'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Areas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_areas'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Societies
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_societies'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Bills
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_bills'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Metrics Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-user-friends mr-2"></i>Customer Statistics</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['total_customers'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['active_customers'] ?? 0 ?>
                            </div>
                            <div class="text-xs text-muted">
                                <?php
                                $total = $metrics['total_customers'] ?? 0;
                                $active = $metrics['active_customers'] ?? 0;
                                $percentage = $total > 0 ? round(($active / $total) * 100, 1) : 0;
                                echo $percentage . '% of total';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $metrics['pending_customers'] ?? 0 ?>
                            </div>
                            <div class="text-xs text-muted">
                                <?php
                                $total = $metrics['total_customers'] ?? 0;
                                $pending = $metrics['pending_customers'] ?? 0;
                                $percentage = $total > 0 ? round(($pending / $total) * 100, 1) : 0;
                                echo $percentage . '% awaiting approval';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-bolt mr-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-users mr-2"></i>Manage Admin Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/customers') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-user-friends mr-2"></i>Manage Customers
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/areas') ?>" class="btn btn-success btn-block">
                                <i class="fas fa-map-marker-alt mr-2"></i>Manage Areas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/societies') ?>" class="btn btn-info btn-block">
                                <i class="fas fa-city mr-2"></i>Manage Societies
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/bills') ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-file-invoice mr-2"></i>Manage Bills
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/settings') ?>" class="btn btn-dark btn-block">
                                <i class="fas fa-cog mr-2"></i>System Settings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/access-management') ?>" class="btn btn-danger btn-block">
                                <i class="fas fa-shield-alt mr-2"></i>Access Management
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/logs') ?>" class="btn btn-light btn-block text-dark">
                                <i class="fas fa-list-alt mr-2"></i>View Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.text-primary {
    color: #5a5c69 !important;
}
.text-success {
    color: #1cc88a !important;
}
.text-info {
    color: #36b9cc !important;
}
.text-warning {
    color: #f6c23e !important;
}
.text-secondary {
    color: #858796 !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

/* Enhanced dashboard styles */
.dashboard-section {
    margin-bottom: 2rem;
}

.metric-card {
    transition: transform 0.2s ease-in-out;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.metric-percentage {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateGreeting() {
        const now = new Date();
        const hour = now.getHours();
        let greeting = '';

        if (hour >= 5 && hour < 12) {
            greeting = 'Good Morning';
        } else if (hour >= 12 && hour < 17) {
            greeting = 'Good Afternoon';
        } else {
            greeting = 'Good Evening';
        }

        const name = '<?= esc($admin['first_name'] . ' ' . $admin['last_name']) ?>';
        document.getElementById('greeting').textContent = greeting + ', ' + name + '!';
    }

    updateGreeting();
});
</script>
<?php $this->endSection() ?>