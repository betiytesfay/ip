$query = "SELECT * FROM users WHERE user_type = 'donor'";
$result = mysqli_query($conn, $query);

// Display user information, including the profile picture
while ($row = mysqli_fetch_assoc($result)) {
    echo "<h2>{$row['username']}</h2>";
    echo "<img src='$row["profile_pi"]' alt='Profile Picture'>";
    // Other user details...
}
ert user information into the database
$query = "INSERT INTO users (username, email, password, user_type, profile_pic)
          VALUES ('$username', '$email', '$password', '$user_type', '$profilePic')";
// Execute the query


// Handle the file upload and save the profile picture
$targetDirectory = 'profile_pics/';
$profilePic = $targetDirectory . basename($_FILES['profile_pic']['name']);
move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profilePic);


//working



<?php
  $query = "SELECT * FROM users WHERE user_type = 'donor'";
  $result = mysqli_query($conn, $query);

  // Display user information, including the profile picture
  while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <h2>
      <?php echo $row['fname']; ?>
    </h2>
    <img src='<?php echo $row['profile_pic'] ;?>' width="200" height="100">

    <?php
  }
  ?>