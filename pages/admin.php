
<?php
session_start();
include "../assets/db/conn.php";
if (empty(@$_SESSION['admin_logged'])) {
  header('Location:  index.php');
}
if (isset($_POST['logout'])) {
  unset($_SESSION['admin_logged']);
  header("Location: log_in.php");
}

//create campaign


if (isset($_POST['a_submit'])) {
  
  // Check if connection is valid
  if (!$conn || !is_object($conn)) {
    $_SESSION['message'] = "Database connection error. Please check your database setup.";
    header("Location: admin.php");
    exit();
  }

  $d_type = mysqli_real_escape_string($conn, $_POST['d_type']);
  $camp_title = mysqli_real_escape_string($conn, $_POST['camp_title']);
  $camp_desc = mysqli_real_escape_string($conn, $_POST['camp_desc']);
  $est_amt = mysqli_real_escape_string($conn, $_POST['est_amt']);
  $targetDirectory = '../pages/camp_image/';
  $c_image = $targetDirectory . basename($_FILES['c_image']['name']);
  move_uploaded_file($_FILES['c_image']['tmp_name'], $c_image);
  $recip_id = $_SESSION['a_id'];
  
  // Get blood donation details if it's a blood campaign
  $donation_address = '';
  $donation_date = '';
  $donation_time = '';
  $blood_group = NULL;
  if ($d_type === 'blood') {
    $donation_address = isset($_POST['donation_address']) ? mysqli_real_escape_string($conn, $_POST['donation_address']) : '';
    $donation_date = isset($_POST['donation_date']) ? mysqli_real_escape_string($conn, $_POST['donation_date']) : '';
    $donation_time = isset($_POST['donation_time']) ? mysqli_real_escape_string($conn, $_POST['donation_time']) : '';
    $blood_group = isset($_POST['blood_group']) ? mysqli_real_escape_string($conn, $_POST['blood_group']) : NULL;
  }
 
  $camp_qry = "INSERT INTO campaigns(camp_title,camp_type,camp_desc,camp_img,est_amt,recip_id,status,donation_address,donation_date,donation_time,blood_group) VALUES('$camp_title','$d_type','$camp_desc','$c_image','$est_amt','$recip_id','active','$donation_address','$donation_date','$donation_time','$blood_group') ";
  $exe = mysqli_query($conn, $camp_qry);
  if ($exe) {

    $_SESSION['message'] = "Campaign Created";
    sleep(2);
    header("Location: admin.php");

    exit(0);
  } else {
    $_SESSION['message'] = "Error! Campaign Not Created";
    exit(0);
  }
}

