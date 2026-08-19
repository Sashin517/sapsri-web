<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 px-4 pt-4">
  <div>
    <h1 class="mb-1 fw-bold fs-3">Manage Publications</h1>
    <p class="mb-0 text-muted" style="font-size: 0.95rem;">Upload and manage downloadable reports</p>
  </div>
  <button class="btn btn-create-project d-flex align-items-center gap-2" onclick="loadView('create-publication', 'Create Publication')">
    <i data-lucide="upload-cloud" style="width: 18px;"></i> Upload Publication
  </button>
</div>

<div class="px-4 pb-4">
  <div class="content-card card-edge-table h-100 mb-0">
    <div class="projects-filter-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h3 class="section-title mb-0 fs-5">All Publications</h3>

      <div class="d-flex align-items-center gap-3 flex-wrap">

        <!-- Search -->
        <div class="search-bar" style="width: 280px;">
          <i data-lucide="search"></i>
          <input type="text" id="post-search-input" class="search-input" placeholder="Search Title or Category...">
        </div>

        <!-- Status Filter Pills -->
        <div class="d-flex gap-2" id="post-status-pills">
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

    <div class="table-responsive mb-0">
      <table class="table table-borderless align-middle mb-0">
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Uploaded By</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="publications-tbody">
          <tr>
            <td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading publications...</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="p-4 border-top">
      <div id="publications-pagination"></div>
    </div>
  </div>
</div>