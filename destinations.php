<?php
require_once 'db.php';

// Capture and sanitize search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$country_filter = isset($_GET['country']) ? trim($_GET['country']) : '';

// Retrieve distinct country list for quick filter tabs
$countries_res = $conn->query("SELECT DISTINCT country_name FROM destinations ORDER BY country_name ASC");

// Build query dynamically with prepared statements
$query = "SELECT * FROM destinations WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND (destination_name LIKE ? OR country_name LIKE ? OR location LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($country_filter !== '') {
    $query .= " AND country_name = ?";
    $params[] = $country_filter;
    $types .= "s";
}

$query .= " ORDER BY destination_id ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Options & Destinations - TravelExplorer</title>
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
        <h1 class="fw-bold display-5">Country Options & Destinations</h1>
        <p class="lead mb-0 text-white-50">Explore countries and top tourist attractions curated for your journey.</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Search Bar & Filters -->
    <div class="card p-4 shadow-sm border-0 mb-5 rounded-4">
        <form method="GET" action="destinations.php" class="row g-3 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Search by country, city, or destination name..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <select name="country" class="form-select">
                    <option value="">All Countries</option>
                    <?php if ($countries_res && $countries_res->num_rows > 0): ?>
                        <?php while ($c = $countries_res->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($c['country_name']); ?>" 
                                    <?php echo ($country_filter === $c['country_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['country_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-grid gap-2">
                <button type="submit" class="btn btn-primary-custom">Filter</button>
            </div>
        </form>
        <?php if ($search !== '' || $country_filter !== ''): ?>
            <div class="mt-3 text-start">
                <span class="text-muted small">Active filter: </span>
                <?php if ($search): ?><span class="badge bg-secondary me-1">Keyword: "<?php echo htmlspecialchars($search); ?>"</span><?php endif; ?>
                <?php if ($country_filter): ?><span class="badge bg-info text-dark me-1">Country: <?php echo htmlspecialchars($country_filter); ?></span><?php endif; ?>
                <a href="destinations.php" class="btn btn-sm btn-link text-danger text-decoration-none p-0 ms-2"><i class="fas fa-times-circle"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Country Options Cards Grid -->
    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card destination-card">
                        <div class="card-img-wrapper">
                            <a href="destination_details.php?id=<?php echo (int)$row['destination_id']; ?>">
                                <img src="<?php echo htmlspecialchars($row['main_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['destination_name']); ?>"
                                     onerror="this.src='assets/images/goa.jpg';">
                            </a>
                            <span class="country-badge">
                                <i class="fas fa-globe-asia me-1"></i><?php echo htmlspecialchars($row['country_name']); ?>
                            </span>
                            <?php if (!empty($row['price']) && $row['price'] > 0): ?>
                                <span class="price-tag">
                                    ₹<?php echo number_format($row['price'], 2); ?>
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
                                echo (strlen($desc) > 110) ? substr($desc, 0, 107) . '...' : $desc;
                                ?>
                            </p>
                            <a href="destination_details.php?id=<?php echo (int)$row['destination_id']; ?>" 
                               class="btn btn-primary-custom w-100 text-center">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-map-marked fa-4x text-muted mb-3"></i>
                <h4 class="text-secondary">No destinations match your search.</h4>
                <p class="text-muted">Try adjusting your keyword or reset filters to browse all options.</p>
                <a href="destinations.php" class="btn btn-outline-primary mt-2">View All Destinations</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>
