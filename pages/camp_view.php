<?php
session_start();
include "../assets/db/conn.php";

// Check login
if (empty(@$_SESSION['d_logged']) && empty(@$_SESSION['admin_logged']) && empty(@$_SESSION['r_logged'])) {
    header('Location: log_in.php');
    exit();
}

// Get IDs
$recip_id = isset($_GET['crby']) ? intval($_GET['crby']) : 0;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header('Location: index.php');
    exit();
}

// Process donation
if (isset($_POST['don_sub'])) {
    $camp_id = $id;
    $don_id = $_SESSION['d_id'];
    $don_amt = isset($_POST['d_amt']) ? floatval($_POST['d_amt']) : 0;

    if (empty($don_amt) || $don_amt <= 0) {
        echo '<script>alert("Invalid donation amount."); window.location.href = "camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
        exit;
    }

    $amt = "SELECT amt_collected, est_amt, camp_type FROM campaigns WHERE camp_id = $camp_id";
    $amtres = mysqli_query($conn, $amt);
    
    if (!$amtres) {
        echo '<script>alert("Database error: ' . mysqli_error($conn) . '"); window.location.href = "camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
        exit;
    }
    
    $fetchAmt = mysqli_fetch_assoc($amtres);
    $curr_amt = $fetchAmt['amt_collected'];
    $est_amt = $fetchAmt['est_amt'];
    $camp_type = $fetchAmt['camp_type'];
    $isBlood = ($camp_type === 'blood');
    $remaining_amount = $est_amt - $curr_amt;
    
    if ($isBlood && $don_amt > 1) {
        echo '<script>alert("Maximum 1 pint per person can be donated."); window.location.href = "camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
        exit;
    }
    
    if ($don_amt > $remaining_amount) {
        echo '<script>alert("Donation exceeds remaining amount."); window.location.href = "camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
        exit;
    }
    
    $updateAmt = $curr_amt + $don_amt;
    $roundpro = ceil(($updateAmt / $est_amt) * 100);
    
    // Check existing donation - FIXED with proper error checking
    $chckDon = "SELECT * FROM donations WHERE donor_id = $don_id AND camp_id = $camp_id";
    $result = mysqli_query($conn, $chckDon);
    
    if (!$result) {
        // Table might not exist, create it
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS donations (
            donation_id INT AUTO_INCREMENT PRIMARY KEY,
            donor_id INT,
            camp_id INT,
            donated_amt DECIMAL(10,2) DEFAULT 0,
            donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $result = mysqli_query($conn, $chckDon);
    }
    
    if ($result && mysqli_num_rows($result) > 0 && $isBlood) {
        echo '<script>alert("You have already donated to this campaign."); window.location.href = "camp_view.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
        exit;
    }
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $newAmount = $row['donated_amt'] + $don_amt;
        $updateSql = "UPDATE donations SET donated_amt = $newAmount WHERE donation_id = {$row['donation_id']}";
        mysqli_query($conn, $updateSql);
    } else {
        $insertSql = "INSERT INTO donations (donor_id, camp_id, donated_amt) VALUES ($don_id, $camp_id, $don_amt)";
        mysqli_query($conn, $insertSql);
    }
    
    $updateQuery = "UPDATE campaigns SET amt_collected = $updateAmt, progress = $roundpro WHERE camp_id = $camp_id";
    mysqli_query($conn, $updateQuery);
    
    if ($updateAmt >= $est_amt) {
        mysqli_query($conn, "UPDATE campaigns SET status='stop' WHERE camp_id = $camp_id");
    }
    
    echo '<script>window.location.href = "confirmation.php?id=' . $camp_id . '&crby=' . $recip_id . '";</script>';
    exit();
}

// Get campaign details
$queryCampaign = "SELECT * FROM campaigns WHERE camp_id = $id";
$resultCampaign = mysqli_query($conn, $queryCampaign);

