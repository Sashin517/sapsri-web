<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPSRI | Publications</title>
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- font awesome v7 -->
    <script src="https://kit.fontawesome.com/6e09983e4e.js" crossorigin="anonymous"></script>
    <!-- pdfjs-viewer-element -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
</head>

<body>

    <!-- header -->
    <?php include "../includes/header.php"; ?>


    <!-- content start -->
    <main>

        <!-- hero -->
        <section class="publications__hero mb-5">

            <div class="container">

                <div class="row">

                    <div class="col-12 text-center text-white">

                        <h1 class="fw-semibold display-2 mb-4">Policies and Publications</h1>
                        <img src="./assets/icons/solar_documents-bold.svg" alt="documents-icon" class="mb-4">
                        
                        <div class="hstack gap-3 flex-wrap justify-content-center mb-5">
                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#organisationalPolicies">
                                Organisational policies
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#annualReports">
                                Annual Reports
                            </a>

                            <a class="btn btn-primary-yellow rounded-pill py-3 px-5 fw-semibold" href="#reportsAndCaseStudies">
                                Reports & Case Studies
                            </a>
                        </div>

                        <!-- Search Bar with Dropdown Container -->
                        <div class="news-search m-auto position-relative" style="max-width: 400px;">
                            <div class="input-group">
                                <input type="text" id="pubSearchInput" class="form-control rounded-start-pill bg-light-orange" style="padding: 12px 32px;"
                                    placeholder="Search publications..." aria-label="search" autocomplete="off">
                                <button class="btn rounded-end-pill" style="padding: 0 18px;" type="button" id="pubSearchBtn">
                                    <i class="fa-solid fa-magnifying-glass mx-2"></i>
                                </button>
                            </div>
                            <!-- Dynamic Dropdown -->
                            <div id="searchDropdown" class="search-dropdown w-100 text-start"></div>
                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- hero -->


        <section class="publications__content">

            <!-- DYNAMIC AJAX CONTAINER -->
            <div class="container mb-5" id="cardContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3 text-muted fw-medium">Loading Publications...</p>
                </div>
            </div>

        </section>

    </main>
    <!-- content end -->

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>

    <!-- Modal -->
    <div class="modal fade pdf-viewer-modal" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-fullscreen">

            <div class="modal-content bg-light-pink">

                <div class="modal-header">
                    <h5 class="text-truncate">
                        <i class='fa-solid fa-file-lines'></i><span class="modal-title">Document Title</span>
                    </h5>

                    <button type="button" class="btn close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-body bg-dark d-flex justify-content-center align-items-center">
                    <div>
                        <canvas id="pageCanvas"></canvas>
                    </div>
                </div>

                <div class="modal-footer justify-content-center justify-content-lg-between">

                    <div class="hstack gap-2">
                        <button class="btn btn-warning" id="pdfPrevPageBtn">
                            <i class="fa-solid fa-angle-left"></i>
                        </button>

                        <span>Page: <span id="pdfPageNum"></span> / <span id="pdfPageCount"></span></span>

                        <button class="btn btn-warning" id="pdfNextPageBtn">
                            <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>

                    <a href="#" class="btn btn-pill btn-primary" id="publicationModalSaveBtn" download>
                        Download <i class="fa-solid fa-download"></i>
                    </a>

                </div>

            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./vendor/masonry/masonry.pkgd.min.js"></script>
    <script src="./assets/js/script.js"></script>

    <!-- AJAX DYNAMIC FETCH & DROPDOWN FILTER LOGIC -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const cardContainer = document.getElementById("cardContainer");
        const searchInput = document.getElementById("pubSearchInput");
        const searchBtn = document.getElementById("pubSearchBtn");
        const searchDropdown = document.getElementById("searchDropdown");
        
        let allPublications = [];

        // Resolves paths efficiently for the subfolder environment
        const cleanUrl = (url) => {
            if (!url) return '#';
            if (url.startsWith('../')) return '/project-sedna/' + url.substring(3);
            if (!url.startsWith('/project-sedna/')) return '/project-sedna/' + url;
            return url;
        };

        // Generates specific IDs to match the hardcoded quick-jump buttons in your hero
        function generateSectionId(str) {
            if (str === "Organisational policies") return "organisationalPolicies";
            if (str === "Annual Reports") return "annualReports";
            if (str === "Reports & Case Studies") return "reportsAndCaseStudies";
            return str.replace(/\s+/g, '-').toLowerCase();
        }

        // 1. Fetch data from DB Endpoint
        async function fetchPublications() {
            try {
                // FIXED PATH: Replaced relative '../includes' with absolute path to bypass .htaccess rewrite issues
                const response = await fetch('/project-sedna/includes/publications-data.php');
                if (!response.ok) throw new Error("Network request failed");
                
                allPublications = await response.json();
                renderPublications(allPublications);
            } catch (error) {
                console.error("AJAX Fetch Error:", error);
                cardContainer.innerHTML = '<div class="alert alert-danger text-center m-5">Failed to load publications. Please try again later.</div>';
            }
        }

        // 2. Render Grid Cards to DOM
        function renderPublications(data) {
            cardContainer.innerHTML = '';

            if (!data || data.length === 0) {
                cardContainer.innerHTML = '<div class="text-center py-5"><p class="text-muted fs-5 fw-medium">No publications found.</p></div>';
                return;
            }

            // Group by category name dynamically
            const groupedDocs = {};
            data.forEach(pub => {
                const cat = pub.category_name || 'Uncategorized';
                if (!groupedDocs[cat]) groupedDocs[cat] = [];
                groupedDocs[cat].push(pub);
            });

            // Loop through each category and inject its Header and Grid
            for (const [category, pubs] of Object.entries(groupedDocs)) {
                
                const sectionId = generateSectionId(category);

                const header = document.createElement('h2');
                header.id = sectionId;
                header.textContent = category;
                cardContainer.appendChild(header);

                const grid = document.createElement('div');
                grid.className = 'row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-5 masonry-grid';
                
                pubs.forEach(pub => {
                    const col = document.createElement('div');
                    col.className = 'col';

                    let displayDate = '';
                    if (pub.publish_date) {
                        const d = new Date(pub.publish_date);
                        displayDate = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                    }

                    const coverImage = pub.cover_image ? cleanUrl(pub.cover_image) : '/project-sedna/assets/media/img/pdf-covers/annual-report-2024-2025.webp';
                    const fileUrl = cleanUrl(pub.file_url);

                    col.innerHTML = `
                        <div class="card publication-card rounded-4 bg-light-pink h-100">
                            <img src="${coverImage}" class="card-img-top rounded-top-4" alt="${pub.title}" style="height: 250px; object-fit: contain; background: #fff; padding: 5px;">
                            <div class="card-body">
                                <h4 class="card-title fs-5">${pub.title}</h4>
                                ${pub.description ? `<p class="card-text">${pub.description}</p>` : ''}
                            </div>
                            <div class="card-footer hstack justify-content-between align-items-end mt-auto">
                                <time>${displayDate}</time>
                                
                                <!-- SIMPLIFIED LINK HERE -->
                                <a href="#" class="wh-0 stretched-link"></a>
                                
                                <a href="${fileUrl}" class="btn btn-download z-3" download="${pub.title}.pdf">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                    grid.appendChild(col);
                });

                cardContainer.appendChild(grid);
            }

            // Initialize Masonry Grids
            setTimeout(() => {
                document.querySelectorAll('.masonry-grid').forEach(el => {
                    if (typeof Masonry !== 'undefined') new Masonry(el, { percentPosition: true });
                });
            }, 100); 
        }

        // ==========================================
        // 3. SEARCH & DROPDOWN LOGIC
        // ==========================================
        
        const hideDropdown = () => searchDropdown.classList.remove('show');
        const showDropdown = () => searchDropdown.classList.add('show');

        // Close dropdown when clicking anywhere outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                hideDropdown();
            }
        });

        // Filter Function 
        function getFilteredResults(query) {
            return allPublications.filter(pub => {
                const titleMatch = (pub.title || "").toLowerCase().includes(query);
                const descMatch = (pub.description || "").toLowerCase().includes(query);
                const catMatch = (pub.category_name || "").toLowerCase().includes(query);
                return titleMatch || descMatch || catMatch;
            });
        }

        // Run when User is Typing
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            if (!query) {
                hideDropdown();
                renderPublications(allPublications); // Reset grid back to normal
                return;
            }

            const filtered = getFilteredResults(query);
            populateDropdown(filtered);
        });

        // Run when User clicks the Search Button
        searchBtn.addEventListener('click', () => {
            executeFullSearch();
        });

        // Run when User presses "Enter" on keyboard
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeFullSearch();
            }
        });

        // Fills the dropdown menu with items
        function populateDropdown(results) {
            searchDropdown.innerHTML = '';
            
            if (results.length === 0) {
                searchDropdown.innerHTML = '<div class="search-dropdown-empty">No matching publications found.</div>';
            } else {
                // Show up to 5 items in the dropdown
                results.slice(0, 5).forEach(pub => {
                    const item = document.createElement('div');
                    item.className = 'search-dropdown-item';
                    
                    const coverImage = pub.cover_image ? cleanUrl(pub.cover_image) : '/project-sedna/assets/media/img/pdf-covers/annual-report-2024-2025.webp';
                    
                    item.innerHTML = `
                        <img src="${coverImage}" class="search-dropdown-item-icon" alt="">
                        <div class="search-dropdown-item-content">
                            <div class="search-dropdown-item-title">${pub.title}</div>
                            <div class="search-dropdown-item-description">${pub.category_name}</div>
                        </div>
                    `;
                    
                    // Click an item -> isolate just that item in the grid below
                    item.addEventListener('click', () => {
                        searchInput.value = pub.title;
                        hideDropdown();
                        renderPublications([pub]);
                        
                        // Scroll down to the grid container
                        document.getElementById('cardContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    
                    searchDropdown.appendChild(item);
                });
                
                // If there are more than 5 results, show a "View all" button inside the dropdown
                if (results.length > 5) {
                    const moreItem = document.createElement('div');
                    moreItem.className = 'search-dropdown-empty fw-bold';
                    moreItem.style.cursor = 'pointer';
                    moreItem.style.color = 'var(--brand-crimson)';
                    moreItem.textContent = `View all ${results.length} results...`;
                    
                    moreItem.addEventListener('click', () => {
                        executeFullSearch();
                    });
                    searchDropdown.appendChild(moreItem);
                }
            }
            showDropdown();
        }

        // Executes a full grid filter
        function executeFullSearch() {
            hideDropdown();
            const query = searchInput.value.toLowerCase().trim();
            
            if (!query) {
                renderPublications(allPublications);
            } else {
                const filtered = getFilteredResults(query);
                renderPublications(filtered);
                // Scroll down to results
                document.getElementById('cardContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Trigger initial fetch
        fetchPublications();
    });
    </script>
    <script type="module" src="./assets/js/pdf.viewer.js"></script>
</body>
</html>