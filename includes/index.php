<?php
require_once 'db.php';

$sql = "SELECT * FROM destinations ORDER BY id DESC LIMIT 3";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelExplorer - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>Explore The World With Us</h1>
        <p>Discover beautiful destinations and create unforgettable memories.</p>
        <a href="destinations.php" class="btn">Explore Destinations</a>
    </div>
</section>

<section class="container">
    <h1>Popular Destinations</h1>
    <p class="section-description">
        Discover some of the most beautiful places around the world.
    </p>

    <div class="destination-container">

        <?php if ($result && $result->num_rows > 0) { ?>

            <?php while ($row = $result->fetch_assoc()) { ?>

                <div class="destination-card">

                    <img
                        src="<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['destination_name']); ?>"
                    >

                    <h2>
                        <?php echo htmlspecialchars($row['destination_name']); ?>
                    </h2>

                    <p>
                        <?php echo htmlspecialchars($row['country']); ?>
                    </p>

                    <a href="destination_details.php?id=<?php echo (int)$row['id']; ?>">
                        View Details
                    </a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>No destinations available.</p>

        <?php } ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>

<?php $conn->close(); ?>