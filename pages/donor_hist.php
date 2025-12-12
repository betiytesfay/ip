<?php 
include "../assets/db/conn.php";
$uid = $_GET['do_id'];


$queryDonors = "SELECT c.camp_title,c.camp_img, c.camp_type, c.blood_group, d.donated_amt
FROM donations d
INNER JOIN campaigns c ON c.camp_id = d.camp_id
WHERE d.donor_id = $uid";;
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
            <li class="list-group-item d-flex align-items-center mb-2 ">
              <div>
                <?php 
                // Define $isBlood before using it
                $isBlood = (isset($donor['camp_type']) && $donor['camp_type'] === 'blood');
                ?>
                <h5 class="mb-1 text-center">
                  <?= $donor['camp_title']; ?>
                  <?php if($isBlood && !empty($donor['blood_group'])): ?>
                    <br><small class="text-danger"><i class="bi bi-heart-pulse"></i> Blood Group: <?= $donor['blood_group']; ?></small>
                  <?php endif; ?>
                </h5>
                <p class="mb-0  ">Donated: 
                  <?php 
                  if($isBlood): ?>
                    <i class="bi bi-droplet"></i><?php echo $donor['donated_amt']; ?> pint<?= $donor['donated_amt'] != 1 ? 's' : ''; ?>
                  <?php else: ?>
                    <i class="bi bi-currency-rupee"></i><?php echo $donor['donated_amt']; ?>
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
          <a href="../pages/admin.php" role="button" class="btn btn-secondary" >Close</a>

        </div>
            </div>
        </div>
    </div>
</body>

</html>