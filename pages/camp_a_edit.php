
<?php
session_start();
include "../assets/db/conn.php";
$camp_id = $_GET['id'];
if (empty($camp_id)) {
    header('Location:  log_in.php');
}

if (isset($_POST['a_update'])) {
  $camp_id= $_GET['id'];
    $d_type = mysqli_real_escape_string($conn, $_POST['d_type']);
    $camp_title = mysqli_real_escape_string($conn, $_POST['camp_title']);
    $camp_desc = mysqli_real_escape_string($conn, $_POST['camp_desc']);
    $est_amt = mysqli_real_escape_string($conn, $_POST['est_amt']);
  
    // Get blood donation details if it's a blood campaign
    $donation_address = '';
    $donation_date = '';
    $donation_time = '';
    $blood_group = NULL;
    if ($d_type === 'blood') {
      $donation_address = isset($_POST['donation_address']) ? mysqli_real_escape_string($conn, $_POST['donation_address']) : '';
      $donation_date = isset($_POST['donation_date']) ? mysqli_real_escape_string($conn, $_POST['donation_date']) : '';
      $donation_time = isset($_POST['donation_time']) ? mysqli_real_escape_string($conn, $_POST['donation_time']) : '';
      $blood_group = isset($_POST['blood_group']) ? mysqli_real_escape_string($conn, $_POST['blood_group']) : NULL;
    }
    
$rupdate = "UPDATE campaigns SET camp_title='$camp_title',camp_desc='$camp_desc',donation_address='$donation_address',donation_date='$donation_date',donation_time='$donation_time',blood_group='$blood_group' WHERE camp_id='$camp_id'";
$exe = mysqli_query($conn, $rupdate);
    
    if ($exe) {
  
      $_SESSION['message'] = "Campaign Updated";
      sleep(2);
      header("Location: admin.php");
  
      exit();
    } else {
      $_SESSION['message'] = "Campaign Not Created";
      header("Location: admin.php");
      exit();
    }
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
  <!--Icon--->
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/navbar.css">
  <title>Donate</title>
</head>
<body>

<div class="container mt-1">
  <div class="row">
    <div class=" col text-center">
      <h3 class="fw-bold">Edit Campaign</h3>
    </div>
    <div class="col text-end mt-1">
      <a href="../pages/admin.php" class="btn btn-danger"><i class="bi bi-arrow-left-circle m-1"></i>BACK</a>
    </div>
  </div>
</div>
                <hr class="shadow">

                <?php include '../includes/message.php'; ?>
      <form id="campaign" class="row g-3 needs-validation d-flex p-5" method="post" enctype="multipart/form-data" novalidate>
                      <?php
             if(isset($_GET['id'])){
             $camp_id =$_GET['id'];
             $sql = "SELECT * FROM campaigns WHERE camp_id='$camp_id'";
             $res=mysqli_query($conn,$sql);
            if(mysqli_num_rows($res)>0)
            {
                    $info =mysqli_fetch_array($res);
                    $Ctitle=$info['camp_title'];
                    $Ctype = $info['camp_type'];
                    $Cdesc = $info['camp_desc'];
                    $Eamt =$info['est_amt'];
                    $DonationAddress = isset($info['donation_address']) ? $info['donation_address'] : '';
                    $DonationDate = isset($info['donation_date']) ? $info['donation_date'] : '';
                    $DonationTime = isset($info['donation_time']) ? $info['donation_time'] : '';
                    $BloodGroup = isset($info['blood_group']) ? $info['blood_group'] : '';
                  
            }
        }
        ?>
          <div class="col-md-7">
                <label for="validationCustom08" class="form-label">Donation Type</label>
                        <select class="form-select" id="d_type"   name="d_type" required style="pointer-events: none;">
                                           <option selected disabled value="">Donation Type</option>
                                           <option name="blood"  value="blood" id="blood "<?php if ($Ctype == 'blood') echo 'selected'; ?>>Blood</option>
                                           <option name="education"  value="education"<?php if ($Ctype == 'education') echo 'selected'; ?>>Education</option>
                                           <option name="health" value="health" <?php if ($Ctype == 'health') echo 'selected'; ?>>Health</option>
                                           <option name="food" value="food" <?php if ($Ctype == 'food') echo 'selected'; ?>>Food</option>
                         </select>
                  <div class="invalid-feedback">
                        Please select a valid donation type.
                  </div>
            </div>
          <br>
         
          <div class="col-md-7">
               <label for="validationCustom01" class="form-label">Campaign Title</label>
                   <input type="text" class="form-control" id="validationCustom01" placeholder="Campaign Title" name="camp_title" value="<?= $Ctitle ;?>" required>
            <div class="valid-feedback">
                  Title valid!
            </div>
            <div class="invalid-feedback">Please enter campaign Title</div>
          </div>

          <div class="col-md-12">
            <label for="validationCustom02" class="form-label">Campaign Description</label>
            <textarea class="form-control"  id="validationCustom02" inputmode="" style="resize:none;" placeholder="Description" name="camp_desc" required rows="7" > <?= $Cdesc?></textarea>
            <div class="valid-feedback">
              Looks good!
            </div>
            <div class="invalid-feedback">Please enter campaign description </div>
          </div>
          
          <div class="col-md-12" id="amount_field">
              <label for="est_amt" class="form-label" id="amount_label"><?php echo ($Ctype == 'blood') ? 'Quantity of Blood (in pints)' : 'Estimated Amount'; ?></label>
              <div class="input-group">
                <span class="input-group-text" id="amount_icon"><i class="bi <?php echo ($Ctype == 'blood') ? 'bi-droplet' : 'bi-currency-rupee'; ?>"></i></span>
                <input type="text" value="<?= $Eamt; ?>" name="est_amt" id="est_amt" maxlength="<?php echo ($Ctype == 'blood') ? '3' : '5'; ?>" placeholder="<?php echo ($Ctype == 'blood') ? 'eg. 5, 10, 20 pints' : 'eg.100,500,1000'; ?>" class="form-control" required style="pointer-events: none;">
                </div>
                <div class="invalid-feedback" id="amount_feedback">
                  <?php echo ($Ctype == 'blood') ? 'Enter quantity of blood needed in pints' : 'Enter Amount To donate'; ?>
                </div>
                <?php if($Ctype == 'blood'): ?>
                <small class="text-muted">1 person can donate only 1 pint (450-500 ml) of blood</small>
                <?php endif; ?>
              </div>
              
          <div id="blood_donation_fields" class="row" style="display:<?php echo ($Ctype == 'blood') ? 'block' : 'none'; ?>;">
            <div class="col-md-6 mb-3">
              <label for="blood_group" class="form-label"><i class="bi bi-heart-pulse"></i> Required Blood Group</label>
              <select class="form-select" id="blood_group" name="blood_group">
                <option selected disabled value="">Select Blood Group</option>
                <option value="O+" <?php echo (isset($BloodGroup) && $BloodGroup == 'O+') ? 'selected' : ''; ?>>O+</option>
                <option value="A+" <?php echo (isset($BloodGroup) && $BloodGroup == 'A+') ? 'selected' : ''; ?>>A+</option>
                <option value="B+" <?php echo (isset($BloodGroup) && $BloodGroup == 'B+') ? 'selected' : ''; ?>>B+</option>
                <option value="AB+" <?php echo (isset($BloodGroup) && $BloodGroup == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                <option value="O-" <?php echo (isset($BloodGroup) && $BloodGroup == 'O-') ? 'selected' : ''; ?>>O-</option>
                <option value="A-" <?php echo (isset($BloodGroup) && $BloodGroup == 'A-') ? 'selected' : ''; ?>>A-</option>
                <option value="B-" <?php echo (isset($BloodGroup) && $BloodGroup == 'B-') ? 'selected' : ''; ?>>B-</option>
                <option value="AB-" <?php echo (isset($BloodGroup) && $BloodGroup == 'AB-') ? 'selected' : ''; ?>>AB-</option>
              </select>
              <div class="invalid-feedback">Please select blood group</div>
            </div>
            <div class="col-md-12 mb-3">
              <label for="donation_address" class="form-label"><i class="bi bi-geo-alt-fill"></i> Donation Address</label>
              <textarea class="form-control" id="donation_address" name="donation_address" rows="3" placeholder="Enter the address where donors should come to donate blood"><?php echo htmlspecialchars($DonationAddress); ?></textarea>
              <div class="invalid-feedback">Please enter donation address</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="donation_date" class="form-label"><i class="bi bi-calendar-event"></i> Donation Date</label>
              <input type="date" class="form-control" id="donation_date" name="donation_date" value="<?= $DonationDate; ?>" min="<?php echo date('Y-m-d'); ?>">
              <div class="invalid-feedback">Please select donation date</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="donation_time" class="form-label"><i class="bi bi-clock"></i> Donation Time</label>
              <input type="time" class="form-control" id="donation_time" name="donation_time" value="<?= $DonationTime; ?>">
              <div class="invalid-feedback">Please select donation time</div>
            </div>
          </div>
          
          <div class="col-12 d-flex justify-content-between">
            <input  type="submit" class="btn btn-primary" value="Update"name="a_update">
            <button class="btn btn-danger " type="reset">Reset</button>
          </div>
        </form>
  
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    
    // Show/hide blood donation fields based on campaign type
    document.addEventListener('DOMContentLoaded', function() {
      const dTypeSelect = document.getElementById('d_type');
      const bloodFields = document.getElementById('blood_donation_fields');
      
      if (dTypeSelect && bloodFields) {
        // Check if blood is selected on page load
        if (dTypeSelect.value === 'blood') {
          bloodFields.style.display = 'block';
          const bloodGroupField = document.getElementById('blood_group');
          if (bloodGroupField) bloodGroupField.setAttribute('required', 'required');
          document.getElementById('donation_address').setAttribute('required', 'required');
          document.getElementById('donation_date').setAttribute('required', 'required');
          document.getElementById('donation_time').setAttribute('required', 'required');
        }
      }
    });

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
                }

                form.classList.add('was-validated')
            }, false)
        })
    })();

    
</script>

</html>