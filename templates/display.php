<?php
// connect to the database
include '../assets/db/conn.php';

// retrieve the image from the database
$query = "SELECT image FROM images WHERE id = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$image = $row['image'];

// display the image on the web page
echo '<img width="800" height="800" src="data:image/jpeg;base64,'.base64_encode($image).'" />';
?>
