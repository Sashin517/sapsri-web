<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPSRI</title>
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- font awesome v7 -->
    <script src="https://kit.fontawesome.com/6e09983e4e.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- loading elements -->
    <!-- <div id="loader-overlay">
        <div class="three-body">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
        </div>
    </div> -->
    <!-- loading elements end -->

    <?php include "includes/header.php"; ?>


    <main>
        <!-- content start -->

        <div class="container-fluid home__hero px-0">

            <!-- carousel start -->
            <div id="carousel" class="carousel slide" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carousel" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <!-- <button type="button" data-bs-target="#carousel" data-bs-slide-to="3" aria-label="Slide 4"></button> -->
                </div>

                <div class="carousel-inner">

                    <div class="carousel-item responsive-hero-height active align-items-center">
                        <img src="assets/media/img/carousel/main-banner-image-biodiversity.webp"
                            class="object-fit-cover w-100 h-100"
                            alt="">

                        <div class="carousel-caption">
                            <!-- <h5>Harvesting Hope</h5> -->
                            <h2>Safeguarding Sri Lanka's unique biodiversity</h2>
                            <!-- <p>Empowering women through sustainable agriculture in Sri Lanka's hill country.</p> -->
                        </div>
                    </div>

                    <div class="carousel-item responsive-hero-height align-items-center">
                        <img src="assets/media/img/carousel/main-banner-image-food-systems.webp"
                            class="object-fit-cover w-100 h-100"
                            alt="">

                        <div class="carousel-caption">
                            <!-- <h2>Crafting Livelihoods</h2> -->
                            <h2>Building climate resilient food systems</h2>
                            <!-- <p>Supporting rural artisans and traditional crafts for economic resilience.</p> -->
                        </div>
                    </div>

                    <div class="carousel-item responsive-hero-height align-items-center">
                        <img src="assets/media/img/carousel/main-banner-image-financial-inclusion.webp"
                            class="object-fit-cover w-100 h-100"
                            alt="">

                        <div class="carousel-caption">
                            <!-- <h2>Future by the Shore</h2> -->
                            <h2>Enhancing financial inclusion</h2>
                            <!-- <p>Building brighter futures for children in coastal communities.</p> -->
                        </div>
                    </div>

                    <!-- <div class="carousel-item responsive-hero-height align-items-center">
                        <img src="assets/media/img/carousel/Main Banner_food system ii.jpg"
                            class="object-fit-cover w-100 h-100"
                            alt="Two young boys from a coastal village sitting on a fishing net, smiling and relaxed.">

                        <div class="carousel-caption">
                            <h2>Enhancing financial inclusion</h2>
                            <p>Building brighter futures for children in coastal communities.</p>
                        </div>
                    </div> -->

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

            </div>
            <!-- carousel end -->

        </div>


        <div class="container-fluid hero-holder py-5">
            
            <div class="container">
                    <h3 class="fw-semibold text-secondary lead mb-0">Empowering Communities, Enabling Change</h3>
                    <p class="fs-6 text-muted mt-6 mb-0">Working Together for a Just and Inclusive Sri Lanka</p>
            </div>

        </div>

        <!-- impact-areas -->
        <div class="container-fluid bg-topography-light-gray-blue py-5">
            <div class="container">
                <h3 class="text-center text-crimson mb-4 position-relative">Impact Areas</h3>

                <div class="row row-cols-2 row-cols-lg-4 g-3 g-lg-4 impact-areas mb-4">

                    <div class="col">
                        <div class="card border-0 rounded-4 h-100">
                            <img src="assets/media/img/impact-areas/impact-area-card-img-climate-and-biodiversity.webp" 
                                class="card-img impact-img-fit rounded-4" 
                                alt="Climate Biodiversity">
                            <div class="card-img-overlay z-1 d-flex align-items-center justify-content-center">
                                <p class="text-white text-center fw-semibold m-0 fs-5">Climate & Biodiversity</p>
                            </div>
                            <a href="climate-and-biodiversity" class="stretched-link"></a>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-0 rounded-4 h-100">
                            <img src="assets/media/img/impact-areas/impact-area-card-img-sustainable-agriculture.webp" 
                                class="card-img impact-img-fit rounded-4" 
                                alt="Sustainable Agriculture">
                            <div class="card-img-overlay z-1 d-flex align-items-center justify-content-center">
                                <p class="text-white text-center fw-semibold m-0 fs-5">Sustainable Agriculture</p>
                            </div>
                            <a href="sustainable-agriculture" class="stretched-link"></a>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-0 rounded-4 h-100">
                            <img src="assets/media/img/impact-areas/finance-and-governance.jpg" 
                                class="card-img impact-img-fit rounded-4" 
                                alt="Finance & Governance">
                            <div class="card-img-overlay z-1 d-flex align-items-center justify-content-center">
                                <p class="text-white text-center fw-semibold m-0 fs-5">Finance & Governance</p>
                            </div>
                            <a href="finance-and-governance" class="stretched-link"></a>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card border-0 rounded-4 h-100">
                            <img src="assets/media/img/impact-areas/impact-area-card-img-gender-inclusion.webp" 
                                class="card-img impact-img-fit rounded-4" 
                                alt="Gender Inclusion">
                            <div class="card-img-overlay z-1 d-flex align-items-center justify-content-center">
                                <p class="text-white text-center fw-semibold m-0 fs-5">Gender Inclusion</p>
                            </div>
                            <a href="gender-inclusion" class="stretched-link"></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- impact-areas -->


        <div class="container mt-5">

            <div class="row mb-5 align-items-center">

                <div class="col-12 text-center mb-3">
                    <h3 class="fw-semibold h3-underline">Featured Projects</h3>
                </div>

                <div class="col-12 col-lg-6 pe-lg-5 mb-3 mb-lg-0 text-center">
                    <div class="ratio ratio-4x3">
                        <img src="assets/media/img/ongoing-projects/smed11.jpeg" alt="" class="img-fluid object-fit-cover">
                    </div>
                </div>

                <div class="col-12 col-lg-6 ps-lg-5 text-center text-lg-start">
                    <h3 class="text-crimson fw-semibold mb-3">SMED</h3>
                    <button class="btn btn-pill btn-light-yellow mb-3">Governance & Finance</button>
                    <button class="btn btn-pill btn-light-yellow mb-3">Gender Inclusion</button>
                    <p>
                        SAPRSI is a community-focused NGO dedicated to promoting sustainable development and inclusive
                        growth across Sri Lanka. Through our work in climate resilience,
                        <span class="text-crimson fw-semibold">
                            small and medium enterprise development
                        </span>
                        (SMED), and rural empowerment, we aim to uplift vulnerable communities
                        while protecting natural resources. We partner with grassroots organizations, local governments,
                        and development agencies to implement impactful, data-driven projects that create lasting
                        change. Our mission is to build climate-smart, economically independent communities equipped for
                        a sustainable future. With a strong foundation in transparency and local engagement, SAPRSI
                        continues to lead transformative initiatives in both environmental and social development.
                    </p>

                    <a href="smed" class="btn btn-pill btn-primary">Discover more</a>
                </div>

            </div>

            <div class="row row-cols-1 row-cols-lg-3 mb-5">

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/two-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">1600+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Beneficiaries</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/three-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">60+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Community Based Organizations</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/stock-increase.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">388+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">New Loans in 2025</h4>
                    </div>
                </div>

            </div>

            <div class="row mb-5 align-items-center">

                <div class="col-12 col-lg-6 pe-lg-5 mb-3 mb-lg-0 text-center order-0 order-lg-1">
                    <div class="ratio ratio-4x3">
                        <img src="assets/media/img/homepage/home_criwmp.jpg" alt="" class="img-fluid object-fit-cover">
                    </div>
                </div>

                <div class="col-12 col-lg-6 pe-lg-5 text-center text-lg-end">
                    <h3 class="text-crimson fw-semibold mb-3">CRIWMP</h3>
                    <button class="btn btn-pill btn-light-yellow mb-3">Climate & Biodiversity</button>
                    <button class="btn btn-pill btn-light-yellow mb-3">Sustainable Agriculture </button>
                    <p>
                        <span class="text-crimson fw-semibold">
                            Climate Resilient Integrated Water Management
                            Project
                        </span>
                        (CRIWMP) is dedicated to strengthening Sri
                        Lank's resilience to climate change through sustainable irrigation and watershed management. By
                        introducing climate-smart farming practices, restoring ecosystems, and improving water resource
                        governance, we help communities adapt to changing weather patterns while safeguarding natural
                        resources. Working closely with local stakeholders, grassroots organizations, and development
                        agencies, CRIWMP builds long-term water security and supports livelihoods, ensuring a more
                        sustainable and climate-resilient future for all.
                    </p>

                    <a href="criwmp" class="btn btn-pill btn-primary">Discover more</a>

                </div>

            </div>

            <div class="row row-cols-1 row-cols-lg-3 mb-5">

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/two-people.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">90500+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Direct & Indirect Beneficiaries</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/streamline-ultimate_data-lake-1-bold.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">46+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">No of Tanks Rehabilitated</h4>
                    </div>
                </div>

                <div class="col d-flex text-center py-5">
                    <div class="flex-fill">
                        <img src="assets/icons/tree.svg" alt="" class="img-fluid mb-3" style="height: 100px;">
                        <h3 class="fw-semibold text-crimson">8+</h3>
                        <h4 class="fw-semibold text-dark-emphasis">Schemes Completed</h4>
                    </div>
                </div>

            </div>

        </div>

        <div class="container-fluid bg-topography-light-gray-blue my-5">

            <div class="container py-5">

                <div class="row">

                    <div class="col-12 text-center">
                        <h3 class="fw-semibold h3-underline">Latest News</h3>
                    </div>

                    <div class="col-12">

                        <div class="swiper">

                            <div class="swiper-wrapper d-flex justify-content-center" id="latestNews">

                                
                            </div>

                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>

                        </div>

                        <div class="swiper-pagination"></div>

                    </div>

                    <div class="col-12 text-center mt-3">
                        <button id="ltsnews-btn" class="btn btn-pill btn-primary">See All</button>
                    </div>

                </div>

            </div>

        </div>

        <!-- <div class="container-fluid bg-light-pink">

            <div class="container py-5">
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
                        <img src="assets/icons/SDG-01.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-02.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-04.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-05.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-06.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-08.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-11.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-12.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-13.png" alt="" width="110px">
                    </div>

                    <div class="col">
                        <img src="assets/icons/SDG-17.png" alt="" width="110px">
                    </div>

                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-pill btn-primary mt-3">
                            Read in detail
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div> -->

        <div class="container my-5 py-5">

            <div class="row align-items-center">

                <div class="col-12 col-lg-6 pe-lg-5 mb-3 mb-lg-0 text-center">
                    <div class="ratio ratio-4x3">
                        <img src="assets/media/img/page-hero/about-us-hero.jpg" alt="" class="img-fluid object-fit-cover">
                    </div>
                </div>

                <div class="col-12 col-lg-6 pe-lg-5 text-center text-lg-start">

                    <h3 class="text-crimson fw-semibold mb-4">About Us</h3>
                    <p>
                        <span class="fw-semibold text-crimson">South Asia Partnership - Sri Lanka</span> (SAPSRI) is a
                        non-profit development-oriented organisation which focuses on uplifting the lives of vulnerable
                        communities in Sri Lanka. SAPSRI is committed to promoting a holistic approach to development
                        through the creation of a just and equitable society by building the capacities of citizens and
                        civil society organisations to be self-reliant and active partners in development.
                    </p>

                    <a href="about-us" class="btn btn-pill btn-primary">Discover more</a>

                </div>

            </div>

        </div>

        <!-- donors start -->
        <?php include "includes/donors-list.php" ?>
        <!-- donors end -->

        <!-- content end -->
    </main>

    <?php include "includes/footer.php"; ?>

    <script>
            const ltsnewsBtn = document.getElementById('ltsnews-btn');
            const loaderOverlay = document.getElementById('loader-overlay');
            ltsnewsBtn.addEventListener('click', () => {
                window.location.href = 'news';
            });
            async function getPostsData() {
                const url = 'includes/post-data.php';
    
                try {
                    const response = await fetch(url);
    
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
    
                    const data = await response.json();
                    allPosts = Object.values(data).reverse();
    
                    renderPosts(allPosts);
    
                } catch (error) {
                    console.error('Failed to fetch posts:', error);
                    document.getElementById('latestNews').innerHTML = `
                        <div class="col-12 text-center w-100">
                            <p class="text-danger">Failed to load posts. Please try again later.</p>
                        </div>
                    `;
                    return null;
                }
            }
            function renderPosts(posts) {
                //const latestNews = document.getElementById('latestNews');
                latestNews.innerHTML = '';
    
                if (posts.length === 0) {
                    latestNews.innerHTML = `
                        <div class="col-12 text-center py-5 w-100">
                            <i class="fa-solid fa-magnifying-glass mb-3" style="font-size: 48px; color: #ccc;"></i>
                            <h5>No posts found</h5>
                            <p class="text-muted">No posts found</p>
                        </div>
                    `;
                    document.querySelector('.news-pagination').innerHTML = '';
                    return;
                }
    
                posts.forEach(post => {
                    let imgUrl = './assets/media/img/thumbnails/default.webp';

                    // Check if the cover_image property exists directly on the post object
                    if (post.cover_image && post.cover_image.trim() !== '') {
                        imgUrl = post.cover_image;
                    } 
                    // Fallback: check the media array just in case it's a different post type
                    else if (post.post_media && post.post_media.length > 0) {
                        const cardImage = post.post_media.find(m => m.type === 'card_image');
                        if (cardImage) imgUrl = cardImage.url;
                    }
                    const dateObj = new Date(post.published_date);
                    const formattedDate = dateObj.toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "long",
                        day: "numeric"
                    });
    
                    let content = post.content || '';
                    if (content.length > 120) {
                        content = content.substring(0, 120) + '...';
                    }
    
                    const cardHtml =
                        `
                        <div class="swiper-slide">
                            <a href="post?id=${post.id}&len_posts=${posts.length}" class="card-link text-decoration-none text-reset h-100">
                                <div class="card news-card rounded-4 h-100">
                                    
                                    <img src="${imgUrl}" 
                                        class="card-img-top rounded-top-4" 
                                        alt="${post.title}" 
                                        style="object-fit: cover; height: 200px;"> 

                                    <div class="card-body">
                                        <h5 class="card-title">
                                            ${post.title}
                                        </h5>
                                        <p class="card-text">
                                            ${content}
                                        </p>
                                    </div>

                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Posted on ${formattedDate || ''}</small>
                                        
                                        <span class="btn btn-sm btn-dark rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                    latestNews.insertAdjacentHTML('beforeend', cardHtml);
                });

                // loaderOverlay.classList.add('hidden');
            }
            getPostsData();
        
    </script>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./vendor/swiper/swiper-bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>

</html>