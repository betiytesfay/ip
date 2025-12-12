<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    html, body {
      overflow-x: hidden !important;
      max-width: 100vw;
      width: 100%;
    }
    body {
      scroll-behavior: smooth;
    }

    .section {
      min-height: 800px;
      max-width: 100%;
      overflow-x: hidden;
    }

    .section {
      width: 100%;
      padding-top: 1.5px;
    }

    .carousel {
      margin-top: -120px;
    }

    .title-text {
      font-family: 'Times New Roman', Times, serif;
      font-size: 70px;
      font-weight: bold;
    }

    .green-text {
      color: rgb(19, 202, 141);
    }

    .red-text {
      color: rgb(240, 71, 71);
    }

    .donate-btn a {

      padding: 10px;
      color: white;
      background-color: red;
      border-radius: 10px;
      font-size: 18px;
      font-family: arial;
      transition: .5s all ease-in;
    }

    .donate-btn a:hover {
      color: #fff;
      background-color: rgb(19, 202, 141);
      transform: scale(103%);

    }

    #explore {
      background-image: url('../assets/images/p1-2.png'), url('../assets/images/des_bg.png');
      background-repeat: no-repeat, no-repeat;
      background-position: 750px 75px, 0px 10px;
      background-size: 40%, 100%;
    }

    .item {
      background: white;
      text-align: center;
      padding: 50px 25px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.07);
      border-radius: 20px;
      margin-top: 30px;
      border: 5px solid rgba(0, 0, 0, 0.07);
      transition: all 0.5s ease 0s;

    }

    .item:hover {
      background-color: #f91942;
      box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.2);
      transition: all 0.5s ease 0s;
    }

    .item:hover .item,
    .item:hover span.icon {
      background: #fff;
      padding: 5px;
      border-radius: 10px;
      transition: all 0.5s ease 0s;
    }

    .item:hover h6,
    .item:hover p {
      color: #fff;
      transition: all 0.5s ease 0s;
    }

    .item .icon {
      font-size: 30px;
      margin-bottom: 25px;
      color: #f91942;
      /* width: 90px;
  height: 90px; */
      line-height: 96px;
      border-radius: 50px;
    }

    .item .feature_box_col_one {
      background: rgba(204, 204, 204, 0.37);
      padding: 10px;
      border-radius: 90px;
    }

    .item .feature_box_col_two {
      background: rgba(204, 204, 204, 0.37);
      padding: 10px;
      border-radius: 90px;
    }

    .item .feature_box_col_three {
      background: rgba(204, 204, 204, 0.37);
      padding: 10px;
      border-radius: 90px;
    }

    #sum {
      background-image: url('../assets/images/serv_bg.png');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: 0px 10px;

    }

    .prof-box {

      transition: all 1s ease 0s;
      background-color: rgb(243, 236, 236);
      text-align: center;
      width: 350px;
      padding-top: 60px;
      padding-bottom: 60px;
      padding-left: -5px;
      padding-left: -5px;
      border-radius: 50px;
      margin: 3px;
    }

    .prof-box:nth-child(1) {
      margin-left: 30px;
    }

    .prof-box:hover {
      box-shadow: 1px 1px 60px rgb(52, 66, 146);

    }

    .prof-box .profile {
      margin-top: 3px;
      margin-bottom: 12px;
    }

    .prof-box .profile-bio {
      line-height: 2em;
    }

    .prof-box .bi {
      font-size: 20px;
    }

    .social {
      padding: 10px;
      margin-left: 80px;
      background: gray;
      width: 200px;
      border-radius: 50px;
      transition: all 0.5s ease 0s;
    }

    .social:hover .bi-envelope-at:hover {
      color: #f91942;
      background-color: #fff;
      border-radius: 30px;
    }

    .social:hover .bi-github:hover {
      color: #4d4d4d;
      background-color: #fff;
      border-radius: 30px;
    }

    .social:hover .bi-twitter:hover {
      color: #3482ff;
      background-color: #fff;
      border-radius: 30px;
    }

    .social:hover .bi-whatsapp:hover {
      color: #00ff6a;
      background-color: #fff;
      border-radius: 30px;
    }

    .profile:hover {
      filter: drop-shadow(3px 3px 10px #1344e2);
      transition: 1s ease all;
    }

    .social i {
      padding: 10px;

    }

    #about {
      background-image: url('../assets/images/abt_bg.png');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: 0px 10px;

    }
  </style>
  <!--Bootstrap-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href=" 	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <!--Icon--->
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/navbar.css">
  <title>Donate</title>
</head>

