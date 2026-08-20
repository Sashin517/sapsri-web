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

<!-- Main Container -->
<div class="px-4 pb-4">
    <div class="content-card card-edge-table h-100 mb-0">
        <div class="projects-filter-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h3 class="section-title mb-0 fs-5">All Publications</h3>

            <div class="d-flex align-items-center gap-3 flex-wrap">

                <!-- Search -->
                <div class="search-bar" style="width: 280px;">
                    <i data-lucide="search"></i>
                    <input type="text" id="publication-search-input" class="search-input" placeholder="Search Title or Category...">
                </div>

                <!-- Status Filter Pills -->
                <div class="d-flex gap-2" id="publication-status-pills">
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
                    <button class="btn btn-light border d-flex align-items-center gap-2 rounded-3 fw-medium bg-white" type="button" id="pubDateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                        <span id="pub-date-filter-label">All Time</span>
                        <i data-lucide="chevron-down" style="width: 16px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="pubDateFilterDropdown" id="pub-date-filter-options">
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
                            <div id="pubCustomDateItem" class="dropdown-item d-flex justify-content-between align-items-center" data-range="custom">
                                <span>Custom</span>
                                <i data-lucide="chevron-right" style="width: 16px;"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Table -->
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

        <!-- Pagination Footer -->
        <div class="p-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-7">Rows:</span>
                <select id="publications-rows-per-page" class="form-select form-select-sm rows-select">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-muted fs-7 ms-2" id="publications-pagination-info-text">Showing 0-0 of 0</span>
            </div>

            <div id="publications-pagination">
                <!-- Dynamic pagination controls -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="viewModalEl">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body p-4 position-relative">
                <button type="button" class="btn-close position-absolute shadow-none" style="top: 16px; right: 16px;" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="container-fluid px-0">
                    <div class="row">
                        <div class="col-12 col-md-auto col-lg-auto col-xl-auto col-xxl-auto order-1 order-md-0 order-lg-0 order-xl-0 order-xxl-0">
                            <div class="card publication-card rounded-4 bg-light-pink" style="min-width: 12rem;">
                                <img src="" class="card-img-top rounded-top-4" alt="" style="height: 250px; object-fit: contain; background: #fff; padding: 5px;">
                                <div class="card-body">
                                    <h4 class="card-title fs-5">--</h4>
                                    <p class="card-text">--</p>
                                </div>
                                <div class="card-footer hstack justify-content-between align-items-end mt-auto border-top-0 bg-transparent">
                                    <time>--</time>

                                    <a href="#" class="wh-0 stretched-link"></a>

                                    <a href="#" class="btn btn-download z-3" download="">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="currentColor"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
                                            <path d="M352 96C352 78.3 337.7 64 320 64C302.3 64 288 78.3 288 96L288 306.7L246.6 265.3C234.1 252.8 213.8 252.8 201.3 265.3C188.8 277.8 188.8 298.1 201.3 310.6L297.3 406.6C309.8 419.1 330.1 419.1 342.6 406.6L438.6 310.6C451.1 298.1 451.1 277.8 438.6 265.3C426.1 252.8 405.8 252.8 393.3 265.3L352 306.7L352 96zM160 384C124.7 384 96 412.7 96 448L96 480C96 515.3 124.7 544 160 544L480 544C515.3 544 544 515.3 544 480L544 448C544 412.7 515.3 384 480 384L433.1 384L376.5 440.6C345.3 471.8 294.6 471.8 263.4 440.6L206.9 384L160 384zM464 440C477.3 440 488 450.7 488 464C488 477.3 477.3 488 464 488C450.7 488 440 477.3 440 464C440 450.7 450.7 440 464 440z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col w-100">
                            <div class="form-floating mb-3 mt-2">
                                <textarea class="form-control-plaintext" placeholder="Title" id="pubTitle" readonly style="height: auto; field-sizing: content;"></textarea>
                                <label for="pubTitle">Title</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control-plaintext" placeholder="Category" id="pubCategory" readonly style="height: auto; field-sizing: content;"></textarea>
                                <label for="pubCategory">Category</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control-plaintext" placeholder="Description" id="pubDescription" readonly style="height: auto; field-sizing: content;"></textarea>
                                <label for="pubDescription">Description</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" readonly class="form-control-plaintext" id="pubDate" placeholder="Publish Date">
                                <label for="pubDate">Publish Date</label>
                            </div>

                            <div class="form-floating mb-3 mb-md-0">
                                <input type="text" readonly class="form-control-plaintext" id="pubStatus" placeholder="Status">
                                <label for="pubStatus">Status</label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>