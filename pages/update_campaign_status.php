<?php
include "../assets/db/conn.php";
// Get the campaign ID and status from the request
$campaignId = $_POST['campaign_id'];
$status = $_POST['status'];





// Check if the connection was successful
if (!$conn) {
  // Connection error, handle the error if needed
  http_response_code(500);
  exit;
}

// Update the campaign status in the database
$query = "UPDATE campaigns SET status = '$status' WHERE camp_id = $campaignId";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
  // Query error, handle the error if needed
  http_response_code(500);
  exit;
}

// Close the database connection
mysqli_close($conn);

// Send a response back to the XHR request
http_response_code(200);
?>
