<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 px-4 pt-4">
  <div>
    <h1 class="mb-1 fw-bold fs-3">Manage News & Posts</h1>
    <p class="mb-0 text-muted" style="font-size: 0.95rem;">Manage your blog and news articles</p>
  </div>
  <button class="btn btn-create-project d-flex align-items-center gap-2" onclick="loadView('create-post', 'Create New Post')">
    <i data-lucide="plus-circle" style="width: 18px;"></i> Create New Post
  </button>
</div>

<!-- Main Container -->
<div class="px-4 pb-4">
  <div class="content-card card-edge-table h-100 mb-0">
    <div class="projects-filter-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h3 class="section-title mb-0 fs-5">All Posts</h3>
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="search-bar" style="width: 280px;">
          <i data-lucide="search"></i>
          <input type="text" class="search-input" placeholder="Search Post Title...">
        </div>
      </div>
    </div>

    <div class="table-responsive mb-0">
      <table class="table table-borderless align-middle mb-0">
        <thead>
          <tr>
            <th>Post Title</th>
            <th>Author</th>
            <th>Status</th>
            <th>Publish Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="posts-tbody">
          <tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading posts...</td></tr>
        </tbody>
      </table>
    </div>
    <div class="p-4 border-top"><div class="custom-pagination mt-0" id="posts-pagination"></div></div>
  </div>
</div>