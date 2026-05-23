<?php
session_start();
include "../assets/db/conn.php";

if (isset($_POST['d_submit'])) {
  // Validate required fields
  if (empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email']) || empty($_POST['phone']) || 
      empty($_POST['place']) || empty($_POST['emp_status']) || empty($_POST['b_type']) || empty($_POST['password'])) {
    $_SESSION['message'] = "Please fill in all required fields.";
    header("Location: register.php");
    exit(0);
  }

  // Check if email already exists
  $email_check = mysqli_real_escape_string($conn, $_POST['email']);
  $check_query = "SELECT email FROM users WHERE email = '$email_check'";
  $check_result = mysqli_query($conn, $check_query);
  
  if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['message'] = "Email already exists. Please use a different email address.";
    header("Location: register.php");
    exit(0);
  }

  // Validate file upload
  if (!isset($_FILES['profile']) || $_FILES['profile']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['message'] = "Please upload a valid profile picture.";
    header("Location: register.php");
    exit(0);
  }

  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $place = mysqli_real_escape_string($conn, $_POST['place']);
  $emp_status = mysqli_real_escape_string($conn, $_POST['emp_status']);
  $user_type = 'donor';
  $b_type = mysqli_real_escape_string($conn, $_POST['b_type']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  
  $targetDirectory = '../pages/profile_pic/';
  if (!file_exists($targetDirectory)) {
    mkdir($targetDirectory, 0777, true);
  }
  $profile = $targetDirectory . basename($_FILES['profile']['name']);
  
  // Validate file type
  $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
  $file_type = $_FILES['profile']['type'];
  if (!in_array($file_type, $allowed_types)) {
    $_SESSION['message'] = "Invalid file type. Please upload a JPG, PNG, or GIF image.";
    header("Location: register.php");
    exit(0);
  }
  
  if (!move_uploaded_file($_FILES['profile']['tmp_name'], $profile)) {
    $_SESSION['message'] = "Failed to upload profile picture. Please try again.";
    header("Location: register.php");
    exit(0);
  }

  $qry = "INSERT INTO users(fname,lname,email,phone,place,emp_status,profile_pic,user_type,blood_type,password) VALUES('$fname','$lname','$email','$phone','$place','$emp_status','$profile','$user_type','$b_type','$password') ";
  $exe = mysqli_query($conn, $qry);
  
  if ($exe) {
    $_SESSION['message'] = "Registration Successfull,Login here..";
    sleep(2);
    header("Location: log_in.php");
    exit(0);
  } else {
    $error_msg = mysqli_error($conn);
    $_SESSION['message'] = "Registration failed: " . ($error_msg ? $error_msg : "Database error occurred. Please try again.");
    header("Location: register.php");
    exit(0);
  }
}
if (isset($_POST['r_submit'])) {
  // Validate required fields
  if (empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email']) || empty($_POST['phone']) || 
      empty($_POST['place']) || empty($_POST['emp_status']) || empty($_POST['b_type']) || empty($_POST['password'])) {
    $_SESSION['message'] = "Please fill in all required fields.";
    header("Location: register.php");
    exit(0);
  }

  // Check if email already exists
  $email_check = mysqli_real_escape_string($conn, $_POST['email']);
  $check_query = "SELECT email FROM users WHERE email = '$email_check'";
  $check_result = mysqli_query($conn, $check_query);
  
  if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['message'] = "Email already exists. Please use a different email address.";
    header("Location: register.php");
    exit(0);
  }

  // Validate file upload
  if (!isset($_FILES['profile']) || $_FILES['profile']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['message'] = "Please upload a valid profile picture.";
    header("Location: register.php");
    exit(0);
  }

  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $place = mysqli_real_escape_string($conn, $_POST['place']);
  $emp_status = mysqli_real_escape_string($conn, $_POST['emp_status']);
  $user_type = 'recipient';
  $b_type = mysqli_real_escape_string($conn, $_POST['b_type']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  
  $targetDirectory = '../pages/profile_pic/';
  if (!file_exists($targetDirectory)) {
    mkdir($targetDirectory, 0777, true);
  }
  $profile = $targetDirectory . basename($_FILES['profile']['name']);
  
  // Validate file type
  $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
  $file_type = $_FILES['profile']['type'];
  if (!in_array($file_type, $allowed_types)) {
    $_SESSION['message'] = "Invalid file type. Please upload a JPG, PNG, or GIF image.";
    header("Location: register.php");
    exit(0);
  }
  
  if (!move_uploaded_file($_FILES['profile']['tmp_name'], $profile)) {
    $_SESSION['message'] = "Failed to upload profile picture. Please try again.";
    header("Location: register.php");
    exit(0);
  }

  $qry = "INSERT INTO users(fname,lname,email,phone,place,emp_status,profile_pic,user_type,blood_type,password) VALUES('$fname','$lname','$email','$phone','$place','$emp_status','$profile','$user_type','$b_type','$password') ";
  $exe = mysqli_query($conn, $qry);
  
  if ($exe) {
    $_SESSION['message'] = "Registration Successfull";
    sleep(2);
    header("Location: log_in.php");
    exit(0);
  } else {
    $error_msg = mysqli_error($conn);
    $_SESSION['message'] = "Registration failed: " . ($error_msg ? $error_msg : "Database error occurred. Please try again.");
    header("Location: register.php");
    exit(0);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!--Icons-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <!--Bootstrap-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <!--Icon--->
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <!--JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registeration</title>
  <style>
    html, body {
      height: 100%;
      overflow: hidden;
      margin: 0;
      padding: 0;
    }
    body {
      overflow-y: auto;
      overflow-x: hidden;
    }
    body::-webkit-scrollbar {
      display: none;
    }
    body {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .container {
      max-height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
    }
    .container::-webkit-scrollbar {
      display: none;
    }
    .container {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .card {
      max-height: calc(100vh - 20px);
      margin-top: 10px !important;
      margin-bottom: 10px !important;
      max-width: 1200px;
    }
    .card-body {
      max-height: calc(100vh - 180px);
      overflow-y: auto;
      overflow-x: hidden;
      padding: 1.25rem !important;
    }
    .card-body::-webkit-scrollbar {
      display: none;
    }
    .card-body {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .row.g-3 {
      --bs-gutter-y: 0.75rem;
      --bs-gutter-x: 0.75rem;
    }
    .form-label {
      margin-bottom: 0.4rem;
      font-size: 0.9rem;
      font-weight: 500;
    }
    .form-control, .form-select {
      padding: 0.5rem 0.75rem;
      font-size: 0.9rem;
    }
    .input-group-text {
      padding: 0.5rem 0.75rem;
      font-size: 0.9rem;
    }
    .card-header {
      padding: 0.75rem 1.25rem !important;
    }
    .card-header h1 {
      font-size: 1.1rem !important;
    }
    .card-header .btn {
      padding: 0.375rem 0.75rem;
      font-size: 0.9rem;
    }
    .mt-5 {
      margin-top: 1.5rem !important;
    }
    .mb-4 {
      margin-bottom: 1rem !important;
    }
    .mb-3 {
      margin-bottom: 0.75rem !important;
    }
    h1 {
      font-size: 1.5rem;
      margin-bottom: 1rem !important;
      font-weight: 600;
    }
    .col-12 {
      margin-top: 0.75rem;
    }
    .btn {
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
    }
    .invalid-feedback, .valid-feedback {
      font-size: 0.8rem;
    }
    .form-check-label a {
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .form-check-label a:hover {
      color: #0a58ca !important;
      text-decoration: underline !important;
    }
  </style>
</head>

<body>
  <div class="container mt-3">
    <div class="card-title ms-auto me-auto" style="max-width: 500px;">
      <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-primary shadow-sm mb-3 rounded " id="pillNav2" role="tablist"
        style="--bs-nav-link-color: var(--bs-white); --bs-nav-pills-link-active-color: var(--bs-primary); --bs-nav-pills-link-active-bg: var(--bs-white);">
        <li class="nav-item" role="presentation">
          <button class="nav-link active rounded-5 fw-bold" onclick="donor()" id="don" data-bs-toggle="tab"
            type="button" role="tab" aria-selected="true">DONOR</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 fw-bold " onclick="recipient()" id="rep" data-bs-toggle="tab" type="button"
            role="tab" aria-selected="false">RECIPIENT</button>
        </li>

      </ul>
    </div>
  </div>
  <?php include '../includes/message.php'; ?>
  <div class="container">
    <div class="card mt-3 mx-auto">
      <div class="card-header d-flex justify-content-between align-items-center  ">
        <img src="../assets/images/logo.png" class="img-fluid" alt="Logo_image" width="40" height="40">
        <h1 class="fs-4 fw-bolder text-center mb-0">DONOR HUB</h1>
        <a href="index.php#home" class="btn btn-danger"><i class="bi bi-arrow-left-circle m-1"></i>BACK</a>
      </div>
      <div class="card-body ">

        <form id="donor" class="row g-3 needs-validation d-flex " method="post" enctype="multipart/form-data"
          novalidate>
          <h1>Donor</h1>
          <div class="col-md-4">
            <label for="validationCustom01" class="form-label">First name</label>
            <input type="text" oninput="validateAlphabeticInput('validationCustom01')" class="form-control"
              id="validationCustom01" placeholder="Firstname" name="fname" required>
            <div class="valid-feedback">
              Looks good!
            </div>
            <div class="invalid-feedback">Please enter first name</div>
          </div>


          <div class="col-md-4">
            <label for="validationCustom02" class="form-label">Last name</label>
            <input type="text" class="form-control" oninput="validateAlphabeticInput('validationCustom02')"
              id="validationCustom02" placeholder="lastname" name="lname" required>
            <div class="valid-feedback">
              Looks good!
            </div>
            <div class="invalid-feedback">Please enter last name</div>
          </div>


          <div class="col-md-4">
            <label for="validationCustom03" class="form-label">Email</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend">@</span>
              <input type="email" class="form-control" id="validationCustom03"
                onblur="validateEmail('validationCustom03');" placeholder="xxx@gmail.com"
                aria-describedby="inputGroupPrepend" name="email" required>
              <div class="invalid-feedback">
                Enter valid email.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <label for="validationCustom04" class="form-label">Phone No.</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend"> <i class="bi bi-telephone"></i></span>
              <input type="tel" min="1000000000" max="9999999999" maxlength="10" class="form-control" name="phone"
                onblur="validatePhoneInput('validationCustom04');" placeholder="10 digit" id="validationCustom04"
                aria-describedby="inputGroupPrepend" required>
              <div class="invalid-feedback">
                Enter valid phone number.
              </div>
            </div>
          </div>


          <div class="col-md-4">
            <label for="validationCustom05" class="form-label">Place</label>
            <input type="text" class="form-control" oninput="validateAlphabeticInput('validationCustom05')"
              id="validationCustom05" list="place" placeholder="Current location" name="place" required>
            <div class="invalid-feedback">
              Enter place name
            </div>
          </div>



          <div class="col-md-4">
            <label for="validationCustom06" class="form-label">Employement Status</label>
            <select class="form-select" id="validationCustom06" name="emp_status" required>
              <option selected disabled value="">Select your employement status</option>
              <option name="employed" value="employed">Employed</option>
              <option name="unemployed" value="unemployed">Unemployed</option>
              <option name="student" value="student">Student</option>
            </select>
            <div class="invalid-feedback">
              Please select valid profession.
            </div>
          </div>
          <div class="col-md-4">
            <label for="image" class="form-label">Choose Profile</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-person-bounding-box "></i>
              </span>
              <input type="file" class="form-control" id="image" name="profile" accept="image/*" required>
            </div>
          </div>
          <div class="col-md-4" id="bg_type">
            <label for="validationCustom07" class="form-label">Select Blood Group</label>
            <select class="form-select" id="validationCustom07" name="b_type">
              <option selected disabled value=""> Type</option>
              <option value="O+">O+</option>
              <option name="A+" value="A+">A+</option>
              <option name="B+" value="B+">B+</option>
              <option name="AB+" value="AB+">AB+</option>
              <option name="O-" value="O-">O-</option>
              <option name="A-" value="A-">A-</option>
              <option name="B-" value="B-">B-</option>
              <option name="AB-" value="AB-">AB-</option>
            </select>
            <div class="invalid-feedback">
              Please select a valid blood group.
            </div>
          </div>

          <div class="col-md-4">
            <label for="pass1" class="form-label">Password</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-key "></i>
              </span>
              <input type="password" class="form-control" id="pass1" name="password" onblur="validatePassword('pass1')"
                required>
              <span class="input-group-text"><i class="bi bi-eye-fill" id="show1"></i></span>
              <div class="invalid-feedback">
                Please enter password.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <label for="pass2" class="form-label">Confirm Password</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-key "></i>
              </span>
              <input type="password" class="form-control" id="pass2" name="cpassword"
                onblur="confirmpass('pass1','pass2');" required>
              <span class="input-group-text"><i class="bi bi-eye-fill" id="show2"></i></span>
              <div class="invalid-feedback">
                Please enter confirm password.
              </div>
            </div>
          </div>


          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="invalidCheckDonor" required>
              <label class="form-check-label" for="invalidCheckDonor">
                I agree to the <a href="terms_and_conditions.php" target="_blank" class="text-primary text-decoration-underline">terms and conditions</a>
              </label>
              <div class="invalid-feedback">
                You must agree before submitting.
              </div>
            </div>
          </div>
          <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-primary" type="submit" name="d_submit">Submit form</button>
            <button class="btn btn-danger" type="reset">Reset</button>
          </div>
        </form>


        <form id="recipient" class="row g-3 needs-validation d-flex" method="post" enctype="multipart/form-data"
          novalidate>
          <h1>Recipient</h1>
          <div class="col-md-4">
            <label for="validationCustom011" class="form-label">First name</label>
            <input type="text" class="form-control" oninput="validateAlphabeticInput('validationCustom011')"
              id="validationCustom011" placeholder="Firstname" name="fname" required>
            <div class="valid-feedback">
              Looks good!
            </div>
            <div class="invalid-feedback">Please enter first name</div>
          </div>

          <div class="col-md-4">
            <label for="validationCustom022" class="form-label">Last name</label>
            <input type="text" class="form-control" oninput="validateAlphabeticInput('validationCustom022')"
              id="validationCustom022" placeholder="lastname" name="lname" required>
            <div class="valid-feedback">
              Looks good!
            </div>
            <div class="invalid-feedback">Please enter last name</div>
          </div>


          <div class="col-md-4">
            <label for="validationCustom033" class="form-label">Email</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend">@</span>
              <input type="email" class="form-control" id="validationCustom033" placeholder="xxx@gmail.com"
                onblur="validateEmail('validationCustom033');" aria-describedby="inputGroupPrepend" name="email"
                required>
              <div class="invalid-feedback">
                Enter valid email.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <label for="validationCustom044" class="form-label">Phone No.</label>
            <div class="input-group has-validation">
              <span class="input-group-text" id="inputGroupPrepend"> <i class="bi bi-telephone"></i></span>
              <input type="tel" min="1000000000" max="9999999999" maxlength="10" class="form-control"
                placeholder="10 digit" onblur="validatePhoneInput('validationCustom044');" id="validationCustom044"
                aria-describedby="inputGroupPrepend" name="phone" required>
              <div class="invalid-feedback">
                Enter valid phone number.
              </div>
            </div>
          </div>


          <div class="col-md-4">
            <label for="validationCustom055" class="form-label">Place</label>
            <input type="text" class="form-control" oninput="validateAlphabeticInput('validationCustom055')"
              id="validationCustom055" placeholder="Current location" name="place" required>
            <div class="invalid-feedback">
              Enter place name.
            </div>
          </div>



          <div class="col-md-4">
            <label for="validationCustom06" class="form-label">Employement Status</label>
            <select class="form-select" id="validationCustom06" name="emp_status" required>
              <option selected disabled>Select your employement status</option>
              <option name="employed" value="employed">Employed</option>
              <option name="unemployed" value="unemployed">Unemployed</option>
              <option name="student" value="student">Student</option>
            </select>
            <div class="invalid-feedback">
              Please select valid profession.
            </div>

          </div>
          <div class="col-md-4" id="bg_type">
            <label for="validationCustom07" class="form-label">Select Blood Group</label>
            <select class="form-select" id="validationCustom07" name="b_type">
              <option selected disabled value=""> Type</option>
              <option value="O+">O+</option>
              <option name="A+" value="A+">A+</option>
              <option name="B+" value="B+">B+</option>
              <option name="AB+" value="AB+">AB+</option>
              <option name="O-" value="O-">O-</option>
              <option name="A-" value="A-">A-</option>
              <option name="B-" value="B-">B-</option>
              <option name="AB-" value="AB-">AB-</option>
            </select>
            <div class="invalid-feedback">
              Please select a valid blood group.
            </div>
          </div>
          <div class="col-md-4">
            <label for="image" class="form-label">Choose Profile</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-person-bounding-box "></i>
              </span>
              <input type="file" class="form-control" id="image" name="profile" accept="image/*" required>
            </div>
          </div>


          <div class="col-md-4">
            <label for="pass3" class="form-label">Password</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-key "></i>
              </span>
              <input type="password" class="form-control" id="pass3" name="password" onblur="validatePassword('pass3');"
                required>
              <span class="input-group-text"><i class="bi bi-eye-fill" id="show3"></i></span>
              <div class="invalid-feedback">
                Please enter password.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <label for="pass4" class="form-label">Confirm Password</label>
            <div class="mb-3 input-group">
              <span class="input-group-text">
                <i class="bi bi-key "></i>
              </span>
              <input type="password" class="form-control" id="pass4" name="cpassword"
                onblur="confirmpass('pass3','pass4');" required>
              <span class="input-group-text"><i class="bi bi-eye-fill" id="show4"></i></span>
              <div class="invalid-feedback">
                Please enter confirm password.
              </div>
            </div>
          </div>


          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="invalidCheckRecipient" required>
              <label class="form-check-label" for="invalidCheckRecipient">
                I agree to the <a href="terms_and_conditions.php" target="_blank" class="text-primary text-decoration-underline">terms and conditions</a>
              </label>
              <div class="invalid-feedback">
                You must agree before submitting.
              </div>
            </div>
          </div>
          <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-primary" type="submit" name="r_submit">Submit form</button>
            <button class="btn btn-danger" type="reset">Reset</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const show1 = document.getElementById("show1");
    const show2 = document.getElementById("show2");
    const show3 = document.getElementById("show3");
    const show4 = document.getElementById("show4");
    const pass1 = document.getElementById("pass1");
    const pass2 = document.getElementById("pass2");
    const pass3 = document.getElementById("pass3");
    const pass4 = document.getElementById("pass4");

    show1.addEventListener("click", () => {
      if (show1.classList.contains("bi-eye-fill")) {

        show1.classList.remove("bi-eye-fill");
        show1.classList.add("bi-eye-slash-fill");
        pass1.type = "text";
      } else {
        show1.classList.remove("bi-eye-slash-fill");
        show1.classList.add("bi-eye-fill");
        pass1.type = "password";
      }
    });
    show2.addEventListener("click", () => {
      if (show2.classList.contains("bi-eye-fill")) {

        show2.classList.remove("bi-eye-fill");
        show2.classList.add("bi-eye-slash-fill");
        pass2.type = "text";
      } else {

        show2.classList.remove("bi-eye-slash-fill");
        show2.classList.add("bi-eye-fill");
        pass2.type = "password";
      }
    });
    show3.addEventListener("click", () => {
      if (show3.classList.contains("bi-eye-fill")) {

        show3.classList.remove("bi-eye-fill");
        show3.classList.add("bi-eye-slash-fill");
        pass3.type = "text";
      } else {

        show3.classList.remove("bi-eye-slash-fill");
        show3.classList.add("bi-eye-fill");
        pass3.type = "password";
      }
    });

    show4.addEventListener("click", () => {
      if (show4.classList.contains("bi-eye-fill")) {

        show4.classList.remove("bi-eye-fill");
        show4.classList.add("bi-eye-slash-fill");
        pass4.type = "text";
      } else {
        show4.classList.remove("bi-eye-slash-fill");
        show4.classList.add("bi-eye-fill");
        pass4.type = "password";
      }
    });

    const donorTab = document.getElementById("don");
    const recipientTab = document.getElementById("rep");


    const donorForm = document.getElementById("donor");
    const recipientForm = document.getElementById("recipient");
    
    donorTab.addEventListener("click", () => {
      recipientForm.classList.add("d-none");
      donorForm.classList.remove("d-none");
    });

    recipientTab.addEventListener("click", () => {
      donorForm.classList.add("d-none");
      recipientForm.classList.remove("d-none");
    });

    (() => {
      'use strict'

      const forms = document.querySelectorAll('.needs-validation')

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


    var tabs = document.querySelectorAll('.nav-link');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        localStorage.setItem('selectedTab', this.id);
      });
    });
    var selectedTab = localStorage.getItem('selectedTab');

    if (selectedTab == null) {
      recipientForm.classList.add("d-none");
      donorForm.classList.remove("d-none");
    } else {
      document.getElementById(selectedTab).click();
    }


    function validateAlphabeticInput(fieldid) {
      var textField = document.getElementById(fieldid);
      var inputValue = textField.value;
      var alphabeticRegex = /^[a-zA-Z]+$/;

      if (!alphabeticRegex.test(inputValue)) {
        textField.value = inputValue.replace(/[^a-zA-Z]/g, '');

        alert("Only alphabetic inputs are allowed.");
      }
    }

    function validatePhoneInput(fieldid) {
      var textField = document.getElementById(fieldid);
      var inputValue = textField.value;
      var numericRegex = /^[0-9]+$/;

      if (!numericRegex.test(inputValue)) {
        textField.value = inputValue.replace(/[^0-9]/g, '');
        textField.focus();
        event.preventDefault();
        alert("Only numeric inputs are allowed for the phone number.");
      } else if (inputValue.length !== 10) {
        textField.focus();
        textField.value = "";
        alert("The phone number must have 10 digits.");
      }
    }
    function validatePassword(fieldid) {
      var textField = document.getElementById(fieldid);
      var inputValue = textField.value;
      var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
      
      if (inputValue.length < 8) {
        alert("Password should be at least 8 characters long.");
        textField.value = "";
        return false;
      } else if (inputValue.length > 15) {
        alert("Password should be less than 15 characters long.");
        textField.value = "";
        return false;
      } else if (!passwordRegex.test(inputValue)) {
        alert("Password should contain at least one uppercase letter, one lowercase letter, and one digit.");
        textField.value = "";
        textField.focus();
        return false;
      }

      return true;
    }
    function confirmpass(field1, field2) {
      var textField1 = document.getElementById(field1).value;
      var textField2 = document.getElementById(field2).value;
      if (textField1 != textField2) {
        alert("Password does not match");
        document.getElementById(field2).value = "";
        return false;
      }
      return true;
    }
    function validateEmail(fieldId) {
      var textField = document.getElementById(fieldId);
      var inputValue = textField.value;
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailRegex.test(inputValue)) {
       
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              var response = xhr.responseText;
              if (response === "exists") {
                alert("The email already exists in the database.");
                textField.value = "";
              }
            } else {
              alert("Error: " + xhr.status);
            }
          }
        };

        xhr.open("GET", "check_email.php?email=" + inputValue, true);
        xhr.send();
      }
    }

  </script>
</body>

</html>

