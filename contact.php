<?php
require_once 'db.php';

$success = "";
$error = "";

$name = "";
$email = "";
$phone = "";
$destination = "";
$message = "";

// Fetch destinations for optional dropdown
$dest_res = $conn->query("SELECT destination_id, destination_name, country_name FROM destinations ORDER BY destination_name ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $phone = trim($_POST['phone'] ?? "");
    $destination = trim($_POST['destination'] ?? "");
    $message = trim($_POST['message'] ?? "");

    if ($name === "" || $email === "" || $message === "") {
        $error = "Please fill in all required fields (Name, Email, Message).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO enquiries (customer_name, email, phone, destination, message)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("sssss", $name, $email, $phone, $destination, $message);

        if ($stmt->execute()) {
            $success = "Your enquiry has been submitted successfully! Our team will contact you soon.";
            $name = "";
            $email = "";
            $phone = "";
            $destination = "";
            $message = "";
        } else {
            $error = "Something went wrong while submitting. Please try again: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TravelExplorer</title>
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
        <h1 class="fw-bold display-5">Get In Touch</h1>
        <p class="lead mb-0 text-white-50">Have a question or planning a custom travel itinerary? Send us an enquiry.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <!-- Contact Information Cards -->
        <div class="col-lg-5">
            <h3 class="fw-bold text-dark mb-4">Contact Information</h3>
            <p class="text-muted">Feel free to reach out to us via any communication channel below or fill in the enquiry form.</p>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-primary text-white p-3 rounded-circle me-3">
                    <i class="fas fa-map-marker-alt fa-fw fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Our Head Office</h5>
                    <p class="text-muted mb-0">TravelExplorer HQ, Park View Avenue, Bangalore, Karnataka, India - 560001</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-primary text-white p-3 rounded-circle me-3">
                    <i class="fas fa-phone-alt fa-fw fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Phone Number</h5>
                    <p class="text-muted mb-0">+91 98765 43210 / +91 80 2345 6789</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-primary text-white p-3 rounded-circle me-3">
                    <i class="fas fa-envelope fa-fw fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Email Address</h5>
                    <p class="text-muted mb-0">support@travelexplorer.com / bookings@travelexplorer.com</p>
                </div>
            </div>

            <div class="d-flex align-items-start mb-4">
                <div class="bg-primary text-white p-3 rounded-circle me-3">
                    <i class="fas fa-clock fa-fw fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Working Hours</h5>
                    <p class="text-muted mb-0">Monday - Saturday: 9:00 AM - 7:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Contact / General Enquiry Form -->
        <div class="col-lg-7">
            <div class="card p-4 p-md-5 border-0 shadow rounded-4">
                <h3 class="fw-bold mb-3">Send Us a Message</h3>

                <?php if ($success !== ""): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter your full name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($phone); ?>" placeholder="Mobile number">
                        </div>

                        <div class="col-md-6">
                            <label for="destination" class="form-label fw-semibold">Interested Destination</label>
                            <select id="destination" name="destination" class="form-select">
                                <option value="">General Travel Inquiry</option>
                                <?php if ($dest_res && $dest_res->num_rows > 0): ?>
                                    <?php while ($d = $dest_res->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($d['destination_name']); ?>" 
                                            <?php echo ($destination === $d['destination_name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($d['destination_name'] . ' (' . $d['country_name'] . ')'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="message" class="form-label fw-semibold">Your Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="5" 
                                      placeholder="Write your questions, plans or travel requirements..." required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary-custom px-4 py-2 fs-6">
                                <i class="fas fa-paper-plane me-2"></i> Submit Enquiry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>
