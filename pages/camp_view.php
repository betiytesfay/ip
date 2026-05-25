<?php
session_start();

include "../assets/db/conn.php";
if (empty(@$_SESSION['d_logged']) && empty(@$_SESSION['d_email'])) {
  header('Location:  log_in.php');
}

$recip_id = $_GET['crby'];
$id = $_GET['id'];



if (isset($_POST['don_sub'])) 
                        {
                      $camp_id = $id;
                     $don_id = $_SESSION['d_id'];
                     $don_amt = $_POST['d_amt'];
  //  $don_amt = !empty($don_amt) ? $don_amt : 0;

                             if (empty($don_amt) || !is_numeric($don_amt) || $don_amt <= 0)
                                                         {
                                                                                 echo '<script>alert("Invalid donation amount.");</script>';
                                                                                 echo '<script>window.location.href = "https://localhost/Dproject/pages/camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                                                 exit;
                                                             }

                                                        $amt = "SELECT amt_collected, est_amt, camp_type FROM campaigns WHERE camp_id = $camp_id";
                                                        $amtres = mysqli_query($conn, $amt);
                                                        $fetchAmt = mysqli_fetch_assoc($amtres);
                                                        $curr_amt = $fetchAmt['amt_collected'];
                                                        $est_amt = $fetchAmt['est_amt'];
                                                        $camp_type = $fetchAmt['camp_type'];
                                                        $isBlood = ($camp_type === 'blood');
                                                       @$_SESSION['amt_new']= $don_amt;
                                                       $remaining_amount = $est_amt -$curr_amt;
                                                       
                                                       // Validation for blood campaigns
                                                       if($isBlood) {
                                                           // Check if donation exceeds 1 pint per person
                                                           if($don_amt > 1) {
                                                               echo '<script>alert("Maximum 1 pint (450-500 ml) per person can be donated.");</script>';
                                                               echo '<script>window.location.href = "https://localhost/Dproject/pages/camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                               exit;
                                                           }
                                                       }
                                                                           
                                                       // Validation for amount-based campaigns
                                                       if($don_amt>$est_amt)
                                                                                            {
                                                                                           echo '<script>alert("Donation ' . ($isBlood ? 'quantity' : 'amount') . ' must be less than ' . ($isBlood ? 'required quantity' : 'estimated amount') . '.");</script>';
                                                                                            echo '<script>window.location.href = "https://localhost/Dproject/pages/camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                                                             exit;
                                                                                               }
                                                                                            if($don_amt>$remaining_amount){
                                                                                             echo '<script>alert("Donation ' . ($isBlood ? 'quantity' : 'amount') . ' must be less than remaining ' . ($isBlood ? 'quantity' : 'amount') . '.");</script>';
                                                                                             echo '<script>window.location.href = "https://localhost/Dproject/pages/camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                                                             exit;
                                                                                            }
                                                                              $updateAmt = $curr_amt + $don_amt;
                                                                              // $left_amt = $est_amt-$curr_amt;
                                                                              $progress = ($updateAmt / $est_amt) * 100;
                                                                              $roundpro = ceil($progress);
                                                                              // Check if the donor has already made a donation to the campaign
                                                                              $chckDon = "SELECT * FROM donations WHERE donor_id = $don_id AND camp_id = $camp_id";
                                                                              $result = mysqli_query($conn, $chckDon);

                                                                                                 if (mysqli_num_rows($result) > 0) {
                                                                                                     // For blood campaigns, prevent multiple donations
                                                                                                     if($isBlood) {
                                                                                                         echo '<script>alert("You have already donated blood for this campaign. Each person can only donate once (1 pint) per blood campaign.");</script>';
                                                                                                         echo '<script>window.location.href = "https://localhost/Dproject/pages/camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                                                                         exit;
                                                                                                     }
                                                                                                     
                                                                                                     // For non-blood campaigns, allow updating donation amount
                                                                                                     $row = mysqli_fetch_assoc($result);
                                                                                                     $existingDonationId = $row['donation_id'];
                                                                                                     $existingDonatedAmount = $row['donated_amt'];
                                                                                                     $newDonatedAmount = $existingDonatedAmount + $don_amt;

                                                                                                     $updateSql = "UPDATE donations SET donated_amt = $newDonatedAmount WHERE donation_id = $existingDonationId";
                                                                                                     $update = mysqli_query($conn, $updateSql);

                                                                                                   if ($update) 
                                                                                                   {
                                                                                                       $updateQuery = "UPDATE campaigns SET amt_collected = $updateAmt, progress = $roundpro WHERE camp_id = $camp_id";
                                                                                                       $updatecamp = mysqli_query($conn, $updateQuery);
     
                                                                                                       if ($updatecamp)
                                                                                                        {
                                                                                                          if($updateAmt==$est_amt){
                                                                                                           $updatests = "UPDATE campaigns SET status='stop' WHERE camp_id = $camp_id";
                                                                                                           $updatesta = mysqli_query($conn, $updatests);
                                                                                                          }
         
         
                                                                                                           sleep(3);

                                                                                                       echo '<script>window.location.href = "https://localhost/Dproject/pages/confirmation.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
                                                                                                     }
                                                                                                }
                                                                                                      }
                                                                                                else 
                                                                                                    {

                                                                                                      $insertSql = "INSERT INTO donations (donor_id, camp_id, donated_amt) VALUES ($don_id, $camp_id, $don_amt)";
                                                                                                      $insert = mysqli_query($conn, $insertSql);
                                                                                                        if ($insert) 
                                                                                                                     {
                                                                                                                          $updateQuery = "UPDATE campaigns SET amt_collected = $updateAmt, progress = $roundpro WHERE camp_id = $camp_id";
                                                                                                                          $updatecamp = mysqli_query($conn, $updateQuery);
                                                                                                                          if ($updatecamp)
                                                                                                                          {
                                                                                                                            if($updateAmt==$est_amt){
                                                                                                                             $updatests = "UPDATE campaigns SET status='stop' WHERE camp_id = $camp_id";
                                                                                                                             $updatesta = mysqli_query($conn, $updatests);
                                                                                                                            }
                              
                                                                                                                              if ($updatecamp) 
                                                                                                                                              {
                                                                                                                                                sleep(3);
                                                                                                                                                echo '<script>window.location.href = "https://localhost/Dproject/pages/confirmation.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';

                                                                                                                                              }

                                                                                                                       }
                                                                                                     }

                                                                                              }
                                                                                            
                        }

