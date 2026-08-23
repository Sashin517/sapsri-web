<?php
include_once '../includes/connection.php';
Database::setUpConnection();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$seo_title = "News & Updates - SAPSRI";
$seo_desc = "Stay updated with the latest news, events, and community development initiatives from South Asia Partnership Sri Lanka.";
$seo_image = "https://sapsri.lk/project-sedna/assets/media/img/page-hero/news-and-events.jpg";

if ($post_id > 0) {
    $result = Database::search("SELECT title, content, cover_image FROM posts WHERE id = " . $post_id);
    if ($result && mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_assoc($result);
        
        if (!empty($post['title'])) {
            $seo_title = htmlspecialchars($post['title']) . " - SAPSRI";
        }
        if (!empty($post['content'])) {
            $stripped_desc = trim(preg_replace('/\s+/', ' ', strip_tags($post['content'])));
            $seo_desc = htmlspecialchars(mb_substr($stripped_desc, 0, 155)) . '...';
        }
        if (!empty($post['cover_image'])) {
            $clean_img = ltrim($post['cover_image'], './');
            $clean_img = ltrim($clean_img, '/');
            $seo_image = "https://sapsri.lk/project-sedna/" . $clean_img;
        }
    }
}
$current_url = "https://sapsri.lk" . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $seo_title ?></title>
    <meta name="description" content="<?= $seo_desc ?>">

    <link rel="canonical" href="<?= $current_url ?>">
    <meta property="og:url" content="<?= $current_url ?>">
    <meta property="og:title" content="<?= $seo_title ?>">
    <meta property="og:description" content="<?= $seo_desc ?>">
    <meta property="og:image" content="<?= $seo_image ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- defualt people style sheet -->
    <link rel="stylesheet" href="./assets/css/post_style.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/3e6ef2b5ef.js" crossorigin="anonymous"></script>

    <!-- Favicon & App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/project-sedna/assets/media/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/project-sedna/assets/media/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/project-sedna/assets/media/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/project-sedna/assets/media/img/favicons/site.webmanifest">
    <link rel="icon" href="/project-sedna/favicon.ico">


    <style>
        /* Define custom color variables */
        :root {
            --brand-dark-blue: #0c1f3e;
            --brand-maroon: #9d2449;
            --text-light-gray: #6c757d;
        }

        /* --- Blog Post Styles --- */
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

        /* --- Post Navigation Buttons --- */
        .btn-post-nav {
            background-color: var(--brand-maroon);
            border-color: var(--brand-maroon);
            color: #fff;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-post-nav:hover {
            background-color: #831e3c;
            border-color: #831e3c;
            color: #fff;
        }

        /* --- Post Navigation Buttons --- */
        .btn-prev-post-nav {
            background-color: var(--text-light-gray);
            border-color: var(--text-light-gray);
            color: #fff;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-prev-post-nav:hover {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
    </style>
    <!-- GLightbox CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
</head>

<body>
    <!-- loading elements -->
    <div id="loader-overlay">
        <div class="three-body">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
        </div>
    </div>
    <!-- loading elements end -->
     
    <!-- header -->
    <?php include "../includes/header.php"; ?>

    <main style="margin-top: 56px;">
        <article class="post-content container py-5">
            <header class="text-center mb-5">
                <h1 class="mb-3 fw-bold tx-theme-prime">Strengthening Rural Entrepreneurship by Adding Value to Traditional Crafts</h1>
                <h2 class="h4 text-muted fw-normal post-meta">Posted on February 10, 2025</h2>
            </header>

            <figure class=" mb-5 d-flex justify-content-center align-items-center rounded-3" style="width: 100%; max-height:585.33px; overflow: hidden;">
                <img src="./assets/media/img/past-projects/Community Participatory Action to build resilience in response to drought and human elephant.png" class="post-banner rounded img-fluid w-100" alt="Handcrafted woven bags and sandals made from natural fibers." style="object-fit: cover; object-position: center;">
            </figure>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <p>In the areas of Nawagattegama and Pallama in the Puttalam District, a transformative initiative has redefined rural entrepreneurship, paving the way for a sustainable future for craftswomen. The project "Strengthening Rural Entrepreneurship by Adding Value to Traditional Crafts," implemented by South Asia Partnership Sri Lanka (SAPSRI) with financial support from HSBC, has opened up new opportunities for unemployed rural women.</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi. Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue.</p>
                    <p>Reviving Heritage, Empowering Women: At the core of this initiative was the goal to equip unemployed rural women with the skills and resources necessary to craft beautiful products from natural fibers and talipot leaves—materials deeply rooted in Sri Lanka’s culture. Over two years, a spirited network of 150 craftswomen was established, empowering them to expand their market presence both locally and internationally. These women are not just promoting their heritage; they are transforming it into a sustainable livelihood, fostering a new wave of rural entrepreneurship.</p>
                </div>
            </div>

            <section class="relevant-photos mt-5 pt-4">
                <h3 class="my-4 fw-semibold" style="color: #A20A35;">Relevant Photos</h3>
                <div id="revImages" class="row g-4">
                    <!-- relevant images are placed here -->
                </div>
                <div class="text-center mt-4">
                    <button id="show-more-btn" class="btn btn-outline-secondary btn-show-more rounded-pill">Show more <i class="bi bi-chevron-down"></i></button>
                </div>
            </section>

            <nav class="d-flex justify-content-center justify-content-lg-end my-5 gap-4" aria-label="Blog post navigation">
                <a href="#" class="btn btn-prev-post-nav rounded-pill"><i class="bi bi-arrow-left"></i> Previous post</a>
                <a href="#" class="btn btn-post-nav rounded-pill">Next post <i class="bi bi-arrow-right"></i></a>
            </nav>

        </article>

    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>
    
    <script>
        // This JavaScript waits for the entire page (including images and other resources) to load.
        window.onload = async function() {
            const loaderOverlay = document.getElementById('loader-overlay');
            let allPosts = [];
            const urlParams = new URLSearchParams(window.location.search);
            const post_id = parseInt(urlParams.get("id")) || null;
            const len_posts = parseInt(urlParams.get("len_posts")) || null;
            
            console.log(post_id, len_posts);

            async function getPostsData() {
                // Pointing directly to the root includes folder
                const url = 'includes/post-data.php'; 
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    allPosts = Object.values(data);
                    const matchedPost = allPosts.find(post => post.id == post_id);
                    console.log("Matched Post:", matchedPost);
                    
                    if (matchedPost) {
                        rederContent(matchedPost);
                        setupNavigation();
                    } else {
                        throw new Error("Post not found");
                    }
                } catch (error) {
                    console.error('Failed to fetch posts:', error);
                    document.getElementById('revImages').innerHTML = `
                        <div class="col-12 text-center w-100">
                            <p class="text-danger">Failed to load post. Please try again later.</p>
                        </div>
                    `;
                    loaderOverlay.classList.add('hidden');
                    return null;
                }
            }

            function setupNavigation() {
                const prevBtn = document.querySelector('.btn-prev-post-nav');
                const nextBtn = document.querySelector('.btn-post-nav');
                
                // Calculate previous and next post IDs
                const prevPostId = post_id - 1;
                const nextPostId = post_id + 1;
                
                // Handle Previous Button
                if (post_id <= 1) {
                    // Disable if at first post
                    prevBtn.classList.add('disabled');
                    prevBtn.style.pointerEvents = 'none';
                    prevBtn.style.opacity = '0.5';
                    prevBtn.removeAttribute('href');
                } else {
                    // Enable and set link
                    prevBtn.href = `?id=${prevPostId}&len_posts=${len_posts}`;
                }
                
                // Handle Next Button
                if (post_id >= len_posts) {
                    // Disable if at last post
                    nextBtn.classList.add('disabled');
                    nextBtn.style.pointerEvents = 'none';
                    nextBtn.style.opacity = '0.5';
                    nextBtn.removeAttribute('href');
                } else {
                    // Enable and set link
                    nextBtn.href = `?id=${nextPostId}&len_posts=${len_posts}`;
                }
            }

            function rederContent(post) {
                const revImages = document.getElementById('revImages');
                const showMoreBtn = document.getElementById('show-more-btn');
                const sectionTitle = document.querySelector(".relevant-photos h3");
                let currentIndex = 0;
                const imagesPerLoad = 3;

                // 1. Include ALL gallery media (images and videos)
                const galleryMedia = post.post_media ? post.post_media.filter(m => m.type === "image" || m.type === "video") : [];
                
                // 2. Map the Cover Image directly from the JSON structure
                let imgUrl = './assets/media/img/thumbnails/default.webp';
                if (post.cover_image && post.cover_image.trim() !== '') {
                    imgUrl = post.cover_image;
                } else if (post.post_media && post.post_media.length > 0) {
                    const cardImage = post.post_media.find(m => m.type === 'card_image');
                    if (cardImage) imgUrl = cardImage.url;
                }
                
                // Set other post content
                document.querySelector('.post-content h1').innerText = post.title;
                
                // Convert SQL date
                const dateObj = new Date(post.published_date);
                const formattedDate = dateObj.toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
                
                // Map the author name directly from the JSON output
                document.querySelector('.post-meta').innerText = `Posted on ${formattedDate} by ${post.author_name}`;
                document.querySelector('.post-banner').src = imgUrl;
                document.querySelector('.post-content .row .col-lg-8').innerHTML = post.content;
                
                // If NO images — hide everything
                if (galleryMedia.length === 0) {
                    sectionTitle.style.display = "none";
                    showMoreBtn.style.display = "none";
                    revImages.innerHTML = "";
                    loaderOverlay.classList.add('hidden');
                    return;
                }
                
                // Render next set of media (3 at a time)
                function loadImages() {
                    const nextMedia = galleryMedia.slice(currentIndex, currentIndex + imagesPerLoad);
                    nextMedia.forEach(media => {
                        const isVideo = media.type === 'video';
                        // Handle DB paths
                        let thumbUrl = media.thumbnail_url ? media.thumbnail_url : media.url;
                        if (!thumbUrl.startsWith('.')) thumbUrl = './' + thumbUrl; // Ensure path correctness
                        let targetUrl = media.url;
                        if (!targetUrl.startsWith('.')) targetUrl = './' + targetUrl;
                        
                        const overlayHtml = isVideo ? `<div class="position-absolute top-50 start-50 translate-middle text-white fs-1 bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; z-index: 5;"><i class="bi bi-play-fill"></i></div>` : '';

                        const html = `
                            <div class="col-md-4 rel-img">
                                <a href="${targetUrl}" class="glightbox d-block position-relative h-100 w-100" data-gallery="post-gallery">
                                    <img src="${thumbUrl}" class="img-fluid rounded shadow-sm gallery-img object-fit-cover h-100 w-100 hover-zoom" style="opacity: ${isVideo ? '0.85' : '1'};">
                                    ${overlayHtml}
                                </a>
                            </div>
                        `;
                        revImages.insertAdjacentHTML("beforeend", html);
                    });
                    currentIndex += imagesPerLoad;
                    
                    // Re-initialize GLightbox for newly added elements
                    if (window.glightboxInstance) {
                        window.glightboxInstance.reload();
                    } else {
                        window.glightboxInstance = GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true, autoplayVideos: true });
                    }

                    // If all media loaded → hide button
                    if (currentIndex >= galleryMedia.length) {
                        showMoreBtn.style.display = "none";
                    }
                }
                
                // Load first 3 images automatically
                loadImages();
                
                // On button click → load next 3
                showMoreBtn.addEventListener("click", loadImages);
                
                // Hide loader after everything is set up
                loaderOverlay.classList.add('hidden');
            }
            
            await getPostsData();
        };
    </script>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/past_project.js"></script>
</body>

</html>