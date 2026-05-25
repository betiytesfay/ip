
<?php
session_start();
include "../assets/db/conn.php";

// Check login
if (empty(@$_SESSION['d_logged'])) {
    header('Location: log_in.php');
    exit();
}

// Get IDs
$camp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$recip_id = isset($_GET['crby']) ? intval($_GET['crby']) : 0;

if ($camp_id == 0) {
    header('Location: d_home.php');
    exit();
}

// Validate donor ID from session
if (empty($_SESSION['d_id'])) {
    die("Error: Donor ID not found in session. Please log in again.");
}

// Get campaign data
$camp_result = mysqli_query($conn, "SELECT * FROM campaigns WHERE camp_id = $camp_id");
if (!$camp_result) {
    die("Campaign Query Error: " . mysqli_error($conn));
}
$camp = mysqli_fetch_assoc($camp_result);
if (!$camp) {
    die("Error: Campaign not found.");
}

// Get donor data - using correct column name 'user_id'
$donor_id = intval($_SESSION['d_id']);
$donor_result = mysqli_query($conn, "SELECT fname, lname, email FROM users WHERE id = $donor_id");
if (!$donor_result) {
    die("Donor Query Error: " . mysqli_error($conn));
}
$donor = mysqli_fetch_assoc($donor_result);
if (!$donor) {
    die("Error: Donor not found.");
}

// Get donation amount
$donation_result = mysqli_query($conn, "SELECT donated_amt FROM donations WHERE donor_id = $donor_id AND camp_id = $camp_id ORDER BY donation_id DESC LIMIT 1");
if (!$donation_result) {
    die("Donation Query Error: " . mysqli_error($conn));
}
$donated_amt = 0;
if (mysqli_num_rows($donation_result) > 0) {
    $donation = mysqli_fetch_assoc($donation_result);
    $donated_amt = $donation['donated_amt'];
}

// Get recipient name
$recipient_name = "Organization";
if ($recip_id > 0) {
    $recipient_result = mysqli_query($conn, "SELECT fname FROM users WHERE id = $recip_id");
    if (!$recipient_result) {
        die("Recipient Query Error: " . mysqli_error($conn));
    }
    if (mysqli_num_rows($recipient_result) > 0) {
        $recipient = mysqli_fetch_assoc($recipient_result);
        $recipient_name = $recipient['fname'];
    }
}

$isBlood = isset($camp['camp_type']) && $camp['camp_type'] === 'blood';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You | DonorHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thank-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon i { font-size: 40px; color: white; }
        .btn-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 10px 25px; border-radius: 50px; color: white; margin: 5px; }
        .btn-custom:hover { color: white; }
    </style>
</head>
<body>
    <div class="thank-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h2 class="fw-bold">Thank You, <?= htmlspecialchars($donor['fname']) ?>! ❤️</h2>
        <p class="text-muted">Your donation has been successfully processed.</p>
        
        <div class="alert alert-success mt-3">
            <strong><?= htmlspecialchars($camp['camp_title']) ?></strong><br>
            <?php if($isBlood): ?>
                <i class="bi bi-droplet text-danger"></i> <strong><?= $donated_amt ?></strong> pint(s) donated
            <?php else: ?>
                <i class="bi bi-currency-dollar text-success"></i> <strong>$<?= number_format($donated_amt, 2) ?></strong> donated
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <a href="d_home.php" class="btn-custom btn">Go to Dashboard</a>
            <a href="camp_view.php?id=<?= $camp_id ?>&crby=<?= $recip_id ?>" class="btn btn-outline-secondary rounded-pill">Back to Campaign</a>
        </div>
        
        <hr>
        <p class="small text-muted">Receipt sent to <?= htmlspecialchars($donor['email']) ?></p>
    </div>
</body>
</html>