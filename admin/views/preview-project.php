<style>
    :root {
        /* from current-project.css */
        --brand-crimson: #A20A35;
        --brand-orange: #f39c12;
        --brand-dark: #343a40;
        --card-bg-light-rose: #F1EBEE;
        --card-bg-light-gray: #f8f9fa;

        /* from project-style.css */
        --brand-dark-blue: #0c1f3e;
        --brand-maroon: #9d2449;
        --text-light-gray: #6c757d;
    }

    /* from style.css */
    .text-crimson {
        color: var(--brand-crimson);
    }


    /* from current-project.css */

    /* success story text truncate */
    .text-truncate-3 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        line-clamp: 3;
    }

    .text-truncate-6 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 6;
        line-clamp: 6;
    }

    /* what's this for? */
    .main iframe {
        filter: url(#blur-and-saturate);
        /* Apply the SVG filter */
        border-radius: 10px;
        width: 50vw;
        height: 28.125vw;
    }

    .tx-theme-prime {
        color: var(--brand-crimson);
    }

    .bg-theme-accent {
        background-color: #F0A02F !important;
    }

    .bg-gold-yellow {
        background-color: #F1BA0D !important;
    }

    .bg-fade-gold {
        background-color: #F4F1B9 !important;
    }

    .bg-theme-saphere {
        background-color: #EFE629 !important;
    }

    /* Custom styles for the carousel indicators */
    .carousel-indicators [data-bs-target] {
        background-color: #c0c0c0;
        /* Color of inactive dots */
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 0 5px;
    }

    .carousel-indicators .active {
        background-color: #212529;
        /* Color of the active dot */
    }

    .captured-photos div {
        width: 50% !important;
    }

    /* --- Profile Card Styles --- */
    .profile-card {
        border: none;
        border-radius: 15px;
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .profile-card .card-img-top {
        width: 90px;
        height: 90px;
        object-fit: cover;
    }

    .profile-card .card-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .profile-card .card-deg {
        font-weight: 100;
        margin-bottom: 0.25rem;
    }

    .profile-card .card-text {
        /* font-weight: 500; */
        color: #A20A35;
        font-size: 0.9rem;
    }

    .profile-card .social-link {
        font-size: 1.2rem;
        color: #0e76a8;
        /* LinkedIn blue */
    }

    .leads-card {
        background-color: var(--card-bg-light-rose);
    }

    .rel-img {
        height: 20rem;
    }

    /* Make gallery images look clickable */
    .gallery-img {
        cursor: pointer;
        transition: opacity 0.2s ease-in-out;
    }

    .gallery-img:hover {
        opacity: 0.8;
    }

    /* Style the modal's close button */
    .modal .btn-close {
        position: absolute;
        top: 0;
        /* Align to the top of the image */
        right: -45px;
        /* Align to the right of the image */

        /* This is the key: */
        /* Move the button UP by its own full height */
        transform: translateY(-100%);

        /* Optional: Add a small gap so it's not touching */
        margin-top: 0px;

        z-index: 10;

        /* Your styles for visibility */
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        filter: invert(1) grayscale(100%);
        padding: 0.5rem;
    }

    /* Ensure the modal image is centered and doesn't exceed screen size */
    #modalImage {
        width: auto;
        max-width: 90vw;
        height: auto;
        max-height: 90vh;
        display: block;
        margin: auto;
    }

    .story-card {
        height: 78vh;
    }

    .story-image {
        height: 100%;
    }

    .story-image,
    .story-text {
        width: 50%;
    }


    /* from project-style.css */
    .post-meta {
        color: var(--text-light-gray);
        font-size: 0.9rem;
    }

    .post-content h1 {
        font-weight: 700;
    }

    .post-content h2 {
        font-size: 1.75rem;
        color: #343a40;
    }

    .section-title {
        color: var(--brand-dark-blue);
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    /* --- Buttons --- */
    .btn-show-more,
    .btn-post-nav,
    .btn-prev-post-nav {
        background-color: var(--brand-maroon);
        border-color: var(--brand-maroon);
        color: #fff;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: background-color 0.3s;
    }

    .btn-show-more:hover,
    .btn-post-nav:hover,
    .btn-prev-post-nav:hover {
        background-color: #831e3c;
        border-color: #831e3c;
        color: #fff;
    }

    .btn-prev-post-nav {
        background-color: var(--text-light-gray);
        border-color: var(--text-light-gray);
    }

    .btn-prev-post-nav:hover {
        background-color: #000;
        border-color: #000;
    }

    /* --- Dynamic Render Utility Classes --- */
    .metric-icon-wrapper {
        padding: 2rem;
        width: 108px;
        height: 108px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .story-text-container {
        background: #EEEFF0;
    }

    .hover-zoom {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .hover-zoom:hover {
        transform: scale(1.08);
        opacity: 1 !important;
    }

    /* from create-project.css */
    .section-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: #fff;
    }


    /* from current-project.css */
    @media (max-width: 768.98px) {
        .text-truncate-6 {
            -webkit-line-clamp: 12;
            line-clamp: 12;
        }

        .modal .btn-close {
            right: 0px;
            /* Adjust position for smaller screens */
        }

        .captured-photos div {
            width: 94% !important;
        }

        .story-card {
            height: auto;
        }

        .story-image {
            height: 52vh;
        }

        .story-image,
        .story-text {
            width: 100%;
        }

    }
</style>

<!-- Top Header / Breadcrumb -->
<div class="d-flex align-items-center justify-content-between mb-4 px-4 pt-4">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('create-project', 'Projects', {restoreView: true})">
            <i data-lucide="arrow-left" style="width: 20px;"></i>
        </button>
        <span class="text-muted" style="font-size: 0.95rem;">Projects > </span>
        <span class="text-muted" style="font-size: 0.95rem;">Create New Project > </span>
        <span class="fw-bold fs-4">Preview Project</span>
    </div>
    <button class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('create-project', 'Projects', {restoreView: true})">
        <i data-lucide="x" style="width: 20px;"></i>
    </button>
</div>

<div class="px-4 pb-5">

    <div class="section-card">

        <div style="margin-top: 56px; min-height: 70vh;" class="main">

            <!-- Loading State Container -->
            <div id="loading-container" class="container py-5 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 50vh;">
                <span class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status"></span>
                <p class="mt-3 text-muted">Loading project details...</p>
            </div>

            <!-- Dynamic Content Container -->
            <article class="post-content container py-5 d-none" id="dynamic-project-container">

                <!-- 1. Header & Cover -->
                <header class="text-center mb-5">
                    <h1 class="mb-3 fw-bold tx-theme-prime" id="proj-title">Project Title</h1>
                    <div id="proj-tags" class="d-flex justify-content-center gap-2 flex-wrap mt-3 mb-4"></div>
                </header>

                <figure class="mb-5 d-flex justify-content-center align-items-center rounded-3" style="width: 100%; max-height:585.33px; overflow: hidden;">
                    <img id="proj-cover" src="" class="rounded img-fluid w-100" alt="Cover Image" style="object-fit: cover; object-position: center;">
                </figure>

                <!-- 2. Main Description -->
                <div class="row justify-content-center">
                    <div class="col-lg-8" id="proj-description">
                        <!-- Rich text injected here -->
                    </div>
                </div>

                <!-- 3. Metrics Section 1 -->
                <div id="metrics-sec-1-wrapper" class="d-none flex-column flex-md-row justify-content-center overflow-hidden my-5">
                    <div class="w-100 d-none d-md-block">
                        <img id="sec1-img" src="" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 0 50rem 50rem 0; min-height: 300px;">
                    </div>
                    <div id="sec1-items" class="d-flex flex-column align-items-start ps-0 ps-md-5 w-100 justify-content-between gap-5 py-4">
                        <!-- Metrics injected here -->
                    </div>
                </div>

                <!-- 4. Success Stories Carousel -->
                <section id="success-stories-wrapper" class="d-none container" style="margin-bottom: 64px;">
                    <h2 class="my-4 text-center fw-semibold">Success Stories</h2>

                    <div id="StoryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators" id="story-indicators" style="bottom: -52px;"></div>
                        <div class="carousel-inner" id="story-inner"></div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#StoryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(100%);"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#StoryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(100%);"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </section>

                <!-- 5. Metrics Section 2 -->
                <div id="metrics-sec-2-wrapper" class="d-none flex-column flex-md-row-reverse justify-content-center overflow-hidden my-5">
                    <div class="w-100 d-none d-md-block">
                        <img id="sec2-img" src="" alt="" class="object-fit-cover w-100 h-100" style="border-radius: 50rem 0 0 50rem; min-height: 300px;">
                    </div>
                    <div id="sec2-items" class="d-flex flex-column align-items-md-end align-items-start pe-0 pe-md-5 w-100 justify-content-between gap-5 py-4">
                        <!-- Metrics injected here -->
                    </div>
                </div>

                <!-- 6. Project Leads -->
                <section id="leads-wrapper" class="d-none">
                    <h2 class="my-4 text-center fw-semibold text-crimson">Project Leads</h2>
                    <div class="row justify-content-center g-4" id="leads-container">
                        <!-- Leads injected here -->
                    </div>
                </section>

                <!-- 7. Media Gallery -->
                <section id="gallery-wrapper" class="relevant-photos my-5 pt-4 d-none">
                    <h3 class="my-4 fw-semibold fs-4 text-center text-md-start text-crimson">Relevant Photos</h3>
                    <div class="row g-4" id="gallery-container">
                        <!-- Images injected here -->
                    </div>
                </section>

            </article>

        </div>

    </div>
</div>