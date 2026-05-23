<?php
session_start();
include "../assets/db/conn.php";
if (empty(@$_SESSION['d_logged'])) {
    header('Location:  index.php');
}
if(isset($_POST['update'])){
    $id =$_GET['id'];
     $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $place = mysqli_real_escape_string($conn, $_POST['place']);
    $emp_status = mysqli_real_escape_string($conn, $_POST['emp_status']);
    $user_type = 'donor';
    $d_type = NULL; // Donation type removed
    $b_type = mysqli_real_escape_string($conn, $_POST['b_type']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
$targetDirectory = '../pages/profile_pic/';

// Check if a new profile picture is uploaded
if ($_FILES['profile']['name'] != "") {
    $profile = $targetDirectory . basename($_FILES['profile']['name']);
    move_uploaded_file($_FILES['profile']['tmp_name'], $profile);
} else {
  
    $profile = $_SESSION['dprofile_pic'];
}

$update = "UPDATE users SET fname='$fname', lname='$lname', email='$email', phone='$phone', place='$place', emp_status='$emp_status', user_type='$user_type', d_type='$d_type',blood_type='$b_type', password='$password', profile_pic='$profile' WHERE user_id='$id'";
$exe = mysqli_query($conn, $update);


if ($exe) {
    $_SESSION['message'] = " Updated successfully";
    sleep(3);
    header("Location: d_home.php");
    $_SESSION['d_logged'] =$fname;
    $_SESSION['dprofile_pic'] = $profile;
    $_SESSION['d_type'] = '';
    exit;
} else {
    $_SESSION['message'] = "Error updating profile";
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
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <title>Profile</title>
</head>

<body>
    <div class="container">
        <div class="card mt-5 mx-auto">
            <div class="card-header d-flex justify-content-between align-items-center  ">
                <img src="../assets/images/logo.png" class="img-fluid" alt="Logo_image" width="40" height="40">
                <h1 class="fs-4 fw-bolder text-center mb-0">DONOR HUB</h1>
                <a href="d_home.php" class="btn btn-danger"><i class="bi bi-arrow-left-circle m-1"></i>BACK</a>
            </div>
            <div class="card-body ">
            <?php
             if(isset($_GET['id'])){
             $user_id =$_GET['id'];
             $sql = "SELECT * FROM users WHERE user_id='$user_id'";
             $res=mysqli_query($conn,$sql);
            if(mysqli_num_rows($res)>0){
                    $info =mysqli_fetch_array($res);
                    $empStatus=$info['emp_status'];
                    $d_type = isset($info['d_type']) ? $info['d_type'] : '';
                    @$b_type = $info['blood_type'];
                    
            }
        }
             ?>
                <form id="donor" class="row g-3 needs-validation d-flex " method="post" enctype="multipart/form-data"
                    novalidate>
                    <div class="text-center">
                    <?php include '../includes/message.php'; ?>
                        <img src="<?= $info['profile_pic'];?>" alt="profile" style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
                    </div>

                    <div class="col-md-4">
                        <label for="validationCustom01" class="form-label">First name</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="Firstname"
                            name="fname" value="<?= $info['fname']; ?>" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback">Please enter first name</div>
                    </div>

                    <div class="col-md-4">
                        <label for="validationCustom02" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="validationCustom02" placeholder="lastname"
                            name="lname" value="<?= $info['lname']; ?>"  required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback">Please enter last name</div>
                    </div>


                    <div class="col-md-4">
                        <label for="validationCustom03" class="form-label">Email</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                            <input type="email" class="form-control" id="validationCustom03" placeholder="xxx@gmail.com"
                                aria-describedby="inputGroupPrepend" name="email"  value="<?= $info['email']; ?>" required readonly >
                            <div class="invalid-feedback">
                                Enter valid email.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="validationCustom04" class="form-label">Phone No.</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend"> <i
                                    class="bi bi-telephone"></i></span>
                            <input type="tel" min="1000000000" max="9999999999" maxlength="10" class="form-control"
                                name="phone" placeholder="10 digit" id="validationCustom04"
                                aria-describedby="inputGroupPrepend"  value="<?= $info['phone']; ?>"readonly required >
                            <div class="invalid-feedback">
                                Enter valid phone number.
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <label for="validationCustom05" class="form-label">Place</label>
                        <input type="text" class="form-control" id="validationCustom05" list="place"
                            placeholder="Current location" name="place" value="<?= $info['place']; ?>" required>
                        <div class="invalid-feedback">
                            Enter place name
                        </div>
                    </div>



                    <div class="col-md-4">
                        <label for="validationCustom06" class="form-label">Employement Status</label>
                        <select class="form-select" id="validationCustom06" name="emp_status"  style="pointer-events: none;" required>
                            <option selected disabled value="">Select your employement status</option>
                            <option name="employed" value="employed"<?php if ($empStatus == 'employed') echo 'selected'; ?>>Employed</option>
                            <option name="unemployed" value="unemployed"  <?php if ($empStatus == 'unemployed') echo 'selected'; ?>>Unemployed</option>
                            <option name="student" value="student" <?php if ($empStatus == 'student') echo 'selected'; ?>>Student</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select valid profession.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="image" class="form-label">Choose Profile</label>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-bounding-box "></i>
                            </span>  
                            <input type="file" class="form-control" id="image" name="profile" accept="image/*"  value=" <?= $info['profile_pic'];?>"  onclick="confirmUpload(event);">
                 
                                 </div>

                    </div>
                    <div class="col-md-5 " id="bg_type"  >
                        <label for="validationCustom07" class="form-label">Select Blood Group</label>
                        <select class="form-select" id="validationCustom07" name="b_type" style="pointer-events: none;" >
                            <option selected disabled value=""> Type</option>
                            <option value="O+"<?php if ($b_type == 'O+') echo 'selected'; ?> >O+</option>
                            <option name="A+" value="A+"<?php if ($b_type == 'A+') echo 'selected'; ?>>A+</option>
                            <option name="B+" value="B+"<?php if ($b_type == 'B+') echo 'selected'; ?>>B+</option>
                            <option name="AB+" value="AB+"<?php if ($b_type == 'AB+') echo 'selected'; ?>>AB+</option>
                            <option name="O-" value="O-"<?php if ($b_type == 'O-') echo 'selected'; ?>>O-</option>
                            <option name="A-" value="A-"<?php if ($b_type == 'A-') echo 'selected'; ?>>A-</option>
                            <option name="B-" value="B-"<?php if ($b_type == 'B-') echo 'selected'; ?>>B-</option>
                            <option name="AB-" value="AB-"<?php if ($b_type == 'AB-') echo 'selected'; ?>>AB-</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid donation type.
                        </div>
                    </div>
                    <br>
                    

                    <div class="col-md-4">
                        <label for="pass1" class="form-label">Password</label>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key "></i>
                            </span>
                            <input type="password" class="form-control" id="pass1" name="password" value="<?= $info['password']; ?> " onclick="confirmPasswordChange(event);" required>
                            <span class="input-group-text"><i class="bi bi-eye-fill" id="show1"></i></span>
                            <div class="invalid-feedback">
                                Please enter password.
                            </div>
                        </div>
                    </div>

                 

                   
                    <div class="col-12 d-flex justify-content-between">
                        <button class="btn btn-primary" type="submit" name=" update">Update</button>
                        <button class="btn btn-danger" type="reset">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    //file upload
  function confirmUpload(event) {
    if (!confirm("Do you want to change the profile picture?")) {
        event.preventDefault(); 
    }
}
function confirmPasswordChange(event) {
    if (!confirm("Are you sure you want to change the password?")) {
        event.preventDefault(); 
        event.target.blur();
    }
   
}
    //pass mask
    const show1 = document.getElementById("show1");
    const show2 = document.getElementById("show2");
    const show3 = document.getElementById("show3");
    const show4 = document.getElementById("show4");
    const pass1 = document.getElementById("pass1");
    const pass2 = document.getElementById("pass2");
    const pass3 = document.getElementById("pass3");
    const pass4 = document.getElementById("pass4");

    show1.addEventListener("click", () => {
        if (show1.classList.contains("bi-eye-fill")) {

            show1.classList.remove("bi-eye-fill");
            show1.classList.add("bi-eye-slash-fill");
            pass1.type = "text";
        } else {

            show1.classList.remove("bi-eye-slash-fill");
            show1.classList.add("bi-eye-fill");
            pass1.type = "password";
        }

    });
    show2.addEventListener("click", () => {
        if (show2.classList.contains("bi-eye-fill")) {

            show2.classList.remove("bi-eye-fill");
            show2.classList.add("bi-eye-slash-fill");
            pass2.type = "text";
        } else {

            show2.classList.remove("bi-eye-slash-fill");
            show2.classList.add("bi-eye-fill");
            pass2.type = "password";
        }

    });
    show3.addEventListener("click", () => {
        if (show3.classList.contains("bi-eye-fill")) {

            show3.classList.remove("bi-eye-fill");
            show3.classList.add("bi-eye-slash-fill");
            pass3.type = "text";
        } else {

            show3.classList.remove("bi-eye-slash-fill");
            show3.classList.add("bi-eye-fill");
            pass3.type = "password";
        }

    });

    show4.addEventListener("click", () => {
        if (show4.classList.contains("bi-eye-fill")) {

            show4.classList.remove("bi-eye-fill");
            show4.classList.add("bi-eye-slash-fill");
            pass4.type = "text";
        } else {

            show4.classList.remove("bi-eye-slash-fill");
            show4.classList.add("bi-eye-fill");
            pass4.type = "password";
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

