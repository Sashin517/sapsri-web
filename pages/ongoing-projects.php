<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPSRI | Ongoing Projects</title>
    <!-- Frontend assets use ./ because of .htaccess root routing -->
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/ongoing-projects.css">
    <link rel="stylesheet" href="./assets/css/project-style.css">
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

                        <div class="hstack gap-3 justify-content-center flex-wrap" id="category-filters">
                            <button class="btn btn-primary-yellow text-dark rounded-pill py-3 px-5 fw-semibold filter-btn" data-filter="all">All</button>
                            <button class="btn btn-dark text-white rounded-pill py-3 px-5 fw-semibold filter-btn" data-filter="climate & biodiversity">Climate & Biodiversity</button>
                            <button class="btn btn-dark text-white rounded-pill py-3 px-5 fw-semibold filter-btn" data-filter="sustainable agriculture">Sustainable Agriculture</button>
                            <button class="btn btn-dark text-white rounded-pill py-3 px-5 fw-semibold filter-btn" data-filter="governance & finance">Governance & Finance</button>
                            <button class="btn btn-dark text-white rounded-pill py-3 px-5 fw-semibold filter-btn" data-filter="gender inclusion">Gender Inclusion</button>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- hero -->

        <!-- Ongoing Projects Section -->
        <section id="ongoing-projects" class="container d-flex flex-column align-items-center gap-4 mb-5">
            
            <!-- Dynamic Grid Container -->
            <div id="ongoingProjectsRow" class="row w-100 g-4">
                <div class="col-12 text-center py-5">
                    <span class="spinner-border text-warning" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Loading ongoing projects...</p>
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
        let allOngoingProjects = [];
        let filteredProjects = [];
        let currentPage = 1;
        const projectsPerPage = 9;
        let currentFilter = 'all';

        async function fetchOngoingProjects() {
            try {
                // Fetch using the root-relative path
                const response = await fetch('./includes/projects-data.php');
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                
                // Filter exclusively for 'ongoing' projects and store in master array
                allOngoingProjects = data.filter(project => project.phase === 'ongoing');
                filteredProjects = allOngoingProjects; // Initially, filtered is all
                
                renderProjects(currentPage);
                
            } catch (error) {
                console.error('Failed to fetch projects:', error);
                document.getElementById('ongoingProjectsRow').innerHTML = `
                    <div class="col-12 text-center w-100 py-5">
                        <p class="text-danger fs-5">Failed to load projects. Please try again later.</p>
                    </div>
                `;
            }
        }

        // --- FILTERING LOGIC ---
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                // 1. Update Button Styles (Active vs Inactive)
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('btn-primary-yellow', 'text-dark');
                    btn.classList.add('btn-dark', 'text-white');
                });
                e.target.classList.remove('btn-dark', 'text-white');
                e.target.classList.add('btn-primary-yellow', 'text-dark');

                // 2. Set Current Filter
                currentFilter = e.target.getAttribute('data-filter');

                // 3. Apply Filter to Data
                if (currentFilter === 'all') {
                    filteredProjects = allOngoingProjects;
                } else {
                    filteredProjects = allOngoingProjects.filter(project => {
                        return project.impact_areas.some(area => {
                            let areaName = area.name.toLowerCase();
                            if (currentFilter === 'climate & biodiversity') return areaName.includes('climate') || areaName.includes('biodiversity');
                            if (currentFilter === 'sustainable agriculture') return areaName.includes('sustainable agriculture');
                            if (currentFilter === 'governance & finance') return areaName.includes('governance') || areaName.includes('finance');
                            if (currentFilter === 'gender inclusion') return areaName.includes('gender');
                            return areaName === currentFilter;
                        });
                    });
                }

                // 4. Reset to page 1 and render
                currentPage = 1;
                renderProjects(currentPage);
            });
        });

        function renderProjects(page) {
            const row = document.getElementById('ongoingProjectsRow');
            row.innerHTML = '';

            if (filteredProjects.length === 0) {
                row.innerHTML = `
                    <div class="col-12 text-center py-5 w-100">
                        <i class="fa-solid fa-folder-open mb-3" style="font-size: 48px; color: #ccc;"></i>
                        <h5>No projects found for this category</h5>
                    </div>
                `;
                document.querySelector('.projects-pagination').innerHTML = '';
                return;
            }

            const start = (page - 1) * projectsPerPage;
            const end = start + projectsPerPage;
            const paginatedProjects = filteredProjects.slice(start, end);

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
                if (cleanText.length > 130) cleanText = cleanText.substring(0, 130) + '...';

                // 3. Render max 4 Metric Labels
                let metricsHtml = '';
                if (project.metrics && project.metrics.length > 0) {
                    const validMetrics = project.metrics.filter(m => m.label && m.label.trim() !== '').slice(0, 4);
                    validMetrics.forEach(m => {
                        metricsHtml += `<div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap" style="font-size: 0.85rem; font-weight: 500;">${m.label}</div>`;
                    });
                }
                
                // Fallback: If no metrics exist, show impact areas instead
                if(metricsHtml === '' && project.impact_areas.length > 0) {
                    project.impact_areas.slice(0, 4).forEach(area => {
                        metricsHtml += `<div class="bg-gold-yellow text-black rounded-5 py-2 px-3 text-nowrap" style="font-size: 0.85rem; font-weight: 500;">${area.name}</div>`;
                    });
                }

                // 4. Build Card HTML
                const cardHtml = `
                    <div class="col-12 col-lg-4">
                        <a href="ongoing-project.php?id=${project.id}" class="card-link text-decoration-none">
                            <div class="card border-0 h-100 d-flex flex-column shadow-sm hover-zoom">
                                <div class="card-img-top" style="height: 332.8px; overflow: hidden;">
                                    <img src="${imgUrl}" alt="${project.title}" class="w-100 h-100 rounded-top-3" style="object-fit: cover;">
                                </div>
                                <div class="card-body bg-fade-gold rounded-bottom-4 d-flex flex-column">
                                    <h3 class="mb-0 fs-4 overflow-hidden text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${project.title}</h3>
                                    
                                    <div class="dis-btns d-flex align-items-center flex-wrap column-gap-2 row-gap-2 my-3">
                                        ${metricsHtml}
                                    </div>

                                    <div class="text-truncate-3 w-100 text-dark opacity-75 mt-auto" style="font-size: 0.95rem;">
                                        ${cleanText}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                row.insertAdjacentHTML('beforeend', cardHtml);
            });

            renderPagination(filteredProjects.length, page);
        }

        function renderPagination(totalItems, page) {
            const pagination = document.querySelector('.projects-pagination');
            pagination.innerHTML = '';

            const totalPages = Math.ceil(totalItems / projectsPerPage);

            if (totalPages > 1) {
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

                pagination.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-sm btn-dark rounded-circle" style="width: 36px; height: 36px;" ${page === totalPages ? 'disabled' : ''} id="nextPage">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                `);

                document.querySelectorAll('.page-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPage = parseInt(btn.dataset.page);
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('ongoingProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    });
                });

                document.getElementById('prevPage')?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('ongoingProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    }
                });

                document.getElementById('nextPage')?.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderProjects(currentPage);
                        window.scrollTo({ top: document.getElementById('ongoingProjectsRow').offsetTop - 100, behavior: 'smooth' });
                    }
                });
            }
        }

        // Start Execution
        document.addEventListener('DOMContentLoaded', fetchOngoingProjects);
    </script>
</body>
</html>