// Retrieve campaign details
// $queryCampaign = "SELECT camp_title, start_date, camp_desc, camp_img, est_amt, amt_collected FROM campaigns WHERE camp_id = $id";
$queryCampaign = "SELECT c.recip_id,c.camp_title, c.start_date, c.camp_desc, c.camp_img, c.est_amt, c.amt_collected,c.progress, c.camp_type, c.blood_group, u.fname AS creator_name
FROM campaigns c
JOIN users u ON c.recip_id = u.user_id
WHERE c.camp_id = $id";

$resultCampaign = mysqli_query($conn, $queryCampaign);

if ($resultCampaign && mysqli_num_rows($resultCampaign) > 0) {
  $campaign = mysqli_fetch_assoc($resultCampaign);

  $campTitle = $campaign['camp_title'];
  $startDate = $campaign['start_date'];
  $campDesc = $campaign['camp_desc'];
  $campPic = $campaign['camp_img'];
  $estAmt = $campaign['est_amt'];
  $creator = $campaign['creator_name'];
  $campType = $campaign['camp_type'];
  $bloodGroup = isset($campaign['blood_group']) ? $campaign['blood_group'] : '';

  $amtCollected = $campaign['amt_collected'];
  
  // Calculate progress dynamically based on current values (not stored value)
  // This ensures progress always reflects the actual current state
  if ($estAmt > 0) {
    $progress = ($amtCollected / $estAmt) * 100;
    $progress = round($progress, 2); // Round to 2 decimal places
    // Ensure progress doesn't exceed 100%
    if ($progress > 100) {
      $progress = 100;
    }
  } else {
    $progress = 0;
  }
  
  // Determine if this is a blood campaign
  $isBloodCampaign = ($campType === 'blood');
  
  // Check if current donor has already donated to this campaign (for blood campaigns)
  $hasAlreadyDonated = false;
  if($isBloodCampaign && isset($_SESSION['d_id'])) {
    $donorId = $_SESSION['d_id'];
    $checkDonation = "SELECT * FROM donations WHERE donor_id = $donorId AND camp_id = $id";
    $donationResult = mysqli_query($conn, $checkDonation);
    if($donationResult && mysqli_num_rows($donationResult) > 0) {
      $hasAlreadyDonated = true;
    }
  }
} else {

  //erro
}

