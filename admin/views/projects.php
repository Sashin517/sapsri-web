<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 px-4 pt-4">
    <div>
        <h1 class="mb-1 fw-bold fs-3">Manage Projects</h1>
        <p class="mb-0 text-muted" style="font-size: 0.95rem;">Manage your SAPSRI web content efficiently</p>
    </div>
    <button class="btn btn-create-project d-flex align-items-center gap-2" onclick="loadView('create-project', 'Create New Project')">
        <i data-lucide="plus-circle" style="width: 18px;"></i> Create New Project
    </button>
</div>

<!-- Main Projects Container -->
<div class="px-4 pb-4">
    <div class="content-card card-edge-table h-100 mb-0">

        <!-- Filter Bar -->
        <div class="projects-filter-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h3 class="section-title mb-0 fs-5">All Projects</h3>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Search -->
                <div class="search-bar" style="width: 280px;">
                    <i data-lucide="search"></i>
                    <input type="text" id="project-search-input" class="search-input" placeholder="Search Title...">
                </div>

                <!-- Status Filter Pills -->
                <div class="d-flex gap-2" id="project-status-pills">
                    <button class="filter-pill active" data-filter="all">
                        <i data-lucide="check" style="width: 16px;"></i> All
                    </button>
                    <button class="filter-pill" data-filter="published">
                        <i data-lucide="check" class="d-none" style="width: 16px;"></i> Published
                    </button>
                    <button class="filter-pill" data-filter="draft">
                        <i data-lucide="check" class="d-none" style="width: 16px;"></i> Draft
                    </button>
                    <button class="filter-pill" data-filter="archived">
                        <i data-lucide="check" class="d-none" style="width: 16px;"></i> Archived
                    </button>
                    <button class="filter-pill" data-filter="ongoing">
                        <i data-lucide="check" class="d-none" style="width: 16px;"></i> Ongoing
                    </button>
                    <button class="filter-pill" data-filter="past">
                        <i data-lucide="check" class="d-none" style="width: 16px;"></i> Past
                    </button>
                </div>

                <!-- Date Filter Dropdown -->
                <div class="dropdown date-filter-dropdown position-relative">
                    <button class="btn btn-light border d-flex align-items-center gap-2 rounded-3 fw-medium bg-white" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                        <span id="date-filter-label">All Time</span>
                        <i data-lucide="chevron-down" style="width: 16px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dateFilterDropdown" id="date-filter-options">
                        <li><a class="dropdown-item" href="#" data-range="today">Today</a></li>
                        <li><a class="dropdown-item" href="#" data-range="yesterday">Yesterday</a></li>
                        <li><a class="dropdown-item" href="#" data-range="last_week">Last Week</a></li>
                        <li><a class="dropdown-item" href="#" data-range="last_month">Last Month</a></li>
                        <li><a class="dropdown-item" href="#" data-range="last_year">Last Year</a></li>
                        <li><a class="dropdown-item active" href="#" data-range="all_time">All Time</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div id="customDateItem" class="dropdown-item d-flex justify-content-between align-items-center" data-range="custom">
                                <span>Custom</span>
                                <i data-lucide="chevron-right" style="width: 16px;"></i>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive mb-0">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Phase</th>
                        <th>Status</th>
                        <th>Lead</th>
                        <th>Created Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="projects-tbody">
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <span class="spinner-border spinner-border-sm"></span> Loading projects...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-7">Rows:</span>
                <select id="projects-rows-per-page" class="form-select form-select-sm rows-select">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-muted fs-7 ms-2" id="pagination-info-text">Showing 0-0 of 0</span>
            </div>

            <div id="projects-pagination">
                <!-- Dynamic pagination controls -->
            </div>
        </div>

    </div>
</div>