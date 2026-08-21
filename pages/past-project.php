<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - SAPSRI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Global and specific style sheets -->
    <link rel="stylesheet" href="./assets/css/current_project.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/project-style.css">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/3e6ef2b5ef.js" crossorigin="anonymous"></script>
    <!-- GLightbox CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
</head>

<body>

    <!-- header -->
    <?php include "../includes/header.php"; ?>

    <main style="margin-top: 56px; min-height: 70vh;">
        
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
            <section id="gallery-wrapper" class="relevant-photos mt-5 pt-4 d-none">
                <h3 class="my-4 fw-semibold fs-4 text-center text-md-start text-crimson">Relevant Photos</h3>
                <div class="row g-4" id="gallery-container">
                    <!-- Images injected here -->
                </div>
            </section>

            <nav class="d-flex justify-content-center justify-content-lg-end my-5 gap-4" aria-label="Blog post navigation">
                <a href="past-projects" class="btn btn-post-nav rounded-pill"><i class="bi bi-arrow-left me-2"></i>Back to Projects</a>
            </nav>

        </article>

    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>

    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>

    <!-- Core Dynamic Data Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            
            // Helper to fix image paths from the DB relative to frontend root
            const resolvePath = (path) => {
                if (!path || path.trim() === '') return './assets/media/img/thumbnails/default.webp';
                return path.replace(/^\.\.\//, './');
            };

            // Get Project ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            const projectId = urlParams.get('id');

            if (!projectId) {
                document.getElementById('loading-container').innerHTML = `<h2 class="text-danger">Error: No project selected.</h2><a href="past-projects" class="btn btn-dark mt-3">Go Back</a>`;
                return;
            }

            try {
                // Fetch data from endpoint
                const response = await fetch('./includes/projects-data.php');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const projects = await response.json();
                
                // Find matching project
                const project = projects.find(p => p.id == projectId);

                if (!project) {
                    document.getElementById('loading-container').innerHTML = `<h2 class="text-danger">Error: Project not found.</h2><a href="past-projects" class="btn btn-dark mt-3">Go Back</a>`;
                    return;
                }

                // =====================================
                // 1. POPULATE HEADER & BASIC INFO
                // =====================================
                document.title = `${project.title} - SAPSRI`;
                document.getElementById('proj-title').innerText = project.title;
                document.getElementById('proj-cover').src = resolvePath(project.cover_image);
                
                // Keep the exact structural spacing for description from the template
                document.getElementById('proj-description').innerHTML = project.description || '<p class="text-muted text-center">No description available.</p>';

                // Tags / Impact Areas
                if (project.impact_areas && project.impact_areas.length > 0) {
                    const tagsHtml = project.impact_areas.map(area => 
                        `<span class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap fw-medium shadow-sm fs-6">${area.name}</span>`
                    ).join('');
                    document.getElementById('proj-tags').innerHTML = tagsHtml;
                }

                // =====================================
                // 2. METRICS SECTION 1
                // =====================================
                const sec1Metrics = project.metrics.filter(m => m.section == '1');
                if (sec1Metrics.length > 0) {
                    document.getElementById('metrics-sec-1-wrapper').classList.remove('d-none');
                    document.getElementById('metrics-sec-1-wrapper').classList.add('d-flex');
                    
                    // Set Section Image (fallback to first available if needed)
                    const sec1Img = sec1Metrics.find(m => m.section_image && m.section_image !== '')?.section_image;
                    if (sec1Img) {
                        document.getElementById('sec1-img').src = resolvePath(sec1Img);
                    } else {
                        document.getElementById('sec1-img').parentElement.classList.add('d-none'); 
                    }

                    // Render Items
                    const sec1Container = document.getElementById('sec1-items');
                    sec1Metrics.forEach(m => {
                        if(!m.label && !m.value) return; // Skip empty rows
                        
                        let iconHtml = '';
                        if (m.icon_image && m.icon_image.trim() !== '') {
                            const iconSrc = resolvePath(m.icon_image);
                            iconHtml = `
                                <div class="d-flex justify-content-center align-items-center bg-dark rounded-circle flex-shrink-0 metric-icon-wrapper shadow-sm">
                                    <img src="${iconSrc}" alt="" class="img-fluid" style="width: 44px; height: 44px;">
                                </div>
                            `;
                        }

                        sec1Container.insertAdjacentHTML('beforeend', `
                            <div class="d-flex align-items-center gap-3 py-3 w-100">
                                ${iconHtml}
                                <div>
                                    <h3 class="fw-semibold text-crimson">${m.value}</h3>
                                    <h4 class="fw-semibold text-dark-emphasis">${m.label}</h4>
                                </div>
                            </div>
                        `);
                    });
                }

                // =====================================
                // 3. SUCCESS STORIES CAROUSEL
                // =====================================
                if (project.success_stories && project.success_stories.length > 0) {
                    document.getElementById('success-stories-wrapper').classList.remove('d-none');
                    
                    const indicators = document.getElementById('story-indicators');
                    const inner = document.getElementById('story-inner');

                    project.success_stories.forEach((story, index) => {
                        const activeClass = index === 0 ? 'active' : '';
                        
                        indicators.insertAdjacentHTML('beforeend', `
                            <button type="button" data-bs-target="#StoryCarousel" data-bs-slide-to="${index}" class="${activeClass}" aria-current="${activeClass === 'active' ? 'true' : 'false'}" aria-label="Slide ${index + 1}"></button>
                        `);

                        const storyImg = resolvePath(story.image);
                        inner.insertAdjacentHTML('beforeend', `
                            <div class="carousel-item ${activeClass}">
                                <div class="story-card d-flex flex-column flex-md-row justify-content-center rounded-4 overflow-hidden mb-5 border border-light-subtle shadow-sm">
                                    <div class="story-image overflow-hidden align-items-center">
                                        <img src="${storyImg}" alt="${story.name}" class="object-fit-cover w-100 h-100">
                                    </div>
                                    <div class="story-text story-text-container d-flex flex-column align-items-center justify-content-center p-5 h-100">
                                        <div class="w-100">
                                            <img src="./assets/icons/Vector.svg" alt="Quote start" class="img-fluid mb-2 opacity-75" style="max-width:32px;">
                                            <p class="text-center m-3 fw-bold fs-5">${story.description}</p>
                                            <span class="d-flex justify-content-end">
                                                <img src="./assets/icons/Vector (1).svg" alt="Quote end" class="img-fluid mt-2 opacity-75" style="max-width:32px;">
                                            </span>
                                        </div>
                                        <p class="text-center m-3 fs-4 text-crimson fw-semibold">${story.name}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                // =====================================
                // 4. METRICS SECTION 2
                // =====================================
                const sec2Metrics = project.metrics.filter(m => m.section == '2');
                if (sec2Metrics.length > 0) {
                    document.getElementById('metrics-sec-2-wrapper').classList.remove('d-none');
                    document.getElementById('metrics-sec-2-wrapper').classList.add('d-flex');
                    
                    const sec2Img = sec2Metrics.find(m => m.section_image && m.section_image !== '')?.section_image;
                    if (sec2Img) {
                        document.getElementById('sec2-img').src = resolvePath(sec2Img);
                    } else {
                        document.getElementById('sec2-img').parentElement.classList.add('d-none'); 
                    }

                    const sec2Container = document.getElementById('sec2-items');
                    sec2Metrics.forEach(m => {
                        if(!m.label && !m.value) return; 
                        
                        let iconHtml = '';
                        if (m.icon_image && m.icon_image.trim() !== '') {
                            const iconSrc = resolvePath(m.icon_image);
                            iconHtml = `
                                <div class="d-flex justify-content-center align-items-center bg-dark rounded-circle flex-shrink-0 metric-icon-wrapper shadow-sm">
                                    <img src="${iconSrc}" alt="" class="img-fluid" style="width: 44px; height: 44px; filter: invert(1);">
                                </div>
                            `;
                        }

                        sec2Container.insertAdjacentHTML('beforeend', `
                            <div class="d-flex align-items-center gap-3 py-3 w-100 flex-md-row-reverse flex-row">
                                ${iconHtml}
                                <div class="text-start text-md-end">
                                    <h3 class="fw-semibold text-crimson">${m.label}</h3>
                                    <h4 class="fw-semibold text-dark-emphasis">${m.value}</h4>
                                </div>
                            </div>
                        `);
                    });
                }

                // =====================================
                // 5. PROJECT LEADS
                // =====================================
                if (project.leads && project.leads.length > 0) {
                    document.getElementById('leads-wrapper').classList.remove('d-none');
                    const leadsContainer = document.getElementById('leads-container');
                    
                    project.leads.forEach(lead => {
                        const avatar = resolvePath(lead.photo);
                        const linkHtml = lead.linkedin ? `<a href="${lead.linkedin}" target="_blank" class="social-link ms-3 fs-4 text-primary"><i class="bi bi-linkedin"></i></a>` : '';
                        
                        leadsContainer.insertAdjacentHTML('beforeend', `
                            <div class="col-lg-6">
                                <div class="card profile-card leads-card h-100">
                                    <div class="d-flex align-items-center">
                                        <img src="${avatar}" class="card-img-top rounded-circle me-3 border border-dark-subtle" alt="${lead.name}">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">${lead.name}</h5>
                                            <p class="card-text">${lead.role}</p>
                                        </div>
                                        ${linkHtml}
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                // =====================================
                // 6. MEDIA GALLERY
                // =====================================
                if (project.media && project.media.length > 0) {
                    document.getElementById('gallery-wrapper').classList.remove('d-none');
                    const galleryContainer = document.getElementById('gallery-container');
                    
                    project.media.forEach(media => {
                        const isVideo = media.type === 'video';
                        const thumbUrl = resolvePath(media.thumbnail_url || media.url);
                        const targetUrl = resolvePath(media.url);
                        
                        const overlayHtml = isVideo ? `<div class="position-absolute top-50 start-50 translate-middle text-white fs-1 bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; z-index: 5;"><i class="bi bi-play-fill"></i></div>` : '';

                        galleryContainer.insertAdjacentHTML('beforeend', `
                            <div class="col-md-4 rel-img">
                                <a href="${targetUrl}" class="glightbox d-block position-relative h-100 w-100" data-gallery="project-gallery">
                                    <img src="${thumbUrl}" class="img-fluid rounded shadow-sm gallery-img object-fit-cover h-100 w-100 hover-zoom" alt="Gallery item" style="opacity: ${isVideo ? '0.85' : '1'};">
                                    ${overlayHtml}
                                </a>
                            </div>
                        `);
                    });

                    // Initialize GLightbox
                    GLightbox({
                        selector: '.glightbox',
                        touchNavigation: true,
                        loop: true,
                        autoplayVideos: true
                    });
                }

                // Hide Loader, Show Content
                document.getElementById('loading-container').classList.add('d-none');
                document.getElementById('dynamic-project-container').classList.remove('d-none');

            } catch (error) {
                console.error("Error loading project:", error);
                document.getElementById('loading-container').innerHTML = `<h2 class="text-danger">Failed to load project data.</h2><p class="text-muted">${error.message}</p><a href="past-projects" class="btn btn-dark mt-3">Go Back</a>`;
            }
        });

        // Stop video playing when modal is closed
        document.getElementById('imageModal')?.addEventListener('hidden.bs.modal', function () {
            const video = this.querySelector('video');
            if (video) video.pause();
        });
    </script>
</body>
</html>