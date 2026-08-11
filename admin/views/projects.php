<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 px-4 pt-4">
  <div>
    <h1 class="mb-1 fw-bold fs-3">Manage all projects</h1>
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
          <input type="text" class="search-input" placeholder="Search Title...">
        </div>
        
        <!-- Status Filter Pills -->
        <div class="d-flex gap-2">
          <button class="filter-pill active">
            <i data-lucide="check" style="width: 16px;"></i> Published
          </button>
          <button class="filter-pill">Draft</button>
          <button class="filter-pill">Ongoing</button>
          <button class="filter-pill">Past</button>
        </div>
        
        <!-- Date Dropdown -->
        <button class="btn btn-light border d-flex align-items-center gap-2 rounded-3 fw-medium bg-white">
          Today <i data-lucide="chevron-down" style="width: 16px;"></i>
        </button>
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
            <td colspan="5" class="text-center py-4">
              <span class="spinner-border spinner-border-sm"></span> Loading projects...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-4 border-top">
      <div class="custom-pagination mt-0" id="projects-pagination">
        <!-- Pagination will be handled dynamically -->
      </div>
    </div>

  </div>
</div>