<?php
session_start();
include "../assets/db/conn.php";
$email = $_GET['email'];

$e_sql="select * from users where email= '$email'";
$count = mysqli_query($conn,$e_sql);

if (mysqli_num_rows($count) > 0) {
  // Email exists in the database
  echo "exists";
} else {
  // Email doesn't exist in the database
  echo "not_exists";
}

?>
