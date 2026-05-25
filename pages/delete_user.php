<?php
session_start();
include "../assets/db/conn.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged'])) {
    header("Location: log_in.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete user
    $query = "DELETE FROM users WHERE user_id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "User deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting user!";
    }
    
    header("Location: ../admin.php#section4");
    exit();
} else {
    header("Location: ../admin.php");
    exit();
}
?>