if (isset($_POST['send_mail'])) {
  
  // Include email config and helper
  $email_config_path = '../assets/config/email_config.php';
  $email_helper_path = '../assets/config/email_helper.php';
  
  if (file_exists($email_config_path)) {
      include $email_config_path;
  }
  
  if (file_exists($email_helper_path)) {
      include $email_helper_path;
  }

  if (!isset($_POST['recipient']) || empty($_POST['recipient'])) {
    echo '<script>alert("No donors email selected.");</script>';
    header("Location: redirect.php");
    exit;
  }
$selectedEmails = $_POST['recipient'];
  $subject = "Notification-Recipients are waiting...";
  $message = "Hey,\n\nThank you for your generous donations you have made! We would like to invite you to visit DonorHub, our online platform for connecting donors and recipients. DonorHub provides a unique opportunity to learn about various causes, find new ways to contribute, and stay updated on the impact of your donations.\n\nVisit DonorHub now:\nhttp://localhost/Dproject/pages/index.php#home\n\nWe appreciate your support!\n\nBest regards,\nThe DonorHub Team";

  // Use email helper function if available, otherwise use mail() with proper headers
  $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com';
  $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub';
  
  $successCount = 0;
  $failureCount = 0;

  foreach ($selectedEmails as $email) {
      $to = $email;

      // Send the email using helper function if available
      if (function_exists('send_email_smtp')) {
          $result = @send_email_smtp($to, $subject, $message, $from_email, $from_name);
      } else {
          // Fallback to mail() with proper headers
          $headers = "From: $from_name <$from_email>\r\n";
          $headers .= "Reply-To: $from_email\r\n";
          $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
          $headers .= "X-Mailer: PHP/" . phpversion();
          $result = @mail($to, $subject, $message, $headers);
      }
      
      if ($result) {
          $successCount++;
      } else {
          $failureCount++;
      }
  }

  if ($successCount > 0) {
    $_SESSION['mail_sent'] = true;
      header('Location: success.php');
      
  }

  if ($failureCount > 0) {
      echo '<script>alert("Failed to send mail to ' . $failureCount . ' donor(s).");</script>';
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href=" 	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  
    <style>
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw;
        width: 100%;
    }
    .section{
     
        min-height:100vh;
        margin-left:269px;
        max-width: calc(100vw - 269px);
        overflow-x: hidden;
    }
#section0{
  background-image:url('../assets/images/adm_welcome.jpg');
  background-repeat: no-repeat;
  background-size: 100%;

}
.container{
  margin-left:0 ;
}
.fixed-top{
width:270px;
}
/* .active{
  background-color: gray;
  border-radius: 10px;
} */

    /* Make all campaign cards equal height */
    .row-cols-md-3 .col {
      display: flex;
    }
    
    .row-cols-md-3 .card {
      display: flex;
      flex-direction: column;
      width: 100%;
    }
    
    .row-cols-md-3 .card-body {
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    
    .row-cols-md-3 .card-text {
      min-height: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    </style>
    <script src="../pages/quickstart.js"></script>
    <!--Icon--->
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <title>Donate</title>
</head>
<body>

<div class="container fixed-top">
   <div class="row" >
     <div class="d-flex flex-column justify-content-between col-auto bg-dark min-vh-100 ">
           <div>
             <a href="" class="text-white text-decoration-none d-flex align-items-center ms-4" role="button">
               <img src="../assets/images/logo.png" class="img-fluid me-4 mt-3"alt="logo" width="30" height="30" style="border-radius: 50%;">
               <span class="fs-5 fw-bold me-2 mt-3">DonorHub<sup><span class="badge badge text-bg-warning rounded-pill"><small>Admin</small></span></sup>
              </span></span>
             </a>
             <hr class="mt-3"style="color: white;">
             <ul class="nav  flex-column mt-4  " id="menu">
               <li class="nav-item " >
                 <a href="#section1"  id="l1 "class="nav-link text-white "  onclick="sec1();">
                <i class="bi bi-cloud-plus"></i>
                   <span class="ms-2 ">Create Campaign</span>
                 </a>
               </li>
               <li class="nav-item ">
                <a href="#section8" class="nav-link text-white"  onclick="sec8();">
                  <i class="bi bi-hourglass-split"></i>
                  <span class="ms-2 ">Previous Campaign</span>
                </a>
               </li>
               <?php
               
      $sql = "SELECT * from campaigns where status= 'pending' order by camp_id desc" ;


      $result = mysqli_query($conn, $sql);
      $badge= mysqli_num_rows($result);
               ?>
               <li class="nav-item">
                <a href="#section2" class="nav-link text-white"  onclick="sec2();">
              <i class="bi bi-check2-circle"></i>
                  <span class="ms-2  ">Approve/Reject </span><sup><span class="badge badge text-bg-danger rounded-pill"><small><?=  $badge;?></small></span></sup>
                </a>
               </li>
               <li class="nav-item ">
                <a href="#section3" class="nav-link text-white"  onclick="sec3();">
                  <i class="bi bi-slash-circle"></i>
                  <span class="ms-2 me-2 ">Stop Campaign</span>               
                 </a>
               </li>
               <li class="nav-item ">
                <a href="#section9" class="nav-link text-white"  onclick="sec9();">
                  <i class="bi bi-pause-circle"></i>
                  <span class="ms-2 ">Stopped Campaigns</span>               
                 </a>
               </li>
               <li class="nav-item ">
                <a href="#section4" class="nav-link text-white"  onclick="sec4();">
                  <i class="bi bi-eye"></i>
                  <span class="ms-2 ">View Donors</span>
                </a> </li>
               </li>
               <li class="nav-item ">
                <a href="#section5" class="nav-link text-white" onclick="sec5();">
                  <i class="bi bi-journal-text"></i>
                  <span class="ms-2 ">View Recipient</span>
                </a>
               </li>
               <li class="nav-item ">
                <a href="#section6" class="nav-link text-white"  onclick="sec6();">
                  <i class="bi bi-reception-4"></i>
                  <span class="ms-2 ">View Campaign Progress</span>
                </a>
               </li>
          
               <li class="nav-item ">
                <a href="#section7" class="nav-link text-white" onclick="sec7();">
                  <i class="bi bi-chat-quote"></i>
                  <span class="ms-2 ">Notify Donors</span>
                </a>
               </li>

             </ul>
           </div>
           <div class="dropdown">
             <hr class="text-white">
             <button class="btn btn-secondary bg-dark border-0 dropdown-toggle text-center d-flex align-items-center justify-content-between w-100" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true"
                 aria-expanded="false" style="padding: 8px 15px;">
             
                   <div class="d-flex align-items-center">
                     <img src="<?= $_SESSION['aprofile_pic'] ?>" class="rounded" alt="profile" width="30" height="30">
                     <span class="ms-3" style="text-transform: uppercase;"><?php echo $_SESSION['admin_logged']; ?></span>
                   </div>
         
                </button>
             <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="triggerId">
             <form method="post">
              <a href="../pages/profile_aupdate.php?id=<?= $_SESSION['a_id']; ?>" class="dropdown-item" name="logout">
                  <i class="bi bi-pen me-2"></i>Edit Profile
              </a>
              <hr class="dropdown-divider">
              <button class="dropdown-item" name="logout" id="logout_btn">
                  <i class="bi bi-box-arrow-left me-2"></i>Logout
              </button>
            </form>
             </div>
           </div>
     </div>
   </div>
 </div>

<!---Section1--->

<section id="section0" class="section">

 </section>
<section id="section1" class="section tab-pane">
           <h3 class="text-center fw-bold">Create Campaign</h3>
<hr class="shadow">
<?php include '../includes/message.php'; ?>
<form id="campaign" class="row g-3 needs-validation d-flex p-5" method="post" enctype="multipart/form-data" novalidate>
  <div class="col-md-7">
    <label for="d_type" class="form-label">Donation Type</label>
    <select class="form-select" id="d_type" name="d_type" required>
      <option selected disabled value="">Donation Type</option>
      <option name="blood" value="blood" id="blood">Blood</option>
      <option name="education" value="education">Education</option>
      <option name="health" value="health">Health</option>
      <option name="food" value="food">Food</option>
    </select>
    <div class="invalid-feedback">
      Please select a valid donation type.
    </div>
  </div>
  <br>
  <div class="col-md-7">
    <label for="validationCustom01" class="form-label">Campaign Title</label>
    <input type="text" class="form-control" id="validationCustom01" placeholder="Campaign Title" name="camp_title" required>
    <div class="valid-feedback">
      Title valid!
    </div>
    <div class="invalid-feedback">Please enter campaign Title</div>
  </div>
  <div class="col-md-12">
    <label for="validationCustom02" class="form-label">Campaign Description</label>
    <textarea class="form-control" id="validationCustom02" style="resize:none;" placeholder="Description" name="camp_desc" required rows="7"></textarea>
    <div class="valid-feedback">
      Looks good!
    </div>
    <div class="invalid-feedback">Please enter campaign description</div>
  </div>
  <div class="col-md-12" id="amount_field">
    <label for="est_amt" class="form-label" id="amount_label">Estimated Amount</label>
    <div class="input-group">
      <span class="input-group-text" id="amount_icon"><i class="bi bi-currency-rupee"></i></span>
      <input type="text" name="est_amt" id="est_amt" placeholder="eg.100,500,1000" class="form-control" onblur="validateAmountInput('est_amt');" maxlength="6" required>
    </div>
    <div class="invalid-feedback" id="amount_feedback">
      Enter Amount To donate
    </div>
    <small class="text-muted" id="amount_help" style="display:none;">1 person can donate only 1 pint (450-500 ml) of blood</small>
  </div>
  <div id="blood_donation_fields" class="row" style="display:none;">
    <div class="col-md-6 mb-3">
      <label for="blood_group" class="form-label"><i class="bi bi-heart-pulse"></i> Required Blood Group</label>
      <select class="form-select" id="blood_group" name="blood_group">
        <option selected disabled value="">Select Blood Group</option>
        <option value="O+">O+</option>
        <option value="A+">A+</option>
        <option value="B+">B+</option>
        <option value="AB+">AB+</option>
        <option value="O-">O-</option>
        <option value="A-">A-</option>
        <option value="B-">B-</option>
        <option value="AB-">AB-</option>
      </select>
      <div class="invalid-feedback">Please select blood group</div>
    </div>
    <div class="col-md-12 mb-3">
      <label for="donation_address" class="form-label"><i class="bi bi-geo-alt-fill"></i> Donation Address</label>
      <textarea class="form-control" id="donation_address" name="donation_address" rows="3" placeholder="Enter the address where donors should come to donate blood"></textarea>
      <div class="invalid-feedback">Please enter donation address</div>
    </div>
    <div class="col-md-6 mb-3">
      <label for="donation_date" class="form-label"><i class="bi bi-calendar-event"></i> Donation Date</label>
      <input type="date" class="form-control" id="donation_date" name="donation_date" min="<?php echo date('Y-m-d'); ?>">
      <div class="invalid-feedback">Please select donation date</div>
    </div>
    <div class="col-md-6 mb-3">
      <label for="donation_time" class="form-label"><i class="bi bi-clock"></i> Donation Time</label>
      <input type="time" class="form-control" id="donation_time" name="donation_time">
      <div class="invalid-feedback">Please select donation time</div>
    </div>
  </div>
  <div class="col-md-4">
    <label for="phone" class="form-label">Choose Campaign Related Image</label>
    <div class="mb-3 input-group">
      <span class="input-group-text">
        <i class="bi bi-image-fill "></i>
      </span>
      <input type="file" class="form-control" id="image" name="c_image" accept="image/*" required>
    </div>
    <div class="invalid-feedback">
      Upload campaign related image
    </div>
  </div>
  <div class="col-12 d-flex justify-content-between">
    <input type="submit" class="btn btn-primary" value="Submit" name="a_submit">
    <button class="btn btn-danger" type="reset">Reset</button>
  </div>
</form>
<script>
// Toggle between Amount and Blood Quantity fields
document.addEventListener('DOMContentLoaded', function() {
  const dTypeSelect = document.getElementById('d_type');
  const amountLabel = document.getElementById('amount_label');
  const amountIcon = document.getElementById('amount_icon');
  const estAmtInput = document.getElementById('est_amt');
  const amountFeedback = document.getElementById('amount_feedback');
  const amountHelp = document.getElementById('amount_help');
  
  function toggleAmountField() {
    const selectedType = dTypeSelect.value;
    
      if (selectedType === 'blood') {
        // Show Blood Quantity field
        amountLabel.textContent = 'Quantity of Blood (in pints)';
        amountIcon.innerHTML = '<i class="bi bi-droplet"></i>';
        estAmtInput.placeholder = 'eg. 5, 10, 20 pints';
        amountFeedback.textContent = 'Enter quantity of blood needed in pints';
        amountHelp.style.display = 'block';
        estAmtInput.removeAttribute('onblur');
        estAmtInput.setAttribute('maxlength', '3');
        // Show blood donation address/date/time fields
        document.getElementById('blood_donation_fields').style.display = 'block';
        document.getElementById('donation_address').setAttribute('required', 'required');
        document.getElementById('donation_date').setAttribute('required', 'required');
        document.getElementById('donation_time').setAttribute('required', 'required');
      } else {
        // Hide blood donation fields
        document.getElementById('blood_donation_fields').style.display = 'none';
        document.getElementById('blood_group').removeAttribute('required');
        document.getElementById('donation_address').removeAttribute('required');
        document.getElementById('donation_date').removeAttribute('required');
        document.getElementById('donation_time').removeAttribute('required');
      // Show Estimated Amount field
      amountLabel.textContent = 'Estimated Amount';
      amountIcon.innerHTML = '<i class="bi bi-currency-rupee"></i>';
      estAmtInput.placeholder = 'eg.100,500,1000';
      amountFeedback.textContent = 'Enter Amount To donate';
      amountHelp.style.display = 'none';
      estAmtInput.setAttribute('onblur', 'validateAmountInput(\'est_amt\');');
      estAmtInput.setAttribute('maxlength', '6');
    }
  }
  
  dTypeSelect.addEventListener('change', toggleAmountField);
  // Trigger on page load if value is already set
  if (dTypeSelect.value) {
    toggleAmountField();
  }
});

(function () {
    'use strict';
    window.addEventListener('load', function () {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation');
      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();
  

</script>
  </section>

  <section id="section8" class="section">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php
  $re_i= $_SESSION['a_id'];
      $sql = "SELECT * from campaigns where recip_id = '$re_i' order by camp_id desc" ;


      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0) {
        foreach ($result as $row) {
          $isBlood = ($row['camp_type'] === 'blood');
          
          // Calculate progress dynamically
          $estAmt = $row['est_amt'];
          $amtCollected = $row['amt_collected'];
          if ($estAmt > 0) {
            $progress = ($amtCollected / $estAmt) * 100;
            $progress = round($progress, 2);
            if ($progress > 100) $progress = 100;
          } else {
            $progress = 0;
          }

          ?>
      <div class="col">

        <div class="card shadow-sm mt-2 me-2">
          <img src="<?=$row['camp_img']?>" class="card-img-top" alt="Not_Found" width="70" height="120">
          <div class="card-body">
            <p class="card-text text-center fw-bold">
              <?php echo $row['camp_title']; ?>
            </p>
            <div class="progress_bar d-flex justify-content-between">
              <div class="circular-progress"
                style="background: conic-gradient(#7d2ae8 <?php echo $progress; ?>%, #ededed 0deg);">
                <span class="progress-value">
                  <?= $progress . '%'; ?>
                </span>
              </div>
              <div class="info ">
                <p class="text-body-secondary ">
                  Raised <br>
                  <?php if($isBlood): ?>
                  <span class="me-3"><i class="bi bi-droplet"></i><b>
                      <?= $row['amt_collected'] ?> pint<?= $row['amt_collected'] != 1 ? 's' : ''; ?>
                    </b></span>
                  <?php else: ?>
                  <span class="me-3"><i class="bi bi-currency-rupee "></i><b>
                      <?= $row['amt_collected'] ?>
                    </b></span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <small class="text-body-secondary">Status:
                <?php 
                if ($row['status']=='active')
                  {
echo '<span class="text-success fw-bold">Active</span>';
                }
                elseif ($row['status']=='inactive') {
                  echo '<span class="text-danger fw-bold">Inactive</span>';
                }
                elseif ($row['status']=='pending') {
                  echo '<span class="text-primary fw-bold">Pending</span>';
                }
                else {
                  echo '<span class=" fw-bold text-warning">Stopped</span>';
                }
              
              
                ?>
                </small>
              </div>
 <?php
$stop='stop';
if ($row['status'] == 'active') {
  echo '<a href="" class="btn btn-sm btn-outline-danger" onclick="confirmStop(event, '. $row['camp_id'] .','.$re_i .');"><i class="bi bi-x-circle m-1"></i>Stop</a>';
}



 if($row['status']=='inactive' || $row['status']=='active' ||$row['status']=='pending')
 {?>      
  <script>
    function confirmStop(event, campaignId,recip) {
        event.preventDefault(); // Prevent the link from being followed immediately

        if (confirm("Do you want to Continue?")) {
            // If confirmed, redirect to the campaign stop page passing the campaign ID
            window.location.href = "../pages/camp_stop.php?id="+ campaignId + "&crby="+ recip +"&action=stop";
        }
    }
</script>
   
              <a href="../pages/camp_a_edit.php?id=<?= $row['camp_id']; ?>&crby=<?=$_SESSION['a_id']; ?>"
                class="btn btn-sm btn-outline-danger" onclick="return confirm('Do you want to Edit');"><i class="bi bi-gear m-1" ></i>Edit </a>
                
                <?php 
                
 }
?>
            </div>
          </div>
        </div>
      </div>
      <?php
        }
      } else {
        echo '<div class="col-12 d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
                <div class="text-center">
                  <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                  <p class="mt-3 text-muted fs-5 mb-1">No campaigns available at the moment</p>
                  <p class="text-muted small">Check back later for new campaigns</p>
                </div>
              </div>';
      }
    
      ?>
    </div>
 </section>
<section id="section2" class="section">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php
      $sql = "SELECT * from campaigns where status= 'pending' order by camp_id desc" ;


      $result = mysqli_query($conn, $sql);
      $badge= mysqli_num_rows($result);
      if (mysqli_num_rows($result) > 0) {
        foreach ($result as $row) {
          $isBlood = ($row['camp_type'] === 'blood');
          
          // Calculate progress dynamically
          $estAmt = $row['est_amt'];
          $amtCollected = $row['amt_collected'];
          if ($estAmt > 0) {
            $progress = ($amtCollected / $estAmt) * 100;
            $progress = round($progress, 2);
            if ($progress > 100) $progress = 100;
          } else {
            $progress = 0;
          }

          ?>
      <div class="col">

        <div class="card shadow-sm mt-2 me-2">
          <img src="<?= $row['camp_img']?>" class="card-img-top" alt="Not_Found" width="70" height="120">
          <div class="card-body">
            <p class="card-text text-center fw-bold">
              <?php echo $row['camp_title']; ?>
              <?php if($isBlood && !empty($row['blood_group'])): ?>
                <br><small class="text-danger"><i class="bi bi-heart-pulse"></i> Blood Group: <?= $row['blood_group']; ?></small>
              <?php endif; ?>
            </p>
            <div class="progress_bar d-flex justify-content-between">
              <div class="circular-progress"
                style="background: conic-gradient(#7d2ae8 <?php echo $progress; ?>%, #ededed 0deg);">
                <span class="progress-value">
                  <?= $progress . '%'; ?>
                </span>
              </div>
              <div class="info ">
                <p class="text-body-secondary ">
                  Raised <br>
                  <?php if($isBlood): ?>
                  <span class="me-3"><i class="bi bi-droplet"></i><b>
                      <?= $row['amt_collected'] ?> pint<?= $row['amt_collected'] != 1 ? 's' : ''; ?>
                    </b></span>
                  <?php else: ?>
                  <span class="me-3"><i class="bi bi-currency-rupee "></i><b>
                      <?= $row['amt_collected'] ?>
                    </b></span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <small class="text-body-secondary">Status:
                <?php 
                if ($row['status']=='active')
                  {
echo '<span class="text-success fw-bold">Active</span>';
                }
                elseif ($row['status']=='inactive') {
                  echo '<span class="text-danger fw-bold">Inactive</span>';
                }
                elseif ($row['status']=='pending') {
                  echo '<span class="text-primary fw-bold">Pending</span>';
                }
                else {
                  echo '<span class=" fw-bold text-warning">Stopped</span>';
                }
              
              
                ?>
                </small>
              </div>
 
             <a href="" class="btn btn-sm btn-outline-success" onclick="confirmStop(event,<?= $row['camp_id'];?>,<?=$row['recip_id'];?>,'active' );"><i class="bi bi-check-lg m-1"></i>Active</a>
              <a href="" class="btn btn-sm btn-outline-danger" onclick="confirmStop(event,<?=  $row['camp_id'];?>,<?=$row['recip_id'];?>,'rejected');"><i class="bi bi-x m-1"></i>Reject</a>

          </div>
          </div>
        </div>
      </div>
      <?php
        }
      } else {
        echo '<div class="col-12 d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
                <div class="text-center">
                  <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                  <p class="mt-3 text-muted fs-5 mb-1">No campaigns available at the moment</p>
                  <p class="text-muted small">Check back later for new campaigns</p>
                </div>
              </div>';
      }
    
      ?>
    </div>   
    
    <script>
    function confirmStop(event, campaignId,recipId,stat) {
        event.preventDefault(); // Prevent the link from being followed immediately

        if (confirm("Do you want to continue?")) {
            // If confirmed, redirect to the campaign stop page passing the campaign ID
            window.location.href = "../pages/camp_stop.php?id=" + campaignId + "&crby="+recipId +"&action="+ stat +"";
        }
    }
</script>        
 </section>


<section id="section3" class="section">

<hr class="shadow mt-1">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php

      $sql = "SELECT c.camp_id,c.camp_title, c.camp_desc,c.camp_img, c.est_amt, c.amt_collected,c.progress,c.camp_type,c.status,c.blood_group,u.user_id, u.fname
            FROM campaigns AS c
            JOIN users AS u ON c.recip_id = u.user_id and c.status='active' and u.user_type='recipient' ";

      // $query = "SELECT * FROM campaigns where status='active'";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0) {
        foreach ($result as $row) {
          $isBlood = ($row['camp_type'] === 'blood');
          
          // Calculate progress dynamically
          $estAmt = $row['est_amt'];
          $amtCollected = $row['amt_collected'];
          if ($estAmt > 0) {
            $progress = ($amtCollected / $estAmt) * 100;
            $progress = round($progress, 2);
            if ($progress > 100) $progress = 100;
          } else {
            $progress = 0;
          }

          ?>
      <div class="col">
        
        <div class="card shadow-sm mt-2 me-2 ms-2">
        <div class="card-header text-body-secondary">Type:<?=" ". $row['camp_type'];?><?php if($isBlood && !empty($row['blood_group'])): ?> | <i class="bi bi-heart-pulse"></i> Blood Group: <?= $row['blood_group']; ?><?php endif; ?></div>
          <img src="<?= $row['camp_img']; ?>" class="card-img-top" alt="Not_Found" width="70" height="120">
          <div class="card-body">
            <p class="card-text text-center fw-bold">
              <?php echo $row['camp_title']; ?>
            </p>
            <div class="progress_bar d-flex justify-content-between">
              <div class="circular-progress"
                style="background: conic-gradient(#7d2ae8 <?php echo $progress; ?>%, #ededed 0deg);">
                <span class="progress-value">
                  <?= $progress . '%'; ?>
                </span>
              </div>
              <div class="info ">
                <p class="text-body-secondary ">
                  Raised <br>
                  <?php if($isBlood): ?>
                  <span class="me-3"><i class="bi bi-droplet"></i><b>
                      <?= $row['amt_collected'] ?> pint<?= $row['amt_collected'] != 1 ? 's' : ''; ?>
                    </b></span>
                  <?php else: ?>
                  <span class="me-3"><i class="bi bi-currency-rupee "></i><b>
                      <?= $row['amt_collected'] ?>
                    </b></span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <small class="text-body-secondary">Created by :
                  <?= $row['fname']; ?>
                </small>
              </div>
              <div class="form-check form-switch">
  <input class="form-check-input toggle-status" type="checkbox" id="toggle-<?php echo $row['camp_id']; ?>" <?php echo ($row['status'] == 'active') ? 'checked' : ''; ?>>
  <label class="form-check-label" for="toggle-<?php echo $row['camp_id']; ?>">
    Status: <b><span id="status-<?php echo $row['camp_id']; ?>"><?php echo ($row['status'] == 'active') ? 'Active' : 'Stopped'; ?></span></b>
  </label>
</div>


            </div>
          </div>
        </div>
      </div>
      <?php
        }
      } else {
        echo '<div class="col-12 d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
                <div class="text-center">
                  <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                  <p class="mt-3 text-muted fs-5 mb-1">No campaigns available at the moment</p>
                  <p class="text-muted small">Check back later for new campaigns</p>
                </div>
              </div>';
      }

      ?>
      <script>
  // Function to handle toggle switch change event
  function handleToggleStatus(event) {
    var toggle = event.target;
    var campaignId = toggle.id.split('-')[1];
    var status = toggle.checked ? 'active' : 'stop';

    // Create an XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Set up the request
    xhr.open('POST', 'update_campaign_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Set up the callback function for the AJAX request
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Update the status text
          var statusSpan = document.getElementById('status-' + campaignId);
          if (statusSpan) {
            statusSpan.textContent = toggle.checked ? 'Active' : 'Stopped';
          }
          // Reload the page to reflect changes in both sections
          location.reload();
        } else {
          // Request failed, revert the toggle
          toggle.checked = !toggle.checked;
        }
      }
    };

    // Send the request with campaign ID and status as parameters
    xhr.send('campaign_id=' + campaignId + '&status=' + status);
  }

  // Add event listeners to the toggle switches
  var toggleSwitches = document.querySelectorAll('.toggle-status');
  toggleSwitches.forEach(function (toggle) {
    toggle.addEventListener('change', handleToggleStatus);
  });
</script>

    </div>                
          
  </section>
<section id="section4" class="section" style="z-index: 99;">
<h1 class="text-center fw-bold">Donor</h1>
    <hr class="shadow">
    <div class="container">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Place</th>
                        <th scope="col">Employement</th>
                        <th scope="col">Profile</th>
                        <th scope="col">Donations</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <?php
                    $sql = "select * from users where user_type='donor'";
                    $res = mysqli_query($conn, $sql);
                    $id = 1;
                    if (mysqli_num_rows($res) > 0) {
                      foreach ($res as $row) {
                        ?>
                            <tr>
                                <td><?= $id; ?></td>
                                <td><?= $row['fname'] . ' ' . $row['lname']; ?></td>
                                <td><?= $row['email']; ?></td>
                                <td><?= $row['phone']; ?></td>
                                <td><?= $row['place']; ?></td>
                                <td><?= $row['emp_status']; ?></td>
                                            <td>
    <img src="<?= $row['profile_pic']; ?>" alt="profile" width="60" height="50" data-bs-toggle="modal" data-bs-target="#myModal<?= $row['user_id']; ?>" onclick="process(<?= $row['user_id']; ?>);">
</td>
<!-- Modal -->
            <div class="modal fade" id="myModal<?= $row['user_id']; ?>" tabindex="-1" style="width:250px;">
                <div class="modal-dialog modal-lg modal-dialog-centered" style="position: fixed; top: 50%; left: 50%;transform: translate(-50%, -50%);">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">PROFILE</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="<?= $row['profile_pic']; ?>" class="img-fluid" width="200" height="200" id="modal-image<?= $row['user_id']; ?>">
                        </div>
                    </div>
                </div>
            </div>
                                           <td>
                                           <a href="../pages/donor_hist.php?do_id=<?= $row['user_id']; ?>" role="button"class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                        </a>

                                            </td>
                                        </tr>
                                <?php
                                $id = $id + 1;
                      }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


  </section>

<section id="section5" class="section" >
  <h1 class="text-center fw-bold">Recipient</h1>
    <hr class="shadow">
    <div class="container">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Place</th>
                        <th scope="col">Employement</th>
                        <th scope="col">Profile</th>
                        <th scope="col">Campaigns</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    $sql = "select * from users where user_type='recipient'";
                    $res = mysqli_query($conn, $sql);
                    $id = 1;
                    if (mysqli_num_rows($res) > 0) {
                      foreach ($res as $row) {
                        ?>
                                        <tr>
                                            <td><?= $id; ?></td>
                                            <td><?= $row['fname'] . ' ' . $row['lname']; ?></td>
                                            <td><?= $row['email']; ?></td>
                                            <td><?= $row['phone']; ?></td>
                                            <td><?= $row['place']; ?></td>
                                            <td><?= $row['emp_status']; ?></td>
                                            <td>
                <img src="<?= $row['profile_pic']; ?>" alt="profile" width="60" height="50" data-bs-toggle="modal" data-bs-target="#rModal<?= $row['user_id']; ?>" onclick="process(<?= $row['user_id']; ?>);">
            </td>
            <!-- Modal -->
            <div class="modal fade" id="rModal<?= $row['user_id']; ?>" tabindex="-1" style="width:250px;">
                <div class="modal-dialog modal-lg modal-dialog-centered" style="position: fixed; top: 50%; left: 50%;transform: translate(-50%, -50%);">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">PROFILE</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="<?= $row['profile_pic']; ?>" class="img-fluid" width="200" height="200" id="modal-image<?= $row['user_id']; ?>">
                        </div>
                    </div>
                </div>
            </div>
                                           <td>
                                           <a href="../pages/recip_hist.php?do_id=<?= $row['user_id']; ?>" role="button"class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                        </a>

                                            </td>
                                        </tr>
                                <?php
                                $id = $id + 1;
                      }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


</section>

<section id="section6" class="section">
<h1 class="text-center fw-bold">Campaign Progress</h1>
<hr class="shadow">
<div class="container">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" >Title</th>
                        <th scope="col">Created By</th>
                        <th scope="col" id="est_header">Target</th>
                        <th scope="col" id="recv_header">Received</th>
                        <th scope="col">Total Donors</th>
                        <th scope="col">View Donors</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <?php
                    $sql = "SELECT c.*, u.fname, u.lname
                            FROM campaigns c
                            JOIN users u ON c.recip_id = u.user_id
                            WHERE c.status = 'active' or c.status='stop' ";
                    $res = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $campaignId = $row['camp_id'];
                            $title = $row['camp_title'];
                            $createdBy = $row['fname'] . ' ' . $row['lname'];
                            $estimatedAmount = $row['est_amt'];
                            $receivedAmount = $row['amt_collected'];
                            $campType = $row['camp_type'];
                            $isBlood = ($campType === 'blood');
                            
                            $donorsQuery = "SELECT COUNT(DISTINCT donor_id) AS totalDonors FROM donations WHERE camp_id = '$campaignId'";
                            $donorsResult = mysqli_query($conn, $donorsQuery);
                            $donorsRow = mysqli_fetch_assoc($donorsResult);
                            $totalDonors = $donorsRow['totalDonors'];
                            ?>
                            <tr>
                                <td><?= $title ?></td>
                                <td><?= $createdBy ?></td>
                                <td><?php if($isBlood): ?><i class="bi bi-droplet"></i><?= $estimatedAmount ?> pint<?= $estimatedAmount != 1 ? 's' : ''; ?><?php else: ?><i class="bi bi-currency-rupee"></i><?= $estimatedAmount ?><?php endif; ?></td>
                                <td><?php if($isBlood): ?><i class="bi bi-droplet"></i><?= $receivedAmount ?> pint<?= $receivedAmount != 1 ? 's' : ''; ?><?php else: ?><i class="bi bi-currency-rupee"></i><?= $receivedAmount ?><?php endif; ?></td>
                                <td><?= $totalDonors ?></td>
                                <td>
                                    <a href="../pages/donor_amodal.php?camp_id=<?= $campaignId ?>" role="button"class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                        </a>
                           
                                </td>
                            </tr>
                            
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4'><i class='bi bi-inbox' style='font-size: 2rem; color: #6c757d; opacity: 0.5;'></i><p class='mt-2 text-muted mb-0'>No active campaigns found.</p></td></tr>";
                    }
                  
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

<section id="section7" class="section">
<div class="container mt-5">
        <h3 class="text-center mb-4">Notify Donors</h3>
          <hr class="shadow">
       
        <?php
        // Check if the connection was successful
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

      
        $query = "SELECT fname,lname,email FROM users WHERE user_type='donor'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Error fetching donor emails: " . mysqli_error($conn));
        }
        ?>

        <form method="post">

          
        <div class="flex-md-row p-4 gap-4 py-md-5 align-items-center justify-content-center">
        <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $email = $row['email'];
                    $fullname = $row['fname'].' '.  $row['lname'];
                    ?> 
                    <div class="list-group">
    <label class="list-group-item d-flex gap-2 mt-2">
      <input class="form-check-input flex-shrink-0" type="checkbox" name="recipient[]" id="check_<?= $email ?>" value="<?= $email ?>" >
      <span>
      <?= $fullname ?>
        <small class="d-block text-body-secondary"><?= $email ?></small>
      </span>
    </label>
        </div>  
          <?php
                }
              
                ?>
        </div>
        <div class="mt-4 text-center">
                <button class="btn btn-primary" name="send_mail" type="submit">Send Email</button>
            </div>
        </form>       
