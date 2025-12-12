<?php
include '../assets/db/conn.php';
if(isset($_GET['id'])){
    $id = $_GET['id'];

    $qry = "SELECT profile_pic FROM users WHERE user_id = '$id'";
    $res = mysqli_query($conn, $qry);
    
    // Check if there is a matching user
    if(mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $profilePic = $row['profile_pic'];

        // Generate XML output
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        echo '<response>';
        echo '<profile_pic>' . $profilePic . '</profile_pic>';
        echo '</response>';
    }
}
?>
