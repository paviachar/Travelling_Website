<?php
require_once 'auth.php';
check_admin_auth();
require_once '../db.php';

// Fetch summary metrics
$dest_count = $conn->query("SELECT COUNT(*) as total FROM destinations")->fetch_assoc()['total'];
$enq_count = $conn->query("SELECT COUNT(*) as total FROM enquiries")->fetch_assoc()['total'];
$img_count = $conn->query("SELECT COUNT(*) as total FROM destination_images")->fetch_assoc()['total'];

// Fetch latest 5 enquiries
$latest_enquiries = $conn->query("SELECT * FROM enquiries ORDER BY enquiry_id DESC LIMIT 5");

// Fetch destinations
$destinations = $conn->query("SELECT * FROM destinations ORDER BY destination_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TravelExplorer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-sidebar {
            background: #002244;
            min-height: 100vh;
            color: #ffffff;
        }
        .admin-sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 6px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background: rgba(255, 255, 255, 0.12);
            color: #ffc107;
        }
        .metric-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 admin-sidebar p-3 d-none d-md-block">
            <div class="text-center py-3 border-bottom border-secondary mb-3">
                <h4 class="fw-bold mb-0 text-white"><i class="fas fa-plane-departure text-warning me-2"></i>Admin</h4>
                <small class="text-white-50">Logged in as <?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
            </div>
            <nav>
                <a href="index.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                <a href="destinations.php"><i class="fas fa-map-marked-alt me-2"></i> Manage Destinations</a>
                <a href="add_destination.php"><i class="fas fa-plus-circle me-2"></i> Add Destination</a>
                <a href="enquiries.php"><i class="fas fa-envelope-open-text me-2"></i> View Enquiries</a>
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> Visit Website</a>
                <a href="logout.php" class="text-danger mt-4"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h2 class="fw-bold text-dark mb-0">Dashboard Overview</h2>
                <div>
                    <a href="add_destination.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Destination</a>
                    <a href="logout.php" class="btn btn-outline-danger ms-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card metric-card bg-primary text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 text-uppercase mb-1">Destinations</h6>
                                <h2 class="fw-bold mb-0"><?php echo $dest_count; ?></h2>
                            </div>
                            <i class="fas fa-map-marker-alt fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card metric-card bg-success text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 text-uppercase mb-1">Customer Enquiries</h6>
                                <h2 class="fw-bold mb-0"><?php echo $enq_count; ?></h2>
                            </div>
                            <i class="fas fa-envelope fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card metric-card bg-info text-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 text-uppercase mb-1">Gallery Slider Images</h6>
                                <h2 class="fw-bold mb-0"><?php echo $img_count; ?></h2>
                            </div>
                            <i class="fas fa-images fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Destinations -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Destinations Overview</h5>
                    <a href="destinations.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Destination</th>
                                <th>Country</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($destinations && $destinations->num_rows > 0): ?>
                                <?php while($row = $destinations->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['destination_id']; ?></td>
                                        <td>
                                            <img src="../<?php echo htmlspecialchars($row['main_image']); ?>" 
                                                 alt="Thumb" class="rounded" style="width: 50px; height: 35px; object-fit: cover;"
                                                 onerror="this.src='../assets/images/goa.jpg';">
                                        </td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['destination_name']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['country_name']); ?></span></td>
                                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                                        <td>
                                            <a href="edit_destination.php?id=<?php echo $row['destination_id']; ?>" class="btn btn-sm btn-light text-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="destination_images.php?id=<?php echo $row['destination_id']; ?>" class="btn btn-sm btn-light text-info" title="Manage Slider Images">
                                                <i class="fas fa-images"></i>
                                            </a>
                                            <a href="delete_destination.php?id=<?php echo $row['destination_id']; ?>" class="btn btn-sm btn-light text-danger" 
                                               onclick="return confirm('Are you sure you want to delete this destination?');" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No destinations added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Enquiries -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Recent Customer Enquiries</h5>
                    <a href="enquiries.php" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Destination</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($latest_enquiries && $latest_enquiries->num_rows > 0): ?>
                                <?php while($enq = $latest_enquiries->fetch_assoc()): ?>
                                    <tr>
                                        <td class="small text-muted"><?php echo date('d M Y, h:i A', strtotime($enq['enquiry_date'])); ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($enq['customer_name']); ?></td>
                                        <td><a href="mailto:<?php echo htmlspecialchars($enq['email']); ?>"><?php echo htmlspecialchars($enq['email']); ?></a></td>
                                        <td><?php echo htmlspecialchars($enq['phone']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($enq['destination'] ?? 'General'); ?></span></td>
                                        <td class="small text-muted"><?php echo htmlspecialchars(substr($enq['message'], 0, 50)) . '...'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No customer enquiries received yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
