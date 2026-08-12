<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPSRI | About Us</title>
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/ongoing-projects.css">
    <!-- font awesome v7 -->
    <script src="https://kit.fontawesome.com/6e09983e4e.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- header -->
    <?php include "../includes/header.php"; ?>


    <!-- content start -->
    <main>

        <!-- hero -->
        <section class="onGoingPro-hero mb-5">

            <div class="container">

                <div class="row">

                    <div class="col-12 text-center text-white justify-content-center d-flex flex-column align-items-center gap-2">

                        <h1 class="fw-semibold mb-3 display-2">Ongoing Projects</h1>
                        <div style="width: 50%; max-width: 121.57px;  aspect-ratio: 1 / 1;" class="d-flex justify-content-center overflow-hidden align-items-center">
                            <img src="./assets/icons/eos-icons_project.svg" alt="Finance & Governance icon" class="w-100 h-100 object-fit-cover">
                        </div>
                        <p class="fs-5 mt-3">Building brighter futures and fostering resilience through community-driven development projects.</p>

                        <div class="hstack gap-3 justify-content-center flex-wrap">

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#climate-and-biodiversity">Climate & Biodiversity</a>
                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#sustainable-agriculture">Sustainable Agriculture</a>
                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#governance-and-finance">Governance & Finance</a>
                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#gender-inclusion">Gender Inclusion</a>

                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- hero -->

        <!-- Ongoing Projects Section -->
        <section id="ongoing-projects" class="container d-flex flex-column align-items-center gap-3 mb-5">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <a href="smed" class="card-link text-decoration-none">
                        <div class="card border-0 h-100 d-flex flex-column">
                            <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                <img src="./assets/media/img/ongoing-projects/smed1.jpeg" alt="Project Image" class="w-100 h-100 rounded-top-3" style="object-fit: cover;">
                            </div>
                            <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column">

                                <h3 class="mb-0 fs-4 overflow-hidden">Small and Medium Enterprise Development Program</h3>

                                <div class="dis-btns d-flex align-items-center flex-wrap column-gap-2 row-gap-2 my-3">
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Financial Inclusion
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Microfinance & Training
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Sustainable Development
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Self-Reliance
                                    </div>
                                </div>

                                <div class="text-truncate-3 w-100">
                                    Since 1992, we have been working with rural communities across Sri Lanka to enhance financial
                                    inclusion and economic development. Our work focuses on the creation and sustainable
                                    management of Community-Based Organizations, providing mentoring and training to aspiring
                                    business owners, and supporting broader community development initiatives.
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- <div class="col-12 col-lg-4">
                    <a href="criwmp" class="card-link text-decoration-none">
                        <div class="card border-0 h-100 d-flex flex-column">
                            <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                <img src="./assets/media/img/ongoing-projects/criwmp2.png" alt="Project Image" class="w-100 h-100 rounded-top-3" style="object-fit: cover;">
                            </div>
                            <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column">

                                <h3 class="mb-0 fs-4 overflow-hidden">Climate Resilient Integrated Water Management Project</h3>

                                <div class="dis-btns d-flex align-items-center flex-wrap column-gap-2 row-gap-2 my-3">
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Puttalam District, Nawagaththegama
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Sustainable Agriculture
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        Climate Resilience
                                    </div>
                                    <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap">
                                        2017-2024
                                    </div>
                                </div>

                                <div class="text-truncate-3 w-100">
                                    We work with communities in Sri Lanka’s Dry Zone to improve food security, enhance
                                    biodiversity and promote sustainable resource management around Ellangawa - village tank
                                    cascade systems.
                                </div>
                            </div>
                        </div>
                    </a>
                </div> -->
                <div class="col-12 col-lg-4">

                </div>
            </div>
            <!-- <button class="btn btn-outline-secondary btn-show-more rounded-pill">Show more <i class="bi bi-chevron-down"></i></button> -->
        </section>
    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>
</html>