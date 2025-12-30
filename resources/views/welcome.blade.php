{{-- @if (Route::has('login'))
<div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
  @auth
  <a href="{{ url('/home') }}"
    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
  @else
  <a href="{{ route('login') }}"
    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log
    in</a>

  @if (Route::has('register'))
  <a href="{{ route('register') }}"
    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
  @endif
  @endauth
</div>
@endif
--}}

<!DOCTYPE html>

<!--
 // WEBSITE: https://themefisher.com
 // TWITTER: https://twitter.com/themefisher
 // FACEBOOK: https://www.facebook.com/themefisher
 // GITHUB: https://github.com/themefisher/
-->

<html lang="zxx">

<head>
  <meta charset="utf-8">
  <title>NextLearning</title>

  <!-- mobile responsive meta -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- theme meta -->
  <meta name="theme-name" content="nextlearning" />

  <!-- ** Plugins Needed for the Project ** -->
  <!-- Bootstrap -->
  <link rel="stylesheet" href="assets/plugins/bootstrap/bootstrap.min.css">
  <!-- themefy-icon -->
  <link rel="stylesheet" href="assets/plugins/themify-icons/themify-icons.css">
  <!-- slick slider -->
  <link rel="stylesheet" href="assets/plugins/slick/slick.css">
  <!-- venobox popup -->
  <link rel="stylesheet" href="assets/plugins/Venobox/venobox.css">
  <!-- aos -->
  <link rel="stylesheet" href="assets/plugins/aos/aos.css">

  <!-- Main Stylesheet -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!--Favicon-->
  <link rel="shortcut icon" href="assets/images/logo2.png" type="image/x-icon">
  <link rel="icon" href="assets/images/logo2.png" type="image/x-icon">

</head>