</div>

</section>

<section id="section9" class="section">

<hr class="shadow mt-1">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php

      $sql = "SELECT c.camp_id,c.camp_title, c.camp_desc,c.camp_img, c.est_amt, c.amt_collected,c.progress,c.camp_type,c.status,c.blood_group,u.user_id, u.fname
            FROM campaigns AS c
            JOIN users AS u ON c.recip_id = u.user_id and c.status='stop' and u.user_type='recipient' ";

      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0) {
        foreach ($result as $row) {
          $isBlood = ($row['camp_type'] === 'blood');
          
          // Calculate progress dynamically
          $estAmt = $row['est_amt'];
          $amtCollected = $row['amt_collected'];
          if ($estAmt > 0) {
            $progress = ($amtCollected / $estAmt) * 100;
            $progress = round($progress, 2);
            if ($progress > 100) $progress = 100;
          } else {
            $progress = 0;
          }

          ?>
      <div class="col">
        
        <div class="card shadow-sm mt-2 me-2 ms-2">
        <div class="card-header text-body-secondary">Type:<?=" ". $row['camp_type'];?><?php if($isBlood && !empty($row['blood_group'])): ?> | <i class="bi bi-heart-pulse"></i> Blood Group: <?= $row['blood_group']; ?><?php endif; ?></div>
          <img src="<?= $row['camp_img']; ?>" class="card-img-top" alt="Not_Found" width="70" height="120">
          <div class="card-body">
            <p class="card-text text-center fw-bold">
              <?php echo $row['camp_title']; ?>
            </p>
            <div class="progress_bar d-flex justify-content-between">
              <div class="circular-progress"
                style="background: conic-gradient(#7d2ae8 <?php echo $progress; ?>%, #ededed 0deg);">
                <span class="progress-value">
                  <?= $progress . '%'; ?>
                </span>
              </div>
              <div class="info ">
                <p class="text-body-secondary ">
                  Raised <br>
                  <?php if($isBlood): ?>
                  <span class="me-3"><i class="bi bi-droplet"></i><b>
                      <?= $row['amt_collected'] ?> pint<?= $row['amt_collected'] != 1 ? 's' : ''; ?>
                    </b></span>
                  <?php else: ?>
                  <span class="me-3"><i class="bi bi-currency-rupee "></i><b>
                      <?= $row['amt_collected'] ?>
                    </b></span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
              <div class="btn-group">
                <small class="text-body-secondary">Created by :
                  <?= $row['fname']; ?>
                </small>
              </div>
              <div class="form-check form-switch">
  <input class="form-check-input toggle-status-stopped" type="checkbox" id="toggle-stopped-<?php echo $row['camp_id']; ?>" <?php echo ($row['status'] == 'active') ? 'checked' : ''; ?>>
  <label class="form-check-label" for="toggle-stopped-<?php echo $row['camp_id']; ?>">
    Status: <b><span id="status-stopped-<?php echo $row['camp_id']; ?>"><?php echo ($row['status'] == 'active') ? 'Active' : 'Stopped'; ?></span></b>
  </label>
</div>


            </div>
          </div>
        </div>
      </div>
      <?php
        }
      } else {
        echo '<div class="col-12 d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
                <div class="text-center">
                  <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                  <p class="mt-3 text-muted fs-5 mb-1">No campaigns available at the moment</p>
                  <p class="text-muted small">Check back later for new campaigns</p>
                </div>
              </div>';
      }

      ?>
      <script>
  // Function to handle toggle switch change event for stopped campaigns
  function handleToggleStatusStopped(event) {
    var toggle = event.target;
    var campaignId = toggle.id.split('-')[2]; // Changed to [2] because id is now "toggle-stopped-{id}"
    var status = toggle.checked ? 'active' : 'stop';

    // Create an XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Set up the request
    xhr.open('POST', 'update_campaign_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Set up the callback function for the AJAX request
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Update the status text
          var statusSpan = document.getElementById('status-stopped-' + campaignId);
          if (statusSpan) {
            statusSpan.textContent = toggle.checked ? 'Active' : 'Stopped';
          }
          // Reload the page to reflect changes
          location.reload();
        } else {
          // Request failed, revert the toggle
          toggle.checked = !toggle.checked;
        }
      }
    };

    // Send the request with campaign ID and status as parameters
    xhr.send('campaign_id=' + campaignId + '&status=' + status);
  }

  // Add event listeners to the toggle switches for stopped campaigns
  var toggleSwitchesStopped = document.querySelectorAll('.toggle-status-stopped');
  toggleSwitchesStopped.forEach(function (toggle) {
    toggle.addEventListener('change', handleToggleStatusStopped);
  });