if (!$resultCampaign || mysqli_num_rows($resultCampaign) == 0) {
    echo "Campaign not found!";
    exit();
}

$campaign = mysqli_fetch_assoc($resultCampaign);

$campTitle = $campaign['camp_title'];
$startDate = date('d M Y', strtotime($campaign['created_at']));
$campDesc = $campaign['camp_desc'];
$campPic = $campaign['camp_img'];
$estAmt = $campaign['est_amt'];
$amtCollected = $campaign['amt_collected'];
$campType = $campaign['camp_type'];
$bloodGroup = isset($campaign['blood_group']) ? $campaign['blood_group'] : '';
$donationAddress = isset($campaign['donation_address']) ? $campaign['donation_address'] : '';
$donationDate = isset($campaign['donation_date']) ? $campaign['donation_date'] : '';
$donationTime = isset($campaign['donation_time']) ? $campaign['donation_time'] : '';
$isBloodCampaign = ($campType === 'blood');

// Get creator name
$creator = "Admin";
if (isset($campaign['recip_id']) && $campaign['recip_id'] > 0) {
    $creatorQuery = "SELECT fname FROM users WHERE user_id = " . $campaign['recip_id'];
    $creatorResult = mysqli_query($conn, $creatorQuery);
    if ($creatorResult && mysqli_num_rows($creatorResult) > 0) {
        $creatorRow = mysqli_fetch_assoc($creatorResult);
        $creator = $creatorRow['fname'];
    }
}

// Calculate progress
$progress = ($estAmt > 0) ? min(round(($amtCollected / $estAmt) * 100, 2), 100) : 0;

// Check if donor has already donated
$hasAlreadyDonated = false;
if ($isBloodCampaign && isset($_SESSION['d_id'])) {
    $donorId = $_SESSION['d_id'];
    $checkDonation = "SELECT * FROM donations WHERE donor_id = $donorId AND camp_id = $id";
    $donationResult = mysqli_query($conn, $checkDonation);
    if ($donationResult && mysqli_num_rows($donationResult) > 0) {
        $hasAlreadyDonated = true;
    }
}

// Get donors list
$donors = array();
$dcounts = 0;
$queryDonors = "SELECT u.fname, u.profile_pic, d.donated_amt 
                FROM users u 
                INNER JOIN donations d ON u.id = d.donor_id 
                WHERE d.camp_id = $id";
