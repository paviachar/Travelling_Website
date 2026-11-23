<?php
require_once 'auth.php';
check_admin_auth();
require_once '../db.php';

// Handle delete enquiry
if (isset($_GET['delete_id']) && ctype_digit($_GET['delete_id'])) {
    $del_id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM enquiries WHERE enquiry_id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: enquiries.php?msg=" . urlencode("Enquiry #$del_id deleted."));
    exit();
}

// Fetch all enquiries
$sql = "SELECT e.*, d.destination_name AS dest_title, d.country_name 
        FROM enquiries e 
        LEFT JOIN destinations d ON e.destination_id = d.destination_id 
        ORDER BY e.enquiry_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Enquiries - Admin</title>
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
                <a href="destinations.php"><i class="fas fa-map-marked-alt me-2"></i> Manage Destinations</a>
                <a href="add_destination.php"><i class="fas fa-plus-circle me-2"></i> Add Destination</a>
                <a href="enquiries.php" class="active"><i class="fas fa-envelope-open-text me-2"></i> View Enquiries</a>
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> Visit Website</a>
                <a href="logout.php" class="text-danger mt-4"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h2 class="fw-bold text-dark mb-0">Customer Enquiries</h2>
                <span class="badge bg-primary fs-6"><?php echo $result ? $result->num_rows : 0; ?> Total Enquiries</span>
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
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Contact Details</th>
                                <th>Destination</th>
                                <th>Travel Info</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['enquiry_id']; ?></td>
                                        <td class="small text-muted" style="white-space: nowrap;">
                                            <?php echo date('d M Y', strtotime($row['enquiry_date'])); ?><br>
                                            <small><?php echo date('h:i A', strtotime($row['enquiry_date'])); ?></small>
                                        </td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td>
                                            <div><i class="fas fa-envelope fa-xs text-muted me-1"></i> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></div>
                                            <?php if (!empty($row['phone'])): ?>
                                                <div><i class="fas fa-phone fa-xs text-muted me-1"></i> <?php echo htmlspecialchars($row['phone']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['dest_title'])): ?>
                                                <a href="../destination_details.php?id=<?php echo $row['destination_id']; ?>" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                                    <?php echo htmlspecialchars($row['dest_title']); ?>
                                                </a>
                                                <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($row['country_name']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['destination'] ?? 'General Inquiry'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <?php if (!empty($row['travel_date'])): ?>
                                                <div><strong>Date:</strong> <?php echo htmlspecialchars($row['travel_date']); ?></div>
                                            <?php endif; ?>
                                            <div><strong>People:</strong> <?php echo htmlspecialchars($row['number_of_people'] ?? 1); ?></div>
                                        </td>
                                        <td style="max-width: 250px;">
                                            <div class="text-secondary small"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div>
                                        </td>
                                        <td>
                                            <a href="enquiries.php?delete_id=<?php echo $row['enquiry_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Delete this enquiry record?');" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">No customer enquiries received yet.</td></tr>
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
