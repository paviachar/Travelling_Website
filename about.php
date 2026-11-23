<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TravelExplorer</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<div class="bg-primary text-white py-5 mb-5 shadow-sm" style="background: linear-gradient(135deg, #003366, #0066aa);">
    <div class="container text-center">
        <h1 class="fw-bold display-5">About TravelExplorer</h1>
        <p class="lead mb-0 text-white-50">Crafting inspiring journeys, memorable holidays, and seamless adventures.</p>
    </div>
</div>

<section class="container mb-5">
    <div class="row g-4 align-items-center mb-5">
        <div class="col-lg-6">
            <span class="badge bg-primary text-uppercase px-3 py-2 mb-2">Who We Are</span>
            <h2 class="fw-bold mb-3">Your Trusted Gateway to the World</h2>
            <p class="text-secondary lead fs-6">
                <strong>TravelExplorer</strong> is a full-featured web application designed to help travellers discover scenic destinations, explore countries, compare trip packages, and make instant booking enquiries.
            </p>
            <p class="text-muted">
                Our platform provides detailed breakdowns of tourist spots, key attractions, ideal visiting seasons, and real-time per-person pricing, backed by a robust MySQL database and Core PHP architecture.
            </p>
        </div>
        <div class="col-lg-6">
            <img src="assets/images/paris.jpg" alt="About TravelExplorer" class="img-fluid rounded-4 shadow" onerror="this.src='assets/images/goa.jpg';">
        </div>
    </div>

    <!-- Mission & Offerings -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white p-3 rounded-circle me-3">
                        <i class="fas fa-bullseye fa-lg"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Our Mission</h4>
                </div>
                <p class="text-muted">
                    Our mission is to simplify travel planning by bridging the gap between tourists and reliable destination data. We empower explorers with comprehensive information, high-definition visual sliders, and fast enquiry resolution.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success text-white p-3 rounded-circle me-3">
                        <i class="fas fa-gem fa-lg"></i>
                    </div>
                    <h4 class="fw-bold mb-0">What We Offer</h4>
                </div>
                <ul class="list-unstyled text-muted mb-0">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Curated Country Options with instant search and filtering.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Multi-image dynamic sliders for every tourist spot.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Direct-to-database destination enquiry and booking submissions.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Admin dashboard for complete destination management.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
