<?php
session_start();
include "../assets/db/conn.php";
// Check if the campaign ID is provided
if (isset($_GET['id'])) {
    $campaignId = $_GET['id'];
   $recip_id = $_GET['crby'];

    // Check if the action is "stop"
    if (isset($_GET['action']) && $_GET['action'] == 'undefined') {
        //select camp title
        $selcamp = "select camp_title from campaigns where camp_id = $campaignId";
         $campt = mysqli_query($conn,$selcamp);
         $fetchCT = mysqli_fetch_assoc($campt);
        // Update the campaign status to "stopped" in the database
        $updateQuery = "UPDATE campaigns SET status = 'stop' WHERE camp_id = '$campaignId'";
        $result = mysqli_query($conn, $updateQuery);
        //select recipient email
            $eselect = "select email,fname from users where user_id= $recip_id";
            $emailret = mysqli_query($conn,$eselect);
            $fetchE = mysqli_fetch_assoc($emailret);

        if ($result) {
            $subject = "Campaign  Stopped";
            $message = "Hey".$fetchE['fname']." , Your campaign ".$fetchCT['camp_title']."has been stopped successfully";
            $headers = "DonorHub Stopped Your Campaign";
            $to=$fetchE['email'] ;
            mail($to,$subject,$message,$headers);

            echo "<script>alert(' Stopped  the campaign');</script>";
    

       echo '  <script>
      window.location = window.history.back();
   </script>'; 
    
        } else {
            echo "<script>alert('Failed to Stop the campaign');</script>";
            echo '  <script>
            window.location = window.history.back();
         </script>'; 
        }
    }
} else {
    echo "Invalid campaign ID.";
}
if (isset($_GET['id'])) {
    $campaignId = $_GET['id'];

    // Check if the action is "active"
    if (isset($_GET['action']) && $_GET['action'] == 'active') {
          //select camp title
          $selcamp = "select camp_title from campaigns where camp_id = $campaignId";
          $campt = mysqli_query($conn,$selcamp);
          $fetchCT = mysqli_fetch_assoc($campt);
   //select recipient email
             $eselect = "select email,fname from users where user_id=$recip_id";
             $emailret = mysqli_query($conn,$eselect);
             $fetchE = mysqli_fetch_assoc($emailret);
        // Update the campaign status to "stopped" in the database
        $updateQuery = "UPDATE campaigns SET status = 'active' WHERE camp_id = '$campaignId'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $subject = "Campaign  Activated";
            $message = "Hey"." ".$fetchE['fname']." , Your campaign "." ".$fetchCT['camp_title']." "."has been Verified successfully by the Admin";
            $headers = "DonorHub Admin Verified Your Campaign";
            $to=$fetchE['email'] ;
            mail($to,$subject,$message,$headers);
           
            echo "<script>alert(' Activated  the campaign');</script>";
  

       echo '  <script>
      window.location = window.history.back();
   </script>'; 
    
        } else {
            echo "<script>alert('Failed to activate the campaign');</script>";
            echo '  <script>
            window.location = window.history.back();
         </script>'; 
        }
    }
} else {
    echo "Invalid campaign ID.";
}

//Rejected

if (isset($_GET['id'])) {
    $campaignId = $_GET['id'];

    // Check if the action is "active"
    if (isset($_GET['action']) && $_GET['action'] == 'rejected') {
           //select camp title
           $selcamp = "select camp_title from campaigns where camp_id = $campaignId";
           $campt = mysqli_query($conn,$selcamp);
           $fetchCT = mysqli_fetch_assoc($campt);
    //select recipient email
              $eselect = "select email,fname from users where user_id=$recip_id";
              $emailret = mysqli_query($conn,$eselect);
              $fetchE = mysqli_fetch_assoc($emailret);
        // Update the campaign status to "stopped" in the database
        $updateQuery = "UPDATE campaigns SET status = 'rejected' WHERE camp_id = '$campaignId'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result) {
            $subject = "Campaign  Rejected";
            $message = " Dear ".$fetchE['fname'] .",\n\nThank you for submitting your campaign proposal to DonorHub.\n We appreciate your effort and enthusiasm in wanting to make a positive impact on our cause. After careful consideration and evaluation of your proposal, we regret to inform you that we are unable to proceed with your campaign at this time.\nWe want to emphasize that our decision is not a reflection of the value or importance of your cause. We receive numerous campaign proposals and must carefully select those that align most closely with our current objectives and priorities.\nWe encourage you to continue your efforts in support of your cause and explore other avenues for collaboration and funding opportunities. We appreciate your understanding and dedication to making a difference in the community.\nIf you have any further questions or would like feedback on your proposal, please don't hesitate to reply this mail. We're here to support and provide guidance whenever possible.\n\nThank you again for your interest in partnering with us. We wish you all the best in your future endeavors.";
            $headers = "DonorHub Stopped Your Campaign";
            $to=$fetchE['email'] ;
            mail($to,$subject,$message,$headers);
            echo "<script>alert(' Rejected the campaign');</script>";
          

       echo '  <script>
      window.location = window.history.back();
   </script>'; 
    
        } else {
            echo "<script>alert('Failed to Reject the campaign');</script>";
            echo '  <script>
            window.location = window.history.back();
         </script>'; 
        }
    }
} else {
    echo "Invalid campaign ID.";
}
?>
