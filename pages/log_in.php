<?php
session_start();
include '../assets/db/conn.php';

// For debugging - show any errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle all logins
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ADMIN LOGIN
    if (isset($_POST['a_submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['a_uname']);
        $password = $_POST['a_pass']; 
        
        $sql = "SELECT * FROM users WHERE email = '$email' AND user_type = 'admin'";
        $result = mysqli_query($conn, $sql);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($password == $row['password']) {
                $_SESSION['admin_logged'] = $row['fname'];
                $_SESSION['aprofile_pic'] = $row['profile_pic'];
                $_SESSION['a_email'] = $row['email'];
                $_SESSION['a_id'] = $row['id'];
                header("Location: admin.php");
                exit();
            } else {
                echo "<script>alert('Wrong password for Admin!'); window.location.href='log_in.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Admin not found!'); window.location.href='log_in.php';</script>";
            exit();
        }
    }
    
    // DONOR LOGIN
    if (isset($_POST['d_submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['d_mail']);
        $password = $_POST['d_pass'];
        
        $sql = "SELECT * FROM users WHERE email = '$email' AND user_type = 'donor'";
        $result = mysqli_query($conn, $sql);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($password == $row['password']) {
                $_SESSION['d_logged'] = $row['fname'];
                $_SESSION['dprofile_pic'] = $row['profile_pic'];
                $_SESSION['d_email'] = $row['email'];
                $_SESSION['d_id'] = $row['id'];
                header("Location: d_home.php");
                exit();
            } else {
                echo "<script>alert('Wrong password for Donor!'); window.location.href='log_in.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Donor not found!'); window.location.href='log_in.php';</script>";
            exit();
        }
    }
    
    // RECIPIENT LOGIN
    if (isset($_POST['r_submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['r_mail']);
        $password = $_POST['r_pass'];
        
        $sql = "SELECT * FROM users WHERE email = '$email' AND user_type = 'recipient'";
        $result = mysqli_query($conn, $sql);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($password == $row['password']) {
                $_SESSION['r_logged'] = $row['fname'];
                $_SESSION['rprofile_pic'] = $row['profile_pic'];
                $_SESSION['r_email'] = $row['email'];
                $_SESSION['r_id'] = $row['id'];
                header("Location: r_home.php");
                exit();
            } else {
                echo "<script>alert('Wrong password for Recipient!'); window.location.href='log_in.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Recipient not found!'); window.location.href='log_in.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DonorHub</title>
    <style>
        body {
            background: #f0f2f5;
            padding: 20px;
        }
        .card {
            border-radius: 12px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background: white;
            padding: 15px 20px;
        }
        .btn-danger {
            background: #dc3545;
            border-radius: 8px;
            padding: 5px 15px;
        }
        .btn-danger:hover {
            background: #bb2d3b;
        }
        .nav-pills {
            background: white;
            padding: 5px;
            border-radius: 40px;
            gap: 5px;
        }
        .nav-pills .nav-link {
            border-radius: 40px;
            padding: 8px 16px;
            color: #666;
        }
        .nav-pills .nav-link.active {
            background: #dc3545;
            color: white;
        }
        h1 {
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
            padding: 8px 12px;
        }
        .btn-success {
            background: #dc3545;
            border-radius: 8px;
            padding: 8px 20px;
        }
        .btn-success:hover {
            background: #bb2d3b;
        }
        .card-footer {
            background: white;
            padding: 15px 20px;
        }
        .card-footer a {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card-title ms-auto me-auto" style="max-width: 500px;">
            <ul class="nav nav-pills nav-fill gap-2 p-1 small shadow-sm mb-4 rounded" id="pillNav2" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-5 fw-bold" onclick="showDonor()" id="don">DONOR</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-5 fw-bold" onclick="showRecipient()" id="rep">RECIPIENT</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-5 fw-bold" onclick="showAdmin()" id="adm">ADMIN</button>
                </li>
            </ul>
        </div>
    </div>

    <?php include '../includes/message.php'; ?>

    <div class="container">
        <div class="card mt-5 mx-auto" style="max-width: 500px;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <img src="../assets/images/logo.png" width="40" height="40">
                <h1 class="fs-4 fw-bolder mb-0">DONOR HUB</h1>
                <a href="index.php#home" class="btn btn-danger">BACK</a>
            </div>

            <div class="card-body p-4">
                <!-- DONOR FORM -->
                <form id="donorForm" method="post" style="display: block;">
                    <h1 class="text-center">DONOR LOGIN</h1>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="d_mail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="d_pass" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="register.php">New Donor? Register</a>
                        <button type="submit" name="d_submit" class="btn btn-success">LOGIN</button>
                    </div>
                </form>

                <!-- RECIPIENT FORM -->
                <form id="recipientForm" method="post" style="display: none;">
                    <h1 class="text-center">RECIPIENT LOGIN</h1>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="r_mail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="r_pass" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="register.php">New Recipient? Register</a>
                        <button type="submit" name="r_submit" class="btn btn-success">LOGIN</button>
                    </div>
                </form>

                <!-- ADMIN FORM -->
                <form id="adminForm" method="post" style="display: none;">
                    <h1 class="text-center">ADMIN LOGIN</h1>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="a_uname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="a_pass" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" name="a_submit" class="btn btn-success">LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDonor() {
            document.getElementById('donorForm').style.display = 'block';
            document.getElementById('recipientForm').style.display = 'none';
            document.getElementById('adminForm').style.display = 'none';
        }
        function showRecipient() {
            document.getElementById('donorForm').style.display = 'none';
            document.getElementById('recipientForm').style.display = 'block';
            document.getElementById('adminForm').style.display = 'none';
        }
        function showAdmin() {
            document.getElementById('donorForm').style.display = 'none';
            document.getElementById('recipientForm').style.display = 'none';
            document.getElementById('adminForm').style.display = 'block';
        }
        
        // Set active tab styling
        document.getElementById('don').addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
        document.getElementById('rep').addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
        document.getElementById('adm').addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
        });
    </script>
</body>

</html>