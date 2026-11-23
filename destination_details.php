<?php
require_once 'db.php';

// Validate destination ID from query string
if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
    header("Location: destinations.php");
    exit();
}

$destination_id = (int) $_GET['id'];

// Retrieve Destination Information
$stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$dest_res = $stmt->get_result();

if ($dest_res->num_rows === 0) {
    die("<div style='text-align:center;margin-top:50px;'><h2>Destination not found.</h2><a href='destinations.php'>Return to destinations list</a></div>");
}

$destination = $dest_res->fetch_assoc();
$stmt->close();

// Retrieve Destination Slider Images from destination_images table
$img_stmt = $conn->prepare("SELECT * FROM destination_images WHERE destination_id = ? ORDER BY image_id ASC");
$img_stmt->bind_param("i", $destination_id);
$img_stmt->execute();
$images_res = $img_stmt->get_result();
$slider_images = [];
while ($img_row = $images_res->fetch_assoc()) {
    $slider_images[] = $img_row['image_path'];
}
$img_stmt->close();

// Fallback: If no extra images exist in destination_images, use the destination's main_image
if (empty($slider_images) && !empty($destination['main_image'])) {
    $slider_images[] = $destination['main_image'];
}

// Check for feedback messages passed via session or query
$msg_success = isset($_GET['success']) ? "Your enquiry has been successfully registered! Our tour specialist will contact you shortly." : "";
$msg_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($destination['destination_name']); ?> - Details & Enquiry</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Breadcrumb / Header -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="destinations.php">Destinations</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($destination['destination_name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <!-- MODULE 3 - A: Selected Destination Image Slider (Bootstrap Carousel) -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="destinationCarousel" class="carousel slide details-carousel shadow" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    <?php foreach ($slider_images as $idx => $img): ?>
                        <button type="button" data-bs-target="#destinationCarousel" data-bs-slide-to="<?php echo $idx; ?>" 
                                class="<?php echo ($idx === 0) ? 'active' : ''; ?>" aria-current="<?php echo ($idx === 0) ? 'true' : 'false'; ?>" 
                                aria-label="Slide <?php echo $idx + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>

                <!-- Carousel Slides -->
                <div class="carousel-inner">
                    <?php foreach ($slider_images as $idx => $img): ?>
                        <div class="carousel-item <?php echo ($idx === 0) ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($img); ?>" 
                                 class="d-block w-100" 
                                 alt="<?php echo htmlspecialchars($destination['destination_name']); ?> image <?php echo $idx + 1; ?>"
                                 onerror="this.src='assets/images/goa.jpg';">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded px-3 py-1">
                                <h5><?php echo htmlspecialchars($destination['destination_name']); ?></h5>
                                <p><?php echo htmlspecialchars($destination['country_name']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Previous & Next Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if ($msg_success): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $msg_success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg_error): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $msg_error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- MODULE 3 - B: Destination Information & MODULE 4: Enquiry Form -->
    <div class="row g-4">
        <!-- Destination Information Column -->
        <div class="col-lg-7">
            <div class="info-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-globe me-1"></i> <?php echo htmlspecialchars($destination['country_name']); ?>
                    </span>
                    <?php if (!empty($destination['price']) && $destination['price'] > 0): ?>
                        <span class="fs-4 fw-bold text-success">
                            ₹<?php echo number_format($destination['price'], 2); ?>
                            <small class="fs-6 text-muted fw-normal">/ person</small>
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($destination['destination_name']); ?></h1>

                <div class="info-badge-item">
                    <i class="fas fa-map-pin"></i>
                    <div>
                        <strong>Exact Location:</strong>
                        <span class="text-muted ms-1"><?php echo htmlspecialchars($destination['location'] ?? $destination['country_name']); ?></span>
                    </div>
                </div>

                <div class="info-badge-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <strong>Best Time to Visit:</strong>
                        <span class="text-muted ms-1"><?php echo htmlspecialchars($destination['best_time_to_visit'] ?? 'All year round'); ?></span>
                    </div>
                </div>

                <hr class="my-4">

                <h4 class="fw-bold mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Overview & Description</h4>
                <p class="text-secondary lead fs-6" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($destination['description'])); ?>
                </p>

                <?php if (!empty($destination['attractions'])): ?>
                    <h4 class="fw-bold mt-4 mb-3"><i class="fas fa-monument text-primary me-2"></i>Key Tourist Attractions</h4>
                    <div class="p-3 bg-light rounded-3 border">
                        <p class="mb-0 text-dark">
                            <i class="fas fa-star text-warning me-1"></i>
                            <?php echo nl2br(htmlspecialchars($destination['attractions'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <a href="destinations.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Destinations
                    </a>
                </div>
            </div>
        </div>

        <!-- MODULE 4: Destination Enquiry Form Column -->
        <div class="col-lg-5">
            <div class="enquiry-card sticky-top" style="top: 90px; z-index: 10;">
                <div class="text-center mb-4">
                    <span class="badge bg-warning text-dark px-3 py-2 text-uppercase mb-2">Book / Enquire</span>
                    <h3 class="fw-bold">Destination Enquiry</h3>
                    <p class="text-muted small">Fill out the form below to receive customized travel quotes for <?php echo htmlspecialchars($destination['destination_name']); ?>.</p>
                </div>

                <form action="submit_enquiry.php" method="POST" id="enquiryForm" novalidate>
                    <!-- Hidden Destination ID and Name -->
                    <input type="hidden" name="destination_id" value="<?php echo (int)$destination['destination_id']; ?>">
                    <input type="hidden" name="destination_name" value="<?php echo htmlspecialchars($destination['destination_name']); ?>">

                    <!-- Customer Name -->
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                   placeholder="e.g. John Doe" required>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="name@example.com" required>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="e.g. 9876543210" required>
                        </div>
                    </div>

                    <!-- Travel Date & Number of Travellers -->
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="travel_date" class="form-label fw-semibold">Travel Date</label>
                            <input type="date" class="form-control" id="travel_date" name="travel_date" 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="number_of_people" class="form-label fw-semibold">Travellers</label>
                            <input type="number" class="form-control" id="number_of_people" name="number_of_people" 
                                   min="1" max="50" value="1">
                        </div>
                    </div>

                    <!-- Message / Special Requests -->
                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold">
                            Enquiry Message <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="message" name="message" rows="3" 
                                  placeholder="Provide any specific preferences, budget, or questions..." required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary-custom w-100 py-2 fs-6">
                        <i class="fas fa-paper-plane me-1"></i> Submit Enquiry
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>
