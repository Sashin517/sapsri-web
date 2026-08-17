<!-- Hero Banner with Dropdown -->
<div class="px-4 mb-4">
  <div class="hero-banner">
    <div>
      <h1 class="mb-1 fw-bold fs-3">Welcome back, <span id="dyn-user-name"></span>!</h1>
      <p class="mb-0 opacity-75">Manage your SAPSRI web content efficiently</p>
    </div>

    <!-- Date Dropdown Container -->
    <div class="position-relative">
      <button class="btn btn-light d-flex align-items-center gap-2 rounded-3 fw-medium" id="dateDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
        Today <i data-lucide="chevron-down" style="width: 16px;"></i>
      </button>

      <!-- Standard Dropdown List -->
      <ul class="dropdown-menu date-dropdown-menu dropdown-menu-end" aria-labelledby="dateDropdownBtn">
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="today">Today</a></li>
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="yesterday">Yesterday</a></li>
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="week">Week-to-Date</a></li>
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="month">Month-to-Date</a></li>
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="year">Year-to-Date</a></li>
        <li><a class="dropdown-item date-dropdown-item date-filter" href="#" data-range="all-time">All Time</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li class="position-relative">
          <div class="dropdown-item date-dropdown-item" id="customDateBtn">
            Custom <i data-lucide="chevron-right" style="width: 16px;"></i>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- Dashboard Content (merged in from the KPI/table/quick-actions build) -->
<div class="dashboard-container px-4">

  <!-- KPI Row -->
  <div class="row g-4 mb-4">

    <!-- Projects -->
    <div class="col-xl-3 col-lg-6">
      <div class="kpi-card">
        <div class="kpi-header">
          <h3 class="kpi-title">Active Projects</h3>
          <div class="kpi-icon-wrapper"><i data-lucide="briefcase"></i></div>
        </div>
        <div class="kpi-value-row">
          <p class="kpi-value" id="val-projects"><span class="spinner-border spinner-border-sm"></span></p>
          <span class="badge-trend" id="trend-projects">-</span>
        </div>
        <div class="kpi-footer" id="desc-projects">Loading...</div>
      </div>
    </div>

    <!-- Publications -->
    <div class="col-xl-3 col-lg-6">
      <div class="kpi-card">
        <div class="kpi-header">
          <h3 class="kpi-title">Publications</h3>
          <div class="kpi-icon-wrapper"><i data-lucide="book-open"></i></div>
        </div>
        <div class="kpi-value-row">
          <p class="kpi-value" id="val-publications"><span class="spinner-border spinner-border-sm"></span></p>
          <span class="badge-trend" id="trend-publications">-</span>
        </div>
        <div class="kpi-footer" id="desc-publications">Loading...</div>
      </div>
    </div>

    <!-- Users -->
    <div class="col-xl-3 col-lg-6">
      <div class="kpi-card">
        <div class="kpi-header">
          <h3 class="kpi-title">Active Users</h3>
          <div class="kpi-icon-wrapper"><i data-lucide="user-check"></i></div>
        </div>
        <div class="kpi-value-row">
          <p class="kpi-value" id="val-users"><span class="spinner-border spinner-border-sm"></span></p>
          <span class="badge-trend" id="trend-users">-</span>
        </div>
        <div class="kpi-footer" id="desc-users">Loading...</div>
      </div>
    </div>

    <!-- Posts -->
    <div class="col-xl-3 col-lg-6">
      <div class="kpi-card">
        <div class="kpi-header">
          <h3 class="kpi-title">Number of Posts</h3>
          <div class="kpi-icon-wrapper"><i data-lucide="newspaper"></i></div>
        </div>
        <div class="kpi-value-row">
          <p class="kpi-value" id="val-posts"><span class="spinner-border spinner-border-sm"></span></p>
          <span class="badge-trend" id="trend-posts">-</span>
        </div>
        <div class="kpi-footer" id="desc-posts">Loading...</div>
      </div>
    </div>

  </div>

  <!-- Bottom Row -->
  <div class="row g-4">

    <!-- Recent Web Content Table -->
    <div class="col-xl-8 col-lg-7">
      <div class="content-card h-100">
        <h3 class="section-title">Recent Web Content</h3>
        <div class="table-responsive">
          <table class="table table-borderless align-middle mb-0">
            <thead>
              <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Lead</th>
                <th>Created Date</th>
              </tr>
            </thead>
            <tbody id="recent-content-tbody">
              <tr>
                <td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading content...</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="custom-pagination" id="recent-content-pagination"></div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4 col-lg-5">
      <div class="content-card h-100 bg-transparent border-0 p-0">
        <h3 class="section-title">Quick Actions</h3>

        <!-- <a href="#" class="quick-action-card">
          <i data-lucide="briefcase" class="quick-action-icon"></i>
          <span class="quick-action-text">Create New Project</span>
        </a> -->

        <div class="quick-action-card" onclick="handleQuickAction('create-new-project');">
          <i data-lucide="briefcase" class="quick-action-icon"></i>
          <span class="quick-action-text">Create New Project</span>
        </div>

        <!-- <a href="#" class="quick-action-card">
          <i data-lucide="newspaper" class="quick-action-icon"></i>
          <span class="quick-action-text">Create New Post</span>
        </a> -->

        <div class="quick-action-card" onclick="handleQuickAction('create-new-post');">
          <i data-lucide="newspaper" class="quick-action-icon"></i>
          <span class="quick-action-text">Create New Post</span>
        </div>

        <!-- <a href="#" class="quick-action-card">
          <i data-lucide="book-open" class="quick-action-icon"></i>
          <span class="quick-action-text">Add New Publication</span>
        </a> -->

        <div class="quick-action-card" onclick="handleQuickAction('add-new-publication');">
          <i data-lucide="book-open" class="quick-action-icon"></i>
          <span class="quick-action-text">Add New Publication</span>
        </div>

      </div>
    </div>