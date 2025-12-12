
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
 <link rel="stylesheet" href=" 	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
 <!--Icon--->
 <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">

 <title>ADMIN DASHBOARD</title>
</head>
<body >

 <div class="container-fluid fixed-top">
   <div class="row">
     <div class="d-flex flex-column justify-content-between col-auto bg-dark min-vh-100 ">
           <div>
             <a href="" class="text-white text-decoration-none d-flex align-items-center ms-4" role="button">
               <img src="../assets/images/logo.png" class="img-fluid me-4 mt-3"alt="logo" width="30" height="30" style="border-radius: 50%;">
               <span class="fs-5 fw-bold me-2 mt-3">DonorHub</span>
             </a>
             <hr style="color: white;">
             <ul class="nav nav-pills flex-column mt-4  " id="menu">
               <li class="nav-item">
                 <a href="#section1"  id="l1 "class="nav-link text-white" aria-current="page" onclick="sectio1();">
                   <i class="bi bi-cloud-plus"></i>
                   <span class="ms-2 ">Create Campaign</span>
                 </a>
               </li>
               <li class="nav-item">
                <a href="#section2" class="nav-link text-white" aria-current="page" onclick="section2();">
                  <i class="bi bi-check2-circle"></i>
                  <span class="ms-2 ">Approve/Reject Campaign</span>
                </a>
               </li>
               <li class="nav-item ">
                <a href="#section3" class="nav-link text-white" aria-current="page" onclick="section3();">
                  <i class="bi bi-slash-circle"></i>
                  <span class="ms-2 ">Stop Campaign</span>
                </a>
               </li>
               <li class="nav-item ">
                <a href="#section4" class="nav-link text-white" aria-current="page">
                  <i class="bi bi-eye"></i>
                  <span class="ms-2 ">View Donors</span>
                </a> </li>
               </li>
               <li class="nav-item ">
                <a href="#section5" class="nav-link text-white" aria-current="page">
                  <i class="bi bi-journal-text"></i>
                  <span class="ms-2 ">View Recipient</span>
                </a>
               </li>
               <li class="nav-item ">
                <a href="#section6" class="nav-link text-white" aria-current="page">
                  <i class="bi bi-reception-4"></i>
                  <span class="ms-2 ">View Campaign Progress</span>
                </a>
               </li>
          
               <li class="nav-item ">
                <a href="#section7" class="nav-link text-white" aria-current="page">
                  <i class="bi bi-chat-quote"></i>
                  <span class="ms-2 ">Notify Donors</span>
                </a>
               </li>
             </ul>
           </div>
     </div>
   </div>
 </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
   
    </body>
    </html>
