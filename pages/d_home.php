<?php
session_start();
$d_type = $_SESSION['d_type'];
include "../assets/db/conn.php";
if (empty(@$_SESSION['d_logged'])) {
  header('Location:  index.php');
}
if (isset($_POST['logout'])) {
  
  unset($_SESSION['d_logged']);
  header("Location: log_in.php");
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
    .section {

      min-height: 100vh;
      margin-left: 269px;
      max-width: calc(100vw - 269px);
      overflow-x: hidden;
    }

    #section0 {
      background-image: url('../assets/images/adm_welcome.jpg');
      background-repeat: no-repeat;
      background-size: 100%;

    }

    .container {
      margin-left: 0;
    }

    .fixed-top {
      width: 270px
    }

    /* .active{
  background-color: gray;
  border-radius: 10px;
} */
    .card-container {
      display: flex;
      flex-direction: row;
      gap: 20px;


    }
    
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
    <div class="row">
      <div class="d-flex flex-column justify-content-between col-auto bg-dark min-vh-100 ">
        <div>
          <a href="" class="text-white text-decoration-none d-flex align-items-center ms-4" role="button">
            <img src="../assets/images/logo.png" class="img-fluid me-4 mt-3" alt="logo" width="30" height="30"
              style="border-radius: 50%;">
            <span class="fs-5 fw-bold me-2 mt-3">DonorHub <sup><span class="badge badge text-bg-warning rounded-pill"><small>Donor</small></span></sup>
              </span></li></span>
          </a>
          <hr style="color: white;">
          <ul class="nav  flex-column mt-4  " id="menu">
            <li class="nav-item">
              <a href="#section1" id="l1 " class="nav-link text-white" aria-current="page" onclick="sec1();">
                <i class="bi bi-grid-fill"></i>
                <span class="ms-2 ">Available Campaign</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#section2" class="nav-link text-white" aria-current="page" onclick="sec2();">
                <i class="bi bi-exclamation-diamond"></i>
                <span class="ms-2 ">Emergency Campaign</span>
              </a>
            </li>
            <li class="nav-item ">
              <a href="#section3" class="nav-link text-white" aria-current="page" onclick="sec3();">
                <i class="bi bi-clipboard-data"></i>
                <span class="ms-2 " >History</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="dropdown">
          <hr class="text-white">
          <button class="btn btn-secondary bg-dark border-0 dropdown-toggle text-center d-flex align-items-center justify-content-between w-100" type="button" id="triggerId"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 8px 15px;">

            <div class="d-flex align-items-center">
              <img src="<?= $_SESSION['dprofile_pic'] ?>" class="rounded" alt="profile"
                  width="30" height="30">
              <span class="ms-3" style="text-transform: uppercase;">
                <?php echo $_SESSION['d_logged'].' '. '<sup><span class="badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill">'.$d_type.'</span></sup>'; ?>
              </span>
            </div>

          </button>
          <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="triggerId">
            <form method="post">
              <a href="../pages/profile_dupdate.php?id=<?= $_SESSION['d_id']; ?>" class="dropdown-item"
                  name="logout">
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
  <?php include '../includes/message.php'; ?>
  <section id="section0" class="section">

  </section>
  <section id="section1" class="section ">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
      <?php
      $sql = "SELECT c.camp_id,c.camp_title, c.camp_desc,c.camp_img, c.est_amt, c.amt_collected,c.progress,c.camp_type,c.blood_group,u.user_id, u.fname
            FROM campaigns AS c
            JOIN users AS u ON c.recip_id = u.user_id WHERE c.status='active' AND u.user_type='recipient'";
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

        <div class="card shadow-sm mt-2 me-2">
          <img src="<?php echo $row['camp_img'];?>" class="card-img-top" alt="Not_Found" width="70" height="120">
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
                <small class="text-body-secondary">Created by :
                  <?= $row['fname']; ?>
                </small>
              </div>

              <a href="../pages/camp_view.php?id=<?= $row['camp_id']; ?>&crby=<?= $row['user_id']; ?>"
                class="btn btn-sm btn-outline-success">READ MORE</a>

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

      $sql = "SELECT c.camp_id,c.camp_title, c.camp_desc, c.camp_img,c.est_amt,c.amt_collected,c.progress,c.camp_type,c.blood_group, u.user_id, u.fname
            FROM campaigns AS c
            JOIN users AS u ON c.recip_id = u.user_id and c.status='active' and u.user_type='admin'";


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

              <a href="../pages/camp_view.php?id=<?= $row['camp_id']; ?>&crby=<?= $row['user_id']; ?>"
                class="btn btn-sm btn-outline-success">READ MORE</a>


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
    <?php
    $donorId = $_SESSION['d_id'];


    $query = "SELECT c.camp_title, c.camp_type, d.donated_amt
          FROM donations d
          INNER JOIN campaigns c ON c.camp_id = d.camp_id
          WHERE d.donor_id = $donorId";


    $result = mysqli_query($conn, $query);
    $donations = mysqli_fetch_all($result, MYSQLI_ASSOC);


    ?>
    <div class="my-1 p-2 bg-body rounded shadow-sm col-sm-6">
      <h6 class="border-bottom pb-2 mb-0">History</h6>
      <div class="list-group">
        <?php foreach ($donations as $donation): 
          $isBlood = (isset($donation['camp_type']) && $donation['camp_type'] === 'blood');
        ?>
        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
          <span>
            <?php echo $donation['camp_title']; ?>
          </span>
          <span>
            <?php if($isBlood): ?>
              <i class="bi bi-droplet"></i><?php echo $donation['donated_amt']; ?> pint<?= $donation['donated_amt'] != 1 ? 's' : ''; ?>
            <?php else: ?>
              <i class="bi bi-currency-rupee"></i><?php echo $donation['donated_amt']; ?>
            <?php endif; ?>
          </span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>


  </section>
  <script src="../pages/selection.js"></script>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>



  for (let i = 0; i <= 3; i++) {
    const section = document.getElementById(`section${i}`);
    section.style.display = i === 0 ? "block" : "none";

  }
  function sec1() {
    for (let i = 0; i <= 3; i++) {
      const section = document.getElementById(`section${i}`);
      section.style.display = i === 1 ? "block" : "none";

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


  function updateProgress(value) {
    const progressValueElement = document.querySelector('.progress-value');
    progressValueElement.textContent = value + '%';
    const circularProgressBarElement = document.querySelector('.circular-progress');
    const degrees = (360 * value) / 100;
    circularProgressBarElement.style.background = `conic-gradient(#7d2ae8 ${degrees}deg, #ededed 0deg)`;
  }


  updateProgress(receivedValue);

  
  function validateDonation(fieldid) {
      var textField = document.getElementById(fieldid);
      var inputValue = textField.value;
      
      // Regular expression to match only alphabetic inputs
      var alphabeticRegex = /^[a-zA-Z]+$/;
      
      if (!alphabeticRegex.test(inputValue)) {
        // Clear the non-alphabetic input from the text field
        textField.value = inputValue.replace(/[^a-zA-Z]/g, '');
        
        alert("Only alphabetic inputs are allowed.");
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