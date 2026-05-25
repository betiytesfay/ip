<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Logout handler
if (isset($_POST['logout'])) {
    session_destroy();
    unset($_SESSION['admin_logged']);
    unset($_SESSION['aprofile_pic']);
    unset($_SESSION['a_email']);
    unset($_SESSION['a_id']);
    header("Location: log_in.php");
    exit();
}
include "../assets/db/conn.php";

if (empty(@$_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit();
}

// Get statistics
$total_donors = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type='donor'"));
$total_recipients = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type='recipient'"));
$total_campaigns = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM campaigns"));
$pending_campaigns = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM campaigns WHERE status='pending'"));

// Create campaign
if (isset($_POST['a_submit'])) {
    $d_type = mysqli_real_escape_string($conn, $_POST['d_type']);
    $camp_title = mysqli_real_escape_string($conn, $_POST['camp_title']);
    $camp_desc = mysqli_real_escape_string($conn, $_POST['camp_desc']);
    $est_amt = mysqli_real_escape_string($conn, $_POST['est_amt']);
    
    $targetDirectory = '../pages/camp_image/';
    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }
    $c_image = $targetDirectory . basename($_FILES['c_image']['name']);
    move_uploaded_file($_FILES['c_image']['tmp_name'], $c_image);
    $recip_id = $_SESSION['a_id'];
    
    $camp_qry = "INSERT INTO campaigns(camp_title, camp_type, camp_desc, camp_img, est_amt, recip_id, status) 
                 VALUES('$camp_title', '$d_type', '$camp_desc', '$c_image', '$est_amt', '$recip_id', 'active')";
    
    if (mysqli_query($conn, $camp_qry)) {
        $_SESSION['message'] = "Campaign Created Successfully!";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($conn);
    }
    header("Location: admin.php");
    exit();
}

// Send email
if (isset($_POST['send_mail'])) {
    if (!empty($_POST['recipient'])) {
        $subject = "Notification from DonorHub";
        $message = "Thank you for your donations! Visit DonorHub for more campaigns.";
        foreach ($_POST['recipient'] as $email) {
            mail($email, $subject, $message);
        }
        $_SESSION['message'] = "Emails sent successfully!";
    }
    header("Location: admin.php");
    exit();
}

// Delete user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$delete_id'");
    $_SESSION['message'] = "User deleted successfully!";
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DonorHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="Logo">
        <h4>DonorHub</h4>
        <small>Admin Panel</small>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item"><a href="#" class="menu-link active" onclick="showSection('dashboard')"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="menu-item"><a href="#" class="menu-link" onclick="showSection('create')"><i class="bi bi-plus-circle"></i> Create Campaign</a></li>
        <li class="menu-item"><a href="#" class="menu-link" onclick="showSection('campaigns')"><i class="bi bi-megaphone"></i> All Campaigns</a></li>
        <li class="menu-item"><a href="#" class="menu-link" onclick="showSection('donors')"><i class="bi bi-people"></i> Donors</a></li>
        <li class="menu-item"><a href="#" class="menu-link" onclick="showSection('recipients')"><i class="bi bi-person-plus"></i> Recipients</a></li>
        <li class="menu-item"><a href="#" class="menu-link" onclick="showSection('notify')"><i class="bi bi-envelope"></i> Notify Donors</a></li>
    </ul>
  <div class="sidebar-footer">
    <button class="profile-btn" data-bs-toggle="dropdown">
        <img src="<?= $_SESSION['aprofile_pic'] ?>" alt="Profile">
        <span><?= $_SESSION['admin_logged'] ?></span>
        <i class="bi bi-chevron-down"></i>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="../pages/profile_aupdate.php?id=<?= $_SESSION['a_id'] ?>"><i class="bi bi-pen"></i> Edit Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="post" style="margin: 0;">
                <button type="submit" name="logout" class="dropdown-item text-danger" style="background: none; border: none; width: 100%; text-align: left;">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
</div>

