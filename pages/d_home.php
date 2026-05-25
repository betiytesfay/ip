<?php
session_start();
include "../assets/db/conn.php";
if (empty(@$_SESSION['d_logged'])) { header('Location: index.php'); exit(); }
if (isset($_POST['logout'])) { unset($_SESSION['d_logged']); header("Location: log_in.php"); exit(); }

$did = $_SESSION['d_id'];
$donations_result = mysqli_query($conn, "SELECT COUNT(*) as t FROM donations WHERE donor_id=$did");
$totalDonations = ($donations_result && mysqli_num_rows($donations_result) > 0) ? mysqli_fetch_assoc($donations_result)['t'] : 0;
$amount_result = mysqli_query($conn, "SELECT COALESCE(SUM(donated_amt),0) as t FROM donations WHERE donor_id=$did");
$totalAmount = ($amount_result && mysqli_num_rows($amount_result) > 0) ? mysqli_fetch_assoc($amount_result)['t'] : 0;
$camp_result = mysqli_query($conn, "SELECT COUNT(*) as t FROM campaigns WHERE status='active'");
$activeCamps = ($camp_result && mysqli_num_rows($camp_result) > 0) ? mysqli_fetch_assoc($camp_result)['t'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/donor.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
</head>
<body>

<button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i> Menu</button>

<div class="sidebar">
    <div>
        <a href="#" class="sidebar-brand">
            <img src="../assets/images/logo.png" width="34" height="34" alt="logo">
            <span>DonorHub <sup><span class="badge text-bg-warning rounded-pill" style="font-size:0.6rem;">Donor</span></sup></span>
        </a>
        <hr>
        <ul class="nav flex-column mt-2">
            <li class="nav-item"><a href="#" class="nav-link active" onclick="showSection(0,this)"><i class="bi bi-house-fill"></i> Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link" onclick="showSection(1,this)"><i class="bi bi-grid-fill"></i> Available Campaigns</a></li>
            <li class="nav-item"><a href="#" class="nav-link" onclick="showSection(2,this)"><i class="bi bi-exclamation-diamond-fill"></i> Emergency Campaigns</a></li>
            <li class="nav-item"><a href="#" class="nav-link" onclick="showSection(3,this)"><i class="bi bi-clock-history"></i> My History</a></li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <hr>
        <div class="dropdown">
            <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown">
                <img src="<?= $_SESSION['dprofile_pic'] ?>" alt="profile">
                <span class="name"><?= $_SESSION['d_logged'] ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="../pages/profile_dupdate.php?id=<?= $_SESSION['d_id'] ?>"><i class="bi bi-pen me-2"></i>Edit Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form method="post"><button class="dropdown-item text-danger" name="logout"><i class="bi bi-box-arrow-left me-2"></i>Logout</button></form></li>
            </ul>
        </div>
    </div>
</div>

<div class="main">
    <!-- Section 0: Home -->
    <div id="section0" class="section active">
        <div class="welcome-hero">
            <div>
                <h2>Welcome back, <?= $_SESSION['d_logged'] ?>! 👋</h2>
                <p>Every donation you make changes a life.</p>
                <button class="btn btn-light mt-3" onclick="showSection(1,document.querySelectorAll('.sidebar .nav-link')[1])"><i class="bi bi-heart-fill text-danger me-2"></i>Browse Campaigns</button>
            </div>
            <img src="<?= $_SESSION['dprofile_pic'] ?>" alt="profile">
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#e8e8f5;"><i class="bi bi-heart-fill" style="color:#6c63ff;"></i></div><div><h5><?= $totalDonations ?></h5><small>Total Donations</small></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#e0fff4;"><i class="bi bi-currency-dollar" style="color:#00b894;"></i></div><div><h5>$<?= number_format($totalAmount) ?></h5><small>Total Donated</small></div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#fff3e0;"><i class="bi bi-megaphone-fill" style="color:#e17055;"></i></div><div><h5><?= $activeCamps ?></h5><small>Active Campaigns</small></div></div></div>
        </div>
    </div>

    <!-- Section 1: Available Campaigns -->
    <div id="section1" class="section">
        <div class="page-header"><h4><i class="bi bi-grid-fill me-2"></i>Available Campaigns</h4></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
            <?php
            $result = mysqli_query($conn, "SELECT c.*, u.fname FROM campaigns c LEFT JOIN users u ON c.recip_id = u.id WHERE c.status='active'");
            if($result && mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
                    $progress = $row['est_amt'] > 0 ? min(round(($row['amt_collected']/$row['est_amt'])*100,1),100) : 0;
            ?>
            <div class="col"><div class="camp-card card"><img src="<?= $row['camp_img'] ?>" class="card-img-top" onerror="this.src='../assets/images/logo.png'"><div class="card-body"><div class="camp-title"><?= $row['camp_title'] ?></div><span class="camp-type-badge badge-<?= $row['camp_type'] ?>"><?= ucfirst($row['camp_type']) ?></span><div class="progress-wrap"><div class="progress"><div class="progress-bar" style="width:<?= $progress ?>%"></div></div><div class="labels"><span><?= $row['amt_collected'] ?> raised</span><span><?= $progress ?>%</span></div></div><div class="card-footer-row"><div class="creator">By <span><?= $row['fname'] ?></span></div><a href="camp_view.php?id=<?= $row['camp_id'] ?>" class="btn btn-sm btn-primary px-3">Donate</a></div></div></div></div>
            <?php endwhile; else: ?>
            <div class="col-12"><div class="empty-state"><i class="bi bi-inbox"></i>No campaigns available.</div></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section 2: Emergency Campaigns -->
    <div id="section2" class="section">
        <div class="page-header"><h4><i class="bi bi-exclamation-diamond-fill me-2" style="color:#d63031;"></i>Emergency Campaigns</h4></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
            <?php
            $result = mysqli_query($conn, "SELECT c.*, u.fname FROM campaigns c LEFT JOIN users u ON c.recip_id = u.id WHERE c.status='active' AND c.camp_type='blood'");
            if($result && mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
                    $progress = $row['est_amt'] > 0 ? min(round(($row['amt_collected']/$row['est_amt'])*100,1),100) : 0;
            ?>
            <div class="col"><div class="camp-card card"><img src="<?= $row['camp_img'] ?>" class="card-img-top" onerror="this.src='../assets/images/logo.png'"><div class="card-body"><div class="camp-title"><?= $row['camp_title'] ?></div><span class="camp-type-badge badge-<?= $row['camp_type'] ?>"><?= ucfirst($row['camp_type']) ?></span><div class="progress-wrap"><div class="progress"><div class="progress-bar" style="width:<?= $progress ?>%"></div></div><div class="labels"><span><?= $row['amt_collected'] ?> raised</span><span><?= $progress ?>%</span></div></div><div class="card-footer-row"><div class="creator">By <span><?= $row['fname'] ?></span></div><a href="camp_view.php?id=<?= $row['camp_id'] ?>" class="btn btn-sm btn-danger px-3">Donate</a></div></div></div></div>
            <?php endwhile; else: ?>
            <div class="col-12"><div class="empty-state"><i class="bi bi-inbox"></i>No emergency campaigns.</div></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section 3: History -->
    <div id="section3" class="section">
        <div class="page-header"><h4><i class="bi bi-clock-history me-2"></i>My Donation History</h4></div>
        <?php
        $history = mysqli_query($conn, "SELECT c.camp_title,c.camp_type,d.donated_amt,d.donation_date FROM donations d JOIN campaigns c ON c.camp_id = d.camp_id WHERE d.donor_id = $did ORDER BY d.donation_date DESC");
        if($history && mysqli_num_rows($history) > 0): ?>
        <div class="history-card">
            <?php while($d = mysqli_fetch_assoc($history)): ?>
            <div class="history-item"><div><div class="camp-name"><?= $d['camp_title'] ?></div><small><?= date('d M Y', strtotime($d['donation_date'])) ?></small></div><div class="amt">$<?= $d['donated_amt'] ?></div></div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-inbox"></i>No donations yet.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showSection(n, el) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById('section' + n).classList.add('active');
    document.querySelectorAll('.sidebar .nav-link').forEach(a => a.classList.remove('active'));
    if (el) el.classList.add('active');
    history.pushState(null, '', '?section=' + n);
}
function toggleSidebar() { document.querySelector('.sidebar').classList.toggle('active'); }
const urlSection = new URLSearchParams(window.location.search).get('section');
if (urlSection !== null) showSection(parseInt(urlSection), document.querySelectorAll('.sidebar .nav-link')[parseInt(urlSection)]);
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.sidebar');
    const btn = document.querySelector('.mobile-menu-btn');
    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
        if (!sidebar.contains(e.target) && !btn.contains(e.target)) sidebar.classList.remove('active');
    }
});
window.addEventListener('resize', function() { if (window.innerWidth > 992) document.querySelector('.sidebar').classList.remove('active'); });
</script>
</body>
</html>