<body class="pt-5">

  <nav class="navbar navbar-expand-md navbar-light bg-white fixed-top shadow rounded-bottom" style="z-index: 1;">
    <div class="container px-1">
      <a class="navbar-brand fw-bold" href="#">
        <img src="../assets/images/logo.png" alt="logo" width="40" height="40" class="img-fluid me-2">
        <span class="logo-text">DonorHub</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="bi bi-list-nested"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav m-auto">
          <li class="nav-item fw-bold">
            <a class="nav-link" aria-current="page" href="#home">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="#explore">Explore</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="#about">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="#sum">Service</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <a href="../pages/register.php" class="btn btn-outline-success me-1 w-md-1">Register</a>
          <a href="../pages/log_in.php" class="btn btn-outline-dark mt-lg-0 mt-md-2 mt-sm-2 log">Login</a>
        </ul>
      </div>
    </div>
  </nav>

  <!--section1-->
  <section id="home" class="section">
    <div class="container-fluid col-sm-12 col-lg-12" style="margin-top: 200px;">
      <div id="carouselId" class="carousel slide " data-bs-ride="carousel">

        <div class="carousel-inner" role="listbox">
          <div class="carousel-item active">
            <img src="../assets/images/caro-1.jpg" class="w-100 d-block" alt="First slide">
          </div>
          <div class="carousel-item">
            <img src="../assets/images/caro-2.png" class="w-100 d-block" alt="Second slide">
          </div>
          <div class="carousel-item">
            <img src="../assets/images/caro-3.jpg" class="w-100 d-block" alt="Third slide">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </section>

  <section id="explore" class="section">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-8 col-sm-4">
          <h1 class="title-text">
            <div>Make someone's <span class="red-text">Life</span><br>by <span class="green-text">giving of yours</span>
            </div>
            <div class="donate-btn">
              <a href="log_in.php" role="button" class="btn">DONATE <i class="bi bi-hearts"></i></a>
            </div>
          </h1>
        </div>
      </div>
    </div>
  </section>
  <!--section3-->
  <section id="about" class="section">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="prof-box ">
            <div class="profile image">
              <img src="../assets/images/logo.png" alt="prof-1" class="img-fluid  rounded-circle m-top" width="90"
                height="90">
            </div>
            <div class="profile-name">
              <h2 class="fw-bold f-24">Team Member 1</h2>
            </div>
            <div class="profile-bio"><i class="bi bi-bookmark-heart"></i> <span class="b-1">Team-Lead</span><br>
              <i class="bi bi-award"></i> <span class="b-1 ">College Name</span>
            </div>
            <div class="social">
            <a href="mailto:contact@donorhub.com" class="text-dark">  
              <i class="bi bi-envelope-at"></i></a>
              <i class="bi bi-github"></i>
              <i class="bi bi-twitter"></i>
              <a href="#" class="text-dark">
              <i class="bi bi-whatsapp"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="prof-box">
            <div class="profile image">
              <img src="../assets/images/logo.png" alt="prof-1" class="img-fluid  rounded-circle m-top" width="90"
                height="90">
            </div>
            <div class="profile-name">
              <h2 class="fw-bold f-24">Team Member 2</h2>
            </div>
            <div class="profile-bio"><i class="bi bi-bookmark-heart"></i> <span class="b-1">Documentation</span><br>
              <i class="bi bi-award"></i> <span class="b-1 ">College Name</span>
            </div>
            <div class="social">
            <a href="mailto:contact@donorhub.com" class="text-dark">  
              <i class="bi bi-envelope-at"></i></a>
              <i class="bi bi-github"></i>
              <i class="bi bi-twitter"></i>
              <a href="#" class="text-dark">
              <i class="bi bi-whatsapp"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="prof-box">
            <div class="profile image">
              <img src="../assets/images/logo.png" alt="prof-1" class="img-fluid  rounded-circle m-top" width="90"
                height="90">
            </div>
            <div class="profile-name">
              <h2 class="fw-bold f-24">Team Member 3</h2>
            </div>
            <div class="profile-bio"><i class="bi bi-bookmark-heart"></i> <span class="b-1">Front & Back-end</span><br>
              <i class="bi bi-award"></i> <span class="b-1 ">College Name</span>
            </div>
            <div class="social">
            <a href="mailto:contact@donorhub.com" class="text-dark">  
              <i class="bi bi-envelope-at"></i></a>
              <i class="bi bi-github"></i>
              <i class="bi bi-twitter"></i>
              <a href="#" class="text-dark">
              <i class="bi bi-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--section4-->
  <section id="sum" class="section">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="item">
            <span class="icon  feature_box_col_one"><i class="bi bi-kanban"></i></span>
            <h6 class="fw-bold">Campaign Mangement</h6>
            <p>Allowing users to create and manage fundraising campaigns</p>
          </div>
        </div>
        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="item">
            <span class="icon feature_box_col_two"><i class="bi bi-credit-card-2-front
              "></i></span>
            <h6 class="fw-bold">Donation processing</h6>
            <p> Contribute funds online through secure payment processing systems</p>
          </div>
        </div>

        <div class="col-md-8 col-lg-4 col-sm-6">
          <div class="item">
            <span class="icon feature_box_col_three"><i class="bi bi-graph-up-arrow"></i></span>
            <h6 class="fw-bold">Reporting and Analytics</h6>
            <p> providing detailed reports on campaign performance</p>
          </div>
        </div>

      </div>
    </div>

  </section>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>


</html>