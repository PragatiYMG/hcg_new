<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Customers Management</h3>
                    <div>
                        <a href="<?= base_url('admin/customers/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Customer
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show auto-hide" role="alert">
                            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Form -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <button class="btn btn-link p-0" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false">
                                    <i class="fas fa-filter"></i> Filters
                                </button>
                            </h5>
                        </div>
                        <div class="collapse" id="filterCollapse">
                            <div class="card-body">
                                <form id="filterForm" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_first_name">First Name</label>
                                            <input type="text" class="form-control" id="filter_first_name" name="first_name" placeholder="Search first name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_last_name">Last Name</label>
                                            <input type="text" class="form-control" id="filter_last_name" name="last_name" placeholder="Search last name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_email">Email</label>
                                            <input type="email" class="form-control" id="filter_email" name="email" placeholder="Search email">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_primary_mobile">Mobile</label>
                                            <input type="text" class="form-control" id="filter_primary_mobile" name="primary_mobile" placeholder="Search mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_gender">Gender</label>
                                            <select class="form-control" id="filter_gender" name="gender">
                                                <option value="">All Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_status">Status</label>
                                            <select class="form-control" id="filter_status" name="status">
                                                <option value="">All Status</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                            <i class="fas fa-search"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="customersTable" class="table table-striped table-bordered" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete customer <strong id="deleteCustomerName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page-scripts') ?>
<script>
let currentFilters = {};

function loadCustomers() {
    const tbody = document.getElementById('customersTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';

    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams();

    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }

    fetch('<?= base_url("admin/customers/get-data") ?>?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // DataTables server-side response format
        if (data.data && Array.isArray(data.data)) {
            renderCustomersTable(data.data);
        } else {
            console.error('Invalid data format:', data);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Invalid data format received. Please try again.</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data. Please check your connection and try again.</td></tr>';
    });
}

function renderCustomersTable(customers) {
    const tbody = document.getElementById('customersTableBody');
    tbody.innerHTML = '';

    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No customers found</td></tr>';
        return;
    }

    customers.forEach(customer => {
        const statusBadge = customer[4]; // Status HTML from server
        const createdDate = customer[5] ? new Date(customer[5]).toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }) : 'N/A';

        const row = `<tr>
            <td>${customer[0]}</td>
            <td>${customer[1]}</td>
            <td>${customer[2]}</td>
            <td>${customer[3]}</td>
            <td>${statusBadge}</td>
            <td>${createdDate}</td>
            <td>${customer[6]}</td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function applyFilters() {
    loadCustomers();
}

function clearFilters() {
    document.getElementById('filterForm').reset();
    loadCustomers();
}

// Load initial data and setup auto-hide notifications
document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();

    // Auto-hide success notifications
    const alerts = document.querySelectorAll('.alert-success.auto-hide');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
});
</script>
<?= $this->endSection() ?>