<?php
include '../assets/db/conn.php';
session_start();
//admin login
if (isset($_POST['a_submit'])) {
  $aname = mysqli_real_escape_string($conn, $_POST['a_uname']);
  $apass = mysqli_real_escape_string($conn, $_POST['a_pass']);

  // First check if user exists
  $check_user = "SELECT user_id FROM users WHERE email='$aname' AND user_type='admin'";
  $user_res = mysqli_query($conn, $check_user);
  
  if ($user_res && mysqli_num_rows($user_res) > 0) {
    // User exists, now check password
    $sql = "SELECT user_id,fname,email,password,profile_pic From users where email='$aname' and password='$apass' and user_type='admin'";
    $res = mysqli_query($conn, $sql);
    
    if ($res && mysqli_num_rows($res) == 1) {
      $row = mysqli_fetch_assoc($res);
      $a_id = $row['user_id'];
      header("Location: admin.php");
      $_SESSION['admin_logged'] = $row['fname'];
      $_SESSION['aprofile_pic'] = $row['profile_pic'];
      $_SESSION['a_email'] = $aname;
      $_SESSION['a_id'] = $a_id;
      exit();
    } else {
      echo "<script>alert('Incorrect Password! Please try again.'); window.location.href='log_in.php';</script>";
      exit();
    }
  } else {
    echo "<script>alert('User not found! Please check your email or register first.'); window.location.href='log_in.php';</script>";
    exit();
  }
}
//donor login
if (isset($_POST['d_submit'])) {
  $d_mail = mysqli_real_escape_string($conn, $_POST['d_mail']);
  $d_pass = mysqli_real_escape_string($conn, $_POST['d_pass']);
 
  // First check if user exists
  $check_user = "SELECT user_id FROM users WHERE email='$d_mail' AND user_type='donor'";
  $user_res = mysqli_query($conn, $check_user);
  
  if ($user_res && mysqli_num_rows($user_res) > 0) {
    // User exists, now check password - get user data first (without d_type to avoid errors)
    $user_data_sql = "SELECT user_id, fname, email, password, profile_pic
    FROM users
    WHERE email = '$d_mail' AND user_type = 'donor'";
    $user_data_res = mysqli_query($conn, $user_data_sql);
    
    if ($user_data_res && mysqli_num_rows($user_data_res) == 1) {
      $user_row = mysqli_fetch_assoc($user_data_res);
      
      // Compare password directly
      if ($user_row['password'] === $d_pass) {
        // Password matches - login successful
        // Get d_type separately if it exists
        $d_type = '';
        $d_type_sql = "SELECT d_type FROM users WHERE user_id = " . intval($user_row['user_id']);
        $d_type_res = @mysqli_query($conn, $d_type_sql);
        if ($d_type_res && mysqli_num_rows($d_type_res) > 0) {
          $d_type_row = mysqli_fetch_assoc($d_type_res);
          $d_type = isset($d_type_row['d_type']) && $d_type_row['d_type'] !== null ? $d_type_row['d_type'] : '';
        }
        
        header("Location: d_home.php");
        $_SESSION['d_logged'] = $user_row['fname'];
        $_SESSION['dprofile_pic'] = $user_row['profile_pic'];
        $_SESSION['d_email'] = $d_mail;
        $_SESSION['d_id'] = $user_row['user_id'];
        $_SESSION['d_type'] = $d_type;
        exit();
      } else {
        // Password doesn't match
        echo "<script>alert('Incorrect Password! Please try again.'); window.location.href='log_in.php';</script>";
        exit();
      }
    } else {
      // Query failed or no rows returned
      $error_msg = mysqli_error($conn);
      echo "<script>alert('Database error occurred. Please try again.'); window.location.href='log_in.php';</script>";
      exit();
    }
  } else {
    echo "<script>alert('User not found! Please check your email or register first.'); window.location.href='log_in.php';</script>";
    exit();
  }
}
//recipient login
if (isset($_POST['r_submit'])) {
  $r_mail = mysqli_real_escape_string($conn, $_POST['r_mail']);
  $r_pass = mysqli_real_escape_string($conn, $_POST['r_pass']);

  // First check if user exists
  $check_user = "SELECT user_id FROM users WHERE email='$r_mail' AND user_type='recipient'";
  $user_res = mysqli_query($conn, $check_user);
  
  if ($user_res && mysqli_num_rows($user_res) > 0) {
    // User exists, now check password
    $sql = "SELECT user_id,fname, email, password,profile_pic
    FROM users
    WHERE email= '$r_mail' AND password = '$r_pass' AND user_type = 'recipient'";
    $res = mysqli_query($conn, $sql);
    
    if ($res && mysqli_num_rows($res) == 1) {
      $row = mysqli_fetch_assoc($res);
      header("Location:r_home.php");
      $_SESSION['r_logged'] = $row['fname'];
      $_SESSION['rprofile_pic'] = $row['profile_pic'];
      $_SESSION['r_id'] = $row['user_id'];
      exit();
    } else {
      echo "<script>alert('Incorrect Password! Please try again.'); window.location.href='log_in.php';</script>";
      exit();
    }
  } else {
    echo "<script>alert('User not found! Please check your email or register first.'); window.location.href='log_in.php';</script>";
    exit();
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
  <title>Login/Register</title>
  <style>
    html, body {
      overflow-x: hidden !important;
      max-width: 100vw;
      width: 100%;
    }

    body {
      min-height: 100vh;
      background:
        radial-gradient(circle at top left, rgba(99, 102, 241, 0.22), transparent 20%),
        linear-gradient(135deg, #0f172a 0%, #111827 48%, #1f2937 100%);
      color: #e2e8f0;
      padding: 28px 16px 40px;
      font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
    }

    .auth-shell {
      max-width: 980px;
      margin: 0 auto;
    }

    .auth-tabbar {
      max-width: 560px;
      margin: 0 auto 18px;
      padding: 6px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.72);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auth-tabbar .nav-link {
      border-radius: 999px;
      font-weight: 700;
      letter-spacing: 0.03em;
      color: rgba(255, 255, 255, 0.85) !important;
      padding: 0.75rem 1.1rem;
      transition: all 0.2s ease;
    }

    .auth-tabbar .nav-link:hover,
    .auth-tabbar .nav-link:focus {
      color: #111827 !important;
      background: rgba(255, 255, 255, 0.92);
    }

    .auth-tabbar .nav-link.active {
      color: #111827 !important;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
    }

    .auth-card {
      border: 0;
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 24px 55px rgba(15, 23, 42, 0.24);
      background: rgba(255, 255, 255, 0.98);
      color: #0f172a;
    }

    .auth-header {
      padding: 1.1rem 1.25rem;
      background: linear-gradient(135deg, #0f172a 0%, #111827 60%, #1d4ed8 100%);
      color: #fff;
      border-bottom: 0;
    }

    .auth-brand {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .auth-brand img {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      border: 2px solid rgba(255, 255, 255, 0.18);
    }

    .auth-kicker {
      margin: 0;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      opacity: 0.8;
    }

    .auth-title {
      margin: 0;
      font-size: 1.05rem;
      font-weight: 800;
    }

    .auth-subtitle {
      margin: 0;
      font-size: 0.9rem;
      color: rgba(226, 232, 240, 0.8);
      max-width: 540px;
      line-height: 1.5;
    }

    .auth-header .btn {
      border-radius: 999px;
      font-weight: 700;
    }

    .auth-body {
      padding: 1.4rem 1.25rem 1.2rem;
    }

    .auth-body h1 {
      font-size: 1.45rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1.1rem;
    }

    .form-label {
      font-size: 0.92rem;
      font-weight: 700;
      color: #334155;
      margin-bottom: 0.45rem;
    }

    .form-control,
    .input-group-text {
      border-radius: 14px;
    }

    .form-control {
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      color: #0f172a;
      padding: 0.78rem 0.9rem;
    }

    .form-control:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
      background: #fff;
    }

    .input-group-text {
      background: #eef2ff;
      color: #4338ca;
      border: 1px solid #c7d2fe;
    }

    .card-footer {
      padding: 0;
      border-top: 0;
      background: transparent;
    }

    .btn {
      border-radius: 999px;
      font-weight: 800;
      padding: 0.7rem 1.15rem;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn-primary,
    .btn-success {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      border: 0;
      color: #fff;
      box-shadow: 0 12px 24px rgba(99, 102, 241, 0.24);
    }

    .btn-outline-danger {
      border-width: 1.5px;
    }

    .auth-link {
      color: #4338ca;
      font-weight: 700;
      text-decoration: none;
    }

    .auth-link:hover {
      color: #3730a3;
      text-decoration: underline;
    }

    @media (max-width: 767.98px) {
      body {
        padding: 20px 12px 28px;
      }

      .auth-header {
        padding: 1rem;
      }

      .auth-body {
        padding: 1.1rem 1rem 1rem;
      }
    }
  </style>
</head>

<body>
  <div class="auth-shell">
    <div class="card-title ms-auto me-auto auth-tabbar">
      <ul class="nav nav-pills nav-fill gap-2 p-1" id="pillNav2" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active rounded-5 fw-bold" onclick="donor()" id="don" data-bs-toggle="tab"
            type="button" role="tab" aria-selected="true">DONOR</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 fw-bold" onclick="recipient()" id="rep" data-bs-toggle="tab" type="button"
            role="tab" aria-selected="false">RECIPIENT</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 fw-bold" onclick="admin()" id="adm" data-bs-toggle="tab" type="button"
            role="tab" aria-selected="false">ADMIN</button>
        </li>
      </ul>
    </div>
  </div>

  <?php include '../includes/message.php'; ?>

  <div class="container">
    <div class="card mt-4 mx-auto auth-card" style="max-width: 560px;">
      <div class="card-header auth-header d-flex justify-content-between align-items-center gap-3">
        <div class="auth-brand">
          <img src="../assets/images/logo.png" class="img-fluid" alt="Logo_image">
          <div>
            <p class="auth-kicker">DonorHub</p>
            <h1 class="auth-title mb-0">Choose your access</h1>
          </div>
        </div>
        <div class="text-end">
          <p class="auth-subtitle mb-2">Sign in as a donor, recipient, or admin to continue.</p>
          <a href="index.php#home" class="btn btn-outline-light btn-sm rounded-pill"><i class="bi bi-arrow-left-circle me-1"></i>BACK</a>
        </div>
      </div>

      <div class="card-body auth-body">


        <!-- admin body -->
        <form method="post" action="" name="adminLoginForm" id="admin" class="tab-content needs-validation" novalidate autocomplete="on">
          <div class="tab-pane fade show active" role="tabpanel">
            <h1 class="text-center mb-4 fs-2 fw-bold me-4">ADMIN LOGIN</h1>
            <div class="mb-3">
              <label for="admin-email" class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>

                <input type="email" name="a_uname" id="admin-email" placeholder="eg.xxx@gmail.com" class="form-control"
                  autocomplete="email" required>
                <div class="invalid-feedback">Please enter email</div>
              </div>
            </div>
            <div class="mb-3">
              <label for="pass1" class="form-label">PASSWORD</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="a_pass" id="pass1" class="form-control" placeholder="Password" autocomplete="current-password" required>
                <span class="input-group-text" id="basic-addon2"><i class="bi bi-eye-fill" id="show1"></i></span>
                <div class="invalid-feedback">Please enter password</div>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-center">
              <input type="submit" name="a_submit" id="admin-submit" value="LOGIN" class="btn btn-success">
            </div>
          </div>
        </form>

        <!-- donor body -->
        <form id="donor" name="donorLoginForm" class=" input-group needs-validation " method="post" action="" novalidate autocomplete="on">
          <h1 class="text-center fw-bold fs-2 me-4">DONOR LOGIN</h1>
          <label for="donor-email" class="form-label">EMAIL</label>
          <div class="mb-4 input-group">
            <span class="input-group-text">
              <i class="bi bi-person"></i>
            </span>
            <input type="email" name="d_mail" id="donor-email" placeholder="eg.xxx@gmail.com" class="form-control "
              placeholder="eg.xxx@gmail.com" autocomplete="email" required>

            <div class="invalid-feedback">
              Please enter email.
            </div>
          </div>
          <label for="pass2" class="form-label">PASSWORD</label>
          <div class="mb-4 input-group">
            <span class="input-group-text">
              <i class="bi bi-mailbox"></i>
            </span>
            <input type="password" name="d_pass" id="pass2" class="form-control" autocomplete="current-password" required>
            <span class="input-group-text" id="basic-addon2"><i class="bi bi-eye-fill" id="show2"></i></span>
            <div class="invalid-feedback">
              Please enter password.
            </div>
          </div>
          <div class="card-footer d-flex justify-content-around">
            <a href="register.php">DONOR REGISTERATION?</a>
            <input type="submit" name="d_submit" id="submit" value="LOGIN" class="btn btn-success">
          </div>
        </form>

        <!-- recipient body-->
        <form id="recipient" name="recipientLoginForm" class="input-group needs-validation" method="post" action="" novalidate autocomplete="on">
          <h1 class="text-center fs-2 me-4 fw-bold">RECIPIENT LOGIN</h1>
          <label for="recipient-email" class="form-label">EMAIL</label>
          <div class="mb-4 input-group">
            <span class="input-group-text">
              <i class="bi bi-person"></i>
            </span>
            <input type="email" name="r_mail" id="recipient-email" placeholder="eg.xxx@gmail.com" class="form-control "
              placeholder="eg.xxx@gmail.com" autocomplete="email" required>
            <div class="invalid-feedback">
              Please enter email.
            </div>
          </div>
          <label for="pass3" class="form-label">PASSWORD</label>
          <div class="mb-4 input-group">
            <span class="input-group-text">
              <i class="bi bi-mailbox"></i>
            </span>
            <input type="password" name="r_pass" id="pass3" class="form-control" autocomplete="current-password" required>
            <span class="input-group-text" id="basic-addon2"><i class="bi bi-eye-fill" id="show3"></i></span>
            <div class="invalid-feedback">
              Please enter password.
            </div>
          </div>
          <div class="card-footer d-flex justify-content-around">
            <a href="register.php">RECIPIENT REGISTERATION?</a>
            <input type="submit" name="r_submit" id="submit" value="LOGIN" class="btn btn-success">
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
  </div>
  <style>
    .nav-link:hover {
      color: #474747;
    }
  </style>
  <script>
    var x = document.getElementById("admin");
    var y = document.getElementById("donor");
    var z = document.getElementById("recipient");

    x.style.display = "none";
    y.style.display = "block";
    z.style.display = "none";

    function admin() {


      x.style.display = "block ";
      y.style.display = "none ";
      z.style.display = "none ";
    }

    function donor() {

      x.style.display = "none ";
      y.style.display = "block ";
      z.style.display = "none ";
    }

    function recipient() {

      x.style.display = "none ";
      y.style.display = "none ";
      z.style.display = "block ";
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
          } else {
            // Ensure password fields are type="password" before submission for browser password saving
            const passwordFields = form.querySelectorAll('input[type="password"], input[type="text"][name*="pass"]');
            passwordFields.forEach(field => {
              if (field.name.includes('pass') || field.id.includes('pass')) {
                field.type = 'password';
              }
            });
          }

          form.classList.add('was-validated')
        }, false)
      })
    })();
    //se
    var tabs = document.querySelectorAll('.nav-link');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        localStorage.setItem('selectedTab', this.id);
      });
    });
    var selectedTab = localStorage.getItem('selectedTab');
    if (selectedTab) {
      document.getElementById(selectedTab).click();
    }



    //PASSWORD MASKING
    var s1 = document.getElementById('show1');
    var p1 = document.getElementById('pass1');
    var s2 = document.getElementById('show2');
    var p2 = document.getElementById('pass2');
    var s3 = document.getElementById('show3');
    var p3 = document.getElementById('pass3');
    s1.addEventListener("click", () => {
      if (s1.classList.contains('bi-eye-fill')) {
        s1.classList.remove('bi-eye-fill');
        s1.classList.add('bi-eye-slash-fill');
        p1.type = "text";
      }
      else {
        s1.classList.remove('bi-eye-slash-fill');
        s1.classList.add('bi-eye-fill');
        p1.type = "password";
      }
    });
    s2.addEventListener("click", () => {
      if (s2.classList.contains('bi-eye-fill')) {
        s2.classList.remove('bi-eye-fill');
        s2.classList.add('bi-eye-slash-fill');
        p2.type = "text";
      }
      else {
        s2.classList.remove('bi-eye-slash-fill');
        s2.classList.add('bi-eye-fill');
        p2.type = "password";
      }
    });

    s3.addEventListener("click", () => {
      if (s3.classList.contains('bi-eye-fill')) {
        s3.classList.remove('bi-eye-fill');
        s3.classList.add('bi-eye-slash-fill');
        p3.type = "text";
      }
      else {
        s3.classList.remove('bi-eye-slash-fill');
        s3.classList.add('bi-eye-fill');
        p3.type = "password";
      }
    });

  </script>
</body>

</html>