$resultDonors = mysqli_query($conn, $queryDonors);
if ($resultDonors && mysqli_num_rows($resultDonors) > 0) {
    while ($donor = mysqli_fetch_assoc($resultDonors)) {
        $donors[] = $donor;
    }
    $dcounts = count($donors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $campTitle ?> | DonorHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <style>
        body { background: #f0f2f5; padding-top: 80px; }
        .camp-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 15px; }
        .progress-circle { width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; position: relative; }
        .progress-circle::before { content: ""; position: absolute; width: 75px; height: 75px; border-radius: 50%; background-color: #fff; }
        .progress-value { position: relative; font-size: 18px; font-weight: 700; color: #7d2ae8; }
        .sidebar-card { position: sticky; top: 100px; }
        @media (max-width: 768px) { body { padding-top: 70px; } .sidebar-card { position: static; margin-top: 20px; } }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <img src="../assets/images/logo.png" alt="logo" width="40" class="me-2">
            <span>DonorHub</span>
        </a>
        <a href="d_home.php?section=1" class="btn btn-outline-danger"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h1 class="fw-bold mb-2"><?= htmlspecialchars($campTitle) ?></h1>
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">Created: <?= $startDate ?></span>
                        <span class="badge bg-success">By: <?= htmlspecialchars($creator) ?></span>
                        <?php if($isBloodCampaign && !empty($bloodGroup)): ?>
                            <span class="badge bg-danger ms-2"><i class="bi bi-heart-pulse"></i> Blood: <?= $bloodGroup ?></span>
                        <?php endif; ?>
                    </div>
                    <img src="<?= $campPic ?>" class="camp-img mb-4" alt="<?= $campTitle ?>" onerror="this.src='../assets/images/logo.png'">
                    <h4 class="fw-bold mt-3">Description</h4>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($campDesc)) ?></p>
                    
                    <?php if($isBloodCampaign && !empty($donationAddress)): ?>
                    <div class="alert alert-info mt-3">
                        <h5><i class="bi bi-geo-alt"></i> Donation Location</h5>
                        <p class="mb-0"><strong>Address:</strong> <?= nl2br(htmlspecialchars($donationAddress)) ?></p>
                        <?php if(!empty($donationDate)): ?>
                            <p class="mb-0"><strong>Date:</strong> <?= date('F d, Y', strtotime($donationDate)) ?></p>
                        <?php endif; ?>
                        <?php if(!empty($donationTime)): ?>
                            <p class="mb-0"><strong>Time:</strong> <?= date('h:i A', strtotime($donationTime)) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sidebar-card">
                <div class="card-body text-center">
                    <div class="progress-circle" style="background: conic-gradient(#7d2ae8 <?= $progress * 3.6 ?>deg, #ededed 0deg);">
                        <span class="progress-value"><?= round($progress) ?>%</span>
                    </div>
                    <div class="mt-3">
                        <?php if($isBloodCampaign): ?>
                            <p><i class="bi bi-droplet text-danger"></i> <strong><?= $amtCollected ?></strong> / <?= $estAmt ?> pints raised</p>
                        <?php else: ?>
                            <p><i class="bi bi-currency-dollar text-success"></i> <strong>$<?= number_format($amtCollected, 2) ?></strong> / $<?= number_format($estAmt, 2) ?> raised</p>
                        <?php endif; ?>
                        <hr>
                        <p><i class="bi bi-people-fill"></i> <strong><?= $dcounts ?></strong> supporters</p>
                    </div>
                    
                    <?php if($isBloodCampaign && $hasAlreadyDonated): ?>
                        <button class="btn btn-secondary w-100 rounded-pill py-2" disabled><i class="bi bi-check-circle"></i> Already Donated</button>
                    <?php else: ?>
                        <button class="btn btn-danger w-100 rounded-pill py-2" data-bs-toggle="modal" data-bs-target="#DonateModal">
                            <i class="bi bi-heart-fill"></i> Donate Now
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-secondary w-100 rounded-pill py-2 mt-2" data-bs-toggle="modal" data-bs-target="#DonorsModal">
                        <i class="bi bi-people-fill"></i> View Supporters (<?= $dcounts ?>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Donate Modal -->
<div class="modal fade" id="DonateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><img src="../assets/images/logo.png" width="30" class="me-2"> Donate to <?= $campTitle ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['d_logged'] ?? 'Guest' ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= $_SESSION['d_email'] ?? '' ?>" disabled>
                    </div>
                    <?php if($isBloodCampaign): ?>
                        <div class="mb-3">
                            <label class="form-label">Quantity (in pints)</label>
                            <input type="text" class="form-control" value="1 pint (450-500 ml)" disabled>
                            <input type="hidden" name="d_amt" value="1">
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Amount ($)</label>
                            <input type="number" name="d_amt" class="form-control" min="100" placeholder="Enter amount" required>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="don_sub" class="btn btn-danger">Confirm Donation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Donors Modal -->
<div class="modal fade" id="DonorsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-people-fill"></i> Supporters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if(count($donors) > 0): ?>
                    <?php foreach($donors as $donor): ?>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= $donor['profile_pic'] ?>" class="rounded-circle me-3" width="50" height="50" onerror="this.src='../assets/images/logo.png'">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($donor['fname']) ?></h6>
                                <small class="text-muted">Donated: <?= $isBloodCampaign ? $donor['donated_amt'] . ' pint(s)' : '$' . number_format($donor['donated_amt'], 2) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No donors yet. Be the first!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>