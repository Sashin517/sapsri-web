<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our People - Sapsri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/impact_area.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/3e6ef2b5ef.js" crossorigin="anonymous"></script>

    <style>
        /* Define custom color variables for easy theming */
        :root {
            --brand-orange: #f39c12;
            --brand-dark: #343a40;
            --card-bg-light-orange: #fff3e0;
            --card-bg-light-gray: #f8f9fa;
        }

        /* --- Hero Section Styles --- */
        section.climate-hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url(./assets/media/img/impact-areas/CLI/cli-banner-img-1.webp);
            background-size: cover;
            background-position: center;
            color: white;
            padding: 10rem 0;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- header -->
    <?php include "../includes/header.php"; ?>

    <main>
        <!-- hero -->
        <section class="climate-hero mb-5">

            <div class="container">

                <div class="row">

                    <div class="col-12 text-center text-white justify-content-center d-flex flex-column align-items-center gap-2">

                        <h1 class="fw-semibold mb-3 display-2">Climate & Biodiversity</h1>
                        <div style="width: 50%; max-width: 121.57px;  aspect-ratio: 1 / 1;" class="d-flex justify-content-center overflow-hidden align-items-center">
                            <img src="./assets/icons/category small icon set.svg" alt="" class="w-100 h-100 object-fit-cover">
                        </div>

                        <p class="fs-5 mt-3">Building resilience to climate change & safeguarding Sri Lanka's unique biodiversity</p>

                        <div class="hstack gap-3 justify-content-center flex-wrap">

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#overview">
                                Overview
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#whatWeDo">
                                What We Do
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#projects">
                                Projects
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#contributionToSDGs">
                                Contribution to SDGs
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#donors">
                                Donors & Partners
                            </a>


                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- hero -->
        <div class="container my-5 problem" id="overview">
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center overflow-hidden my-5">
                <div class="d-flex flex-column  pe-0 pe-md-5 w-100 justify-content-center py-4 text-start">
                    <h2 class="text-crimson fw-semibold mb-3">Climate</h2>
                    <p>
                        <span class="fs-5 fw-semibold">Sustainable water management is vital in Sri Lanka's Dry Zone, where erratic rainfall,
                            prolonged dry spells, and increasing climate pressures threaten agricultural livelihoods and
                            the long-term viability of traditional irrigation systems.</span></br></br>
                        Sri Lanka's dry zone, which spans over 60% of the island, is both the nation's agricultural
                        heartland and home to a majority of smallholder farming communities. More than half the
                        population in this region is engaged in farming or agriculture-related livelihoods. However, the
                        area faces high seasonal rainfall variability, prolonged dry spells, and increasingly frequent
                        extreme weather events, including floods and droughts. These climate shocks have disrupted
                        crop production, damaged irrigation infrastructure, and reduced both the quality and quantity
                        of drinking water. Despite these challenges, centuries-old tank cascade systems remain central
                        to rural water security, providing a vital buffer against erratic rainfall and sustaining agriculture
                        across the dry zone.
                        Agriculture and food security in this region face growing stress from:
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Climate variability (intense droughts and erratic rainfall) </li>
                        <li class="list-group-item">Catchment degradation </li>
                        <li class="list-group-item">Siltation and poor maintenance of tanks </li>
                        <li class="list-group-item">Groundwater depletion </li>
                        <li class="list-group-item">Inefficient irrigation practices</li>
                    </ul>

                    </p>
                    <!-- <button class="btn btn-primary rounded-pill">Discover more</button> -->
                </div>
                <!-- Solution Image -->
                <div class="ratio ratio-4x3 w-100">
                    <img src="./assets/media/img/impact-areas/CLI/cli-side-img-1.jpg" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 50rem 0 0 50rem;">
                </div>
            </div>
        </div>
        <div class="container my-5 problem" id="whatWeDo">
            <div class="d-flex flex-column flex-md-row-reverse justify-content-center align-items-center overflow-hidden my-5">
                <div class="d-flex flex-column  ps-0 ps-md-5 w-100 justify-content-center py-4 text-start">
                    <h2 class="text-crimson fw-semibold mb-3">What is SAPSRI doing?</h2>
                    <ul class="list-group list-group-flush bg-white rounded">

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Rainwater Harvesting and Storage</h5>
                            <p class="mb-0">
                                We focus on rehabilitating traditional tank cascade systems, constructing small-scale water
                                retention structures, and promoting community-led maintenance — all aimed at capturing
                                seasonal rainfall, improving groundwater recharge, and ensuring reliable water availability for
                                agriculture and domestic use in the Dry Zone.
                            </p>
                        </li>

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Efficient Irrigation Practices</h5>
                            <p class="mb-0">
                                We promote efficient irrigation practices by introducing methods such as alternate wetting and
                                drying (AWD) in paddy cultivation, encouraging drip and sprinkler systems for field crops, and
                                training farmers in water budgeting and scheduling — all to reduce water waste, enhance crop
                                productivity, and build resilience against water scarcity in the Dry Zone.
                            </p>
                        </li>

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Community-Based Water Governance</h5>
                            <p class="mb-0">
                                SAPSRI supports community-based water governance by strengthening Farmer Organizations (FOs),
                                establishing Cascade Management Committees, and promoting participatory planning and maintenance
                                of irrigation systems — ensuring that water resources are managed equitably, sustainably, and
                                with strong local ownership in the Dry Zone.
                            </p>
                        </li>

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Data and Decision Support</h5>
                            <p class="mb-0">
                                We enhance data and decision support by promoting the use of climate and water monitoring tools,
                                supporting farmer access to weather advisories, and facilitating community-level planning through
                                participatory mapping and water budgeting — enabling more informed and adaptive management of
                                water and agriculture.
                            </p>
                        </li>

                    </ul>
                    <!-- <button class="btn btn-primary rounded-pill">Discover more</button> -->
                </div>
                <!-- Solution Image -->
                <div class="ratio ratio-4x3 w-100">
                    <img src="./assets/media/img/impact-areas/CLI/cli-side-img-2.jpg" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 0 50rem 50rem 0;">
                </div>
            </div>
        </div>
        <div class="container my-5 solution">
            <!-- <h2 class="fw-semibold mb-3 text-center mb-5">Solution</h2> -->
            <!--  -->
            <div class="row row-cols-1 row-cols-lg-3 justify-content-center mb-5">

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/two-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">90538+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Direct & Indirect Beneficiaries</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/streamline-ultimate_data-lake-1-bold.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">46+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">No of Tanks Rehabilitated</h4>
                    </div>
                </div>

                <!-- <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/stock-increase.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">388+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">New Loans in 2025</h4>
                    </div>
                </div> -->
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center overflow-hidden my-5">
                <div class="d-flex flex-column  pe-0 pe-md-5 w-100 justify-content-center py-4 text-start">
                    <h2 class="text-crimson fw-semibold mb-3">Biodiversity </h2>
                    <p>
                        <span class="fs-5 fw-semibold">Restoring forest cover and enriching habitats in the Dry Zone is vital for protecting water
                            sources, improving biodiversity, and enhancing resilience to climate extremes such as droughts
                            and floods. </span></br></br>
                        Sri Lanka is home to remarkable biodiversity and considered to be the richest country in the
                        Asian region in terms of species concentration. In recognition of its unique natural
                        environment, the Sri Lankan government has committed to the 30x30 targets under the Global
                        Biodiversity Framework. Currently, 25% of Sri Lanka's terrestrial lands are under protection.</br>
                        However, deforestation and land-use changes continue to threaten biodiversity and climate
                        resilience. This is particularly pronounced Sri Lankas dry zone, where approximately 246,958
                        hectares of forest cover was lost between 1992 and 2019, an 8% reduction over 27 years. This
                        decline is linked to smallholder agricultural expansion, land tenure insecurity, and inadequate
                        sustainable land management practices.

                    </p>
                    <!-- <button class="btn btn-primary rounded-pill">Discover more</button> -->
                </div>
                <!-- Solution Image -->
                <div class="ratio ratio-4x3 w-100">
                    <img src="./assets/media/img/impact-areas/CLI/cli-side-img-3.webp" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 50rem 0 0 50rem;">
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row-reverse justify-content-center align-items-center overflow-hidden my-5">
                <div class="d-flex flex-column  ps-0 ps-md-5 w-100 justify-content-center py-4 text-start">
                    <h2 class="text-crimson fw-semibold mb-3">What is SAPSRI doing?</h2>
                    <ul class="list-group list-group-flush bg-white rounded">

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Reforestation and habitat restoration </h5>
                            <p class="mb-0">
                                We enable reforestation and habitat restoration by working with communities to replant native
                                species, restore degraded catchment areas, and establish buffer zones around tanks, helping to
                                reduce erosion, enhance biodiversity, and improve the long-term sustainability of water
                                systems in the Dry Zone.
                            </p>
                        </li>

                        <li class="list-group-item">
                            <h5 class="fw-semibold">Bio fencing </h5>
                            <p class="mb-0">
                                We promote bio-fencing by encouraging the cultivation of commercially valuable, non
                                palatable plant species—such as citrus, gliricidia, or thorny shrubs—along the boundaries of
                                farms and tank areas, creating a natural barrier that helps reduce human-elephant conflict
                                while providing additional income and ecosystem benefits to Dry Zone communities.
                            </p>
                            <!-- <button class="btn btn-primary rounded-pill mt-2">Discover more</button> -->
                        </li>



                    </ul>
                    <!-- <button class="btn btn-primary rounded-pill">Discover more</button> -->
                </div>
                <!-- Solution Image -->
                <div class="ratio ratio-4x3 w-100">
                    <img src="./assets/media/img/impact-areas/CLI/cli-side-img-4.webp" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 0 50rem 50rem 0;">
                </div>
            </div>

            <!-- <div class="row row-cols-1 row-cols-lg-3 mb-5">

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/two-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">1600+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Beneficiaries</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/three-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">60+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Community Based Organizations</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="./assets/icons/stock-increase.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">388+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">New Loans in 2025</h4>
                    </div>
                </div>
            </div> -->
            <!--second highlighted key elements end-->
        </div>

        <!-- related projects -->
        <section class="container my-5" id="projects">
            <h3 class="text-crimson fw-semibold text-center mb-5">Related Projects</h3>

            <div id="projectCarousel" class="carousel slide" data-bs-ride="carousel">

                <div class="carousel-indicators" style="bottom: -45px;">
                    <button type="button" data-bs-target="#projectCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#projectCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row justify-content-center g-4">
                            <!-- SMED related project card -->
                            <div class="col-md-4 mb-3">                                    
                                    <div class="card border-0 h-100 position-relative">
                                        <a href="smed" class="card-link text-decoration-none stretched-link" aria-label="View Project"></a>
                                        <!-- 1. The Container DIV -->
                                        <!-- It has a fixed height and overflow is hidden. -->
                                        <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                            <!-- 2. The Image IMG -->
                                            <!-- w-100 and h-100 make it fill the div. -->
                                            <!-- object-fit: cover tells it how to fill the space without distortion. -->
                                            <img src="./assets/media/img/past-projects/Climate Resilient Integrated Water Management Project.jpg"
                                                alt="Project CodeKids"
                                                class="w-100 h-100 rounded-top-3"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column justify-content-between">
                                            <span class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-4 flex-grow-2 overflow-hidden text-truncate-3">Climate Resilient Integrated Water Management Project</h3>
                                                <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 ms-2 flex-grow-1 text-nowrap">
                                                    completed
                                                </div>
                                            </span>

                                            <div class="text-truncate-3 w-100 flex-grow-1">
                                                We work with communities in Sri Lanka’s Dry Zone to improve food security, enhance biodiversity and promote sustainable resource management around Ellangawa - village tank cascade systems.
                                                Since 2017, SAPSRI has been enabling the rehabilitation and sustainable management of 46 tanks within three cascade systems, supporting 1447 acres of paddy cultivation (across Maha and Yala seasons) and 1,059 beneficiaries, in Puttalam and Kurunegala districts.
                                            </div>

                                            <span class="d-flex justify-content-start align-items-center mt-3">
                                                <!-- <p class="text fs-6 fw-semibold mb-0">Posted on 17 May 2025</p> -->
                                                <a href="#" class="d-flex justify-content-center align-items-center bg-black rounded-pill border-0 px-3 py-2 position-relative z-2">
                                                    <img src="./assets/icons/iconamoon_link-external-bold.svg" alt="External link icon" style="width: 21px; height: 21px;">
                                                </a>
                                            </span>
                                        </div>
                                        <!-- </a> -->
                                    </div>                                
                            </div>
                            <!-- end of SMED related project card -->
                            <!-- A Tank Based Biodiversity Improvement & Protection Project related project card -->
                            <div class="col-md-4 mb-3">                                    
                                    <div class="card border-0 h-100 position-relative">
                                        <a href="past-projects" target="_blank" class="card-link text-decoration-none stretched-link" aria-label="View Project"></a>
                                        <!-- 1. The Container DIV -->
                                        <!-- It has a fixed height and overflow is hidden. -->
                                        <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                            <!-- 2. The Image IMG -->
                                            <!-- w-100 and h-100 make it fill the div. -->
                                            <!-- object-fit: cover tells it how to fill the space without distortion. -->
                                            <img src="./assets/media/img/past-projects/A Tank Based Biodiversity Improvement & Protection Project 2.JPG"
                                                alt="Project CodeKids"
                                                class="w-100 h-100 rounded-top-3"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column justify-content-between">
                                            <span class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-4 flex-grow-2 overflow-hidden text-truncate-3">A Tank Based Biodiversity Improvement & Protection Project</h3>
                                                <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 ms-2 flex-grow-1 text-nowrap">
                                                    completed
                                                </div>
                                            </span>

                                            <div class="text-truncate-3 w-100 flex-grow-1">
                                                Anuradhapura District,
                                                Funded by Gef Small grant Program,
                                                2014-2016
                                            </div>

                                            <span class="d-flex justify-content-start align-items-center mt-3">
                                                <!-- <p class="text fs-6 fw-semibold mb-0">Posted on 17 May 2025</p> -->
                                                <a href="#" class="d-flex justify-content-center align-items-center bg-black rounded-pill border-0 px-3 py-2 position-relative z-2">
                                                    <img src="./assets/icons/iconamoon_link-external-bold.svg" alt="External link icon" style="width: 21px; height: 21px;">
                                                </a>
                                            </span>
                                        </div>
                                        <!-- </a> -->
                                    </div>                                
                            </div>
                            <!-- A Tank Based Biodiversity Improvement & Protection Project related project card -->
                            <div class="col-md-4 mb-3">                                    
                                    <div class="card border-0 h-100 position-relative">
                                        <a href="past-projects" target="_blank" class="card-link text-decoration-none stretched-link" aria-label="View Project"></a>
                                        <!-- 1. The Container DIV -->
                                        <!-- It has a fixed height and overflow is hidden. -->
                                        <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                            <!-- 2. The Image IMG -->
                                            <!-- w-100 and h-100 make it fill the div. -->
                                            <!-- object-fit: cover tells it how to fill the space without distortion. -->
                                            <img src="./assets/media/img/past-projects/Community Participatory Action to build resilience in response to drought and human elephant.jpeg"
                                                alt="Project CodeKids"
                                                class="w-100 h-100 rounded-top-3"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column justify-content-between">
                                            <span class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-4 flex-grow-2 overflow-hidden text-truncate-3">Community Participatory Action to build resilience in response to drought and human elephant conflict (CPABR)</h3>
                                                <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 ms-2 flex-grow-1 text-nowrap">
                                                    completed
                                                </div>
                                            </span>

                                            <div class="text-truncate-3 w-100 flex-grow-1">                                                
                                                Puttalam District
                                            </div>

                                            <span class="d-flex justify-content-start align-items-center mt-3">
                                                <!-- <p class="text fs-6 fw-semibold mb-0">Posted on 17 May 2025</p> -->
                                                <a href="#" class="d-flex justify-content-center align-items-center bg-black rounded-pill border-0 px-3 py-2 position-relative z-2">
                                                    <img src="./assets/icons/iconamoon_link-external-bold.svg" alt="External link icon" style="width: 21px; height: 21px;">
                                                </a>
                                            </span>
                                        </div>
                                        <!-- </a> -->
                                    </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row justify-content-center g-4">
                            <!-- Sustainable Agriculture and Global Exchange (SAGE) program related project card -->
                            <div class="col-md-4 mb-3">                                    
                                    <div class="card border-0 h-100 position-relative">
                                        <a href="smed" class="card-link text-decoration-none stretched-link" aria-label="View Project"></a>
                                        <!-- 1. The Container DIV -->
                                        <!-- It has a fixed height and overflow is hidden. -->
                                        <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                            <!-- 2. The Image IMG -->
                                            <!-- w-100 and h-100 make it fill the div. -->
                                            <!-- object-fit: cover tells it how to fill the space without distortion. -->
                                            <img src="./assets/media/img/past-projects/sage-program.webp"
                                                alt="Project CodeKids"
                                                class="w-100 h-100 rounded-top-3"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column justify-content-between">
                                            <span class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-4 flex-grow-2 overflow-hidden text-truncate-3">Sustainable Agriculture and Global Exchange (SAGE) program</h3>
                                                <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 ms-2 flex-grow-1 text-nowrap">
                                                    completed
                                                </div>
                                            </span>

                                            <div class="text-truncate-3 w-100 flex-grow-1">

                                            </div>

                                            <span class="d-flex justify-content-start align-items-center mt-3">
                                                <!-- <p class="text fs-6 fw-semibold mb-0">Posted on 17 May 2025</p> -->
                                                <a href="#" class="d-flex justify-content-center align-items-center bg-black rounded-pill border-0 px-3 py-2 position-relative z-2">
                                                    <img src="./assets/icons/iconamoon_link-external-bold.svg" alt="External link icon" style="width: 21px; height: 21px;">
                                                </a>
                                            </span>
                                        </div>
                                        <!-- </a> -->
                                    </div>                                
                            </div>
                            <!-- end of SMED related project card -->
                            <!-- Peoples' Initiative for Responsible Management of Water Resources at Kudadodanathathawa Project related project card -->
                            <div class="col-md-4 mb-3">                                    
                                    <div class="card border-0 h-100 position-relative">
                                        <a href="past-projects" target="_blank" class="card-link text-decoration-none stretched-link" aria-label="View Project"></a>
                                        <!-- 1. The Container DIV -->
                                        <!-- It has a fixed height and overflow is hidden. -->
                                        <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                            <!-- 2. The Image IMG -->
                                            <!-- w-100 and h-100 make it fill the div. -->
                                            <!-- object-fit: cover tells it how to fill the space without distortion. -->
                                            <img src="./assets/media/img/past-projects/Peoples' Initiative for Responsible Management of Water Resources at Kudadodanathathawa 2.jpg"
                                                alt="Project CodeKids"
                                                class="w-100 h-100 rounded-top-3"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column justify-content-between">
                                            <span class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-4 flex-grow-2 overflow-hidden text-truncate-3">Peoples' Initiative for Responsible Management of Water Resources at Kudadodanathathawa</h3>
                                                <div class="bg-gold-yellow text-black rounded-5 py-2 px-3 ms-2 flex-grow-1 text-nowrap">
                                                    completed
                                                </div>
                                            </span>

                                            <div class="text-truncate-3 w-100 flex-grow-1">
                                                Puttalam District
                                            </div>

                                            <span class="d-flex justify-content-start align-items-center mt-3">
                                                <!-- <p class="text fs-6 fw-semibold mb-0">Posted on 17 May 2025</p> -->
                                                <a href="#" class="d-flex justify-content-center align-items-center bg-black rounded-pill border-0 px-3 py-2 position-relative z-2">
                                                    <img src="./assets/icons/iconamoon_link-external-bold.svg" alt="External link icon" style="width: 21px; height: 21px;">
                                                </a>
                                            </span>
                                        </div>
                                        <!-- </a> -->
                                    </div>                                
                            </div>
                        </div>
                    </div>
                </div>               

                <button class="carousel-control-prev" type="button" data-bs-target="#projectCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#projectCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <!-- <div class="d-flex gap-3 justify-content-center mt-4">
                <button id="pastProjectsBtn" class="btn bg-black rounded-pill py-3 px-4 fs-6 mt-2" style="color: #fff;">Past Projects</button>
                <button id="ongoingProjectsBtn" class="btn btn-primary rounded-pill py-3 px-4 fs-6 mt-2" style="color: #fff;">Ongoing Projects</button>
            </div> -->
        </section>
        <!-- related projects end-->
        <!-- photo banner -->

        <div class="w-100 overflow-hidden d-flex flex-column justify-content-center align-items-center mb-5 bg-dark" style="max-height: 439px;">
            <img src="./assets/media/img/impact-areas/CLI/cli-stript-img-1.jpg" alt="sdg_img" class="object-fit-cover w-100 h-100">
        </div>
        <!-- success story -->
        <section class="container" id="success-stories" style="margin-bottom: 64px;">

            <h2 class="my-4">Success Stories</h2>

            <div id="StoryCarousel" class="carousel slide" data-bs-ride="carousel">

                <div class="carousel-indicators" style="bottom: -52px;">
                    <button type="button" data-bs-target="#StoryCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#StoryCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                </div>

                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="story-card d-flex flex-column flex-md-row justify-content-center rounded-4 overflow-hidden">
                            <!-- story image -->
                            <div class="story-image overflow-hidden align-items-center">
                                <img src="./assets/media/img/53501cd9-23-neil-palmer-ciat-ccafs.jpg" alt="success_story_img" class="object-fit-cover w-100 h-100">
                            </div>
                            <!-- story text -->
                            <div class="story-text d-flex flex-column align-items-center justify-content-center p-5 h-100" style="background: #EEEFF0;">
                                <div class="w-100">
                                    <img src="./assets/icons/Vector.svg" alt="People icon" class="img-fluid mb-2" style="max-width:32px;">
                                    <p class="text-center m-3 fw-bold fs-5 text-truncate-6">The SMED project by SAPSRI aims to uplift rural livelihoods through sustainable microenterprise development and skill-building. By empowering individuals—especially women and youth—with vocational training, access to microfinance, and entrepreneurial guidance, the project...</p>
                                    <span class="d-flex justify-content-end">
                                        <img src="./assets/icons/Vector (1).svg" alt="People icon" class="img-fluid mt-2" style="max-width:32px;">
                                    </span>
                                </div>
                                <p class="text-center m-3 fs-4">Nimal Rajakaruna</p>
                                <!-- <button class="btn bg-theme-accent rounded-pill py-3 px-4 fs-6 my-4">See story in detail</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="story-card d-flex flex-column flex-md-row justify-content-center rounded-4 overflow-hidden">
                            <!-- story image -->
                            <div class="story-image overflow-hidden align-items-center">
                                <img src="./assets/media/img/pexels-tu-nguyen-477344610-18682443.jpg" alt="success_story_img" class="object-fit-cover w-100 h-100">
                            </div>
                            <!-- story text -->
                            <div class="story-text d-flex flex-column align-items-center justify-content-center p-5 h-100" style="background: #EEEFF0;">
                                <div class="w-100">
                                    <img src="./assets/icons/Vector.svg" alt="People icon" class="img-fluid mb-2" style="max-width:32px;">
                                    <p class="text-center m-3 fw-bold fs-5 text-truncate-6">The SMED project by SAPSRI aims to uplift rural livelihoods through sustainable microenterprise development and skill-building. By empowering individuals—especially women and youth—with vocational training, access to microfinance, and entrepreneurial guidance, the project...</p>
                                    <span class="d-flex justify-content-end">
                                        <img src="./assets/icons/Vector (1).svg" alt="People icon" class="img-fluid mt-2" style="max-width:32px;">
                                    </span>
                                </div>
                                <p class="text-center m-3 fs-4">Nimal Rajakaruna</p>
                                <!-- <button class="btn bg-theme-accent rounded-pill py-3 px-4 fs-6 my-4">See story in detail</button> -->
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev z-10" type="button" data-bs-target="#StoryCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#StoryCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
        </section>
        <!-- success story end-->

        </div>

        <div class="container-fluid bg-light-pink mb-5">

            <div class="container py-5" id="contributionToSDGs">
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <h4 class="text-crimson fw-semibold mb-3">Our Contribution to Sustainable Development Goals</h4>
                        <p>
                            Through our Climate Resilient and SMED projects, we contribute to achieving Sustainable
                            Development Goals by promoting climate action (SDG 13) through sustainable agriculture and
                            water
                            management, supporting industry, innovation, and infrastructure (SDG 9) by empowering small
                            enterprises and rural development, and fostering decent work and economic growth (SDG 8) by
                            creating livelihoods, strengthening community-based organizations, and encouraging inclusive
                            entrepreneurship.
                        </p>
                    </div>
                </div>

                <div class="row row-cols-auto g-3 justify-content-center mb-3">

                    <div class="col">
                        <img src="./assets/icons/SDG-01.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-02.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-05.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-06.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-12.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-13.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/E_SDG_Icons-15.jpg" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="./assets/icons/SDG-17.png" alt="" width="110px">
                    </div>

                </div>

                <!-- <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-primary rounded-pill mt-3">
                            Read in detail
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div> -->

            </div>

        </div>

        <!-- donors start -->
        <?php include "../includes/donors-list.php" ?>
        <!-- donors end -->

    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>
    <script>
        // const pastProjectsBtn = document.getElementById('pastProjectsBtn');
        // const ongoingProjectsBtn = document.getElementById('ongoingProjectsBtn');
        // pastProjectsBtn.addEventListener('click', () => {
        //     window.location.href = '/past-projects';
        // });
        // ongoingProjectsBtn.addEventListener('click', () => {
        //     window.location.href = '/ongoing-projects';
        // });
    </script>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>

</body>

</html>