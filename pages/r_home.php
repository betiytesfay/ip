<?php
session_start();
include "../assets/db/conn.php";
if (empty(@$_SESSION['r_logged'])) {
  header('Location: index.php');
}
if (isset($_POST['logout'])) {
  unset($_SESSION['r_logged']);
  header("Location: log_in.php");
}

if (isset($_POST['r_submit'])) {
  if (!$conn || !is_object($conn)) {
    $_SESSION['message'] = "Database connection error.";
    header("Location: r_home.php"); exit();
  }
  $d_type = mysqli_real_escape_string($conn, $_POST['d_type']);
  $camp_title = mysqli_real_escape_string($conn, $_POST['camp_title']);
  $camp_desc = mysqli_real_escape_string($conn, $_POST['camp_desc']);
  $est_amt = mysqli_real_escape_string($conn, $_POST['est_amt']);
  $targetDirectory = '../pages/camp_image/';
  $c_image = $targetDirectory . basename($_FILES['c_image']['name']);
  move_uploaded_file($_FILES['c_image']['tmp_name'], $c_image);
  $recip_id = $_SESSION['r_id'];
  $donation_address = $donation_date = $donation_time = '';
  $blood_group = NULL;
  if ($d_type === 'blood') {
    $donation_address = isset($_POST['donation_address']) ? mysqli_real_escape_string($conn, $_POST['donation_address']) : '';
    $donation_date = isset($_POST['donation_date']) ? mysqli_real_escape_string($conn, $_POST['donation_date']) : '';
    $donation_time = isset($_POST['donation_time']) ? mysqli_real_escape_string($conn, $_POST['donation_time']) : '';
    $blood_group = isset($_POST['blood_group']) ? mysqli_real_escape_string($conn, $_POST['blood_group']) : NULL;
  }
  $camp_qry = "INSERT INTO campaigns(camp_title,camp_type,camp_desc,camp_img,est_amt,recip_id,status,donation_address,donation_date,donation_time,blood_group) VALUES('$camp_title','$d_type','$camp_desc','$c_image','$est_amt','$recip_id','pending','$donation_address','$donation_date','$donation_time','$blood_group')";
  $exe = mysqli_query($conn, $camp_qry);
  $_SESSION['message'] = $exe ? "Campaign submitted for approval!" : "Error! Campaign not created.";
  header("Location: r_home.php"); exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <title>Recipient Home</title>
  <style>
    body { background: #f4f6fb; overflow-x: hidden; }

    .sidebar {
      width: 250px; min-height: 100vh;
      background: #1a1a2e;
      position: fixed; top: 0; left: 0;
      display: flex; flex-direction: column;
      justify-content: space-between; z-index: 100;
    }
    .sidebar-brand {
      padding: 20px 18px 10px;
      display: flex; align-items: center; gap: 10px; text-decoration: none;
    }
    .sidebar-brand img { border-radius: 50%; }
    .sidebar-brand span { color: #fff; font-weight: 700; font-size: 1rem; }
    .sidebar hr { border-color: rgba(255,255,255,0.15); margin: 0 16px; }
    .sidebar .nav-link {
      color: rgba(255,255,255,0.7); padding: 10px 18px;
      border-radius: 8px; margin: 2px 10px;
      display: flex; align-items: center; gap: 10px;
      font-size: 0.9rem; transition: all 0.2s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background: rgba(255,255,255,0.12); color: #fff;
    }
    .sidebar .nav-link i { font-size: 1rem; width: 18px; }

    .sidebar-footer { padding: 12px; }
    .profile-btn {
      background: rgba(255,255,255,0.08); border: none;
      border-radius: 10px; padding: 10px 12px; width: 100%;
      display: flex; align-items: center; gap: 10px;
      color: #fff; cursor: pointer; transition: background 0.2s;
    }
    .profile-btn:hover { background: rgba(255,255,255,0.15); }
    .profile-btn img { border-radius: 50%; width: 34px; height: 34px; object-fit: cover; }
    .profile-btn .name { font-size: 0.85rem; font-weight: 600; text-align: left; flex: 1; }

    .main { margin-left: 250px; padding: 28px 24px; min-height: 100vh; }
    .section { display: none; }
    .section.active { display: block; }

    .page-header { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; }
    .page-header h4 { margin: 0; font-weight: 700; color: #1a1a2e; }

    /* FORM CARD */
    .form-card {
      background: #fff; border-radius: 16px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.07);
      padding: 28px; max-width: 780px;
    }

    /* CAMPAIGN CARDS */
    .camp-card {
      border: none; border-radius: 14px; overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      transition: transform 0.2s, box-shadow 0.2s; height: 100%;
    }
    .camp-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .camp-card img { height: 160px; object-fit: cover; width: 100%; }
    .camp-card .card-body { padding: 14px; display: flex; flex-direction: column; }
    .camp-card .camp-title { font-weight: 700; font-size: 0.95rem; color: #1a1a2e; margin-bottom: 4px; }
    .camp-type-badge {
      display: inline-block; font-size: 0.72rem;
      padding: 2px 9px; border-radius: 20px;
      font-weight: 600; margin-bottom: 10px;
    }
    .badge-blood { background: #ffe0e0; color: #d63031; }
    .badge-education { background: #e0f0ff; color: #0984e3; }
    .badge-health { background: #e0fff4; color: #00b894; }
    .badge-food { background: #fff3e0; color: #e17055; }

    .progress-wrap { margin: 8px 0 12px; }
    .progress-wrap .labels { display: flex; justify-content: space-between; font-size: 0.78rem; color: #888; margin-bottom: 4px; }
    .progress-wrap .labels span:last-child { font-weight: 600; color: #6c63ff; }
    .progress { height: 7px; border-radius: 10px; background: #ececec; }
    .progress-bar { background: linear-gradient(90deg, #6c63ff, #a29bfe); border-radius: 10px; }

    .card-footer-row {
      display: flex; justify-content: space-between;
      align-items: center; margin-top: auto;
      padding-top: 10px; border-top: 1px solid #f0f0f0;
    }

    /* STATUS BADGES */
    .status-badge { font-size: 0.75rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
    .status-active { background: #e0fff4; color: #00b894; }
    .status-pending { background: #e0f0ff; color: #0984e3; }
    .status-stop { background: #fff3e0; color: #e17055; }
    .status-inactive, .status-rejected { background: #ffe0e0; color: #d63031; }

    /* PROGRESS TABLE */
    .progress-table { border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .progress-table table { margin: 0; }
    .progress-table thead th { background: #1a1a2e; color: #fff; font-weight: 600; font-size: 0.85rem; border: none; padding: 13px 16px; }
    .progress-table tbody td { padding: 12px 16px; font-size: 0.88rem; vertical-align: middle; border-color: #f0f0f0; }
    .progress-table tbody tr:hover { background: #f8f8ff; }

    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }

    /* WELCOME */
    .welcome-hero {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
      border-radius: 20px; padding: 40px 36px;
      color: #fff; margin-bottom: 24px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .welcome-hero h2 { font-weight: 800; font-size: 1.8rem; margin-bottom: 6px; }
    .welcome-hero p { color: rgba(255,255,255,0.7); margin: 0; font-size: 0.95rem; }
    .welcome-hero img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.3); flex-shrink: 0; }
    .stat-card {
      background: #fff; border-radius: 14px;
      padding: 20px 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      display: flex; align-items: center; gap: 16px;
    }
    .stat-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem; flex-shrink: 0;
    }
    .stat-card h5 { margin: 0; font-weight: 800; font-size: 1.5rem; color: #1a1a2e; }
    .stat-card small { color: #888; font-size: 0.8rem; }
    .quick-card {
      background: #fff; border-radius: 14px; padding: 22px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
      text-align: center; text-decoration: none; color: inherit; display: block;
    }
    .quick-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); color: inherit; }
    .quick-card i { font-size: 2rem; display: block; margin-bottom: 10px; }
    .quick-card span { font-weight: 600; font-size: 0.9rem; color: #1a1a2e; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div>
    <a href="" class="sidebar-brand">
      <img src="../assets/images/logo.png" width="34" height="34" alt="logo">
      <span>DonorHub <sup><span class="badge text-bg-warning rounded-pill" style="font-size:0.6rem;">Recipient</span></sup></span>
    </a>
    <hr>
    <ul class="nav flex-column mt-2">
      <li class="nav-item">
        <a href="#" class="nav-link active" onclick="showSection(0, this)">
          <i class="bi bi-house-fill"></i> Home
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="showSection(1, this)">
          <i class="bi bi-cloud-plus-fill"></i> Create Campaign
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="showSection(2, this)">
          <i class="bi bi-collection-fill"></i> My Campaigns
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="showSection(3, this)">
          <i class="bi bi-graph-up-arrow"></i> Campaign Progress
        </a>
      </li>
    </ul>
  </div>
  <div class="sidebar-footer">
    <hr>
    <div class="dropdown">
      <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown">
        <img src="<?= $_SESSION['rprofile_pic'] ?>" alt="profile">
        <span class="name"><?= $_SESSION['r_logged'] ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow border-0">
        <li><a class="dropdown-item" href="../pages/profile_rupdate.php?id=<?= $_SESSION['r_id'] ?>"><i class="bi bi-pen me-2"></i>Edit Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="post">
            <button class="dropdown-item text-danger" name="logout"><i class="bi bi-box-arrow-left me-2"></i>Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- MAIN -->
<div class="main">
  <?php include '../includes/message.php'; ?>

  <!-- Section 0: Welcome -->
  <?php
    $re_i = $_SESSION['r_id'];
    $totalCamps = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM campaigns WHERE recip_id = $re_i"))['t'];
    $activeCamps = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM campaigns WHERE recip_id = $re_i AND status='active'"))['t'];
    $totalRaised = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(c.amt_collected),0) as t FROM campaigns c WHERE c.recip_id = $re_i"))['t'];
  ?>
  <div id="section0" class="section active">
    <div class="welcome-hero">
      <div>
        <h2>Welcome, <?= $_SESSION['r_logged'] ?>! 🙌</h2>
        <p>Your campaigns are making a real difference. Keep going!</p>
        <button class="btn btn-light mt-3" onclick="showSection(1, document.querySelectorAll('.sidebar .nav-link')[1])" style="border-radius:20px;font-weight:600;"><i class="bi bi-plus-circle me-2"></i>Create Campaign</button>
      </div>
      <img src="<?= $_SESSION['rprofile_pic'] ?>" alt="profile">
    </div>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#e8e8f5;"><i class="bi bi-collection-fill" style="color:#6c63ff;"></i></div>
          <div><h5><?= $totalCamps ?></h5><small>Total Campaigns</small></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#e0fff4;"><i class="bi bi-check-circle-fill" style="color:#00b894;"></i></div>
          <div><h5><?= $activeCamps ?></h5><small>Active Campaigns</small></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-icon" style="background:#fff3e0;"><i class="bi bi-currency-dollar" style="color:#e17055;"></i></div>
          <div><h5>$<?= number_format($totalRaised) ?></h5><small>Total Raised</small></div>
        </div>
      </div>
    </div>
    <h6 class="fw-700 mb-3" style="color:#1a1a2e;">Quick Actions</h6>
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <a class="quick-card" onclick="showSection(1, document.querySelectorAll('.sidebar .nav-link')[1])">
          <i class="bi bi-cloud-plus-fill" style="color:#6c63ff;"></i>
          <span>New Campaign</span>
        </a>
      </div>
      <div class="col-6 col-md-3">
        <a class="quick-card" onclick="showSection(2, document.querySelectorAll('.sidebar .nav-link')[2])">
          <i class="bi bi-collection-fill" style="color:#0984e3;"></i>
          <span>My Campaigns</span>
        </a>
      </div>
      <div class="col-6 col-md-3">
        <a class="quick-card" onclick="showSection(3, document.querySelectorAll('.sidebar .nav-link')[3])">
          <i class="bi bi-graph-up-arrow" style="color:#00b894;"></i>
          <span>View Progress</span>
        </a>
      </div>

    </div>
  </div>

  <!-- Section 1: Create Campaign -->
  <div id="section1" class="section">
    <div class="page-header">
      <h4><i class="bi bi-cloud-plus-fill me-2" style="color:#6c63ff;"></i>Create Campaign</h4>
    </div>
    <div class="form-card">
      <form id="campaignForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-600">Donation Type</label>
            <select class="form-select" id="d_type" name="d_type" required>
              <option selected disabled value="">Select type</option>
              <option value="blood">Blood</option>
              <option value="education">Education</option>
              <option value="health">Health</option>
              <option value="food">Food</option>
            </select>
            <div class="invalid-feedback">Please select a donation type.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Campaign Title</label>
            <input type="text" class="form-control" name="camp_title" placeholder="Enter campaign title" required>
            <div class="invalid-feedback">Please enter a title.</div>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="camp_desc" rows="4" placeholder="Describe your campaign..." required style="resize:none;"></textarea>
            <div class="invalid-feedback">Please enter a description.</div>
          </div>
          <div class="col-md-6" id="amount_field">
            <label class="form-label" id="amount_label">Estimated Amount</label>
            <div class="input-group">
              <span class="input-group-text" id="amount_icon"><i class="bi bi-currency-dollar"></i></span>
              <input type="text" name="est_amt" id="est_amt" maxlength="5" onblur="validateAmountInput('est_amt');" placeholder="e.g. 5000" class="form-control" required>
            </div>
            <small class="text-muted" id="amount_help" style="display:none;">1 person can donate only 1 pint (450-500 ml)</small>
            <div class="invalid-feedback">Please enter an amount.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Campaign Image</label>
            <input type="file" class="form-control" name="c_image" accept="image/*" required>
            <div class="invalid-feedback">Please upload an image.</div>
          </div>

          <!-- Blood fields -->
          <div id="blood_donation_fields" class="col-12 row g-3" style="display:none;">
            <div class="col-md-4">
              <label class="form-label">Required Blood Group</label>
              <select class="form-select" id="blood_group" name="blood_group">
                <option selected disabled value="">Select</option>
                <option value="O+">O+</option><option value="A+">A+</option>
                <option value="B+">B+</option><option value="AB+">AB+</option>
                <option value="O-">O-</option><option value="A-">A-</option>
                <option value="B-">B-</option><option value="AB-">AB-</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Donation Date</label>
              <input type="date" class="form-control" id="donation_date" name="donation_date" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Donation Time</label>
              <input type="time" class="form-control" id="donation_time" name="donation_time">
            </div>
            <div class="col-12">
              <label class="form-label">Donation Address</label>
              <textarea class="form-control" id="donation_address" name="donation_address" rows="2" placeholder="Where should donors come?"></textarea>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 pt-2">
            <button type="submit" class="btn btn-primary px-4" name="r_submit">Submit Campaign</button>
            <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Section 2: My Campaigns -->
  <div id="section2" class="section">
    <div class="page-header">
      <h4><i class="bi bi-collection-fill me-2" style="color:#6c63ff;"></i>My Campaigns</h4>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
      <?php
      $re_i = $_SESSION['r_id'];
      $sql = "SELECT * FROM campaigns WHERE recip_id = '$re_i' ORDER BY camp_id DESC";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0):
        foreach ($result as $row):
          $isBlood = ($row['camp_type'] === 'blood');
          $estAmt = $row['est_amt'];
          $amtCollected = $row['amt_collected'];
          $progress = $estAmt > 0 ? min(round(($amtCollected / $estAmt) * 100, 1), 100) : 0;
          $statusMap = ['active'=>'status-active','pending'=>'status-pending','stop'=>'status-stop','inactive'=>'status-inactive','rejected'=>'status-rejected'];
          $statusClass = $statusMap[$row['status']] ?? 'status-inactive';
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
                <span><?= $isBlood ? $amtCollected.' pints' : '$'.$amtCollected ?> raised</span>
                <span><?= $progress ?>%</span>
              </div>
              <div class="progress"><div class="progress-bar" style="width:<?= $progress ?>%"></div></div>
            </div>
            <div class="card-footer-row">
              <span class="status-badge <?= $statusClass ?>"><?= ucfirst($row['status']) ?></span>
              <div class="d-flex gap-1">
                <?php if ($row['status'] === 'active'): ?>
                  <a href="" class="btn btn-sm btn-outline-danger" onclick="confirmStop(event, <?= $row['camp_id'] ?>)"><i class="bi bi-stop-circle"></i></a>
                <?php endif; ?>
                <?php if (in_array($row['status'], ['active','pending','inactive'])): ?>
                  <a href="../pages/camp_r_edit.php?id=<?= $row['camp_id'] ?>&crby=<?= $re_i ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Edit this campaign?')"><i class="bi bi-pencil"></i></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; else: ?>
      <div class="col-12"><div class="empty-state"><i class="bi bi-inbox"></i>No campaigns yet. Create one!</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Section 3: Campaign Progress -->
  <div id="section3" class="section">
    <div class="page-header">
      <h4><i class="bi bi-graph-up-arrow me-2" style="color:#6c63ff;"></i>Campaign Progress</h4>
    </div>
    <?php
    $sql = "SELECT * FROM campaigns WHERE recip_id = $re_i AND (status='active' OR status='stop')";
    $res = mysqli_query($conn, $sql);
    ?>
    <?php if (mysqli_num_rows($res) > 0): ?>
    <div class="progress-table">
      <table class="table table-hover bg-white mb-0">
        <thead>
          <tr>
            <th>Campaign</th>
            <th>Target</th>
            <th>Raised</th>
            <th>Donors</th>
            <th>Progress</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($res)):
            $isBlood = ($row['camp_type'] === 'blood');
            $p = $row['est_amt'] > 0 ? min(round(($row['amt_collected'] / $row['est_amt']) * 100, 1), 100) : 0;
            $dq = "SELECT COUNT(DISTINCT donor_id) AS t FROM donations WHERE camp_id = '{$row['camp_id']}'";
            $dr = mysqli_fetch_assoc(mysqli_query($conn, $dq));
          ?>
          <tr>
            <td><strong><?= $row['camp_title'] ?></strong><br><small class="text-muted"><?= ucfirst($row['camp_type']) ?></small></td>
            <td><?= $isBlood ? $row['est_amt'].' pints' : '$'.$row['est_amt'] ?></td>
            <td><?= $isBlood ? $row['amt_collected'].' pints' : '$'.$row['amt_collected'] ?></td>
            <td><span class="badge bg-light text-dark border"><?= $dr['t'] ?></span></td>
            <td style="min-width:100px;">
              <div class="progress" style="height:6px;">
                <div class="progress-bar" style="width:<?= $p ?>%"></div>
              </div>
              <small class="text-muted"><?= $p ?>%</small>
            </td>
            <td><a href="../pages/donor_rmodal.php?camp_id=<?= $row['camp_id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="bi bi-inbox"></i>No active campaigns to show progress for.</div>
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
  return false;
}

function confirmStop(event, id) {
  event.preventDefault();
  if (confirm("Stop this campaign?")) {
    window.location.href = "../pages/camp_stop.php?id=" + id + "&crby=<?= $re_i ?>&action=undefined";
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const dType = document.getElementById('d_type');
  if (!dType) return;
  dType.addEventListener('change', function () {
    const isBlood = this.value === 'blood';
    document.getElementById('blood_donation_fields').style.display = isBlood ? 'flex' : 'none';
    document.getElementById('amount_label').textContent = isBlood ? 'Quantity (pints)' : 'Estimated Amount';
    document.getElementById('amount_icon').innerHTML = isBlood ? '<i class="bi bi-droplet"></i>' : '<i class="bi bi-currency-dollar"></i>';
    document.getElementById('est_amt').placeholder = isBlood ? 'e.g. 10' : 'e.g. 5000';
    document.getElementById('amount_help').style.display = isBlood ? 'block' : 'none';
  });

  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    });
  });
});

function validateAmountInput(id) {
  const f = document.getElementById(id);
  if (!/^[0-9]+$/.test(f.value)) { f.value = ''; alert("Only numbers allowed."); }
  else if (parseInt(f.value) < 100) { f.value = ''; alert("Amount must be at least 100."); }
}
</script>
</body>
</html>


