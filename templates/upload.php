<?php
// connect to the database
include '../assets/db/conn.php';

// check if the form has been submitted
if(isset($_POST['submit'])) {
    // get the image file data
    $name = $_FILES['image']['name'];
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    // insert the image into the database
    $query = "INSERT INTO images (name, image) VALUES ('$name', '$image')";
    mysqli_query($conn, $query);

    // display a message to the user
    echo "Image uploaded successfully.";
}
?>


<form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="image">
    <input type="submit" name="submit" value="Upload">
</form>
