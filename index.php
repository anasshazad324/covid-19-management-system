<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Covid System — Test & Vaccination</title>

  <!-- Bootstrap CSS + Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

  <style>
    /* ====== Global ====== */
    :root{
      --accent: #0d6efd;
      --accent-2: #6610f2;
      --muted: #6c757d;
      --card-bg: rgba(255,255,255,0.92);
      --glass: rgba(255,255,255,0.06);
    }
    *{box-sizing: border-box}
    body{
      font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
      background: linear-gradient(180deg, #f6f9ff 0%, #eef4ff 100%);
      color: #222;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* ====== NAV ====== */
    .navbar{backdrop-filter: blur(6px);}
    .brand-gradient{background: linear-gradient(90deg, var(--accent), var(--accent-2)); -webkit-background-clip: text; background-clip:text; color: transparent}

    /* ====== HERO ====== */
    .hero{
      min-height: 82vh;
      display:flex;align-items:center;justify-content:center;position:relative;padding:60px 0;
      background: radial-gradient(1200px 400px at 10% 10%, rgba(13,110,253,0.08), transparent 8%), radial-gradient(900px 300px at 90% 90%, rgba(102,16,242,0.06), transparent 8%), url('https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=1600') no-repeat center/cover;
    }
    .hero::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg, rgba(0,0,0,0.22), rgba(0,0,0,0.18));}
    .hero-inner{position:relative;z-index:2;max-width:1200px;width:100%;padding:40px}
    .hero-card{background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9)); border-radius:18px; padding:36px; box-shadow: 0 10px 30px rgba(13,110,253,0.08);}
    .hero-title{font-size:2.4rem;font-weight:700;margin-bottom:8px;line-height:1.05}
    .hero-sub{color:var(--muted);margin-bottom:18px}

    /* CTA group */
    .cta .btn-primary{padding:12px 24px;border-radius:10px;font-weight:600;}
    .cta .btn-outline{border-radius:10px;padding:12px 20px}

    /* ====== FEATURES / CARDS ====== */
    .cards .card{border:none;border-radius:14px;padding:22px;background:var(--card-bg);transition:transform .25s, box-shadow .25s}
    .cards .card:hover{transform:translateY(-10px);box-shadow:0 18px 40px rgba(20,30,72,0.08)}
    .cards .bi{font-size:2.1rem;color:var(--accent)}

    /* ====== FOOTER BIG ====== */
    footer.bigfooter{background:linear-gradient(180deg,#0d6efd,#6610f2);color:#fff;padding:48px 0 24px;margin-top:40px}
    footer .footer-card{background: rgba(255,255,255,0.06); padding:22px;border-radius:12px}
    .social-btn{width:42px;height:42px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);margin-right:8px}

    /* small responsive tweaks */
    @media (max-width:768px){
      .hero-title{font-size:1.6rem}
      .hero{padding:40px 0}
    }

    /* subtle animated pulse for accent */
    .pulse{position:relative}
    .pulse::after{content:'';position:absolute;inset:-6px;border-radius:14px;background:linear-gradient(90deg, rgba(13,110,253,0.06), rgba(102,16,242,0.04));filter:blur(18px);opacity:0;transition:opacity .3s}
    .pulse:hover::after{opacity:1}
  </style>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <div class="p-2" style="background:linear-gradient(45deg,var(--accent),var(--accent-2));border-radius:10px;color:#fff;font-weight:700">CS</div>
      <div class="d-none d-sm-block">
        <div class="brand-gradient">Covid System</div>
        <small class="text-muted d-block">Test & Vaccination</small>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="patient_register.php">Register</a></li>
        <li class="nav-item"><a class="nav-link" href="patient_login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="hospital_register.php">Hospitals</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_login.php"><span class="badge bg-secondary">Admin</span></a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="hero-card">
          <div class="row">
            <div class="col-12 col-md-8">
              <h1 class="hero-title">Fast Covid Tests & Trusted Vaccination Slots</h1>
              <p class="hero-sub">Book tests, view results, and schedule vaccinations at verified hospitals. Secure, fast, and free for everyone.</p>
              <div class="d-flex gap-2 cta flex-wrap">
                <a href="patient_register.php" class="btn btn-primary pulse">Get Started</a>
                <a href="patient_login.php" class="btn btn-outline btn-light">Login</a>
              </div>

              <div class="mt-4 d-flex gap-3 align-items-center">
                <div class="text-center">
                  <div class="fw-bold">98%</div>
                  <small class="text-muted">Fast results</small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                  <div class="fw-bold">500+</div>
                  <small class="text-muted">Verified hospitals</small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                  <div class="fw-bold">99.9%</div>
                  <small class="text-muted">Secure records</small>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4 d-none d-md-flex justify-content-center align-items-center">
              <!-- small cards on right -->
              <div class="card shadow-sm p-3" style="width:180px;border-radius:12px;">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-calendar-check fs-2 text-primary"></i>
                  <div>
                    <div class="small text-muted">Next Available</div>
                    <div class="fw-bold">Sep 30, 2025</div>
                  </div>
                </div>
                <div class="mt-3">
                  <a href="patient_register.php" class="btn btn-sm btn-outline-primary w-100">Book Slot</a>
                </div>
              </div>
            </div>
          </div> <!-- row inside card -->
        </div> <!-- hero-card -->
      </div>

      <div class="col-lg-5 mt-4 mt-lg-0">
        <!-- Feature/Quick action card -->
        <div class="card hero-card shadow-sm">
          <div class="card-body">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-grid gap-2">
              <a href="patient_register.php" class="btn btn-lg btn-outline-primary text-start"><i class="bi bi-person-plus me-2"></i> New Patient Registration</a>
              <a href="patient_login.php" class="btn btn-lg btn-outline-primary text-start"><i class="bi bi-box-arrow-in-right me-2"></i> Patient Login</a>
              <a href="hospital_register.php" class="btn btn-lg btn-outline-primary text-start"><i class="bi bi-hospital me-2"></i> Register Hospital</a>
              <a href="hospital_login.php" class="btn btn-lg btn-outline-primary text-start"><i class="bi bi-clock-history me-2"></i> Manage Appointments</a>
            </div>
          </div>
        </div>

        <div class="mt-3 d-flex gap-3">
          <div class="card footer-card flex-grow-1 text-center">
            <div class="fw-bold small">Support</div>
            <div class="small text-muted">support@covidsystem.com</div>
          </div>
          <div class="card footer-card text-center" style="width:110px;">
            <div class="fw-bold small">Call</div>
            <div class="small text-muted">+92 300 1234567</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CARDS GRID -->
<section class="container my-5 cards">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Platform Features</h3>
    <small class="text-muted">Secure • Fast • Reliable</small>
  </div>

  <div class="row g-4">
    <div class="col-md-6 col-lg-3">
      <div class="card h-100 p-3">
        <i class="bi bi-calendar-event"></i>
        <h6 class="mt-2">Easy Booking</h6>
        <p class="small text-muted">Choose time & place that fits your schedule.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card h-100 p-3">
        <i class="bi bi-file-earmark-medical"></i>
        <h6 class="mt-2">Digital Reports</h6>
        <p class="small text-muted">Download verified test results instantly.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card h-100 p-3">
        <i class="bi bi-person-badge"></i>
        <h6 class="mt-2">Verified Hospitals</h6>
        <p class="small text-muted">Hospitals approved by admin & quality checks.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card h-100 p-3">
        <i class="bi bi-shield-lock"></i>
        <h6 class="mt-2">Secure Data</h6>
        <p class="small text-muted">Encrypted records and role-based access.</p>
      </div>
    </div>
  </div>
</section>


<!-- BIG FOOTER -->
<footer class="bigfooter">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="footer-card">
          <h5>About Covid System</h5>
          <p class="small">A lightweight system to manage Covid tests and vaccinations. Built for scale and security — friendly UX for patients and hospitals.</p>
          <div class="mt-2">
            <span class="social-btn"><i class="bi bi-twitter text-white"></i></span>
            <span class="social-btn"><i class="bi bi-facebook text-white"></i></span>
            <span class="social-btn"><i class="bi bi-instagram text-white"></i></span>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="footer-card">
          <h5>Quick Links</h5>
          <ul class="list-unstyled small mb-0">
            <li><a href="patient_register.php">Patient Register</a></li>
            <li><a href="patient_login.php">Patient Login</a></li>
            <li><a href="hospital_register.php">Hospital Register</a></li>
            <li><a href="hospital_login.php">Hospital Login</a></li>
            <li><a href="admin_login.php">Admin Login</a></li>
          </ul>
        </div>
      </div>

      <div class="col-md-4">
        <div class="footer-card">
          <h5>Contact & Support</h5>
          <p class="small mb-1">Email: support@covidsystem.com</p>
          <p class="small mb-1">Phone: +92 300 1234567</p>
          <p class="small mb-0">Address: Savana City, Karachi</p>
        </div>
      </div>
    </div>

    <div class="text-center mt-4 small">&copy; <?php echo date('Y'); ?> Covid System — All rights reserved.</div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
