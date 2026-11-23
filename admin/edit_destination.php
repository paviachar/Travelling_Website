<?php
require_once 'auth.php';
check_admin_auth();
require_once '../db.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: destinations.php");
    exit();
}

$destination_id = (int) $_GET['id'];
$error = "";
$success = "";

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $country_name      = trim($_POST['country_name'] ?? '');
    $destination_name  = trim($_POST['destination_name'] ?? '');
    $location          = trim($_POST['location'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $attractions       = trim($_POST['attractions'] ?? '');
    $best_time_to_visit= trim($_POST['best_time_to_visit'] ?? '');
    $price             = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;
    $main_image        = trim($_POST['current_image'] ?? 'assets/images/goa.jpg');

    if (empty($country_name) || empty($destination_name)) {
        $error = "Country name and Destination name are required.";
    } else {
        // Handle image upload if provided
        if (isset($_FILES['main_image_file']) && $_FILES['main_image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['main_image_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES['main_image_file']['name']));
            $target_dir = "../assets/images/";
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (move_uploaded_file($file_tmp, $target_dir . $file_name)) {
                    $main_image = 'assets/images/' . $file_name;
                }
            }
        } elseif (!empty($_POST['main_image_url'])) {
            $main_image = trim($_POST['main_image_url']);
        }

        $stmt = $conn->prepare("UPDATE destinations SET country_name = ?, destination_name = ?, location = ?, description = ?, attractions = ?, best_time_to_visit = ?, price = ?, main_image = ? WHERE destination_id = ?");
        $stmt->bind_param("ssssssdsi", $country_name, $destination_name, $location, $description, $attractions, $best_time_to_visit, $price, $main_image, $destination_id);

        if ($stmt->execute()) {
            $success = "Destination updated successfully!";
        } else {
            $error = "Update error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch current destination
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$dest = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dest) {
    die("Destination not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destination - Admin</title>
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
                <h2 class="fw-bold text-dark mb-0">Edit Destination #<?php echo $dest['destination_id']; ?></h2>
                <div>
                    <a href="destination_images.php?id=<?php echo $dest['destination_id']; ?>" class="btn btn-outline-info me-2"><i class="fas fa-images me-1"></i> Manage Slider Images</a>
                    <a href="destinations.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-3 p-4">
                <form method="POST" action="edit_destination.php?id=<?php echo $destination_id; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($dest['main_image']); ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destination Name <span class="text-danger">*</span></label>
                            <input type="text" name="destination_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($dest['destination_name']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country Name <span class="text-danger">*</span></label>
                            <input type="text" name="country_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($dest['country_name']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location / Region</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?php echo htmlspecialchars($dest['location'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estimated Price per Person (INR ₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" 
                                   value="<?php echo htmlspecialchars($dest['price']); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Best Time to Visit</label>
                            <input type="text" name="best_time_to_visit" class="form-control" 
                                   value="<?php echo htmlspecialchars($dest['best_time_to_visit'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Main Image Preview</label>
                            <div class="d-flex align-items-center gap-3">
                                <img src="../<?php echo htmlspecialchars($dest['main_image']); ?>" 
                                     alt="Preview" class="rounded border" style="width: 70px; height: 50px; object-fit: cover;"
                                     onerror="this.src='../assets/images/goa.jpg';">
                                <div class="flex-grow-1">
                                    <input type="file" name="main_image_file" class="form-control form-control-sm" accept="image/*">
                                    <input type="text" name="main_image_url" class="form-control form-control-sm mt-1" 
                                           placeholder="assets/images/..." value="<?php echo htmlspecialchars($dest['main_image']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Tour Description</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($dest['description']); ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Key Tourist Attractions (Comma-separated)</label>
                            <textarea name="attractions" class="form-control" rows="2"><?php echo htmlspecialchars($dest['attractions'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2"><i class="fas fa-save me-1"></i> Update Destination</button>
                            <a href="destinations.php" class="btn btn-light ms-2">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
