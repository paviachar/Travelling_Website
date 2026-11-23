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

// Fetch destination details
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$dest = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dest) {
    die("Destination not found.");
}

// Handle delete image
if (isset($_GET['delete_img']) && ctype_digit($_GET['delete_img'])) {
    $img_id = (int)$_GET['delete_img'];
    $del_stmt = $conn->prepare("DELETE FROM destination_images WHERE image_id = ? AND destination_id = ?");
    $del_stmt->bind_param("ii", $img_id, $destination_id);
    $del_stmt->execute();
    $del_stmt->close();
    header("Location: destination_images.php?id=" . $destination_id . "&msg=" . urlencode("Slider image removed."));
    exit();
}

// Handle add image via file upload or path
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Multiple or single file upload
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $files = $_FILES['images'];
        $uploaded_count = 0;
        $target_dir = "../assets/images/";

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $orig_name = basename($files['name'][$i]);
                $clean_name = time() . '_' . $i . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", $orig_name);
                $ext = strtolower(pathinfo($clean_name, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    if (move_uploaded_file($files['tmp_name'][$i], $target_dir . $clean_name)) {
                        $img_path = 'assets/images/' . $clean_name;
                        $ins = $conn->prepare("INSERT INTO destination_images (destination_id, image_path) VALUES (?, ?)");
                        $ins->bind_param("is", $destination_id, $img_path);
                        $ins->execute();
                        $ins->close();
                        $uploaded_count++;
                    }
                }
            }
        }
        if ($uploaded_count > 0) {
            $success = "$uploaded_count images added to slider!";
        } else {
            $error = "No valid image files uploaded.";
        }
    } elseif (!empty($_POST['image_path'])) {
        $img_path = trim($_POST['image_path']);
        $ins = $conn->prepare("INSERT INTO destination_images (destination_id, image_path) VALUES (?, ?)");
        $ins->bind_param("is", $destination_id, $img_path);
        if ($ins->execute()) {
            $success = "Image path added to slider!";
        } else {
            $error = "Error adding image: " . $conn->error;
        }
        $ins->close();
    }
}

// Fetch all gallery images for this destination
$img_stmt = $conn->prepare("SELECT * FROM destination_images WHERE destination_id = ? ORDER BY image_id ASC");
$img_stmt->bind_param("i", $destination_id);
$img_stmt->execute();
$images_result = $img_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slider Images - <?php echo htmlspecialchars($dest['destination_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-sidebar { background: #002244; min-height: 100vh; color: #ffffff; }
        .admin-sidebar a { color: #cbd5e1; text-decoration: none; padding: 12px 20px; display: block; border-radius: 6px; margin-bottom: 5px; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: rgba(255, 255, 255, 0.12); color: #ffc107; }
        .gallery-card { border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; position: relative; }
        .gallery-card img { width: 100%; height: 160px; object-fit: cover; }
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
                <div>
                    <h2 class="fw-bold text-dark mb-0">Slider Images for <?php echo htmlspecialchars($dest['destination_name']); ?></h2>
                    <p class="text-muted small mb-0">These photos appear in the Bootstrap Carousel slider on the details page.</p>
                </div>
                <div>
                    <a href="../destination_details.php?id=<?php echo $destination_id; ?>" target="_blank" class="btn btn-outline-primary me-2"><i class="fas fa-eye me-1"></i> Preview Details Page</a>
                    <a href="destinations.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to Destinations</a>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Upload Multiple Images Card -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-cloud-upload-alt text-primary me-2"></i>Upload Multiple Slider Images</h5>
                <form method="POST" action="destination_images.php?id=<?php echo $destination_id; ?>" enctype="multipart/form-data">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Choose Images (Select multiple files)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Or Existing Asset Path</label>
                            <input type="text" name="image_path" class="form-control" placeholder="assets/images/manali.jpg">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-upload me-1"></i> Upload</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Images Grid -->
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="fw-bold text-dark mb-3">Current Slider Photos (<?php echo $images_result->num_rows; ?>)</h5>
                
                <div class="row g-3">
                    <?php if ($images_result && $images_result->num_rows > 0): ?>
                        <?php while($img = $images_result->fetch_assoc()): ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="gallery-card bg-white p-2">
                                    <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" 
                                         alt="Slider Image" 
                                         onerror="this.src='../assets/images/goa.jpg';">
                                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                        <small class="text-truncate text-muted" style="max-width: 140px;"><?php echo basename($img['image_path']); ?></small>
                                        <a href="destination_images.php?id=<?php echo $destination_id; ?>&delete_img=<?php echo $img['image_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger py-0 px-2"
                                           onclick="return confirm('Remove this image from slider?');" title="Delete Image">
                                            <i class="fas fa-trash fa-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 py-4 text-center text-muted">
                            <i class="fas fa-images fa-3x mb-2 text-secondary"></i>
                            <p>No slider images uploaded yet for this destination. The main destination image will be used as default fallback.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$img_stmt->close();
$conn->close(); 
?>
