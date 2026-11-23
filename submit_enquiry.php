<?php
require_once 'db.php';

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and sanitize form data
    $destination_id   = isset($_POST['destination_id']) ? (int)$_POST['destination_id'] : null;
    $destination_name = isset($_POST['destination_name']) ? trim($_POST['destination_name']) : '';
    $customer_name    = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $email            = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone            = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $travel_date      = !empty($_POST['travel_date']) ? trim($_POST['travel_date']) : null;
    $number_of_people = isset($_POST['number_of_people']) && (int)$_POST['number_of_people'] > 0 ? (int)$_POST['number_of_people'] : 1;
    $message          = isset($_POST['message']) ? trim($_POST['message']) : '';

    $redirect_url = $destination_id ? "destination_details.php?id=" . $destination_id : "destinations.php";

    // Validation
    if (empty($customer_name) || empty($email) || empty($message)) {
        header("Location: " . $redirect_url . "&error=" . urlencode("Please fill in all required fields (Name, Email, Message)."));
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: " . $redirect_url . "&error=" . urlencode("Invalid email address format."));
        exit();
    }

    // Insert into MySQL enquiries table using prepared statements
    $stmt = $conn->prepare(
        "INSERT INTO enquiries (destination_id, customer_name, email, phone, destination, travel_date, number_of_people, message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        header("Location: " . $redirect_url . "&error=" . urlencode("Database preparation error: " . $conn->error));
        exit();
    }

    $stmt->bind_param(
        "isssssis",
        $destination_id,
        $customer_name,
        $email,
        $phone,
        $destination_name,
        $travel_date,
        $number_of_people,
        $message
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: " . $redirect_url . "&success=1");
        exit();
    } else {
        $error_msg = "Error submitting enquiry: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: " . $redirect_url . "&error=" . urlencode($error_msg));
        exit();
    }

} else {
    // If accessed directly via GET, redirect to destinations page
    header("Location: destinations.php");
    exit();
}
?>
