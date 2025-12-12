<?php
// Start session
session_start();

// Connect to database
include '../assets/db/conn.php';

// Get user ID from session variable
$user_id = $_SESSION['user_id'];

// Generate new verification code and update user record in database
$verification_code = uniqid();
$query = "UPDATE users SET verification_code = '$verification_code' WHERE id = $user_id";
mysqli_query($conn, $query);

// Send verification email
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$email = $row['email'];
$to =$email;
$subject = "Verify your email";
$message = "Click the link below to verify your email:\n\n";
$message .= "http://localhost/templates/verify.php?code=$verification_code";
$headers = "From: noreply@donorhub.com";
mail($to, $subject, $message, $headers);

// Redirect to verification page
header("Location: verify.php");
exit();

// Close database connection
mysqli_close($conn);
?>
