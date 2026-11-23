<?php
require_once 'db.php';

// Fetch featured 3 destinations for home page
$sql = "SELECT * FROM destinations ORDER BY destination_id ASC LIMIT 3";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelExplorer - Explore The World With Us</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Explore The World With Us</h1>
        <p>Discover breathtaking tourist destinations, planned itineraries, and create unforgettable memories.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="destinations.php" class="btn btn-warning btn-lg fw-bold px-4">Browse Country Options</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg px-4">Contact Us</a>
        </div>
    </div>
</section>

<!-- Country Options / Featured Destinations Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Featured Places</span>
            <h2 class="display-6 fw-bold text-dark">Popular Country Options</h2>
            <p class="text-muted mx-auto" style="max-width: 650px;">
                Handpicked global holiday packages tailored for every traveller. Browse our top featured selections below.
            </p>
        </div>

        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card destination-card">
                            <div class="card-img-wrapper">
                                <img src="<?php echo htmlspecialchars($row['main_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['destination_name']); ?>"
                                     onerror="this.src='assets/images/goa.jpg';">
                                <span class="country-badge">
                                    <i class="fas fa-flag me-1"></i><?php echo htmlspecialchars($row['country_name']); ?>
                                </span>
                                <?php if (!empty($row['price']) && $row['price'] > 0): ?>
                                    <span class="price-tag">
                                        ₹<?php echo number_format($row['price'], 2); ?> / person
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['destination_name']); ?></h3>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    <?php echo htmlspecialchars($row['location'] ?? $row['country_name']); ?>
                                </p>
                                <p class="card-text">
                                    <?php 
                                    $desc = htmlspecialchars($row['description']);
                                    echo (strlen($desc) > 105) ? substr($desc, 0, 102) . '...' : $desc;
                                    ?>
                                </p>
                                <a href="destination_details.php?id=<?php echo (int)$row['destination_id']; ?>" 
                                   class="btn btn-primary-custom w-100 text-center">
                                    <i class="fas fa-info-circle me-1"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">No destinations available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="destinations.php" class="btn btn-outline-primary btn-lg px-4">
                View All Destinations <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features / Highlights -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h4 class="fw-bold">Safe & Verified</h4>
                    <p class="text-muted">All tour packages and itinerary plans adhere strictly to global safety guidelines.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                    <h4 class="fw-bold">Affordable Pricing</h4>
                    <p class="text-muted">Transparent rates per person without hidden booking surcharges.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h4 class="fw-bold">Dedicated Support</h4>
                    <p class="text-muted">Get rapid responses on custom queries and group travel bookings anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>
