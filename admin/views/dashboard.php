<!-- Hero Banner with Dropdown -->
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
          <li><hr class="dropdown-divider"></li>
          <li class="position-relative">
            <!-- <div class="dropdown-item date-dropdown-item" id="customDateBtn" onclick="toggleCalendar(event)"> -->
            <div class="dropdown-item date-dropdown-item" id="customDateBtn">
              Custom <i data-lucide="chevron-right" style="width: 16px;"></i>
            </div>

            <!-- Custom Calendar Popover -->
            <div class="calendar-popover" id="calendarPopover">
              <div class="d-flex dual-cal-wrapper gap-4">

                <!-- October Calendar -->
                <div class="flex-grow-1 border-end pe-md-4">
                  <div class="cal-header-row">
                    <i data-lucide="chevron-left" style="cursor:pointer;"></i>
                    <span>October 2026</span>
                    <div style="width:24px;"></div>
                  </div>
                  <hr>
                  <div class="cal-grid">
                    <div class="cal-day-name">Mo</div><div class="cal-day-name">Tu</div><div class="cal-day-name">We</div>
                    <div class="cal-day-name">Th</div><div class="cal-day-name">Fr</div><div class="cal-day-name">Sa</div><div class="cal-day-name">Su</div>

                    <div class="cal-day muted">1</div><div class="cal-day muted">2</div><div class="cal-day muted">3</div>
                    <div class="cal-day muted">4</div><div class="cal-day muted">5</div><div class="cal-day muted">6</div>
                    <div class="cal-day muted">7</div><div class="cal-day muted">8</div><div class="cal-day muted">9</div>
                    <div class="cal-day muted">10</div><div class="cal-day muted">11</div><div class="cal-day muted">12</div>
                    <div class="cal-day muted">13</div><div class="cal-day muted">14</div><div class="cal-day muted">15</div>
                    <div class="cal-day muted">16</div><div class="cal-day muted">17</div><div class="cal-day muted">18</div>
                    <div class="cal-day muted">19</div><div class="cal-day muted">20</div>

                    <div class="cal-range-start"><div class="cal-day">21</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">22</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">23</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">24</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">25</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">26</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">27</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">28</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">29</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">30</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">31</div></div>
                  </div>
                </div>

                <!-- November Calendar -->
                <div class="flex-grow-1">
                  <div class="cal-header-row">
                    <div style="width:24px;"></div>
                    <span>November 2026</span>
                    <i data-lucide="chevron-right" style="cursor:pointer;"></i>
                  </div>
                  <hr>
                  <div class="cal-grid">
                    <div class="cal-day-name">Mo</div><div class="cal-day-name">Tu</div><div class="cal-day-name">We</div>
                    <div class="cal-day-name">Th</div><div class="cal-day-name">Fr</div><div class="cal-day-name">Sa</div><div class="cal-day-name">Su</div>

                    <div class="cal-range-bg"><div class="cal-day text-dark">1</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">2</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">3</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">4</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">5</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">6</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">7</div></div>
                    <div class="cal-range-bg"><div class="cal-day text-dark">8</div></div>
                    <div class="cal-range-end"><div class="cal-day fw-bold">9</div></div>

                    <div class="cal-day">10</div><div class="cal-day">11</div>
                    <div class="cal-day">12</div><div class="cal-day">13</div><div class="cal-day">14</div>
                    <div class="cal-day">15</div><div class="cal-day">16</div><div class="cal-day">17</div><div class="cal-day">18</div>
                    <div class="cal-day">19</div><div class="cal-day">20</div><div class="cal-day">21</div>
                    <div class="cal-day">22</div><div class="cal-day">23</div><div class="cal-day">24</div><div class="cal-day">25</div>
                    <div class="cal-day">26</div><div class="cal-day">27</div><div class="cal-day">28</div>
                    <div class="cal-day">29</div><div class="cal-day">30</div>
                  </div>
                </div>
              </div>

              <hr class="my-3">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">16 days</span>
                <div>
                  <button class="btn btn-light border px-4 rounded-3 me-2" onclick="toggleCalendar(event)">Cancel</button>
                  <button class="btn px-4 rounded-3 text-white" style="background-color: var(--cal-active-bg);">Done</button>
                </div>
              </div>
            </div>
            <!-- End Custom Calendar -->

          </li>
        </ul>
      </div>
    </div>

    <!-- Dashboard Content (merged in from the KPI/table/quick-actions build) -->
    <div class="dashboard-container">

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
                  <tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading content...</td></tr>
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

            <a href="#" class="quick-action-card">
              <i data-lucide="briefcase" class="quick-action-icon"></i>
              <span class="quick-action-text">Create New Project</span>
            </a>

            <a href="#" class="quick-action-card">
              <i data-lucide="newspaper" class="quick-action-icon"></i>
              <span class="quick-action-text">Create New Post</span>
            </a>

            <a href="#" class="quick-action-card">
              <i data-lucide="book-open" class="quick-action-icon"></i>
              <span class="quick-action-text">Add New Publication</span>
            </a>
          </div>
        </div>