<!-- Main Content -->
<div class="main-content">
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-custom"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- Dashboard Section -->
    <div id="dashboard" class="section-container active">
        <div class="welcome-card">
            <h2>Welcome back, <?= $_SESSION['admin_logged'] ?>! 👋</h2>
            <p>Manage campaigns, donors, and recipients from your admin dashboard.</p>
        </div>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <h3><?= $total_donors ?></h3>
                    <p>Total Donors</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
                <div class="stat-info">
                    <h3><?= $total_recipients ?></h3>
                    <p>Total Recipients</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon"><i class="bi bi-megaphone-fill"></i></div>
                <div class="stat-info">
                    <h3><?= $total_campaigns ?></h3>
                    <p>Total Campaigns</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-info">
                    <h3><?= $pending_campaigns ?></h3>
                    <p>Pending Approval</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Campaign Section -->
    <div id="create" class="section-container">
        <h3 class="section-title"><i class="bi bi-plus-circle me-2"></i>Create New Campaign</h3>
        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Campaign Title</label>
                        <input type="text" name="camp_title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Donation Type</label>
                        <select name="d_type" class="form-select" required>
                            <option value="blood">Blood Donation</option>
                            <option value="education">Education</option>
                            <option value="health">Health</option>
                            <option value="food">Food</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="camp_desc" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Target Amount</label>
                        <input type="number" name="est_amt" class="form-control" placeholder="Enter target amount" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Campaign Image</label>
                        <input type="file" name="c_image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="a_submit" class="btn btn-primary">Create Campaign</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- All Campaigns Section -->
    <div id="campaigns" class="section-container">
        <h3 class="section-title"><i class="bi bi-megaphone me-2"></i>All Campaigns</h3>
        <div class="campaign-grid">
            <?php
            $sql = "SELECT * FROM campaigns ORDER BY camp_id DESC";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
            ?>
            <div class="campaign-card">
                <div class="campaign-img">
                    <img src="<?= $row['camp_img'] ?>" alt="<?= $row['camp_title'] ?>">
                </div>
                <div class="campaign-body">
                    <div class="campaign-type type-<?= $row['camp_type'] ?>"><?= ucfirst($row['camp_type']) ?></div>
                    <div class="campaign-title"><?= $row['camp_title'] ?></div>
                    <small class="text-muted">Target: $<?= number_format($row['est_amt'], 2) ?></small>
                    <div class="mt-2">
                        <span class="badge <?= $row['status'] == 'active' ? 'bg-success' : 'bg-warning' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="text-center py-5">No campaigns found.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Donors Section -->
    <div id="donors" class="section-container">
        <h3 class="section-title"><i class="bi bi-people me-2"></i>Registered Donors</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM users WHERE user_type='donor'";
                    $result = mysqli_query($conn, $sql);
                    $i = 1;
                    while($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['place'] ?></td>
                        <td><a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this donor?')"><i class="bi bi-trash"></i> Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recipients Section -->
    <div id="recipients" class="section-container">
        <h3 class="section-title"><i class="bi bi-person-plus me-2"></i>Registered Recipients</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM users WHERE user_type='recipient'";
                    $result = mysqli_query($conn, $sql);
                    $i = 1;
                    while($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $row['fname'] . ' ' . $row['lname'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['place'] ?></td>
                        <td><a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this recipient?')"><i class="bi bi-trash"></i> Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notify Donors Section -->
    <div id="notify" class="section-container">
        <h3 class="section-title"><i class="bi bi-envelope me-2"></i>Notify Donors</h3>
        <div class="form-card">
            <form method="post">
                <?php
                $query = "SELECT id, fname, lname, email FROM users WHERE user_type='donor'";
                $result = mysqli_query($conn, $query);
                while($row = mysqli_fetch_assoc($result)):
                ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="recipient[]" value="<?= $row['email'] ?>" id="donor_<?= $row['id'] ?>">
                    <label class="form-check-label" for="donor_<?= $row['id'] ?>">
                        <?= $row['fname'] . ' ' . $row['lname'] ?> (<?= $row['email'] ?>)
                    </label>
                </div>
                <?php endwhile; ?>
                <button type="submit" name="send_mail" class="btn btn-primary mt-3">Send Notification</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.section-container').forEach(section => {
            section.classList.remove('active');
        });
        // Show selected section
        document.getElementById(sectionId).classList.add('active');
        
        // Update active menu link
        document.querySelectorAll('.menu-link').forEach(link => {
            link.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
    }
</script>
</body>
</html>