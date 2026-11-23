<?php
// Initialize session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper to determine active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Navigation Bar adhering to Faculty Diagram & Requirements -->
<nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container-fluid px-lg-5">
        <!-- Website Brand / Logo & Name -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="brand-icon me-2"><i class="fas fa-plane-departure"></i></span>
            <span class="brand-text">Travel<span class="brand-highlight">Explorer</span></span>
        </a>

        <!-- Mobile Hamburger Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#travelNavbar" aria-controls="travelNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links and Social Icons -->
        <div class="collapse navbar-collapse" id="travelNavbar">
            <!-- Navigation Links -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'destinations.php' || $current_page == 'destination_details.php') ? 'active' : ''; ?>" href="destinations.php">
                        <i class="fas fa-map-marked-alt me-1"></i> Destinations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">
                        <i class="fas fa-info-circle me-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>

            <!-- Social Media Icons & Admin Link -->
            <div class="d-flex align-items-center">
                <div class="social-icons me-3">
                    <a href="https://facebook.com" target="_blank" title="Facebook" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com" target="_blank" title="Twitter" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank" title="Instagram" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="https://youtube.com" target="_blank" title="YouTube" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
                <a href="admin/login.php" class="btn btn-sm btn-outline-light admin-portal-btn" title="Admin Portal">
                    <i class="fas fa-user-shield me-1"></i> Admin
                </a>
            </div>
        </div>
    </div>
</nav>
