<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>BulkMail - Professional Email Marketing & Communication Platform</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('homepage/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('homepage/assets/css/main.css') }}" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WVR1139744"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-WVR1139744');
    </script>
  
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="{{ route('index') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('homepage/assets/img/logo.png') }}" alt="">
                <!-- <h1 class="sitename">India E-Voting</h1> -->
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('index') }}" class="">Home</a></li>
                    <li><a href="#about">About us</a></li>
                    <li><a href="#contact">Contact us</a></li>
                    <li><a href="{{ route('login') }}">Admin Login</a></li>

                    <!-- <li><a href="index.html#features">Features</a></li>
                    <li><a href="index.html#services">Services</a></li>
                    <li><a href="index.html#pricing">Pricing</a></li>
                    <li class="dropdown"><a href="#"><span>Dropdown</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Dropdown 1</a></li>
                            <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i
                                        class="bi bi-chevron-down toggle-dropdown"></i></a>
                                <ul>
                                    <li><a href="#">Deep Dropdown 1</a></li>
                                    <li><a href="#">Deep Dropdown 2</a></li>
                                    <li><a href="#">Deep Dropdown 3</a></li>
                                    <li><a href="#">Deep Dropdown 4</a></li>
                                    <li><a href="#">Deep Dropdown 5</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Dropdown 2</a></li>
                            <li><a href="#">Dropdown 3</a></li>
                            <li><a href="#">Dropdown 4</a></li>
                        </ul>
                    </li>
                    <li><a href="index.html#contact">Contact</a></li>  -->
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>


        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div class="hero-bg">
                <img src="{{ asset('homepage/assets/img/hero-bg-light.webp') }}" alt="">
            </div>
            <div class="container text-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <h1 data-aos="fade-up" class="">Welcome to <span>BulkMail</span></h1>
                    <p data-aos="fade-up" data-aos-delay="100" class="">Professional Email Marketing & Bulk Communication Platform<br></p>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        <a href="#about" class="btn-get-started">Get Started</a>
                        <!-- <a href="#"
                            class="glightbox btn-watch-video d-flex align-items-center"><i
                                class="bi bi-play-circle"></i><span>Watch Video</span></a> -->
                    </div>
                    <img src="{{ asset('homepage/assets/img/hero-services-img.webp') }}" class="img-fluid hero-img"
                        alt="" data-aos="zoom-out" data-aos-delay="300">
                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- Featured Services Section -->
        <section id="featured-services" class="featured-services section">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-envelope"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Bulk Email Sending</a></h4>
                                <p class="description">Send thousands of personalized emails to your contacts with advanced template customization and tracking.</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-people"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Contact Management</a></h4>
                                <p class="description">Organize and manage your contacts efficiently with import/export capabilities and custom attributes.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-graph-up"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Email Analytics</a>
                                </h4>
                                <p class="description">Track email delivery, opens, and engagement with comprehensive reporting and analytics.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                </div>

            </div>

        </section><!-- /Featured Services Section -->

        <!-- About Section -->
        <section id="about" class="about section">
    <div class="container">
        <p class="who-we-are"><Center><b><h1><u>Who We Are</u></h1> </b></Center></p>
        <h3>BulkMail – Revolutionizing Email Communication</h3>

        <p class="fst-italic">
            At BulkMail, we leverage cutting-edge technology to deliver powerful, reliable, and scalable email marketing solutions. Our mission is to empower businesses and organizations to communicate effectively with their audiences through personalized bulk email campaigns.
        </p>

        <p class="fst-italic">
            Founded with a vision to simplify mass communication, we have evolved into a trusted platform for email marketing across diverse industries. Our system is designed to ensure high deliverability, security, and comprehensive tracking of every email sent.
        </p>

        <p class="fst-italic">
            What truly sets us apart is our commitment to user experience and results. We provide intuitive tools for contact management, template customization, and real-time analytics that help you understand and improve your email campaigns.
        </p>

        <p class="fst-italic">
            Our comprehensive offerings include bulk email sending, contact management, attachment handling, email tracking, and detailed reporting—built to meet the highest standards of deliverability and compliance.
        </p>

        <p class="fst-italic">
            At BulkMail, we are more than an email service—we are your partner in building meaningful connections with your audience. Join us as we transform the way you communicate with innovation, reliability, and trust.
        </p>
    </div>
</section>


        <!-- Features Section -->
        <section id="features-details" class="features-details section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2 class="">Key Features</h2>
                <p>Powerful tools to manage and execute your email campaigns effectively.</p>
            </div>

            <div class="container">

                <div class="row gy-4 justify-content-between features-item">

                    <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Personalized Email Templates</h3>
                            <p>
                                Create dynamic email templates with custom tags like @{{name}}, @{{email}}, and custom attributes. Our system automatically personalizes each email for every recipient, making your communication more engaging and effective.
                            </p>
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Advanced Contact Management</h3>
                            <p>
                                Import contacts in bulk, organize them with custom attributes, and segment your audience for targeted campaigns. Our intuitive interface makes managing thousands of contacts simple and efficient.
                            </p>
                            
                        </div>
                        </div>
                        
                         <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Attachment Management</h3>
                            <p>
                                Easily attach files to your email campaigns and generate downloadable attachment lists. Perfect for sending documents, reports, or resources to your contacts with secure download links.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Real-Time Email Tracking</h3>
                            <p>
                                Monitor the status of every email sent with detailed logs. Track delivery, failures, and engagement metrics to optimize your campaigns and improve deliverability rates.
                            </p>
                            
                        </div>

                </div><!-- Features Item -->

              <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                             <h3>Multiple SMTP Connections</h3>
                            <p>
                                Configure multiple SMTP connections and let our system automatically distribute emails across them for better deliverability and load balancing. Supports AWS SES and other SMTP providers.
                            </p>
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-5 d-flex align-items-center order-2 order-lg-1" data-aos="fade-up"
                        data-aos-delay="100">

                        <div class="content">
                            <h3>Comprehensive Reporting</h3>
                            <p>
                               Get detailed reports on your email campaigns including total sent, failed, and delivery statistics. Resend failed emails with a single click and maintain complete activity logs.
                            </p>
                           
                        </div>

                    </div>
                    
            
           

        </section><!-- /Features Details Section -->

     
        <!-- Contact Section -->
        <section id="contact" class="contact section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Get in Touch</h2>
        <p>We’re here to help. Reach out to us for any queries, support, or collaboration.</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 justify-content-center">

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="200">
                    <i class="bi bi-envelope-open"></i>
                    <h3>Official Email</h3>
                    <p><a href="mailto:info@bulkmail.com">info@bulkmail.com</a></p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="300">
                    <i class="bi bi-telephone-forward"></i>
                    <h3>Call Us</h3>
                    <p><a href="tel:+917990822351">+91 79908 22351</a></p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center text-center"
                     data-aos="fade-up" data-aos-delay="400">
                    <i class="bi bi-envelope-at"></i>
                    <h3>Support Email</h3>
                    <p><a href="mailto:support@bulkmail.com">support@bulkmail.com</a></p>
                </div>
            </div>

        </div>
    </div>
</section>


    </main>

    <footer id="footer" class="">
        <div class="container copyright text-center mt-1">
            <p>©2024<span> Copyright</span><strong class="px-2 sitename">BulkMail</strong>
            <span>All Rights Reserved</span>
            </p>
            </div>
    </footer>


    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('homepage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('homepage/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('homepage/assets/js/main.js') }}"></script>

</body>

</html>
