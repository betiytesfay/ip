<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <title>DonorHub</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; overflow-x: hidden; }

    /* NAVBAR */
    .navbar {
      background: rgba(255,255,255,0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.08);
      padding: 12px 0;
    }
    .navbar-brand { font-weight: 800; font-size: 1.3rem; color: #1a1a2e !important; }
    .nav-link { font-weight: 600; color: #444 !important; transition: color 0.2s; }
    .nav-link:hover { color: #6c63ff !important; }
    .btn-register { background: linear-gradient(135deg, #6c63ff, #a29bfe); color: #fff !important; border: none; border-radius: 25px; padding: 8px 22px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; }
    .btn-register:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(108,99,255,0.4); }
    .btn-login { border: 2px solid #1a1a2e; border-radius: 25px; padding: 7px 22px; font-weight: 600; color: #1a1a2e !important; transition: all 0.2s; }
    .btn-login:hover { background: #1a1a2e; color: #fff !important; }

    /* HERO */
    .hero {
      min-height: 100vh;
      background: linear-gradient(135deg, #232347 0%, #1e2d50 50%, #1a4080 100%);
      display: flex; align-items: center;
      position: relative; overflow: hidden;
      padding-top: 80px;
    }
    .hero::before {
      content: '';
      position: absolute; top: -50%; right: -20%;
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(108,99,255,0.2) 0%, transparent 70%);
      border-radius: 50%;
    }
    .hero::after {
      content: '';
      position: absolute; bottom: -30%; left: -10%;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(214,48,49,0.15) 0%, transparent 70%);
      border-radius: 50%;
    }
    .hero-content { position: relative; z-index: 2; }
    .hero-badge { display: inline-block; background: rgba(108,99,255,0.2); color: #a29bfe; border: 1px solid rgba(108,99,255,0.4); border-radius: 25px; padding: 6px 16px; font-size: 0.82rem; font-weight: 600; margin-bottom: 20px; }
    .hero h1 { font-size: 3.5rem; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 20px; }
    .hero h1 span.accent { background: linear-gradient(135deg, #6c63ff, #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .hero h1 span.red { background: linear-gradient(135deg, #d63031, #ff7675); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .hero p { color: rgba(255,255,255,0.75); font-size: 1.1rem; margin-bottom: 32px; max-width: 500px; line-height: 1.7; }
    .hero-btns { display: flex; gap: 14px; flex-wrap: wrap; }
    .btn-hero-primary { background: linear-gradient(135deg, #6c63ff, #a29bfe); color: #fff; border: none; border-radius: 30px; padding: 14px 32px; font-weight: 700; font-size: 1rem; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; }
    .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(108,99,255,0.5); color: #fff; }
    .btn-hero-secondary { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,0.4); border-radius: 30px; padding: 13px 32px; font-weight: 700; font-size: 1rem; transition: all 0.2s; text-decoration: none; }
    .btn-hero-secondary:hover { background: rgba(255,255,255,0.1); border-color: #fff; color: #fff; }

    /* HERO STATS */
    .hero-stats { display: flex; gap: 32px; margin-top: 48px; flex-wrap: wrap; }
    .hero-stat { text-align: center; }
    .hero-stat .num { font-size: 2rem; font-weight: 900; color: #fff; }
    .hero-stat .lbl { font-size: 0.8rem; color: rgba(255,255,255,0.5); font-weight: 500; }
    .hero-stat-divider { width: 1px; background: rgba(255,255,255,0.15); }

    /* HERO VISUAL */
    .hero-visual { position: relative; z-index: 2; }
    .hero-card {
      background: rgba(255,255,255,0.09);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 20px; padding: 24px;
      color: #fff; margin-bottom: 16px;
    }
    .hero-card .card-label { font-size: 0.75rem; color: rgba(255,255,255,0.5); margin-bottom: 6px; }
    .hero-card .card-value { font-size: 1.4rem; font-weight: 800; }
    .hero-card .card-sub { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-top: 4px; }
    .mini-progress { height: 6px; background: rgba(255,255,255,0.15); border-radius: 10px; margin-top: 12px; }
    .mini-progress-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #6c63ff, #a29bfe); }
    .floating-badge {
      position: absolute; right: -20px; top: 20px;
      background: linear-gradient(135deg, #d63031, #ff7675);
      color: #fff; border-radius: 14px; padding: 10px 16px;
      font-size: 0.8rem; font-weight: 700;
      box-shadow: 0 8px 24px rgba(214,48,49,0.4);
    }

    /* SECTION COMMON */
    section { padding: 90px 0; }
    .section-tag { display: inline-block; background: #e8e8f5; color: #6c63ff; border-radius: 20px; padding: 5px 14px; font-size: 0.8rem; font-weight: 700; margin-bottom: 14px; }
    .section-title { font-size: 2.2rem; font-weight: 800; color: #1a1a2e; margin-bottom: 14px; }
    .section-sub { color: #888; font-size: 1rem; max-width: 520px; line-height: 1.7; }

    /* HOW IT WORKS */
    .how-section { background: #fff; }
    .step-card { text-align: center; padding: 36px 24px; border-radius: 20px; transition: transform 0.2s, box-shadow 0.2s; }
    .step-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.1); }
    .step-num { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 900; margin: 0 auto 18px; }
    .step-card h5 { font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
    .step-card p { color: #888; font-size: 0.9rem; line-height: 1.6; }
    .step-connector { display: flex; align-items: center; justify-content: center; padding-top: 26px; }
    .step-connector i { color: #ddd; font-size: 1.5rem; }

    /* DONATION TYPES */
    .types-section { background: #f4f6fb; }
    .type-card { background: #fff; border-radius: 18px; padding: 30px 24px; text-align: center; box-shadow: 0 2px 16px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
    .type-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.12); }
    .type-icon { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 16px; }
    .type-card h6 { font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
    .type-card p { color: #888; font-size: 0.85rem; margin: 0; }

    /* TEAM */
    .team-section { background: #fff; }
    .team-card { background: #f4f6fb; border-radius: 20px; padding: 32px 24px; text-align: center; transition: transform 0.2s, box-shadow 0.2s; }
    .team-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
    .team-card img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 14px; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .team-card h5 { font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
    .team-card .role { color: #6c63ff; font-size: 0.82rem; font-weight: 600; margin-bottom: 12px; }
    .team-social a { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: #fff; color: #555; margin: 0 3px; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; }
    .team-social a:hover { background: #6c63ff; color: #fff; }

    /* CTA */
    .cta-section {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
      position: relative; overflow: hidden;
    }
    .cta-section::before {
      content: ''; position: absolute; top: -40%; right: -10%;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(108,99,255,0.3) 0%, transparent 70%);
      border-radius: 50%;
    }
    .cta-section .content { position: relative; z-index: 2; text-align: center; }
    .cta-section h2 { font-size: 2.4rem; font-weight: 900; color: #fff; margin-bottom: 14px; }
    .cta-section p { color: rgba(255,255,255,0.7); font-size: 1rem; margin-bottom: 32px; }

    /* FOOTER */
    footer { background: #1a1a2e; color: rgba(255,255,255,0.5); padding: 28px 0; text-align: center; font-size: 0.88rem; }
    footer span { color: #6c63ff; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-md fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="../assets/images/logo.png" width="36" height="36" class="me-2" style="border-radius:50%;" alt="logo">
      DonorHub
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <i class="bi bi-list fs-4"></i>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#how">How It Works</a></li>
        <li class="nav-item"><a class="nav-link" href="#types">Campaigns</a></li>
        <li class="nav-item"><a class="nav-link" href="#team">About</a></li>
      </ul>
      <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="../pages/log_in.php" class="btn btn-login">Login</a>
        <a href="../pages/register.php" class="btn btn-register">Get Started</a>
      </div>
    </div>
  </div>
</nav>

<!-- HERO -->
<section id="home" class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 hero-content">
        <div class="hero-badge"><i class="bi bi-heart-fill me-2"></i>Making a difference together</div>
        <h1>Give Hope,<br>Change <span class="red">Lives</span>,<br>Build <span class="accent">Community</span></h1>
        <p>DonorHub connects generous donors with people in need. Support blood drives, education, health campaigns, and food donations — all in one place.</p>
        <div class="hero-btns">
          <a href="../pages/register.php" class="btn-hero-primary"><i class="bi bi-heart me-2"></i>Start Donating</a>
          <a href="../pages/log_in.php" class="btn-hero-secondary">Login <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><div class="num">500+</div><div class="lbl">Donors</div></div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat"><div class="num">120+</div><div class="lbl">Campaigns</div></div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat"><div class="num">$80K+</div><div class="lbl">Raised</div></div>
        </div>
      </div>
      <div class="col-lg-5 offset-lg-1 hero-visual">
        <div style="position:relative;">
          <div class="hero-card">
            <div class="card-label">Active Campaign</div>
            <div class="card-value">Help Fund Education</div>
            <div class="card-sub">by Sarah M. &nbsp;·&nbsp; <span style="color:#a29bfe;">Education</span></div>
            <div class="mini-progress mt-3">
              <div class="mini-progress-bar" style="width:72%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:0.78rem;color:rgba(255,255,255,0.5);">
              <span>$3,600 raised</span><span style="color:#a29bfe;font-weight:700;">72%</span>
            </div>
          </div>
          <div class="hero-card">
            <div class="card-label">Blood Drive</div>
            <div class="card-value">Emergency O+ Needed</div>
            <div class="card-sub">City Hospital &nbsp;·&nbsp; <span style="color:#ff7675;">Blood</span></div>
            <div class="mini-progress mt-3">
              <div class="mini-progress-bar" style="width:45%;background:linear-gradient(90deg,#d63031,#ff7675);"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:0.78rem;color:rgba(255,255,255,0.5);">
              <span>9 pints donated</span><span style="color:#ff7675;font-weight:700;">45%</span>
            </div>
          </div>
          <div class="floating-badge"><i class="bi bi-lightning-fill me-1"></i>Live Now</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section id="how" class="how-section">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag">Simple Process</div>
      <div class="section-title">How DonorHub Works</div>
      <p class="section-sub mx-auto">Three simple steps to start making a difference in someone's life today.</p>
    </div>
    <div class="row align-items-start g-4">
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-num" style="background:#e8e8f5;color:#6c63ff;">1</div>
          <h5>Create an Account</h5>
          <p>Register as a donor or recipient in minutes. Set up your profile and get started right away.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-num" style="background:#e0fff4;color:#00b894;">2</div>
          <h5>Browse or Create Campaigns</h5>
          <p>Donors browse active campaigns. Recipients create campaigns and wait for admin approval.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-num" style="background:#ffe0e0;color:#d63031;">3</div>
          <h5>Donate & Track Impact</h5>
          <p>Make your donation and watch the campaign progress in real time. Every contribution counts.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- DONATION TYPES -->
<section id="types" class="types-section">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag">What We Support</div>
      <div class="section-title">Types of Campaigns</div>
      <p class="section-sub mx-auto">From blood drives to education funds — we support causes that matter most.</p>
    </div>
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="type-card">
          <div class="type-icon" style="background:#ffe0e0;"><i class="bi bi-droplet-fill" style="color:#d63031;"></i></div>
          <h6>Blood Donation</h6>
          <p>Connect blood donors with patients in urgent need.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="type-card">
          <div class="type-icon" style="background:#e0f0ff;"><i class="bi bi-book-fill" style="color:#0984e3;"></i></div>
          <h6>Education</h6>
          <p>Fund scholarships and learning opportunities for students.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="type-card">
          <div class="type-icon" style="background:#e0fff4;"><i class="bi bi-heart-pulse-fill" style="color:#00b894;"></i></div>
          <h6>Health</h6>
          <p>Support medical treatments and health campaigns.</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="type-card">
          <div class="type-icon" style="background:#fff3e0;"><i class="bi bi-basket-fill" style="color:#e17055;"></i></div>
          <h6>Food</h6>
          <p>Help feed families and communities in need.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section id="team" class="team-section">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag">Our Team</div>
      <div class="section-title">The People Behind DonorHub</div>
      <p class="section-sub mx-auto">Built with passion by a team that believes in the power of giving.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="team-card">
          <img src="../assets/images/logo.png" alt="member">
          <h5>Team Member 1</h5>
          <div class="role">Team Lead</div>
          <div class="team-social">
            <a href="mailto:contact@donorhub.com"><i class="bi bi-envelope-fill"></i></a>
            <a href="#"><i class="bi bi-github"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="team-card">
          <img src="../assets/images/logo.png" alt="member">
          <h5>Team Member 2</h5>
          <div class="role">Documentation</div>
          <div class="team-social">
            <a href="mailto:contact@donorhub.com"><i class="bi bi-envelope-fill"></i></a>
            <a href="#"><i class="bi bi-github"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="team-card">
          <img src="../assets/images/logo.png" alt="member">
          <h5>Team Member 3</h5>
          <div class="role">Front & Back-end</div>
          <div class="team-social">
            <a href="mailto:contact@donorhub.com"><i class="bi bi-envelope-fill"></i></a>
            <a href="#"><i class="bi bi-github"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="content py-4">
      <h2>Ready to Make a Difference?</h2>
      <p>Join hundreds of donors and recipients already using DonorHub.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="../pages/register.php" class="btn-hero-primary"><i class="bi bi-person-plus me-2"></i>Register Now</a>
        <a href="../pages/log_in.php" class="btn-hero-secondary">Login <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <p>© 2024 <span>DonorHub</span> — Built with <i class="bi bi-heart-fill text-danger"></i> for a better world.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
