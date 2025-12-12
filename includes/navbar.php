<?php include("../includes/header.php") ;?>
<link rel="stylesheet" href="../assets/css/navbar.css">

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow rounded-bottom " style="z-index:1;">
  <div class="container px-1 ">
    <a class="navbar-brand fw-bold" href="#">
      <img src="../assets/images/logo.png" alt="logo" width="40" height="40" class="img-fluid me-2">
      <span class="logo-text">DonorHub</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="bi bi-list-nested"></span>
    </button>
    <div class="collapse navbar-collapse " id="navbarNav">
      <ul class="navbar-nav m-auto ">
        <li class="nav-item fw-bold">
          <a class="nav-link " aria-current="page" href="#home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold" href="#explore">Explore</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold" href="#about">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold" href="#summ">Summary</a>
        </li>
      </ul>
       <ul class="navbar-nav ">
           <a href="../pages/register.html" class="btn btn-outline-success me-1 w-md-1">Register</a>
           <a href="../pages/log_in.html" class="btn btn-outline-dark mt-lg-0 mt-md-2 mt-sm-2 log">Login</a>
       </ul>
    </div>
  </div>
</nav>

<?php include("../includes/footer.php") ;?>