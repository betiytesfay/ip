<?php
// Start session
session_start();

// Connect to database
include '../assets/db/conn.php';
// Process registration form
if(isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $verification_code = uniqid();
  
  // Insert user into database with verification code
  $query = "INSERT INTO users (email, password, verification_code) VALUES ('$email', '$password', '$verification_code')";
  mysqli_query($conn, $query);
  
  // Send verification email
  $to = $email;
  $subject = "Verify your email";
  $message = "Click the link below to verify your email:\n\n";
  $message .= "http://example.com/verify.php?code=$verification_code";
  $headers = "From: noreply@donorhub.com";
  mail($to, $subject, $message, $headers);
  
  // Set session variable for user ID
  $_SESSION['user_id'] = mysqli_insert_id($conn);
  
  // Redirect to verification page
  header("Location: ../templates/verify.php");
  exit();
}

// Close database connection
mysqli_close($conn);
?>
