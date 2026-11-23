<?php
require_once 'auth.php';
check_admin_auth();
require_once '../db.php';

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $destination_id = (int) $_GET['id'];

    // Delete associated images first (Foreign Key cascade is also configured)
    $stmt1 = $conn->prepare("DELETE FROM destination_images WHERE destination_id = ?");
    $stmt1->bind_param("i", $destination_id);
    $stmt1->execute();
    $stmt1->close();

    // Delete destination
    $stmt2 = $conn->prepare("DELETE FROM destinations WHERE destination_id = ?");
    $stmt2->bind_param("i", $destination_id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: destinations.php?msg=" . urlencode("Destination #$destination_id deleted successfully."));
    exit();
} else {
    header("Location: destinations.php");
    exit();
}
?>