</script>

    </div>                
          
  </section>

<script src="../pages/selection.js"></script>
</body> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script> 
   
 
    
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 0 ? "block" : "none";
    
  }
  function sec1() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 1 ? "block" : "none" ; 
 
  }
}
function sec2() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 2 ? "block" : "none";

  }
}
function sec3() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 3 ? "block" : "none";

  }
}
function sec4() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 4 ? "block" : "none";
  }
}
function sec5() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 5 ? "block" : "none";
  }
}
function sec6() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 6 ? "block" : "none";
  }
}
function sec7() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 7 ? "block" : "none";
  }
}
function sec8() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 8 ? "block" : "none";
  }
}
function sec9() {
  for (let i = 0; i <= 9; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 9 ? "block" : "none";
  }
}

  (() => {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })
    })();
    function validateAmountInput(fieldid) {
      var textField = document.getElementById(fieldid);
      var inputValue = textField.value;
      var numericRegex = /^[0-9]+$/;

      if (!numericRegex.test(inputValue)) {
        // Clear the non-numeric input from the text field
        textField.value = inputValue.replace(/[^0-9]/g, '');
        textField.focus();
        event.preventDefault();

        alert("Only numeric inputs are allowed in the Amount field.");
      } else if (inputValue<=99) 
      {
        textField.focus();
        textField.value = "";
        alert("The Amount must be greater than 100 or equal.");

      }
    }
  
</script>
<style>
  .circular-progress {
    position: relative;
    height: 70px;
    /* Decreased height */
    width: 70px;
    /* Decreased width */
    border-radius: 50%;
    background: conic-gradient(#7d2ae8 3.6deg, #ededed 0deg);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .circular-progress::before {
    content: "";
    position: absolute;
    height: 50px;
    /* Decreased height */
    width: 50px;
    /* Decreased width */
    border-radius: 50%;
    background-color: #fff;
  }

  .progress-value {
    position: relative;
    font-size: 16px;
    /* Decreased font size */
    font-weight: 600;
    color: #7d2ae8;
  }

  .text {
    font-size: 12px;
    /* Decreased font size */
    font-weight: 500;
    color: #606060;
  }
</style>
    </html>