<body>


  <!-- navigation -->
  <section class="fixed-top navigation">
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.html"><img src="assets/images/logo2.png" alt="logo"></a>
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbar"
          aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- navbar -->
        <div class="collapse navbar-collapse text-center" id="navbar">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link  page-scroll" href="#home">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link page-scroll" href="#feature">Feature</a>
            </li>
            <li class="nav-item">
              <a class="nav-link  page-scroll" href="#about">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link  page-scroll" href="#service">Service</a>
            </li>
            <li class="nav-item">
              <a class="nav-link page-scroll" href="#team">Team</a>
            </li>
          </ul>
          @if (Route::has('login'))
          <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            @auth

            <a href="{{ route('admin.index') }}" class="btn btn-primary ml-lg-3 primary-shadow">Home</a>
            @else
            <a href="{{ route('login') }}" class="btn btn-primary ml-lg-3 primary-shadow">Login</a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-primary ml-lg-3 primary-shadow">Register</a>
            @endif
            @endauth
          </div>
          @endif

        </div>
      </nav>
    </div>
  </section>
  <!-- /navigation -->

  <!-- hero area -->
  <section class="hero-section hero" data-background=""
    style="background-image: url(assets/images/hero-area/banner-bg.png);" id="home">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center zindex-1">
          <h1 class="mb-3">A New Era of <br>
            Learning Starts Here</h1>
          <p class="mb-4">Unlock a flexible, interactive, and self-paced learning experience tailored to every student's
            needs.</p>
          <a href="#" class="btn btn-secondary btn-lg">explore us</a>
          <!-- banner image -->
          <img class="img-fluid w-100 banner-image" src="assets/images/hero-area/banner-img.png" alt="banner-img">
        </div>
      </div>
    </div>
    <!-- background shapes -->
    <div id="scene">
      <img class="img-fluid hero-bg-1 up-down-animation" src="assets/images/background-shape/feature-bg-2.png" alt="">
      <img class="img-fluid hero-bg-2 left-right-animation" src="assets/images/background-shape/seo-ball-1.png" alt="">
      <img class="img-fluid hero-bg-3 left-right-animation" src="assets/images/background-shape/seo-half-cycle.png"
        alt="">
      <img class="img-fluid hero-bg-4 up-down-animation" src="assets/images/background-shape/green-dot.png" alt="">
      <img class="img-fluid hero-bg-5 left-right-animation" src="assets/images/background-shape/blue-half-cycle.png"
        alt="">
      <img class="img-fluid hero-bg-6 up-down-animation" src="assets/images/background-shape/seo-ball-1.png" alt="">
      <img class="img-fluid hero-bg-7 left-right-animation" src="assets/images/background-shape/yellow-triangle.png"
        alt="">
      <img class="img-fluid hero-bg-8 up-down-animation" src="assets/images/background-shape/service-half-cycle.png"
        alt="">
      <img class="img-fluid hero-bg-9 up-down-animation" src="assets/images/background-shape/team-bg-triangle.png"
        alt="">
    </div>
  </section>
  <!-- /hero-area -->

  <!-- feature -->
  <section class="section feature mb-0" id="feature">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-title">Awesome Features of NextLearning</h2>
          <p class="mb-100">
            NextLearning is designed with a strong focus on
            key software quality attributes such as usability, reliability, maintainability, and responsiveness.
            Each feature is carefully evaluated to ensure a stable, user-centered, and efficient learning experience.
          </p>
        </div>
        <!-- feature item -->
        <div class="col-md-6 mb-80">
          <div class="d-flex feature-item">
            <div>
              <i class="ti-ruler-pencil feature-icon mr-4"></i>
            </div>
            <div>
              <h4>Intuitive Design</h4>
              <p>
                Designed with usability in mind, ensuring students and teachers can navigate the system easily
                with minimal learning effort.
              </p>
            </div>
          </div>
        </div>
        <!-- feature item -->
        <div class="col-md-6 mb-80">
          <div class="d-flex feature-item">
            <div>
              <i class="ti-layout-cta-left feature-icon mr-4"></i>
            </div>
            <div>
              <h4>Flexible Customization</h4>
              <p>Supports easy content updates and modifications by educators, enhancing system maintainability
  and adaptability to changing learning needs.</p>

            </div>
          </div>
        </div>
        <!-- feature item -->
        <div class="col-md-6 mb-80">
          <div class="d-flex feature-item">
            <div>
              <i class="ti-split-v-alt feature-icon mr-4"></i>
            </div>
            <div>
              <h4>Reliable Platform</h4>
              <p>Developed following Quality Assurance practices to ensure system stability, error prevention,
  and consistent performance during usage.</p>

            </div>
          </div>
        </div>
        <!-- feature item -->
        <div class="col-md-6 mb-80">
          <div class="d-flex feature-item">
            <div>
              <i class="ti-layers-alt feature-icon mr-4"></i>
            </div>
            <div>
              <h4>Streamlined Layout</h4>
              <p>Structured layouts improve efficiency by allowing users to access learning materials, assessments,
  and progress tracking quickly and clearly.</p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <img class="feature-bg-1 up-down-animation" src="assets/images/background-shape/feature-bg-1.png" alt="bg-shape">
    <img class="feature-bg-2 left-right-animation" src="assets/images/background-shape/feature-bg-2.png" alt="bg-shape">
  </section>
  <!-- /feature -->

  <!-- marketing -->
  <section class="section-lg seo" id="about">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="seo-image">
            <img class="img-fluid" src="assets/images/marketing/marketing.png" alt="form-img">
          </div>
        </div>
        <div class="col-md-5">
          <h2 class="section-title">A Complete Digital Learning Experience</h2>
          <p>NextLearning offers an intuitive and dynamic learning platform that helps students develop
            essential digital skills, preparing them for higher education and future careers.</p>

        </div>
      </div>
    </div>
    <!-- background image -->
    <img class="img-fluid seo-bg" src="assets/images/backgrounds/seo-bg.png" alt="seo-bg">
    <!-- background-shape -->
    <img class="seo-bg-shape-1 left-right-animation" src="assets/images/background-shape/seo-ball-1.png" alt="bg-shape">
    <img class="seo-bg-shape-2 up-down-animation" src="assets/images/background-shape/seo-half-cycle.png"
      alt="bg-shape">
    <img class="seo-bg-shape-3 left-right-animation" src="assets/images/background-shape/seo-ball-2.png" alt="bg-shape">
  </section>
  <!-- /marketing -->

  <!-- service -->
  <section class="section-lg service" id="service">
    <div class="container">
      <div class="row justify-content-between">
        <div class="col-md-5 order-2 order-md-1">
          <h2 class="section-title">Empowering Teachers and Students with Digital Tools</h2>
          <p>NextLearning provides educators with effective tools to manage learning materials, assess student performance,
            and track progress in real time. As part of the Quality Assurance assessment, the system is designed to ensure
            high usability, reliability, and efficiency in supporting a personalized learning experience.</p>

          <li>Fully responsive across multiple devices and screen sizes</li>
          <li>Easy to customize and maintain for different teaching needs</li>
          <li>Well tested to ensure system stability and reliability</li>
          </ul>
        </div>
        <div class="col-md-7 order-1 order-md-2">
          <img class="img-fluid layer-3" src="assets/images/service/service.png" alt="service">
        </div>
      </div>
    </div>
    <!-- background image -->
    <img class="img-fluid service-bg" src="assets/images/backgrounds/service-bg.png" alt="service-bg">
    <!-- background shapes -->
    <img class="service-bg-shape-1 up-down-animation" src="assets/images/background-shape/service-half-cycle.png"
      alt="background-shape">
    <img class="service-bg-shape-2 left-right-animation" src="assets/images/background-shape/feature-bg-2.png"
      alt="background-shape">
  </section>
  <!-- /service -->

  <!-- team -->
  <section class="section-lg team" id="team">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-title">Meet the Team </h2>
          <p>Our team is dedicated to creating a platform that empowers both students and teachers.</p>

        </div>
      </div>
      <div class="col-10 mx-auto">
        <div class="team-slider">
          <!-- team-member -->
          <div class="team-member">
            <div class="d-flex mb-4">
              <div class="mr-3">
                <img class="rounded-circle img-fluid" src="assets/images/team/akmal.png" alt="team-member">
              </div>
              <div class="align-self-center">
                <h4>Akmal Razelan</h4>
                <h6 class="text-color">web designer</h6>
              </div>
            </div>
            <p>Responsible for Learning Material Module.Responsible for user interface design and layout structure. Focused on usability,
  reliability, and responsive design to ensure a positive user experience.</p>
          </div>
          <!-- team-member -->
          <div class="team-member">
            <div class="d-flex mb-4">
              <div class="mr-3">
                <img class="rounded-circle img-fluid" src="assets/images/team/steve.jpeg" alt="team-member">
              </div>
              <div class="align-self-center">
                <h4>Steve Demius</h4>
                <h6 class="text-color">web developer</h6>
              </div>
            </div>
            <p>Responsible for Report Module. Involved in front-end and back-end development, implementing core system
  functionalities and ensuring smooth interaction between system components.</p>
          </div>
          <!-- team-member -->
          <div class="team-member">
            <div class="d-flex mb-4">
              <div class="mr-3">
                <img class="rounded-circle img-fluid" src="assets/images/team/tay.jpeg" alt="team-member">
              </div>
              <div class="align-self-center">
                <h4>Tay Shon Da</h4>
                <h6 class="text-color">Programmer</h6>
              </div>
            </div>
            <p>Responsible for User Module.Assisted in system development and logic implementation, contributing to feature
  functionality and supporting debugging and testing activities.</p>
          </div>
          <!-- team-member -->
          <div class="team-member">
            <div class="d-flex mb-4">
              <div class="mr-3">
                <img class="rounded-circle img-fluid" src="assets/images/team/azleen.jpeg" alt="team-member">
              </div>
              <div class="align-self-center">
                <h4>Azleen Farisya</h4>
                <h6 class="text-color">Programmer</h6>
              </div>
            </div>
            <p>Responsible for Assessment Module.Participated in coding, system testing, and validation to ensure the application
  meets functional and non-functional requirements.</p>
          </div>
          
        </div>
      </div>
    </div>
    <!-- backgound image -->
    <img src="assets/images/backgrounds/team-bg.png" alt="team-bg" class="img-fluid team-bg">
    <!-- background shapes -->
    <img class="team-bg-shape-1 up-down-animation" src="assets/images/background-shape/seo-ball-1.png"
      alt="background-shape">
    <img class="team-bg-shape-2 left-right-animation" src="assets/images/background-shape/seo-ball-1.png"
      alt="background-shape">
    <img class="team-bg-shape-3 left-right-animation" src="assets/images/background-shape/team-bg-triangle.png"
      alt="background-shape">
    <img class="team-bg-shape-4 up-down-animation img-fluid" src="assets/images/background-shape/team-bg-dots.png"
      alt="background-shape">
  </section>
  <!-- /team -->


  {{-- <!-- client logo slider -->
  <section class="section">
    <div class="container">
      <div class="client-logo-slider align-self-center">
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-1.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-2.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-3.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-4.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-5.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-1.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-2.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-3.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-4.png" alt="client-logo"></a>
        <a href="#" class="text-center d-block outline-0 p-5"><img class="d-unset img-fluid"
            src="assets/images/clients-logo/client-logo-5.png" alt="client-logo"></a>
      </div>
    </div>
  </section>
  <!-- /client logo slider --> --}}


  <!-- footer -->
  <footer class="footer-section footer" style="background-image: url(assets/images/backgrounds/footer-bg.png);">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 text-center text-lg-left mb-4 mb-lg-0">
          <!-- logo -->
          <a href="index.html">
            <img class="img-fluid" src="assets/images/logo2.png" alt="logo">
          </a>
        </div>
        <!-- footer menu -->
        <nav class="col-lg-8 align-self-center mb-5">
          <ul class="list-inline text-lg-right text-center footer-menu">
            <li class="list-inline-item active"><a href="#home">Home</a></li>
            <li class="list-inline-item"><a class="page-scroll" href="#feature">Feature</a></li>
            <li class="list-inline-item"><a href="about.html">About</a></li>
            <li class="list-inline-item"><a class="page-scroll" href="#team">Team</a></li>
            <li class="list-inline-item"><a class="page-scroll" href="#pricing">Pricing</a></li>
            <li class="list-inline-item"><a href="contact.html">Contact</a></li>
          </ul>
        </nav>
        <!-- footer social icon -->
        <nav class="col-12">
          <ul class="list-inline text-lg-right text-center social-icon">
            <li class="list-inline-item">
              <a class="facebook" href="#"><i class="ti-facebook"></i></a>
            </li>
            <li class="list-inline-item">
              <a class="twitter" href="#"><i class="ti-twitter-alt"></i></a>
            </li>
            <li class="list-inline-item">
              <a class="linkedin" href="#"><i class="ti-linkedin"></i></a>
            </li>
            <li class="list-inline-item">
              <a class="black" href="#"><i class="ti-github"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </footer>
  <!-- /footer -->

  <!-- jQuery -->
  <script src="assets/plugins/jQuery/jquery.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="assets/plugins/bootstrap/bootstrap.min.js"></script>
  <!-- slick slider -->
  <script src="assets/plugins/slick/slick.min.js"></script>
  <!-- venobox -->
  <script src="assets/plugins/Venobox/venobox.min.js"></script>
  <!-- aos -->
  <script src="assets/plugins/aos/aos.js"></script>
  <!-- Main Script -->
  <script src="assets/js/script.js"></script>

</body>

</html>