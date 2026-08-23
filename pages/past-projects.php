<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPSRI | Past Projects</title>
    <!-- Frontend assets use ./ because of .htaccess root routing -->
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/past-projects.css">
    <!-- font awesome v7 -->
    <script src="https://kit.fontawesome.com/6e09983e4e.js" crossorigin="anonymous"></script>
    <!-- Favicon & App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/project-sedna/assets/media/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/project-sedna/assets/media/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/project-sedna/assets/media/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/project-sedna/assets/media/img/favicons/site.webmanifest">
    <link rel="icon" href="/project-sedna/favicon.ico">
</head>

<body>

    <!-- header (PHP requires physical paths, so ../ is correct here) -->
    <?php include "../includes/header.php"; ?>

    <!-- content start -->
    <main>

        <!-- hero -->
        <section class="onGoingPro-hero mb-5">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center text-white justify-content-center d-flex flex-column align-items-center gap-2">
                        <h1 class="fw-semibold mb-4 display-2">Past Projects</h1>
                        <div style="width: 50%; max-width: 121.57px;  aspect-ratio: 1 / 1;" class="d-flex justify-content-center overflow-hidden align-items-center">
                            <img src="./assets/icons/pepicons-pop_rewind-time-circle-filled.svg" alt="Past Projects icon" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- hero -->

        <!-- Past Projects Section -->
        <section class="container d-flex flex-column align-items-center gap-4 mb-5">
            
            <!-- Dynamic Grid Container -->
            <div id="pastProjectsRow" class="row w-100 g-4">
                <div class="col-12 text-center py-5">
                    <span class="spinner-border text-warning" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Loading past projects...</p>
                </div>
            </div>

            <!-- Pagination Container -->
            <div class="row w-100 mt-4">
                <div class="col-12 d-flex justify-content-center">
                    <div class="projects-pagination d-flex align-items-center gap-2">
                        <!-- Pagination buttons will be dynamically inserted here -->
                    </div>
                </div>
            </div>

        </section>

    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>

    <!-- Dynamic Fetch & Render Logic -->
    <script>
        let pastProjects = [];
        let currentPage = 1;
        const projectsPerPage = 9;

        async function fetchPastProjects() {
            try {
                // Fetch using the root-relative path
                const response = await fetch('./includes/projects-data.php');
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                
                // Filter exclusively for 'past' projects
                pastProjects = data.filter(project => project.phase === 'past');
                
                renderProjects(currentPage);
                
            } catch (error) {
                console.error('Failed to fetch projects:', error);
                document.getElementById('pastProjectsRow').innerHTML = `
                    <div class="col-12 text-center w-100 py-5">
                        <p class="text-danger fs-5">Failed to load projects. Please try again later.</p>
                    </div>
                `;
            }
        }

        function renderProjects(page) {
            const row = document.getElementById('pastProjectsRow');
            row.innerHTML = '';

            if (pastProjects.length === 0) {
                row.innerHTML = `
                    <div class="col-12 text-center py-5 w-100">
                        <i class="fa-solid fa-folder-open mb-3" style="font-size: 48px; color: #ccc;"></i>
                        <h5>No past projects found</h5>
                    </div>
                `;
                document.querySelector('.projects-pagination').innerHTML = '';
                return;
            }

            const start = (page - 1) * projectsPerPage;
            const end = start + projectsPerPage;
            const paginatedProjects = pastProjects.slice(start, end);

            paginatedProjects.forEach(project => {
                
                // 1. Resolve Cover Image (Replacing the admin `../` with the frontend `./`)
                let imgUrl = './assets/media/img/thumbnails/default.webp';
                if (project.cover_image && project.cover_image.trim() !== '') {
                    imgUrl = project.cover_image.replace(/^\.\.\//, './');
                }

                // 2. Clean HTML from description & Truncate
                let tempDiv = document.createElement("div");
                tempDiv.innerHTML = project.description || '';
                let cleanText = tempDiv.textContent || tempDiv.innerText || "";
                if (cleanText.length > 120) cleanText = cleanText.substring(0, 120) + '...';

                // 3. Render max 3 Metric Labels
                let metricsHtml = '';
                if (project.metrics && project.metrics.length > 0) {
                    const validMetrics = project.metrics.filter(m => m.label && m.label.trim() !== '').slice(0, 3);
                    validMetrics.forEach(m => {
                        metricsHtml += `<div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap" style="font-size: 0.85rem; font-weight: 500;">${m.label}</div>`;
                    });
                }
                
                // Fallback: If no metrics exist, show impact areas instead to keep the design balanced
                if(metricsHtml === '' && project.impact_areas.length > 0) {
                    project.impact_areas.slice(0, 3).forEach(area => {
                        metricsHtml += `<div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap" style="font-size: 0.85rem; font-weight: 500;">${area.name}</div>`;
                    });
                }

                // 4. Build Card HTML
                const cardHtml = `
                    <div class="col-12 col-lg-4">
                        <a href="past-project?id=${project.id}" class="card-link text-decoration-none">
                            <div class="card border-0 h-100 shadow-sm transition-hover">
                                <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                    <img src="${imgUrl}" alt="${project.title}" class="w-100 h-100 rounded-top-3" style="object-fit: cover; transition: transform 0.3s ease;">
                                </div>
                                <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column">
                                    <h3 class="mb-0 fs-4 overflow-hidden text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${project.title}</h3>
                                    
                                    <div class="dis-btns d-flex align-items-center flex-wrap column-gap-2 row-gap-2 my-3">
                                        ${metricsHtml}
                                    </div>

                                    <div class="w-100 text-dark opacity-75 mt-auto" style="font-size: 0.95rem;">
                                        ${cleanText}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                row.insertAdjacentHTML('beforeend', cardHtml);
            });

            renderPagination(pastProjects.length, page);
        }

        function renderPagination(totalItems, page) {
            const pagination = document.querySelector('.projects-pagination');
            pagination.innerHTML = '';

            const totalPages = Math.ceil(totalItems / projectsPerPage);

            if (totalPages > 1) {
                // Previous Button
                pagination.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-sm btn-dark rounded-circle" style="width: 36px; height: 36px;" ${page === 1 ? 'disabled' : ''} id="prevPage">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                `);

                let startPage = Math.max(1, page - 2);
                let endPage = Math.min(totalPages, page + 2);

                if (startPage > 1) {
                    pagination.insertAdjacentHTML('beforeend', `<button class="btn btn-sm btn-dark page-btn" style="min-width: 36px;" data-page="1">1</button>`);
                    if (startPage > 2) pagination.insertAdjacentHTML('beforeend', `<span class="mx-1 text-muted">...</span>`);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === page ? 'btn-warning fw-bold text-dark' : 'btn-dark';
                    pagination.insertAdjacentHTML('beforeend', `
                        <button class="btn btn-sm ${activeClass} page-btn" style="min-width: 36px;" data-page="${i}">${i}</button>
                    `);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) pagination.insertAdjacentHTML('beforeend', `<span class="mx-1 text-muted">...</span>`);
                    pagination.insertAdjacentHTML('beforeend', `<button class="btn btn-sm btn-dark page-btn" style="min-width: 36px;" data-page="${totalPages}">${totalPages}</button>`);
                }

                // Next Button
                pagination.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-sm btn-dark rounded-circle" style="width: 36px; height: 36px;" ${page === totalPages ? 'disabled' : ''} id="nextPage">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                `);

                // Event Listeners for Pagination
                document.querySelectorAll('.page-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPage = parseInt(btn.dataset.page);
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('pastProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    });
                });

                document.getElementById('prevPage')?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('pastProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    }
                });

                document.getElementById('nextPage')?.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('pastProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    }
                });
            }
        }

        // Add a subtle hover effect via JS dynamically injected CSS
        document.head.insertAdjacentHTML("beforeend", `<style>.transition-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; } .transition-hover:hover img { transform: scale(1.05); }</style>`);

        // Start Execution
        document.addEventListener('DOMContentLoaded', fetchPastProjects);
    </script>
</body>
</html>