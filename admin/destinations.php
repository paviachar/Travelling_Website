<?php
require_once 'auth.php';
check_admin_auth();
require_once '../db.php';

$sql = "SELECT d.*, COUNT(di.image_id) AS gallery_count 
        FROM destinations d 
        LEFT JOIN destination_images di ON d.destination_id = di.destination_id 
        GROUP BY d.destination_id 
        ORDER BY d.destination_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-sidebar { background: #002244; min-height: 100vh; color: #ffffff; }
        .admin-sidebar a { color: #cbd5e1; text-decoration: none; padding: 12px 20px; display: block; border-radius: 6px; margin-bottom: 5px; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: rgba(255, 255, 255, 0.12); color: #ffc107; }
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
                <a href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                <a href="destinations.php" class="active"><i class="fas fa-map-marked-alt me-2"></i> Manage Destinations</a>
                <a href="add_destination.php"><i class="fas fa-plus-circle me-2"></i> Add Destination</a>
                <a href="enquiries.php"><i class="fas fa-envelope-open-text me-2"></i> View Enquiries</a>
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> Visit Website</a>
                <a href="logout.php" class="text-danger mt-4"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h2 class="fw-bold text-dark mb-0">Manage Destinations</h2>
                <a href="add_destination.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add New Destination</a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Main Image</th>
                                <th>Destination</th>
                                <th>Country</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Slider Images</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['destination_id']; ?></td>
                                        <td>
                                            <img src="../<?php echo htmlspecialchars($row['main_image']); ?>" 
                                                 alt="Image" class="rounded" style="width: 60px; height: 42px; object-fit: cover;"
                                                 onerror="this.src='../assets/images/goa.jpg';">
                                        </td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['destination_name']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['country_name']); ?></span></td>
                                        <td class="small text-muted"><?php echo htmlspecialchars($row['location'] ?? 'N/A'); ?></td>
                                        <td class="text-success fw-bold">₹<?php echo number_format($row['price'], 2); ?></td>
                                        <td>
                                            <a href="destination_images.php?id=<?php echo $row['destination_id']; ?>" class="badge bg-info text-dark text-decoration-none p-2">
                                                <i class="fas fa-images me-1"></i> <?php echo $row['gallery_count']; ?> Photos
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <a href="../destination_details.php?id=<?php echo $row['destination_id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="View Public Page">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="edit_destination.php?id=<?php echo $row['destination_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Destination">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_destination.php?id=<?php echo $row['destination_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Delete this destination and its associated slider images?');" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">No destinations available. Click "Add New Destination" to create one.</td></tr>
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