// donor info
$queryDonors = "SELECT u.fname, u.profile_pic, d.donated_amt FROM users u INNER JOIN donations d ON u.user_id = d.donor_id WHERE d.camp_id =$id";
$resultDonors = mysqli_query($conn, $queryDonors);
$donors = array();
$dcounts = '0';
if ($resultDonors && mysqli_num_rows($resultDonors) > 0) {
  while ($donor = mysqli_fetch_assoc($resultDonors)) {
    $donors[] = $donor;

  }

  if (isset($donors) && !empty($donors)) {
    $dcounts = count($donors);
  }


} else {
  //false block
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw;
        width: 100%;
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(99, 102, 241, 0.28), transparent 20%),
            linear-gradient(180deg, #0f172a 0%, #111827 50%, #1f2937 100%);
        color: #e2e8f0;
        font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
        padding-top: 88px;
    }

    .navbar {
        background: rgba(15, 23, 42, 0.96) !important;
        backdrop-filter: blur(12px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .navbar .logo-text {
        color: #fff;
        font-weight: 800;
        letter-spacing: 0.01em;
    }

    .navbar .btn-outline-danger {
        border-radius: 999px;
        border-width: 1.5px;
    }

    .container.mt-5 {
        max-width: 1200px;
    }

    .col-lg-8 article {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        color: #0f172a;
    }

    .col-lg-8 h1,
    .col-lg-8 h2 {
        color: #111827;
    }

    .col-lg-8 .text-muted {
        color: #6b7280 !important;
    }

    .col-lg-8 .img-fluid.rounded {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        border-radius: 20px !important;
    }

    .col-lg-4 {
        position: static !important;
        top: auto !important;
        right: auto !important;
        transform: none !important;
    }

    .col-lg-4 .card {
        border: 0;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        color: #0f172a;
    }

    .col-lg-4 .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        color: #111827;
        font-weight: 700;
    }

    .badge.bg-secondary,
    .badge.bg-success,
    .badge.bg-danger {
        border-radius: 999px;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: 0;
        border-radius: 999px;
        font-weight: 700;
    }

    .btn-danger:hover {
        transform: translateY(-1px);
    }

    .modal-content {
        border-radius: 20px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .list-group-item {
        border-radius: 16px;
        margin-bottom: 8px;
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .circular-progress::before {
        background-color: #fff;
    }

    .progress-value {
        color: #7d2ae8;
    }

    @media (max-width: 991.98px) {
        body {
            padding-top: 70px;
        }

        .col-lg-8 article {
            padding: 22px;
        }
    }
</style>
  <!--Bootstrap-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href=" 	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <!--Icon--->
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/navbar.css">
  <title>Donate</title>
</head>

<body class="pt-5">

  <nav class="navbar navbar-expand-md navbar-light bg-white fixed-top shadow rounded-bottom" style="z-index: 1;">
    <div class="container px-1">
      <a class="navbar-brand fw-bold" href="#">
        <img src="../assets/images/logo.png" alt="logo" width="40" height="40" class="img-fluid me-2">
        <span class="logo-text">DonorHub</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="bi bi-list-nested"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav m-auto">

        </ul>
        <ul class="navbar-nav">
          <a href="../pages/d_home.php?section=1" class="btn btn-outline-danger me-1 w-md-1">BACK</a>

        </ul>
      </div>
    </div>
  </nav>
  <section>
    <div class="container mt-5">
      <div class="row">
        <div class="col-lg-8">

          <article>

            <header class="mb-4">

              <h1 class="fw-bolder mb-1">
                <?= $campTitle ?>
              </h1>

              <div class="text-muted fst-italic mb-2">created on,
                <?= $startDate ?>
              </div>

              <a class="badge bg-secondary text-decoration-none link-light" href="#!">Created By:</a>
              <a class="badge bg-success text-decoration-none link-light" href="#!">
                <?= $creator; ?>
              </a>
              <?php if($isBloodCampaign && !empty($bloodGroup)): ?>
              <br><span class="badge bg-danger text-decoration-none link-light mt-2"><i class="bi bi-heart-pulse"></i> Required Blood Group: <?= $bloodGroup; ?></span>
              <?php endif; ?>
            </header>

            <figure class="mb-4"><img class="img-fluid rounded" src="<?= $campPic ?>" alt="..." width="500"
                height="90" /></figure>

            <section class="mb-5">
              <h2 class="fw-bolder mb-4 mt-5">Description</h2>
              <p class="fs-5 mb-4">
                <?= $campDesc ?>
              </p>

            </section>
          </article>
        </div>

        <div class="col-lg-4 " style="  position: fixed;  top: 30%; right: 0; transform: translateY(-50%);">
          <div class="card mb-4">
            <div class="d-flex ">
              <div class="card-header bg-white fw-bold" style=" border:none;"><i class="bi bi-person-hearts me-2"></i>
                Donate</div>
              <div class="card-header bg-white" style="margin-left: 150px; border:none;">

              </div>
              <button type="button" class="btn text-primary" data-bs-toggle="modal" data-bs-target="#DonorsView"
                data-bs-toggle="modal" data-bs-target="#DonorsView" style="border-radius: 20px;"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Show Supporters">
                <u>
                  <?= $dcounts; ?> Donors
                </u>
              </button>



            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-sm-9">
                  <div class="progress_bar d-flex justify-content-between">
                    <div class="circular-progress"
                      style="background: conic-gradient(#7d2ae8 <?php echo $progress ?>%, #ededed 0deg);">
                      <span class="progress-value">
                        <?= $progress . '%'; ?>
                      </span>
                    </div>
                    <div class="info ">
                      <p class="text-body-secondary ">
                        Raised <br>
                        <?php if($isBloodCampaign): ?>
                        <span class="me-2 text-body-dark"><i class="bi bi-droplet"></i><b>
                            <?= $amtCollected; ?> pint<?= $amtCollected != 1 ? 's' : ''; ?>
                          </b></span>
                        of &nbsp;<span class="me-3 text-body-secondary"><i class="bi bi-droplet"></i><b>
                            <?= $estAmt; ?> pint<?= $estAmt != 1 ? 's' : ''; ?>
                          </b></span>
                        <?php else: ?>
                        <span class="me-2 text-body-dark"><i class="bi bi-currency-dollar t"></i><b>
                            <?= $amtCollected; ?>
                          </b></span>
                        of &nbsp;<span class="me-3 text-body-secondary"><i class="bi bi-currency-dollar "></i><b>
                            <?= $estAmt; ?>
                          </b></span>
                        <?php endif; ?>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="donate text-center">
                  <?php if($isBloodCampaign && isset($hasAlreadyDonated) && $hasAlreadyDonated): ?>
                    <button type="button" class="btn btn-secondary text-center mt-4 fw-bolder text-white col-sm-5" disabled style="border-radius: 20px;" title="You have already donated blood for this campaign">
                      Already Donated
                    </button>
                    <p class="text-muted small mt-2">You have already donated blood for this campaign. Each person can only donate once.</p>
                  <?php else: ?>
                    <button type="button" class="btn btn-danger  text-center mt-4 fw-bolder text-white col-sm-5"
                      data-bs-toggle="modal" data-bs-target="#Donate" style="border-radius: 20px;"
                      data-bs-toggle="tooltip" data-bs-placement="top" title="Donate To Campaign">
                      Donate Now
                    </button>
                  <?php endif; ?>
                </div>
              </div>
  </section>
  <div class="modal fade" id="DonorsView" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5"><i class="bi bi-bag-heart-fill me-2"></i><span class="mb-3 fw-bold">Supporters
            </span></h1>

        </div>
        <div class="modal-body">
          <ul class="list-group">
            <?php
            if ($dcounts > 0) {
              if (!empty($donors)) {
                foreach ($donors as $donor): ?>
            <li class="list-group-item d-flex align-items-center mb-2">
              <img src="<?= $donor['profile_pic']; ?>" class="rounded-circle me-3" width="50" height="50"
                alt="Donor Image">
              <div>
                <h5 class="mb-1">
                  <?= $donor['fname']; ?>
                </h5>
                <p class="mb-0">Donated: 
                  <?php if($isBloodCampaign): ?>
                    <i class="bi bi-droplet"></i><?php echo $donor['donated_amt']; ?> pint<?= $donor['donated_amt'] != 1 ? 's' : ''; ?>
                  <?php else: ?>
                    <i class="bi bi-currency-dollar"></i><?php echo $donor['donated_amt']; ?>
                  <?php endif; ?>
                </p>
              </div>
            </li>
            <?php endforeach;
              }
            } else {
              echo '<li class="list-group-item d-flex align-items-center mb-2"> <p>No Donors Found</p?</li>';
            }
            ?>
          </ul>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="Donate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalCenteredScrollableTitle">
            <img src="../assets/images/logo.png" alt="logo" width="40" height="40" class="img-fluid me-2">
            <span class="logo-text">DonorHub</span>
            </a>
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <?php if($isBloodCampaign && isset($hasAlreadyDonated) && $hasAlreadyDonated): ?>
            <div class="alert alert-warning text-center" role="alert">
              <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Already Donated</h5>
              <p>You have already donated blood for this campaign. Each person can only donate once (1 pint) per blood campaign.</p>
              <hr>
              <p class="mb-0">Thank you for your contribution!</p>
            </div>
          <?php else: ?>
          <form id="campaign" class="row g-3 needs-validation d-flex p-1" method="post" novalidate>
            <h3 class="text-center fs-3 fw-bold">Donate</h3>
            <hr class="shadow">

            <div class="mb-3">
              <label for="validationCustom01" class="form-label">Name</label>
              <input type="text" class="form-control" id="validationCustom01" placeholder="Firstname" name="fname"
                value="<?= $_SESSION['d_logged']; ?>" required disabled>
            </div>
            <div class="mb-3">
              <label for="" class="form-label">Email</label>
              <input type="email" class="form-control" id="" placeholder="email" name="email"
                value="<?= $_SESSION['d_email']; ?>" required disabled>
            </div>

            <?php if(isset($isBloodCampaign) && $isBloodCampaign): ?>
            <!-- For blood campaigns, show fixed 1 pint donation -->
            <div class="mb-3">
              <label for="donation_quantity" class="form-label">Quantity (in pints)</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-droplet"></i></span>
                <input type="text" class="form-control" id="donation_quantity" value="1 pint (450-500 ml)" required disabled>
              </div>
              <input type="hidden" name="d_amt" value="1">
            </div>
            <?php else: ?>
            <!-- For non-blood campaigns, show amount input -->
            <div class="mb-3">
              <label for="admin-email" class="form-label" id="donate_label">Amount</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                <input type="number" name="d_amt" id="d_amt" step="1" min="100" max="999999" placeholder="eg.100,500,1000" class="form-control" required>
                <div class="invalid-feedback" id="donate_feedback">
                  Enter Amount To donate
                </div>
              </div>
            </div>
            <?php endif; ?>

              <div class="donate text-center">
                <input type="submit" name="don_sub"
                  class="btn btn-danger  text-center mt-4 fw-bolder text-white col-sm-5" value="Donate"
                  data-bs-toggle="modal" data-bs-target="#Donate" style="border-radius: 20px;" data-bs-toggle="tooltip"
                  data-bs-placement="top" title="Donate To Campaign">
              </div>
          </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Prevent modal from opening if donor has already donated to blood campaign
document.addEventListener('DOMContentLoaded', function() {
  <?php if($isBloodCampaign && isset($hasAlreadyDonated) && $hasAlreadyDonated): ?>
  // Disable the donate modal trigger
  var donateModal = document.getElementById('Donate');
  if(donateModal) {
    donateModal.addEventListener('show.bs.modal', function(event) {
      event.preventDefault();
      event.stopPropagation();
      alert('You have already donated blood for this campaign. Each person can only donate once (1 pint) per blood campaign.');
      return false;
    });
  }
  <?php endif; ?>
  
  // Add form validation to prevent infinite alerts
  var donateForm = document.getElementById('campaign');
  if (donateForm) {
    donateForm.addEventListener('submit', function(event) {
      var amountField = document.getElementById('d_amt');
      if (amountField && !amountField.disabled) {
        var amountValue = amountField.value.trim();
        
        // Check if amount is valid
        if (amountValue === '' || amountValue === null) {
          event.preventDefault();
          event.stopPropagation();
          if (!validationAlertShown) {
            validationAlertShown = true;
            alert('Please enter a donation amount.');
            setTimeout(function() {
              validationAlertShown = false;
            }, 1000);
          }
          return false;
        }
        
        // Check if amount is numeric
        var numericRegex = /^[0-9]+$/;
        if (!numericRegex.test(amountValue)) {
          event.preventDefault();
          event.stopPropagation();
          if (!validationAlertShown) {
            validationAlertShown = true;
            alert('Only numeric inputs are allowed in the Amount field.');
            setTimeout(function() {
              validationAlertShown = false;
            }, 1000);
          }
          return false;
        }
        
        // Check if amount is >= 100
        if (parseInt(amountValue) < 100) {
          event.preventDefault();
          event.stopPropagation();
          if (!validationAlertShown) {
            validationAlertShown = true;
            alert('The Donation Amount must be greater than or equal to 100.');
            setTimeout(function() {
              validationAlertShown = false;
            }, 1000);
          }
          return false;
        }
      }
    });
  }
});

// Flag to prevent multiple alerts
var validationAlertShown = false;

function validateAmountInput(fieldid) {
      // Prevent multiple alerts
      if (validationAlertShown) {
        return;
      }
      
      var textField = document.getElementById(fieldid);
      if (!textField) return;
      
      var inputValue = textField.value.trim();
      
      // If field is empty, don't validate (let HTML5 required handle it)
      if (inputValue === '' || inputValue === null) {
        return;
      }
      
      var numericRegex = /^[0-9]+$/;

      if (!numericRegex.test(inputValue)) {
        // Clear the non-numeric input from the text field
        textField.value = inputValue.replace(/[^0-9]/g, '');
        validationAlertShown = true;
        alert("Only numeric inputs are allowed in the Amount field.");
        // Reset flag after a delay
        setTimeout(function() {
          validationAlertShown = false;
        }, 1000);
        return false;
      } else if (parseInt(inputValue) <= 99) {
        textField.value = "";
        validationAlertShown = true;
        alert("The Donation Amount must be greater than 100 or equal.");
        // Reset flag after a delay
        setTimeout(function() {
          validationAlertShown = false;
        }, 1000);
        return false;
      }
      
      return true;
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

