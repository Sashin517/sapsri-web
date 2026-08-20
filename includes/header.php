<!-- Load FontAwesome Globally -->
<script src="https://kit.fontawesome.com/6e09983e4e.js" crossorigin="anonymous"></script>

<header class="sticky-top bg-body-header">
    <!-- Hide Google Translate Banner -->
    <style>
        body { top: 0 !important; }
        .skiptranslate, .goog-te-banner-frame { display: none !important; }
    </style>

    <!-- navigation start -->
    <div class="container px-0 py-md-2 py-1">

        <nav class="navbar navbar-expand-lg" data-bs-theme="dark">

            <div class="container-fluid">

                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center" href="index.php" style="text-decoration: none;">
                    <img src="assets/icons/sapsri-logo.svg" alt="sapsri_logo" height="40">
                    <div class="d-flex flex-column ms-2 justify-content-center">
                        <span class="fw-bold lh-1 text-crimson" style="font-size: 1.3rem;">SAPSRI</span>
                        
                        <!-- The d-none d-sm-block ensures it hides on tiny phones to prevent layout breaking! -->
                        <span class="lh-1 mt-1 text-white d-none d-sm-block" style="font-size: 0.75rem;">
                            South Asia Partnership - Sri Lanka
                        </span>
                    </div>
                </a>

                <!-- Mobile Buttons (Updated with Translate Toggle) -->
                <div class="d-flex d-lg-none m-0 align-items-center gap-2">
                    <button class="btn btn-sm btn-light fw-bold rounded-pill px-2" onclick="toggleLanguage()">
                        <span class="lang-toggle-text">සිං</span>
                    </button>
                    
                    <a class="btn btn-dark rounded-pill" style="padding-left:9px;padding-right:9px;" href="https://www.slreedshop.com/" target="_blank">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </a>
                    <button class="nav-toggler" type="button" id="nav-icon" onclick="toggleNavIcon(this)"
                        data-bs-toggle="collapse" data-bs-target="#navbarNavItems" aria-controls="navbarNavItems"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span></span><span></span><span></span><span></span>
                    </button>
                </div>

                <!-- Nav Items -->
                <div class="collapse navbar-collapse flex-grow-0" id="navbarNavItems">

                    <ul class="navbar-nav text-center fw-semibold me-2 align-items-lg-center">
                        
                        <!-- NEW Home Button -->
                        <li class="nav-item me-lg-2 my-2 my-lg-0">
                            <a class="nav-link active" href="index.php" title="Home">
                                <i class="fa-solid fa-house text-white fs-5"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Who We Are
                            </a>
                            <ul class="dropdown-menu text-center text-lg-start">
                                <li><a class="dropdown-item" href="about-us">About Us</a></li>
                                <li><a class="dropdown-item" href="people">People</a></li>
                                <li><a class="dropdown-item" href="publications">Publications</a></li>
                                <li><a class="dropdown-item" href="past-projects">Past Projects</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                What We Do
                            </a>
                            <ul class="dropdown-menu text-center text-lg-start">
                                <li><a class="dropdown-item" href="climate-and-biodiversity">Climate & Biodiversity</a></li>
                                <li><a class="dropdown-item" href="sustainable-agriculture">Sustainable agriculture</a></li>
                                <li><a class="dropdown-item" href="finance-and-governance">Finance & Governance</a></li>
                                <li><a class="dropdown-item" href="gender-inclusion">Gender Inclusion</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Latest
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-center text-lg-start">
                                <li><a class="dropdown-item" href="news">News & Events</a></li>
                                <li><a class="dropdown-item" href="ongoing-projects">Ongoing Projects</a></li>
                            </ul>
                        </li>

                    </ul>

                    <!-- Desktop Shop & Translate Buttons -->
                    <div class="d-none d-lg-flex gap-2 align-items-center ms-2">
                        <a class="btn btn-pill btn-dark" aria-current="page" href="https://www.slreedshop.com/" target="_blank">
                            <i class="fa-solid fa-bag-shopping me-1"></i> Shop
                        </a>
                        <button class="btn btn-pill btn-light fw-bold px-3" onclick="toggleLanguage()">
                            <span class="lang-toggle-text">සිං</span>
                        </button>
                    </div>

                    <!-- Hidden element required for Google Translate API -->
                    <div id="google_translate_element" style="display:none;"></div>

                </div>

            </div>
        </nav>
    </div>
    <!-- navigation end -->
</header>