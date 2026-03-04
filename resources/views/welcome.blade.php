<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apexrise Consultant & E-Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(13, 27, 42, 0.95);
        }
        .navbar a {
            color: #fff !important;
            font-weight: 500;
        }
        .navbar a:hover {
            color: #ffc107 !important;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0d1b2a, #1b263b, #415a77);
            color: white;
            padding: 130px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
        }
        .hero p {
            font-size: 18px;
            opacity: 0.9;
        }

        /* Section Title */
        .section-title {
            font-weight: 700;
            margin-bottom: 50px;
        }

        /* Service Cards */
        .service-card {
            background: #fff;
            border-radius: 15px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            transition: 0.4s;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.15);
        }
        .service-card i {
            font-size: 40px;
            margin-bottom: 20px;
            color: #0d6efd;
        }
        .service-card h5 {
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, #1b263b, #0d1b2a);
            color: white;
            padding: 80px 0;
        }
        .stats h3 {
            font-size: 36px;
            font-weight: 700;
        }

        /* About Section */
        .about-box {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        /* Contact */
        .contact-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        /* Footer */
        footer {
            background: #0d1b2a;
            color: white;
            padding: 30px 0;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Apexrise Consultant</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li><a href="{{ route('index') }}" class="nav-link">Home</a></li>
                <li><a href="#about" class="nav-link">About Us</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
                <li><a href="{{ route('login') }}" class="nav-link btn btn-warning text-dark ms-2 px-3">User Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>Apexrise Consultant & E-Services</h1>
        <p>Secure • Transparent • Compliant Digital Solutions for Corporate & Insolvency Professionals</p>
        <a href="#services" class="btn btn-warning btn-lg mt-4 px-4">Explore Our Services</a>
    </div>
</section>

<!-- SERVICES -->
<section id="services" class="py-5 mt-5">
    <div class="container">
        <h2 class="text-center section-title">Our Professional Services</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-vote-yea"></i>
                    <h5><a href =https://indiaevoting.com/ target="_blank" rel="noopener noreferrer">E-Voting</a></h5>
                    <p>Secure corporate voting platform for CoC meetings, AGM and shareholder approvals.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-gavel"></i>
                    <h5><a href =https://indiaeauction.com/ target="_blank" rel="noopener noreferrer">E-Auction</a></h5>
                    <p>Transparent and compliant digital auction system for liquidation & asset sales.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-chart-line"></i>
                    <h5><a href =https://indiaeauction.com/ target="_blank" rel="noopener noreferrer">NPV E-Bidding</a></h5>
                    <p>Advanced NPV-based bidding platform for insolvency resolution processes.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-database"></i>
                    <h5><a href =https://datasafehub.in/ target="_blank" rel="noopener noreferrer">Virtual Data Room</a></h5>
                    <p>Highly secure confidential document sharing platform.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-envelope-open-text"></i>
                    <h5><a href =https://apexriseconsultant.com/ target="_blank" rel="noopener noreferrer">Bulk Mail</a></h5>
                    <p>Professional bulk email system for stakeholder & compliance communication.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-file-invoice"></i>
                    <h5><a href =https://apexriseconsultant.com/ target="_blank" rel="noopener noreferrer">Claim Management</a></h5>
                    <p>Digital claim submission, verification and tracking system.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-laptop-code"></i>
                    <h5><a href =https://apexriseconsultant.com/ target="_blank" rel="noopener noreferrer">Website Design & Maintenance</a></h5>
                    <p>Corporate websites with secure hosting and long-term maintenance.</p>
                </div>
            </div>

            

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-bullhorn"></i>
                    <h5><a href =https://apexriseconsultant.com/ target="_blank" rel="noopener noreferrer">Social Media</a></h5>
                    <p>Professional digital branding and online corporate presence management.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h5><a href =https://apexriseconsultant.com/ target="_blank" rel="noopener noreferrer">Application & Software</a></h5>
                    <p>Custom web & mobile application development solutions.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3>400+</h3>
                <p>Corporate Clients</p>
            </div>
            <div class="col-md-4">
                <h3>30+</h3>
                <p>Successful Projects</p>
            </div>
            <div class="col-md-4">
                <h3>99.9%</h3>
                <p>Secure Transactions</p>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about" class="py-5">
    <div class="container">
        <h2 class="text-center section-title">About Apexrise</h2>
        <div class="about-box text-center">
            <p>
                Apexrise Consultant & E-Services provides legally compliant, secure and scalable 
                digital platforms for Insolvency Professionals, Financial Institutions and Corporates 
                across India. Our focus is transparency, security and technical excellence.
            </p>
        </div>
    </div>
</section>

<section id="contact" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title mb-5">Contact Us</h2>

        <div class="row g-4">

            <!-- Contact Information -->
            <div class="col-md-5">
                <div class="contact-box p-4 shadow rounded bg-white h-100">

                    <h5 class="fw-bold mb-4">Get In Touch</h5>

                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary fs-4">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <strong>Email</strong><br>
                            info@apexriseconsultant.com<br>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="me-3 text-primary fs-4">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <strong>Mobile</strong><br>
                            +91 78741 38237<br>
                            +91 79908 22351
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="me-3 text-primary fs-4">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong>Office Address</strong><br>
                            1018 , Derasar Vado Khancho,<br>
                            Lalabhai Ni Pole, Mandavi Ni Pole,<br>
                            Maneck Chowk , Ahmedabad, Gujarat – 380009
                        </div>
                    </div>

                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-md-7">
                <div class="contact-box p-4 shadow rounded bg-white">
                    <form method="POST" action="#">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-3" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control mb-3" placeholder="Your Email" required>
                            </div>
                        </div>

                        <input type="text" class="form-control mb-3" placeholder="Mobile Number" required>

                        <textarea class="form-control mb-3" rows="4" placeholder="Your Message" required></textarea>

                        <button class="btn btn-primary w-100 py-2">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- FOOTER -->
<footer class="text-center">
    <div class="container">
        <p class="mb-0">© {{ date('Y') }} Apexrise Consultant & E-Services | All Rights Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>