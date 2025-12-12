
<?php

session_start();
include "../assets/db/conn.php";
if(empty(@ $_SESSION['r_logged']))
{
  header('Location:  index.php');
}
if(isset($_POST['logout'])){
  unset($_SESSION['r_logged']);
  header("Location: log_in.php");
}


//campaign 


if (isset($_POST['r_submit'])) {
  
  // Check if connection is valid
  if (!$conn || !is_object($conn)) {
    $_SESSION['message'] = "Database connection error. Please check your database setup.";
    header("Location: r_home.php");
    exit();
  }

  $d_type = mysqli_real_escape_string($conn, $_POST['d_type']);
  $camp_title = mysqli_real_escape_string($conn, $_POST['camp_title']);
  $camp_desc = mysqli_real_escape_string($conn, $_POST['camp_desc']);
  $est_amt = mysqli_real_escape_string($conn, $_POST['est_amt']);
  $targetDirectory = '../pages/camp_image/';
  $c_image = $targetDirectory . basename($_FILES['c_image']['name']);
  move_uploaded_file($_FILES['c_image']['tmp_name'], $c_image);
  $recip_id = $_SESSION['r_id'];
  
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
 
  $camp_qry = "INSERT INTO campaigns(camp_title,camp_type,camp_desc,camp_img,est_amt,recip_id,status,donation_address,donation_date,donation_time,blood_group) VALUES('$camp_title','$d_type','$camp_desc','$c_image','$est_amt','$recip_id','pending','$donation_address','$donation_date','$donation_time','$blood_group') ";
  $exe = mysqli_query($conn, $camp_qry);
  if ($exe) {

    $_SESSION['message'] = "Campaign Created";
    sleep(2);
    header("Location: r_home.php");

    exit(0);
  } else {
    $_SESSION['message'] = "Error! Campaign Not Created";
    header("Location: r_home.php");
    exit(0);
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
width:270px
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
               <span class="fs-5 fw-bold me-2 mt-3">DonorHub<sup><span class="badge badge text-bg-warning rounded-pill"><small>Recipient</small></span></sup>
              </span></span>
             </a>
             <hr style="color: white;">
             <ul class="nav  flex-column mt-4  " id="menu">
             <li class="nav-item">
    <a href="#section1"  id="l1 "class="nav-link text-white" aria-current="page" onclick="sec1();">
      <i class="bi bi-cloud-plus"></i>
      <span class="ms-2 ">Create Campaign</span>
    </a>
  </li>
  <li class="nav-item">
   <a href="#section2" class="nav-link text-white" aria-current="page" onclick="sec2();">
     <i class="bi bi-check2-circle"></i>
     <span class="ms-2 ">Previous Campaign</span>
   </a>
  </li>
  <li class="nav-item ">
   <a href="#section3" class="nav-link text-white" aria-current="page" onclick="sec3();">
     <i class="bi bi-graph-up-arrow"></i>
     <span class="ms-2 ">Campaign Progress</span>
   </a>
  </li>
  
              
             </ul>
           </div>
           <div class="dropdown">
             <hr class="text-white">
             <button class="btn btn-secondary bg-dark border-0 dropdown-toggle text-center d-flex align-items-center justify-content-between w-100" type="button" id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true"
                 aria-expanded="false" style="padding: 8px 15px;">
             
                   <div class="d-flex align-items-center">
                     <img src="<?= $_SESSION['rprofile_pic'] ?>" class="rounded" alt="profile" width="30" height="30">
                     <span class="ms-3" style="text-transform: uppercase;"><?php echo $_SESSION['r_logged']; ?></span>
                   </div>
         
                </button>
             <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="triggerId">
             <form method="post">
              <a href="../pages/profile_rupdate.php?id=<?= $_SESSION['r_id']; ?>" class="dropdown-item" name="logout">
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
                <label for="validationCustom08" class="form-label">Donation Type</label>
                        <select class="form-select" id="d_type"   name="d_type" required>
                                           <option selected disabled value="">Donation Type</option>
                                           <option name="blood"  value="blood" id="blood ">Blood</option>
                                           <option name="education"  value="education">Education</option>
                                           <option name="health" value="health" >Health</option>
                                           <option name="food" value="food" >Food</option>
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
            <div class="invalid-feedback">Please enter campaign description </div>
          </div>
          
          <div class="col-md-12" id="amount_field">
              <label for="est_amt" class="form-label" id="amount_label">Estimated Amount</label>
              <div class="input-group">
                <span class="input-group-text" id="amount_icon"><i class="bi bi-currency-rupee"></i></span>
                <input type="text" name="est_amt" id="est_amt" maxlength="5" onblur="validateAmountInput('est_amt');" placeholder="eg.100,500,1000" class="form-control" required>
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
            <div class="invalid-feedback">Upload campaign related image </div>
          </div>
          <div class="col-12 d-flex justify-content-between">
            <input  type="submit" class="btn btn-primary" value="Submit"name="r_submit">
            <button class="btn btn-danger " type="reset">Reset</button>
          </div>
        </form>
  

  </section>

<section id="section2" class="section">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php
  $re_i= $_SESSION['r_id'];
      $sql = "SELECT *, blood_group from campaigns where recip_id = '$re_i' order by camp_id desc" ;


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
                  <?php $isBlood = ($row['camp_type'] === 'blood'); ?>
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
                elseif ($row['status']=='rejected') {
                  echo '<span class="text-danger fw-bold">Rejected</span>';
                }
                else {
                  echo '<span class=" fw-bold text-warning">Stopped</span>';
                }
              
              
                ?>
                </small>
              </div>
 <?php

if ($row['status'] == 'active') {
  echo '<a href="" class="btn btn-sm btn-outline-danger" onclick="confirmStop(event, ' . $row['camp_id'] . ');"><i class="bi bi-x-circle m-1"></i>Stop</a>';
}



 if($row['status']=='inactive' || $row['status']=='active' ||$row['status']=='pending')
 {?>      
 <script>
    function confirmStop(event, campaignId) {
        event.preventDefault(); // Prevent the link from being followed immediately

        if (confirm("Do you want to Continue?")) {
            // If confirmed, redirect to the campaign stop page passing the campaign ID
            window.location.href = "../pages/camp_stop.php?id=" + campaignId + "&crby=<?= $re_i; ?>&action=undefined";
        }
    }
</script>
   
              <a href="../pages/camp_r_edit.php?id=<?= $row['camp_id']; ?>&crby=<?=$re_i ; ?>"
                class="btn btn-sm btn-outline-danger" onclick="return confirm('Do you want to Edit');"><i class="bi bi-gear m-1" ></i>Edit</a>
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
<section id="section3" class="section">
<h1 class="text-center fw-bold">Campaign Progress</h1>
<hr class="shadow">
<div class="container">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="text-center">
                    <tr>
                        <th scope="col" >Title</th>
                        
                        <th scope="col">Estimated Amount</th>
                        <th scope="col">Received Amount</th>
                        <th scope="col">Total Donors</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <?php
                    $sql = "SELECT * from campaigns where recip_id = $re_i and (status='active' or status='stop')";
                    $res = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $campaignId = $row['camp_id'];
                            $title = $row['camp_title'];
                           
                            $estimatedAmount = $row['est_amt'];
                            $receivedAmount = $row['amt_collected'];
                            
                            $donorsQuery = "SELECT COUNT(DISTINCT donor_id) AS totalDonors FROM donations WHERE camp_id = '$campaignId'";
                            $donorsResult = mysqli_query($conn, $donorsQuery);
                            $donorsRow = mysqli_fetch_assoc($donorsResult);
                            $totalDonors = $donorsRow['totalDonors'];
                            ?>
                            <tr>
                                <td><?= $title ?></td>
                                <td><?= $estimatedAmount ?></td>
                                <td><?= $receivedAmount ?></td>
                                <td><?= $totalDonors ?></td>
                                <td>
                                    <a href="../pages/donor_rmodal.php?camp_id=<?= $campaignId ?>" role="button"class="btn btn-sm btn-primary">
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

<script src="../pages/selection.js"></script>
</body> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle between Amount and Blood Quantity fields
document.addEventListener('DOMContentLoaded', function() {
  const dTypeSelect = document.getElementById('d_type');
  const amountLabel = document.getElementById('amount_label');
  const amountIcon = document.getElementById('amount_icon');
  const estAmtInput = document.getElementById('est_amt');
  const amountFeedback = document.getElementById('amount_feedback');
  const amountHelp = document.getElementById('amount_help');
  
  if (dTypeSelect && amountLabel && amountIcon && estAmtInput) {
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
        const bloodFields = document.getElementById('blood_donation_fields');
        if (bloodFields) {
          bloodFields.style.display = 'block';
          const bloodGroupField = document.getElementById('blood_group');
          if (bloodGroupField) bloodGroupField.setAttribute('required', 'required');
          document.getElementById('donation_address').setAttribute('required', 'required');
          document.getElementById('donation_date').setAttribute('required', 'required');
          document.getElementById('donation_time').setAttribute('required', 'required');
        }
      } else {
        // Hide blood donation fields
        const bloodFields = document.getElementById('blood_donation_fields');
        if (bloodFields) {
          bloodFields.style.display = 'none';
          const bloodGroupField = document.getElementById('blood_group');
          if (bloodGroupField) bloodGroupField.removeAttribute('required');
          document.getElementById('donation_address').removeAttribute('required');
          document.getElementById('donation_date').removeAttribute('required');
          document.getElementById('donation_time').removeAttribute('required');
        }
        // Show Estimated Amount field
        amountLabel.textContent = 'Estimated Amount';
        amountIcon.innerHTML = '<i class="bi bi-currency-rupee"></i>';
        estAmtInput.placeholder = 'eg.100,500,1000';
        amountFeedback.textContent = 'Enter Amount To donate';
        amountHelp.style.display = 'none';
        estAmtInput.setAttribute('onblur', 'validateAmountInput(\'est_amt\');');
        estAmtInput.setAttribute('maxlength', '5');
      }
    }
    
    dTypeSelect.addEventListener('change', toggleAmountField);
    // Trigger on page load if value is already set
    if (dTypeSelect.value) {
      toggleAmountField();
    }
  }
});
</script>
<script> 
   

    
  for (let i = 0; i <= 3; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 0 ? "block" : "none";
    
  }
  function sec1() {
  for (let i = 0; i <= 3; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 1 ? "block" : "none" ; 
 
  }
}
function sec2() {
  for (let i = 0; i <= 3; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 2 ? "block" : "none";

  }
}
function sec3() {
  for (let i = 0; i <= 3; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 3 ? "block" : "none";

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

