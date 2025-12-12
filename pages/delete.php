<?php
include '../includes/message.php';
include "../assets/db/conn.php";
if (isset($_POST['delete_rec'])) {
    $id = $_POST['delete_rec'];
    $qry = "DELETE FROM users WHERE user_id ='$id'";
    $res = mysqli_query($conn, $qry);
    header("Location: admin.php");
} else {
    $_SESSION['message'] = "No Records To Delete!";

    exit(0);
}

?>