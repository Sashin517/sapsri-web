<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our People - Sapsri</title>
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- <link rel="stylesheet" href="./assets/css/people.style.css"> -->
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/3e6ef2b5ef.js" crossorigin="anonymous"></script>
    <!-- Favicon & App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/project-sedna/assets/media/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/project-sedna/assets/media/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/project-sedna/assets/media/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/project-sedna/assets/media/img/favicons/site.webmanifest">
    <link rel="icon" href="/project-sedna/favicon.ico">
</head>

<body>

    <!-- header -->
    <?php include "../includes/header.php"; ?>

    <main>

        <!-- hero -->
        <section class="people__hero mb-5">

            <div class="container">

                <div class="row">

                    <div class="col-12 text-center text-white">

                        <h1 class="fw-semibold display-2 mb-0">Our People</h1>
                        <img src="./assets/icons/fluent_people-12-filled.svg" alt="" class="mb-5 mt-5">
                        <p class="fs-5">Meet the individuals who lead SAPSRI with vision and dedication.</p>

                        <div class="hstack gap-3 justify-content-center flex-wrap">

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#governingCouncil">
                                Governing Council
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#staffMembers">
                                Staff Members
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- hero -->


        <section class="people__content">

            <div class="container pb-3" id="memberCardsContainer">

                <h2 id="governingCouncil">Governing Council</h2>

                <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-pale-orange" id="councilCards"></div>

                <h2 id="staffMembers">Staff Members</h2>

                <h4 class="text-secondary mb-4">Executive Director</h4>

                <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-pale-orange">

                    <div class="col">

                        <div class="card p-4 rounded-4 border-0 h-100">

                            <div class="d-flex flex-column flex-sm-row gap-3 gap-sm-0 align-items-center">

                                <div>
                                    <img src="./assets/media/img/people/staff-members/executive-director/janaka-amarasinghe.jpg"
                                        width="104px" class="rounded-circle object-fit-cover"
                                        alt="Profile image of Mr. Prasanna Premarathna">
                                </div>

                                <div class="card-body py-0 text-center text-sm-start">

                                    <h5 class="card-title fw-bold">Mr Janaka Amarasinghe</h5>

                                    <h6 class="card-subtitle mb-2 text-body-secondary fw-light">
                                        MRDP, B.Com. (Sp), PGDip Com.Dev.
                                    </h6>

                                    <span class="card-text text-crimson">Executive Director</span>

                                    <p class="d-none">
                                        Mr. Amarasinghe brings over 20 years of experience in project leadership, with a
                                        focus on livelihood improvement, agriculture, and sustainable development. Mr.
                                        Amarasinghe has held several significant positions throughout his career,
                                        including Project Director for the Smallholder Tea and Rubber Revitalization
                                        Project funded by IFAD, Deputy Project Director for the Second Community
                                        Development and Livelihood Improvement Project funded by the World Bank, and
                                        Project Director for the Electronic National Identity Card Project under the
                                        Ministry of Internal Affairs and Wayamba Development.
                                        <br>
                                        An accomplished project director, Mr. Amarasinghe specializes in project
                                        planning, execution, monitoring and evaluation, risk management, and stakeholder
                                        engagement. He has extensive expertise in the operational procedures and
                                        compliance frameworks for international donors and multilateral funds.
                                    </p>

                                </div>

                                <div>
                                    <a href="#" class="text-decoration-none" aria-label="LinkedIn Profile">
                                        <i class="bi bi-linkedin fs-4"></i>
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- <h4 class="text-secondary mb-4">Administrative Unit</h4> -->

                <!-- <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-light-gray-blue" id="administrativeUnitCards">
                </div> -->

                <h4 class="text-secondary mb-4">Finance Unit</h4>

                <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-light-gray-blue" id="financeUnitCards"></div>

                <h4 class="text-secondary mb-4">Program Unit</h4>

                <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-light-gray-blue" id="programUnitCards"></div>

                <!-- <h4 class="text-secondary mb-4">
                    Field Staff - Small & Medium Entrepreneurship Development Program (SMED)
                </h4> -->

                <!-- <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-light-gray-blue" id="fieldStaffSMEDCards">
                </div> -->

                <!-- <h4 class="text-secondary mb-4">
                    Project Staff - Climate Resilient Integrated Water Management Project (CRIWMP)
                </h4> -->

                <!-- <div class="row row-cols-1 row-cols-lg-2 g-4 mb-5 card-bg-light-gray-blue" id="projectStaffCRIWMPCards">
                </div> -->

            </div>

        </section>

    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>

    <!-- member-card-viewer -->
    <div class="modal fade people-modal" id="memberCardViewer" tabindex="-1" aria-labelledby="memberCardViewerLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content rounded-4">

                <div class="modal-header border-bottom-0">
                    <img src="./assets/media/img/people/staff-members/executive-director/janaka-amarasinghe.jpg"
                        width="200px" class="rounded-circle object-fit-cover m-auto"
                        alt="Profile image of Mr. Prasanna Premarathna">
                    <button type="button" class="btn close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-body text-center">

                    <h5 class="fw-bold">Mr Janaka Amarasinghe</h5>

                    <h6 class="mb- text-body-secondary fw-light">
                        MRDP, B.Com. (Sp), PGDip Com.Dev.
                    </h6>

                    <span class="text-crimson">Executive Director</span>

                    <p class="mt-3">
                        Mr. Amarasinghe brings over 20 years of experience in project leadership, with a
                        focus on livelihood improvement, agriculture, and sustainable development. Mr.
                        Amarasinghe has held several significant positions throughout his career,
                        including Project Director for the Smallholder Tea and Rubber Revitalization
                        Project funded by IFAD, Deputy Project Director for the Second Community
                        Development and Livelihood Improvement Project funded by the World Bank, and
                        Project Director for the Electronic National Identity Card Project under the
                        Ministry of Internal Affairs and Wayamba Development.
                        <br>
                        An accomplished project director, Mr. Amarasinghe specializes in project
                        planning, execution, monitoring and evaluation, risk management, and stakeholder
                        engagement. He has extensive expertise in the operational procedures and
                        compliance frameworks for international donors and multilateral funds.
                    </p>

                </div>

                <div class="modal-footer justify-content-center border-top-0">
                    <a href="#" class="text-decoration-none" aria-label="LinkedIn Profile">
                        <i class="bi bi-linkedin fs-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- member-card-viewer -->
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/people.js"></script>
</body>

</html>