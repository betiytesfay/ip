<?php 
include "../assets/db/conn.php";
$campaignId = $_GET['camp_id'];

// Get campaign type to check if it's blood
$campQuery = "SELECT camp_type FROM campaigns WHERE camp_id = $campaignId";
$campResult = mysqli_query($conn, $campQuery);
$isBlood = false;
if ($campResult && mysqli_num_rows($campResult) > 0) {
  $campData = mysqli_fetch_assoc($campResult);
  $isBlood = ($campData['camp_type'] === 'blood');
}

$queryDonors = "SELECT u.fname, u.profile_pic, d.donated_amt FROM users u INNER JOIN donations d ON u.user_id = d.donor_id WHERE d.camp_id =$campaignId";
$resultDonors = mysqli_query($conn, $queryDonors);
$donors = array();
$dcounts = '0';
if ($resultDonors && mysqli_num_rows($resultDonors) > 0) {
  while ($donor = mysqli_fetch_assoc($resultDonors)) {
    $donors[] = $donor;

  }

  if (isset($donors) && !empty($donors)) {
    $dcounts = count($donors);
  }


} else {
  //false block
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
        <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
        <title>Donate</title>
    </head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>

window.addEventListener('load', function() {
  var donorModal = document.getElementById('donorModal');  // Get the modal element
  var bootstrapModal = new bootstrap.Modal(donorModal);  // Create a new Bootstrap Modal instance
  bootstrapModal.show();  // Show the modal
});
function closeWindow() {
      window.close();
    }
</script>
<body>
    <!-- Donor Modal -->
    <div class="modal fade" id="donorModal" aria-labelledby="donorModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="donorModalLabel">Donor Information</h5>   
                 </div>
                <div class="modal-body">
                <ul class="list-group">
                    <?php
                if ($dcounts > 0) {
                    if (!empty($donors)) {
                      foreach ($donors as $donor): ?>
            <li class="list-group-item d-flex align-items-center mb-2">
              <img src="<?= $donor['profile_pic']; ?>" class="rounded-circle me-3" width="50" height="50"
                alt="Donor Image">
              <div>
                <h5 class="mb-1">
                  <?= $donor['fname']; ?>
                </h5>
                <p class="mb-0">Donated: 
                  <?php if($isBlood): ?>
                    <i class="bi bi-droplet"></i><?php echo $donor['donated_amt']; ?> pint<?= $donor['donated_amt'] != 1 ? 's' : ''; ?>
                  <?php else: ?>
                    <i class="bi bi-currency-dollar"></i><?php echo $donor['donated_amt']; ?>
                  <?php endif; ?>
                </p>
              </div>
            </li>
            <?php endforeach;
            }
            }
             else {
              echo '<li class="list-group-item d-flex align-items-center mb-2"> <p>No Donors Found</p?</li>';
            }
            ?>
                </ul>
                </div>
                <div class="modal-footer">
          <a href="../pages/r_home.php" role="button" class="btn btn-secondary" >Close</a>

        </div>
            </div>
        </div>
    </div>
</body>

</html>

