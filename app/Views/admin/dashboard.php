<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .welcome {
            text-align: center;
            margin-bottom: 30px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        .stat-card p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (!empty($site_logo) && file_exists(ROOTPATH . 'public/uploads/' . $site_logo)): ?>
            <img src="<?= base_url('uploads/' . $site_logo) ?>" alt="Site Logo" style="height: 40px;">
        <?php else: ?>
            <h1>Admin Dashboard</h1>
        <?php endif; ?>
        <a href="<?= base_url('admin/logout') ?>" class="logout-btn">Logout</a>
    </div>
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?= session()->get('admin_username') ?>!</h2>
            <p>You are now logged in to the admin panel.</p>
        </div>
        <div class="stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p>0</p>
            </div>
            <div class="stat-card">
                <h3>Total Posts</h3>
                <p>0</p>
            </div>
            <div class="stat-card">
                <h3>Total Comments</h3>
                <p>0</p>
            </div>
        </div>
        <div>
            <h3>Quick Actions</h3>
            <p><a href="<?= base_url('admin/settings') ?>">Manage Settings</a></p>
            <p>This is where you can add management features for your application.</p>
        </div>
    </div>
</body>
</html>