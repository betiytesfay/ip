<?php
// Start session
session_start();

// Connect to database
include '../assets/db/conn.php';
// Check if verification code is set
if(isset($_GET['code'])) {
  $code = $_GET['code'];
  
  // Query the database for the user with the given verification code
  $query = "SELECT * FROM users WHERE verification_code = '$code'";
  $result = mysqli_query($conn, $query);
  
  // If a user is found with the given verification code, update their record to mark email as verified
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'];
    $query = "UPDATE users SET email_verified = 1 WHERE user_id = $user_id";
    mysqli_query($conn, $query);
    echo "<div class='container mt-5'><h1>Your email has been verified!</h1></div>";
    // Destroy
    // Destroy session
    session_destroy();
  } else {
    echo "<div class='container mt-5'><h1>Invalid verification code</h1></div>";
  }
} else {
  // If verification code is not set, redirect to index page
  header("Location: index.php");
  exit();
}

// Close database connection
mysqli_close($conn);
?>
