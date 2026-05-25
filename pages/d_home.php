<?php
session_start();
$d_type = $_SESSION['d_type'];
include "../assets/db/conn.php";
if (empty(@$_SESSION['d_logged'])) { header('Location: index.php'); }
if (isset($_POST['logout'])) { unset($_SESSION['d_logged']); header("Location: log_in.php"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <title>Donor Home</title>
  <style>
    body { background: #f4f6fb; overflow-x: hidden; }
    .sidebar { width: 250px; min-height: 100vh; background: #1a1a2e; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; justify-content: space-between; z-index: 100; }
    .sidebar-brand { padding: 20px 18px 10px; display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .sidebar-brand img { border-radius: 50%; }
    .sidebar-brand span { color: #fff; font-weight: 700; font-size: 1rem; }
    .sidebar hr { border-color: rgba(255,255,255,0.15); margin: 0 16px; }
    .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 10px 18px; border-radius: 8px; margin: 2px 10px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; transition: all 0.2s; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.12); color: #fff; }
    .sidebar .nav-link i { font-size: 1rem; width: 18px; }
    .sidebar-footer { padding: 12px; }
    .profile-btn { background: rgba(255,255,255,0.08); border: none; border-radius: 10px; padding: 10px 12px; width: 100%; display: flex; align-items: center; gap: 10px; color: #fff; cursor: pointer; transition: background 0.2s; }
    .profile-btn:hover { background: rgba(255,255,255,0.15); }
    .profile-btn img { border-radius: 50%; width: 34px; height: 34px; object-fit: cover; }
    .profile-btn .name { font-size: 0.85rem; font-weight: 600; text-align: left; flex: 1; }
    .main { margin-left: 250px; padding: 28px 24px; min-height: 100vh; }
    .section { display: none; }
    .section.active { display: block; }
    .page-header { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; }
    .page-header h4 { margin: 0; font-weight: 700; color: #1a1a2e; }
    .camp-card { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); transition: transform 0.2s, box-shadow 0.2s; height: 100%; }
    .camp-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .camp-card img { height: 160px; object-fit: cover; width: 100%; }
    .camp-card .card-body { padding: 14px; display: flex; flex-direction: column; }
    .camp-title { font-weight: 700; font-size: 0.95rem; color: #1a1a2e; margin-bottom: 4px; }
    .camp-type-badge { display: inline-block; font-size: 0.72rem; padding: 2px 9px; border-radius: 20px; font-weight: 600; margin-bottom: 10px; }
    .badge-blood { background: #ffe0e0; color: #d63031; }
    .badge-education { background: #e0f0ff; color: #0984e3; }
    .badge-health { background: #e0fff4; color: #00b894; }
    .badge-food { background: #fff3e0; color: #e17055; }
    .progress-wrap { margin: 8px 0 12px; }
    .progress-wrap .labels { display: flex; justify-content: space-between; font-size: 0.78rem; color: #888; margin-bottom: 4px; }
    .progress-wrap .labels span:last-child { font-weight: 600; color: #6c63ff; }
    .progress { height: 7px; border-radius: 10px; background: #ececec; }
    .progress-bar { background: linear-gradient(90deg, #6c63ff, #a29bfe); border-radius: 10px; }
    .card-footer-row { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 10px; border-top: 1px solid #f0f0f0; }
    .creator { font-size: 0.78rem; color: #888; }
    .creator span { color: #1a1a2e; font-weight: 600; }
    .history-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; background: #fff; }
    .history-item { display: flex; justify-content: space-between; align-items: center; padding: 13px 18px; border-bottom: 1px solid #f4f4f4; transition: background 0.15s; }
    .history-item:last-child { border-bottom: none; }
    .history-item:hover { background: #f8f8ff; }
    .history-item .camp-name { font-weight: 600; font-size: 0.9rem; color: #1a1a2e; }
    .history-item .amt { font-weight: 700; color: #6c63ff; font-size: 0.9rem; }
    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; width: 100%; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }
    .welcome-hero { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%); border-radius: 20px; padding: 40px 36px; color: #fff; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
    .welcome-hero h2 { font-weight: 800; font-size: 1.8rem; margin-bottom: 6px; }
    .welcome-hero p { color: rgba(255,255,255,0.7); margin: 0; font-size: 0.95rem; }
    .welcome-hero img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.3); flex-shrink: 0; }
    .stat-card { background: #fff; border-radius: 14px; padding: 20px 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); display: flex; align-items: center; gap: 16px; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .stat-card h5 { margin: 0; font-weight: 800; font-size: 1.5rem; color: #1a1a2e; }
    .stat-card small { color: #888; font-size: 0.8rem; }
    .quick-card { background: #fff; border-radius: 14px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-align: center; text-decoration: none; color: inherit; display: block; }
    .quick-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); color: inherit; }
    .quick-card i { font-size: 2rem; display: block; margin-bottom: 10px; }
    .quick-card span { font-weight: 600; font-size: 0.9rem; color: #1a1a2e; }
  </style>
</head>
<body>

<div class="sidebar">
  <div>
    <a href="" class="sidebar-brand">
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
  <?php include '../includes/message.php'; ?>

  <?php
    $did = $_SESSION['d_id'];
    $totalDonations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM donations WHERE donor_id=$did"))['t'];
    $totalAmount    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(donated_amt),0) as t FROM donations WHERE donor_id=$did"))['t'];
    $activeCamps    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM campaigns WHERE status='active'"))['t'];
  ?>

  <!-- Section 0: Home -->
  <div id="section0" class="section active">
    <div class="welcome-hero">
      <div>
        <h2>Welcome back, <?= $_SESSION['d_logged'] ?>! 👋</h2>
        <p>Every donation you make changes a life. Thank you for being here.</p>
        <button class="btn btn-light mt-3" onclick="showSection(1,document.querySelectorAll('.sidebar .nav-link')[1])" style="border-radius:20px;font-weight:600;"><i class="bi bi-heart-fill text-danger me-2"></i>Browse Campaigns</button>
      </div>
      <img src="<?= $_SESSION['dprofile_pic'] ?>" alt="profile">
    </div>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#e8e8f5;"><i class="bi bi-heart-fill" style="color:#6c63ff;"></i></div>
          <div><h5><?= $totalDonations ?></h5><small>Total Donations Made</small></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#e0fff4;"><i class="bi bi-currency-dollar" style="color:#00b894;"></i></div>
          <div><h5>$<?= number_format($totalAmount) ?></h5><small>Total Amount Donated</small></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#fff3e0;"><i class="bi bi-megaphone-fill" style="color:#e17055;"></i></div>
          <div><h5><?= $activeCamps ?></h5><small>Active Campaigns</small></div>
        </div>
      </div>
    </div>
    <h6 class="fw-bold mb-3" style="color:#1a1a2e;">Quick Actions</h6>
    <div class="row g-3">
      <div class="col-6 col-md-4">
        <a class="quick-card" onclick="showSection(1,document.querySelectorAll('.sidebar .nav-link')[1])">
          <i class="bi bi-grid-fill" style="color:#6c63ff;"></i><span>Browse Campaigns</span>
        </a>
      </div>
      <div class="col-6 col-md-4">
        <a class="quick-card" onclick="showSection(2,document.querySelectorAll('.sidebar .nav-link')[2])">
          <i class="bi bi-exclamation-diamond-fill" style="color:#d63031;"></i><span>Emergency</span>
        </a>
      </div>
      <div class="col-6 col-md-4">
        <a class="quick-card" onclick="showSection(3,document.querySelectorAll('.sidebar .nav-link')[3])">
          <i class="bi bi-clock-history" style="color:#00b894;"></i><span>My History</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Section 1: Available Campaigns -->
  <div id="section1" class="section">
    <div class="page-header">
      <h4><i class="bi bi-grid-fill me-2" style="color:#6c63ff;"></i>Available Campaigns</h4>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 justify-content-center">
      <?php
      $sql = "SELECT c.camp_id,c.camp_title,c.camp_img,c.est_amt,c.amt_collected,c.camp_type,c.blood_group,u.user_id,u.fname
              FROM campaigns c JOIN users u ON c.recip_id=u.user_id
              WHERE c.status='active' AND u.user_type='recipient'";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0):
        foreach ($result as $row):
          $isBlood = ($row['camp_type'] === 'blood');
          $progress = $row['est_amt'] > 0 ? min(round(($row['amt_collected']/$row['est_amt'])*100,1),100) : 0;
      ?>
      <div class="col">
        <div class="camp-card card">
          <img src="<?= $row['camp_img'] ?>" alt="campaign">
          <div class="card-body">
            <div class="camp-title"><?= $row['camp_title'] ?></div>
            <span class="camp-type-badge badge-<?= $row['camp_type'] ?>"><?= ucfirst($row['camp_type']) ?></span>
            <?php if ($isBlood && !empty($row['blood_group'])): ?>
              <div class="mb-2"><small class="text-danger"><i class="bi bi-heart-pulse"></i> <?= $row['blood_group'] ?></small></div>
            <?php endif; ?>
            <div class="progress-wrap">
              <div class="labels">
                <span><?= $isBlood ? $row['amt_collected'].' pints' : '$'.$row['amt_collected'] ?> raised</span>
                <span><?= $progress ?>%</span>
              </div>
              <div class="progress"><div class="progress-bar" style="width:<?= $progress ?>%"></div></div>
            </div>
            <div class="card-footer-row">
              <div class="creator">By <span><?= $row['fname'] ?></span></div>
              <a href="../pages/camp_view.php?id=<?= $row['camp_id'] ?>&crby=<?= $row['user_id'] ?>" class="btn btn-sm btn-primary px-3" style="border-radius:20px;">Donate</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; else: ?>
      <div class="col-12"><div class="empty-state"><i class="bi bi-inbox"></i>No campaigns available right now.</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Section 2: Emergency Campaigns -->
  <div id="section2" class="section">
    <div class="page-header">
      <h4><i class="bi bi-exclamation-diamond-fill me-2" style="color:#d63031;"></i>Emergency Campaigns</h4>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 justify-content-center">
      <?php
      $sql = "SELECT c.camp_id,c.camp_title,c.camp_img,c.est_amt,c.amt_collected,c.camp_type,c.blood_group,u.user_id,u.fname
              FROM campaigns c JOIN users u ON c.recip_id=u.user_id
              WHERE c.status='active' AND u.user_type='admin'";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0):
        foreach ($result as $row):
          $isBlood = ($row['camp_type'] === 'blood');
          $progress = $row['est_amt'] > 0 ? min(round(($row['amt_collected']/$row['est_amt'])*100,1),100) : 0;
      ?>
      <div class="col">
        <div class="camp-card card">
          <img src="<?= $row['camp_img'] ?>" alt="campaign">
          <div class="card-body">
            <div class="camp-title"><?= $row['camp_title'] ?></div>
            <span class="camp-type-badge badge-<?= $row['camp_type'] ?>"><?= ucfirst($row['camp_type']) ?></span>
            <?php if ($isBlood && !empty($row['blood_group'])): ?>
              <div class="mb-2"><small class="text-danger"><i class="bi bi-heart-pulse"></i> <?= $row['blood_group'] ?></small></div>
            <?php endif; ?>
            <div class="progress-wrap">
              <div class="labels">
                <span><?= $isBlood ? $row['amt_collected'].' pints' : '$'.$row['amt_collected'] ?> raised</span>
                <span><?= $progress ?>%</span>
              </div>
              <div class="progress"><div class="progress-bar" style="width:<?= $progress ?>%"></div></div>
            </div>
            <div class="card-footer-row">
              <div class="creator">By <span><?= $row['fname'] ?></span></div>
              <a href="../pages/camp_view.php?id=<?= $row['camp_id'] ?>&crby=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger px-3" style="border-radius:20px;">Donate</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; else: ?>
      <div class="col-12"><div class="empty-state"><i class="bi bi-inbox"></i>No emergency campaigns right now.</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Section 3: History -->
  <div id="section3" class="section">
    <div class="page-header">
      <h4><i class="bi bi-clock-history me-2" style="color:#6c63ff;"></i>My Donation History</h4>
    </div>
    <?php
    $query = "SELECT c.camp_title,c.camp_type,d.donated_amt,d.donation_date
              FROM donations d INNER JOIN campaigns c ON c.camp_id=d.camp_id
              WHERE d.donor_id=$did ORDER BY d.donation_date DESC";
    $donations = mysqli_fetch_all(mysqli_query($conn,$query), MYSQLI_ASSOC);
    ?>
    <?php if (!empty($donations)): ?>
    <div class="history-card">
      <?php foreach ($donations as $d): $isBlood = ($d['camp_type']==='blood'); ?>
      <div class="history-item">
        <div>
          <div class="camp-name"><?= $d['camp_title'] ?></div>
          <small class="text-muted"><?= date('d M Y', strtotime($d['donation_date'])) ?></small>
        </div>
        <div class="amt">
          <?php if ($isBlood): ?>
            <i class="bi bi-droplet-fill text-danger"></i> <?= $d['donated_amt'] ?> pint<?= $d['donated_amt']!=1?'s':'' ?>
          <?php else: ?>
            $<?= $d['donated_amt'] ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
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
  return false;
}
const urlSection = new URLSearchParams(window.location.search).get('section');
if (urlSection !== null) showSection(parseInt(urlSection), document.querySelectorAll('.sidebar .nav-link')[parseInt(urlSection)]);

window.addEventListener('popstate', function() {
  const s = new URLSearchParams(window.location.search).get('section') || '0';
  const n = parseInt(s);
  document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
  document.getElementById('section' + n).classList.add('active');
  document.querySelectorAll('.sidebar .nav-link').forEach(a => a.classList.remove('active'));
  document.querySelectorAll('.sidebar .nav-link')[n].classList.add('active');
});
</script>
</body>
</html>


