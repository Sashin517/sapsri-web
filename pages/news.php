<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 1. Primary SEO Meta Tags -->
    <title>News & Events - SAPSRI</title>
    <meta name="description" content="Stay updated with the latest news, events, and community development initiatives from South Asia Partnership Sri Lanka (SAPSRI).">
    
    <!-- 2. Canonical Link -->
    <link rel="canonical" href="https://sapsri.lk/project-sedna/news">

    <!-- 3. Open Graph / Social Media Sharing Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sapsri.lk/project-sedna/news">
    <meta property="og:title" content="News & Events - SAPSRI">
    <meta property="og:description" content="Stay updated with the latest news, events, and community development initiatives from South Asia Partnership Sri Lanka (SAPSRI).">
    <meta property="og:image" content="https://sapsri.lk/project-sedna/assets/media/img/page-hero/news-and-events.jpg">
    <link rel="stylesheet" href="./vendor/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/style.css">
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

    <!-- header -->
    <?php include "../includes/header.php"; ?>


    <main>
        <!-- content start -->

        <!-- hero -->
        <section class="news__hero mb-5">

            <div class="container">

                <div class="row">

                    <div class="col-12 text-center text-white">

                        <h1 class="fw-semibold display-2 mb-4">News and Events</h1>
                        <!-- <i class="fa-solid fa-circle-info mb-5" style="font-size: 80px;"></i> -->
                        <img src="./assets/icons/solar_documents-bold.svg" alt="documents-icon" class="mb-4">
                        <!-- <p class="fs-5">
                            Explore our research, case studies, and community development reports.
                        </p> -->

                        <div class="input-group news-search m-auto mt-4" style="max-width: 400px;">
                            <input type="text" class="form-control rounded-start-pill bg-light-orange" style="padding: 12px 32px;"
                                placeholder="Search..." aria-label="search" aria-describedby="button-addon2">
                            <button class="btn rounded-end-pill" style="padding: 0 18px;" type="button" id="button-addon2">
                                <i class="fa-solid fa-magnifying-glass mx-2"></i>
                            </button>
                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- hero -->

        <div class="container pb-5">

            <div class="row mb-3">

                <div class="col-12 text-center d-flex flex-wrap">

                    <button class="btn btn-pill btn-dark me-1 mb-2 fs-6">All</button>

                    <button class="btn btn-pill btn-dark me-1 mb-2  py-3 px-5 fs-6">
                        Climate & Biodiversity
                    </button>

                    <button class="btn btn-pill btn-dark me-1 mb-2 py-3 px-5 fs-6">
                        Sustainable Agriculture
                    </button>

                    <button class="btn btn-pill btn-dark me-1 mb-2 py-3 px-5 fs-6">
                        Governance & Finance
                    </button>

                    <button class="btn btn-pill btn-dark me-1 mb-2 py-3 px-5 fs-6">
                        Gender Inclusion
                    </button>

                    <button class="btn btn-pill btn-dark me-1 mb-2 py-3 px-5 fs-6">Other</button>
                </div>

            </div>

            <div id="newsRow" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 mb-4 g-3">
                <!-- News cards will be dynamically inserted here -->
            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-between">
                    <div>
                        <button class="d-none btn btn-outline-dark rounded-pill py-2 px-4">First Page</button>
                    </div>

                    <div class="news-pagination">
                        <!-- Pagination buttons will be dynamically inserted here -->
                    </div>

                    <div>
                        <button class="d-none btn btn-outline-dark rounded-pill py-2 px-4">Last Page</button>
                    </div>
                </div>
            </div>


        </div>

        <!-- content end -->
    </main>

    <!-- footer -->
    <?php include "../includes/footer.php"; ?>
    <script>
        let allPosts = [];
        let filteredPosts = [];
        let currentPage = 1;
        const postsPerPage = 12;
        let currentFilter = 'all';
        let searchQuery = '';

        // Create dropdown element
        const searchContainer = document.querySelector('.news-search');
        const searchInput = document.querySelector('.news-search input');
        const searchButton = document.querySelector('.news-search button');

        // Create dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'search-dropdown';
        searchContainer.appendChild(dropdown);

        async function getPostsData() {
            const url = './includes/post-data.php';

            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                allPosts = Object.values(data);
                filteredPosts = allPosts;

                renderPosts(filteredPosts, currentPage);

            } catch (error) {
                console.error('Failed to fetch posts:', error);
                document.getElementById('newsRow').innerHTML = `
                    <div class="col-12 text-center w-100">
                        <p class="text-danger">Failed to load posts. Please try again later.</p>
                    </div>
                `;
                return null;
            }
        }

        function highlightText(text, query) {
            if (!query.trim()) return text;
            const regex = new RegExp(`(${query.trim()})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }

        function showSearchDropdown(query) {
            if (!query.trim() || query.trim().length < 2) {
                dropdown.classList.remove('show');
                return;
            }

            const searchLower = query.toLowerCase();
            const results = allPosts.filter(post => {
                const titleMatch = post.title.toLowerCase().includes(searchLower);
                const contentMatch = post.content && post.content.toLowerCase().includes(searchLower);
                const impactMatch = post.impact_areas.some(area => 
                    area.toLowerCase().includes(searchLower)
                );
                return titleMatch || contentMatch || impactMatch;
            }).slice(0, 8); // Show max 8 results

            if (results.length === 0) {
                dropdown.innerHTML = `
                    <div class="search-dropdown-empty">
                        <i class="fa-solid fa-magnifying-glass mb-2"></i>
                        <div>No results found for "${query}"</div>
                    </div>
                `;
                dropdown.classList.add('show');
                return;
            }

            dropdown.innerHTML = results.map(post => {
                // UPDATE: Correct image mapping for search dropdown
                let imgUrl = './assets/media/img/thumbnails/default.webp';
                if (post.cover_image && post.cover_image.trim() !== '') {
                    imgUrl = post.cover_image;
                } else if (post.post_media && post.post_media.length > 0) {
                    const cardImage = post.post_media.find(m => m.type === 'card_image');
                    if (cardImage) imgUrl = cardImage.url;
                }

                let content = post.content || '';
                if (content.length > 80) {
                    content = content.substring(0, 80) + '...';
                }

                return `
                    <div class="search-dropdown-item" data-title="${post.title.replace(/"/g, '&quot;')}">
                        <img src="${imgUrl}" alt="${post.title}" class="search-dropdown-item-icon">
                        <div class="search-dropdown-item-content">
                            <div class="search-dropdown-item-title">${highlightText(post.title, query)}</div>
                            <div class="search-dropdown-item-description">${highlightText(content, query)}</div>
                        </div>
                    </div>
                `;
            }).join('');

            dropdown.classList.add('show');

            // Add click handlers to dropdown items
            dropdown.querySelectorAll('.search-dropdown-item').forEach(item => {
                item.addEventListener('click', () => {
                    const title = item.getAttribute('data-title');
                    searchInput.value = title;
                    dropdown.classList.remove('show');
                    searchInput.focus();
                });
            });
        }

        function applyFilters() {
            let posts = allPosts;

            // Apply category filter
            if (currentFilter !== 'all') {
                posts = posts.filter(post => {
                    return post.impact_areas.some(area => {
                        area = area.toLowerCase();
                        if (currentFilter === 'climate & biodiversity') {
                            return area === 'climate' || area === 'biodiversity';
                        }
                        if (currentFilter === 'sustainable agriculture') {
                            return area === 'sustainable_agriculture' || area === 'sustainable agriculture';
                        }
                        if (currentFilter === 'governance & finance') {
                            return area === 'governance' || area === 'finance';
                        }
                        if (currentFilter === 'gender inclusion') {
                            return area === 'gender_inclusion' || area === 'gender inclusion';
                        }
                        if (currentFilter === 'other') {
                            return !['climate', 'biodiversity', 'sustainable_agriculture', 'sustainable agriculture', 'governance', 'finance', 'gender_inclusion', 'gender inclusion'].includes(area);
                        }
                        return false;
                    });
                });
            }

            // Apply search filter
            if (searchQuery.trim() !== '') {
                const query = searchQuery.toLowerCase();
                posts = posts.filter(post => {
                    const titleMatch = post.title.toLowerCase().includes(query);
                    const contentMatch = post.content && post.content.toLowerCase().includes(query);
                    const impactMatch = post.impact_areas.some(area => 
                        area.toLowerCase().includes(query)
                    );
                    return titleMatch || contentMatch || impactMatch;
                });
            }

            filteredPosts = posts;
            currentPage = 1;
            renderPosts(filteredPosts, currentPage);
        }

        function renderPosts(posts, page = 1) {
            const newsRow = document.getElementById('newsRow');
            newsRow.innerHTML = '';

            if (posts.length === 0) {
                newsRow.innerHTML = `
                    <div class="col-12 text-center py-5 w-100">
                        <i class="fa-solid fa-magnifying-glass mb-3" style="font-size: 48px; color: #ccc;"></i>
                        <h5>No posts found</h5>
                        <p class="text-muted">Try adjusting your search or filter</p>
                    </div>
                `;
                document.querySelector('.news-pagination').innerHTML = '';
                return;
            }

            const start = (page - 1) * postsPerPage;
            const end = start + postsPerPage;
            const paginatedPosts = posts.slice(start, end);

            paginatedPosts.forEach(post => {
                // UPDATE: Correct image mapping for main grid
                let imgUrl = './assets/media/img/thumbnails/default.webp';
                if (post.cover_image && post.cover_image.trim() !== '') {
                    imgUrl = post.cover_image;
                } else if (post.post_media && post.post_media.length > 0) {
                    const cardImage = post.post_media.find(m => m.type === 'card_image');
                    if (cardImage) imgUrl = cardImage.url;
                }

                let content = post.content || '';
                if (content.length > 120) {
                    content = content.substring(0, 120) + '...';
                }
                
                // UPDATE: Cleanly format the date
                const dateObj = new Date(post.published_date);
                const formattedDate = dateObj.toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "short",
                    day: "numeric"
                });

                const cardHtml = `
                    <div class="col">
                        <a href="post?id=${post.id}&len_posts=${posts.length}" class="card-link text-decoration-none">
                            <div class="card news-card rounded-4 h-100">
                                <img src="${imgUrl}" class="card-img-top rounded-top-4" alt="${post.title}">
                                <div class="card-body">
                                    <h5 class="card-title">${post.title}</h5>
                                    <p class="card-text">${content}</p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Posted on ${formattedDate || ''}</small>
                                    <button class="btn btn-sm btn-dark rounded-circle" style="aspect-ratio: 1;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                newsRow.insertAdjacentHTML('beforeend', cardHtml);
            });

            renderPagination(posts, page);
        }

        function renderPagination(posts, page) {
            const pagination = document.querySelector('.news-pagination');
            pagination.innerHTML = '';

            const totalPages = Math.ceil(posts.length / postsPerPage);

            if (totalPages > 1) {
                pagination.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-sm btn-dark rounded-circle me-1" ${page === 1 ? 'disabled' : ''} id="prevPage">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                `);

                let startPage = Math.max(1, page - 2);
                let endPage = Math.min(totalPages, page + 2);

                if (startPage > 1) {
                    pagination.insertAdjacentHTML('beforeend', `
                        <button class="btn btn-sm btn-dark page-btn me-1" data-page="1">1</button>
                    `);
                    if (startPage > 2) {
                        pagination.insertAdjacentHTML('beforeend', `<span class="mx-2">...</span>`);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    pagination.insertAdjacentHTML('beforeend', `
                        <button class="btn btn-sm ${i === page ? 'btn-primary-orange fw-semibold' : 'btn-dark'} page-btn me-1" data-page="${i}">
                            ${i}
                        </button>
                    `);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        pagination.insertAdjacentHTML('beforeend', `<span class="mx-2">...</span>`);
                    }
                    pagination.insertAdjacentHTML('beforeend', `
                        <button class="btn btn-sm btn-dark page-btn me-1" data-page="${totalPages}">${totalPages}</button>
                    `);
                }

                pagination.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-sm btn-dark rounded-circle ms-1" ${page === totalPages ? 'disabled' : ''} id="nextPage">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                `);

                document.querySelectorAll('.page-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPage = parseInt(btn.dataset.page);
                        renderPosts(filteredPosts, currentPage);
                    });
                });

                document.getElementById('prevPage')?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPosts(filteredPosts, currentPage);
                    }
                });

                document.getElementById('nextPage')?.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderPosts(filteredPosts, currentPage);
                    }
                });
            }
        }

        // Category filter buttons
        document.querySelectorAll('.btn-pill').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.btn-pill').forEach(btn => {
                    btn.classList.remove('btn-primary-orange', 'fw-semibold');
                    btn.classList.add('btn-dark');
                });
                button.classList.remove('btn-dark');
                button.classList.add('btn-primary-orange', 'fw-semibold');

                currentFilter = button.textContent.trim().toLowerCase();
                applyFilters();
            });
        });

        // Search input - show dropdown as you type
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            const value = e.target.value;
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                showSearchDropdown(value);
            }, 300);
        });

        // Search button - perform actual search
        searchButton.addEventListener('click', () => {
            searchQuery = searchInput.value;
            dropdown.classList.remove('show');
            applyFilters();
        });

        // Enter key - perform actual search
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchQuery = searchInput.value;
                dropdown.classList.remove('show');
                applyFilters();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchContainer.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Initialize
        getPostsData();
    </script>
    <script src="./assets/js/translation.js"></script>
    <script src="./vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>

</html>