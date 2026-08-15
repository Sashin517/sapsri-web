<?php
session_start();

// Redirect to login if the session is not set
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

// Assign session variables for use in the HTML below
$adminName = $_SESSION['admin_name'] ?? 'Admin User';
$adminRole = $_SESSION['admin_role_name'] ?? 'User';
// Note: You can query the roles table to get the actual text name of the role later.
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SAPSRI Admin | Dashboard</title>

  <!-- Date Range Picker -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/project-sedna/vendor/bootstrap/bootstrap-custom.css">
  <link rel="stylesheet" href="/project-sedna/vendor/daterangepicker/daterangepicker-bs5.css">
  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root {
      --sapsri-red: #A20A35;
      --sapsri-red-light: #F9E7EC;
      --bg-light-gray: #F8F9FA;
      --border-color: #E9ECEF;
      --text-dark: #1A1A1A;
      --text-muted: #6C757D;
      --sidebar-width: 260px;
      --sidebar-collapsed-width: 80px;

      /* Calendar Colors */
      --cal-selected-bg: #C1D0E4;
      --cal-active-bg: #214F94;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg-light-gray);
      color: var(--text-dark);
      overflow-x: hidden;
    }

    /* --- Sidebar Styling & Transitions --- */
    .sidebar {
      width: var(--sidebar-width);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      background-color: #ffffff;
      border-right: 1px solid var(--border-color);
      z-index: 1050;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
    }

    .sidebar-header {
      padding: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 80px;
    }

    .logo-container {
      display: flex;
      align-items: center;
      height: 40px;
      cursor: pointer;
      position: relative;
    }

    .sidebar-logo {
      height: 32px;
      transition: opacity 0.2s ease;
    }

    /* Hover effect for collapsed logo */
    .logo-expand-hint {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.2s ease;
      color: var(--sapsri-red);
    }

    .sidebar.collapsed .logo-container:hover .logo-expand-hint {
      opacity: 1;
    }

    .toggle-sidebar-btn {
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .nav-menu {
      padding: 1rem;
      list-style: none;
      margin: 0;
      flex-grow: 1;
      overflow-y: auto;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      color: var(--text-muted);
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
      text-decoration: none;
      white-space: nowrap;
      margin-bottom: 0.5rem;
    }

    .nav-link i,
    .nav-link svg {
      min-width: 20px;
      margin-right: 0.75rem;
      transition: margin 0.3s ease;
    }

    .nav-link:hover {
      background-color: var(--sapsri-red-light);
      color: var(--sapsri-red);
    }

    .nav-link.active-primary {
      background-color: var(--sapsri-red);
      color: #ffffff;
    }

    .nav-link.active-secondary {
      background-color: var(--sapsri-red-light);
      color: var(--sapsri-red);
    }

    /* Collapsed State Styles */
    .sidebar.collapsed {
      width: var(--sidebar-collapsed-width);
    }

    .sidebar.collapsed .nav-link-text {
      display: none;
    }

    .sidebar.collapsed .nav-link {
      justify-content: center;
      padding: 0.75rem 0;
    }

    .sidebar.collapsed .nav-link i,
    .sidebar.collapsed .nav-link svg {
      margin-right: 0;
    }

    .sidebar.collapsed .sidebar-header {
      justify-content: center;
      padding: 1.5rem 0;
    }

    .sidebar.collapsed .toggle-sidebar-btn {
      display: none;
      /* Hide standard toggle when collapsed, rely on logo hover */
    }

    /* --- Main Content Area --- */
    .main-content {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: margin-left 0.3s ease;
    }

    .main-content.expanded {
      margin-left: var(--sidebar-collapsed-width);
    }

    /* --- Top Navbar --- */
    .top-navbar {
      height: 80px;
      background-color: var(--bg-light-gray);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--text-dark);
      cursor: pointer;
    }

    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0;
    }

    .search-bar {
      position: relative;
      width: 350px;
    }

    .search-bar svg {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      width: 18px;
      height: 18px;
    }

    .search-input {
      width: 100%;
      padding: 0.6rem 1rem 0.6rem 2.5rem;
      border-radius: 50px;
      border: 1px solid var(--border-color);
      background-color: #ffffff;
      font-size: 0.9rem;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--sapsri-red);
      box-shadow: 0 0 0 3px rgba(162, 10, 53, 0.1);
    }

    .user-profile-btn {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      background: #ffffff;
      border: 1px solid var(--border-color);
      padding: 0.4rem 0.75rem;
      border-radius: 50px;
      cursor: pointer;
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
    }

    .notification-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #ffffff;
      border: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-dark);
      cursor: pointer;
    }

    /* --- Hero Banner --- */
    .hero-banner {
      background-color: var(--sapsri-red);
      background-image: url('assets/img/sapsri-fluid-bg.png');
      background-size: cover;
      background-position: center;
      border-radius: 12px;
      padding: 2rem;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 0 2rem 2rem 2rem;
      position: relative;
    }

    /* --- Custom Date Picker & Dropdown --- */
    .date-dropdown-menu {
      width: 200px;
      padding: 0.5rem 0;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: none;
    }

    .date-dropdown-item {
      padding: 0.75rem 1.5rem;
      color: var(--text-dark);
      font-weight: 500;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .date-dropdown-item:hover {
      background-color: #f1f5f9;
    }

    /* Calendar Popover */
    .calendar-popover {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 10px;
      background: #F4F5F7;
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      padding: 1.5rem;
      z-index: 1060;
      width: 650px;
      /* Dual calendar width */
    }

    .calendar-popover.show {
      display: block;
    }

    .cal-header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .cal-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      text-align: center;
      gap: 4px;
      font-size: 0.9rem;
    }

    .cal-day-name {
      color: var(--text-muted);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .cal-day {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      cursor: pointer;
      margin: auto;
    }

    .cal-day.muted {
      color: #ADB5BD;
    }

    /* Range Selection Styles */
    .cal-range-bg {
      background-color: var(--cal-selected-bg);
      border-radius: 0;
      width: 100%;
    }

    .cal-range-start {
      background-color: var(--cal-active-bg);
      color: white;
      border-radius: 50%;
      position: relative;
    }

    .cal-range-start::after {
      content: '';
      position: absolute;
      right: -4px;
      top: 0;
      width: 50%;
      height: 100%;
      background: var(--cal-selected-bg);
      z-index: -1;
    }

    .cal-range-end {
      background-color: #ffffff;
      color: var(--text-dark);
      border: 2px solid var(--cal-active-bg);
      border-radius: 50%;
      position: relative;
    }

    .cal-range-end::before {
      content: '';
      position: absolute;
      left: -4px;
      top: -2px;
      width: 50%;
      height: calc(100% + 4px);
      background: var(--cal-selected-bg);
      z-index: -1;
    }

    /* --- Dashboard Cards (from content build) --- */
    .dashboard-container {
      padding: 0 2rem 2rem 2rem;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      border: 1px solid var(--border-color);
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .kpi-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .kpi-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--text-dark);
      margin: 0;
    }

    .kpi-icon-wrapper {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background-color: var(--bg-light-gray);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-dark);
    }

    .kpi-value-row {
      display: flex;
      align-items: baseline;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
    }

    .kpi-value {
      font-size: 2.5rem;
      font-weight: 700;
      margin: 0;
      line-height: 1;
    }

    .kpi-value .spinner-border {
      font-size: 0.75rem;
      vertical-align: middle;
    }

    .badge-trend {
      background-color: #D1F4E0;
      color: #147A42;
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .kpi-footer {
      border-top: 1px solid var(--border-color);
      padding-top: 1rem;
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: auto;
    }

    /* --- Table Styling --- */
    .content-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .section-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .table th {
      background-color: #EFEFEF;
      color: var(--text-dark);
      font-weight: 600;
      font-size: 0.9rem;
      padding: 1rem;
      border-bottom: none;
      vertical-align: middle;
      /* add this line */
    }

    .table th:first-child {
      border-top-left-radius: 8px;
      border-bottom-left-radius: 8px;
    }

    .table th:last-child {
      border-top-right-radius: 8px;
      border-bottom-right-radius: 8px;
    }

    .table td {
      padding: 1rem;
      vertical-align: middle;
      font-size: 0.95rem;
      border-bottom: 1px solid var(--border-color);
    }

    /* Pill Badges */
    .status-pill {
      padding: 0.35rem 0.75rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-block;
    }

    .status-published {
      background-color: #D1F4E0;
      color: #147A42;
    }

    .status-draft {
      background-color: #FEF3C7;
      color: #B45309;
    }

    /* Custom Pagination */
    .custom-pagination {
      display: flex;
      gap: 0.25rem;
      justify-content: center;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }

    .page-btn {
      padding: 0.4rem 0.75rem;
      border: 1px solid var(--border-color);
      background: #ffffff;
      border-radius: 6px;
      color: var(--text-dark);
      font-size: 0.9rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.25rem;
      text-decoration: none;
    }

    .page-btn.active {
      background-color: var(--sapsri-red);
      color: #ffffff;
      border-color: var(--sapsri-red);
    }

    .page-btn:hover:not(.active) {
      background-color: var(--bg-light-gray);
    }

    /* --- Quick Actions --- */
    .quick-action-card {
      cursor: pointer;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.25rem;
      margin-bottom: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      background-color: #ffffff;
      transition: box-shadow 0.2s ease;
      text-decoration: none;
    }

    .quick-action-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .quick-action-icon {
      color: var(--sapsri-red);
    }

    .quick-action-text {
      color: var(--sapsri-red);
      font-weight: 600;
      font-size: 0.95rem;
    }

    .page-btn.disabled {
      opacity: 0.5;
      pointer-events: none;
      background-color: var(--bg-light-gray);
      color: var(--text-muted);
      border-color: var(--border-color);
    }

    /* --- Projects View Specific Styles --- */
    .filter-pill {
      border: 1px solid var(--border-color);
      background-color: #ffffff;
      color: var(--text-dark);
      border-radius: 8px;
      padding: 0.4rem 1rem;
      font-size: 0.9rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .filter-pill:hover {
      background-color: var(--bg-light-gray);
    }

    .filter-pill.active {
      background-color: var(--sapsri-red-light);
      color: var(--sapsri-red);
      border-color: var(--sapsri-red);
    }

    .btn-create-project {
      border: 1px solid var(--text-dark);
      background-color: #ffffff;
      color: var(--text-dark);
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .btn-create-project:hover {
      background-color: var(--bg-light-gray);
    }

    /* Edge-to-edge table overrides for full-width cards */
    .card-edge-table {
      padding: 0 !important;
      overflow: hidden;
    }

    .card-edge-table .table th {
      border-radius: 0 !important;
      /* Removes the rounded corners from the dashboard table headers */
      background-color: #E2E2E2;
      /* Slightly darker gray to match the mockup */
      padding: 1rem 1.5rem;
    }

    .card-edge-table .table td {
      padding: 1rem 1.5rem;
    }

    .projects-filter-bar {
      padding: 1.5rem;
      border-bottom: 1px solid var(--border-color);
    }

    /* --- Responsive Queries --- */
    @media (max-width: 991px) {
      .sidebar {
        transform: translateX(-100%);
        /* Hide completely off-screen on mobile */
        width: var(--sidebar-width);
      }

      .sidebar.mobile-open {
        transform: translateX(0);
      }

      .main-content,
      .main-content.expanded {
        margin-left: 0;
      }

      .mobile-menu-btn {
        display: block;
      }

      .calendar-popover {
        width: 320px;
        /* Single column on mobile */
        right: -50px;
      }

      .dual-cal-wrapper {
        flex-direction: column;
        gap: 1.5rem;
      }

      .hero-banner {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .dashboard-container {
        padding: 0 1rem 1.5rem 1rem;
      }

      .top-navbar {
        padding: 0 1rem;
      }

      .hero-banner {
        margin: 0 1rem 1.5rem 1rem;
      }
    }

    @media (max-width: 575px) {
      .content-card {
        padding: 1rem;
      }

      .table-responsive {
        font-size: 0.85rem;
      }
    }
  </style>

</head>

<body>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="logo-container" id="logo-container" onclick="toggleSidebar()">
        <!-- Default Logo -->
        <img id="sidebar-logo" src="assets/img/sapsri-logo.png" alt="SAPSRI" class="sidebar-logo">
        <!-- Hover Hint when collapsed -->
        <div class="logo-expand-hint">
          <i data-lucide="panel-left-open"></i>
        </div>
      </div>
      <button class="toggle-sidebar-btn" onclick="toggleSidebar()">
        <i data-lucide="panel-left-close"></i>
      </button>
    </div>

    <ul class="nav-menu">
      <li>
        <a href="#" class="nav-link spa-link active-primary" data-view="dashboard" data-title="Dashboard">
          <i data-lucide="layout-dashboard"></i> <span class="nav-link-text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="projects" data-title="Projects Management">
          <i data-lucide="folder"></i> <span class="nav-link-text">Projects</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="posts" data-title="Posts Management">
          <i data-lucide="file-text"></i> <span class="nav-link-text">Posts</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="publications" data-title="Publications">
          <i data-lucide="book-open"></i> <span class="nav-link-text">Publications</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="users" data-title="User Management">
          <i data-lucide="users"></i> <span class="nav-link-text">User Management</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- Overlay for mobile sidebar -->
  <div id="mobile-overlay" class="position-fixed w-100 h-100 bg-dark opacity-50 d-none" style="z-index: 1040; top:0; left:0;" onclick="toggleMobileMenu()"></div>

  <!-- Main Content -->
  <main class="main-content" id="main-content">

    <!-- Top Navbar -->
    <header class="top-navbar">
      <h2 class="page-title" id="page-title">Dashboard</h2>

      <div class="d-flex align-items-center gap-3">
        <div class="search-bar">
          <i data-lucide="search"></i>
          <input type="text" class="search-input" placeholder="Search...">
        </div>

        <button class="notification-btn border-0 shadow-sm">
          <i data-lucide="bell"></i>
        </button>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
          <div class="user-profile-btn shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            <img src="/project-sedna/admin/assets/img/default-user-avatar.svg" alt="<?php echo htmlspecialchars($adminName); ?>" class="user-avatar">
            <div class="d-flex flex-column text-start">
              <span style="font-size: 0.85rem; font-weight: 600; line-height: 1;"><?php echo htmlspecialchars($adminName); ?></span>
              <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($adminRole); ?></span>
            </div>
            <i data-lucide="chevron-down" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
          </div>

          <!-- Dropdown Menu -->
          <ul class="dropdown-menu date-dropdown-menu dropdown-menu-end mt-2 border-0 shadow">
            <li>
              <a class="dropdown-item date-dropdown-item d-flex justify-content-start align-items-center gap-2" href="#">
                <i data-lucide="settings" style="width: 18px; height: 18px;"></i> Account Settings
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item date-dropdown-item d-flex justify-content-start align-items-center gap-2 text-danger" href="actions/auth/logout.php">
                <i data-lucide="log-out" style="width: 18px; height: 18px;"></i> Log Out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- MAIN SPA INJECTION CONTAINER -->
    <div id="app-content">
      <!-- Views will be loaded here dynamically -->

    </div>
    </div>
    <!-- End Dashboard Content -->

  </main>

  <!-- Bootstrap Bundle with Popper (Required for dropdowns) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- 1. Global Layout & Sidebar Logic (Remains Intact) -->
  <script>
    // Initialize Icons for static elements
    lucide.createIcons();

    // Global Elements
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarLogo = document.getElementById('sidebar-logo');
    const mobileOverlay = document.getElementById('mobile-overlay');

    // Logic: Toggle Sidebar (Desktop)
    function toggleSidebar() {
      if (window.innerWidth <= 991) return;
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
      if (sidebar.classList.contains('collapsed')) {
        sidebarLogo.src = 'assets/img/sapsri-logo-icon.png';
      } else {
        sidebarLogo.src = 'assets/img/sapsri-logo.png';
      }
    }

    // Logic: Toggle Sidebar (Mobile)
    function toggleMobileMenu() {
      sidebar.classList.toggle('mobile-open');
      if (sidebar.classList.contains('mobile-open')) {
        mobileOverlay.classList.remove('d-none');
        sidebarLogo.src = 'assets/img/sapsri-logo.png';
      } else {
        mobileOverlay.classList.add('d-none');
      }
    }
  </script>

  <!-- 2. SPA Routing & Module Initialization -->
  <script>
    // Pass PHP session variables safely to JavaScript
    const sessionData = {
      userName: "<?php echo addslashes($adminName); ?>",
      userRole: "<?php echo addslashes($adminRole); ?>"
    };

    document.addEventListener('DOMContentLoaded', () => {

      const appContent = document.getElementById('app-content');
      const pageTitle = document.getElementById('page-title');

      // --- SPA ROUTER FUNCTION ---
      async function loadView(viewName, title, params = {}) {

        // Show loader
        appContent.innerHTML = '<div class="d-flex justify-content-center mt-5"><div class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status"></div></div>';

        try {
          const response = await fetch(`views/${viewName}.php`);
          if (!response.ok) throw new Error('View not found');

          const html = await response.text();
          appContent.innerHTML = html;

          if (pageTitle) pageTitle.innerText = title;
          lucide.createIcons(); // Re-initialize icons for newly injected HTML

          // Initialize specific scripts based on the loaded view
          if (viewName === 'dashboard') {
            initDashboardScript();
          } else if (viewName === 'projects') {
            initProjectsScript();
          } else if (viewName === 'posts') {
            initPostsScript();
          } else if (viewName === 'publications') {
            initPublicationsScript();
          } else if (viewName === 'users') {
            initUsersScript();
          } else if (viewName === 'create-project') {
            initCreateProjectScript();
          } else if (viewName === 'create-post') {
            initCreatePostScript();
          } else if (viewName === 'edit-post') {
            initEditPostScript(params);
          } else if (viewName === 'create-publication') {
            initCreatePublicationScript();
          }

        } catch (error) {
          appContent.innerHTML = `<div class="alert alert-danger m-4">Failed to load module: ${viewName}. Please ensure views/${viewName}.php exists.</div>`;
          console.error(error);
        }
      }

      window.loadView = loadView;

      // --- SIDEBAR NAVIGATION HANDLER ---
      const navLinks = document.querySelectorAll('.spa-link');

      navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();

          // Manage Active States
          navLinks.forEach(l => l.classList.remove('active-primary'));
          e.currentTarget.classList.add('active-primary');

          // Get routing data from the clicked link
          const targetView = e.currentTarget.getAttribute('data-view');
          const targetTitle = e.currentTarget.getAttribute('data-title');

          // Load the view
          loadView(targetView, targetTitle);

          // Close mobile sidebar automatically after clicking a link
          if (window.innerWidth <= 991) {
            toggleMobileMenu();
          }
        });
      });

      // Load default view on start
      loadView('dashboard', 'Dashboard');


      // ==========================================
      // --- VIEW: DASHBOARD LOGIC ---
      // ==========================================
      function initDashboardScript() {

        // 1. Set dynamic user name
        const nameEl = document.getElementById('dyn-user-name');
        if (nameEl) nameEl.innerText = sessionData.userName;

        // 2. Calendar Popover Logic
        // const customDateBtn = document.getElementById('customDateBtn');
        // const calendarPopover = document.getElementById('calendarPopover');


        // if (customDateBtn && calendarPopover) {
        //   customDateBtn.addEventListener('click', (event) => {
        //     event.stopPropagation();
        //     calendarPopover.classList.toggle('show');
        //   });

        //   document.addEventListener('click', function(event) {
        //     if (!calendarPopover.contains(event.target) && event.target !== customDateBtn) {
        //       calendarPopover.classList.remove('show');
        //     }
        //   });
        // }

        // 3. KPI Fetcher
        function fetchDashboardStats(range = 'today', start = '', end = '') {
          const modules = ['projects', 'publications', 'users', 'posts'];

          const params = new URLSearchParams({
            range: range,
            start: start,
            end: end
          });

          // Set loading state
          modules.forEach(mod => {
            if (document.getElementById(`val-${mod}`)) {
              document.getElementById(`val-${mod}`).innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }
          });

          fetch(`actions/dashboard/fetch-stats.php?${params.toString()}`)
            .then(res => res.json())
            .then(result => {
              if (result.status === 'success') {
                modules.forEach(mod => {
                  if (document.getElementById(`val-${mod}`)) {
                    document.getElementById(`val-${mod}`).innerText = result.data[mod].count;
                    document.getElementById(`trend-${mod}`).innerText = result.data[mod].trend;
                    document.getElementById(`trend-${mod}`).className = `badge-trend status-pill ${result.data[mod].trend_class}`;
                    document.getElementById(`desc-${mod}`).innerText = result.period_text;
                  }
                });
              }
            }).catch(err => console.error("KPI Error:", err));
        }

        // 4. Recent Content Table Fetcher
        window.loadRecentContent = function(page = 1) {
          const tbody = document.getElementById('recent-content-tbody');
          if (!tbody) return;

          tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading content...</td></tr>';

          fetch(`actions/dashboard/fetch-recent-content.php?page=${page}`)
            .then(res => res.json())
            .then(result => {
              if (result.status === 'success') {
                tbody.innerHTML = '';
                if (result.data.length === 0) {
                  tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No recent content found.</td></tr>';
                } else {
                  result.data.forEach(item => {
                    let pillClass = ['published', 'completed', 'ongoing', 'active'].includes(item.status.toLowerCase()) ? 'status-published' : 'status-draft';
                    tbody.insertAdjacentHTML('beforeend', `<tr><td class="fw-medium">${item.title}</td><td>${item.type}</td><td><span class="status-pill ${pillClass}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td><td>${item.lead}</td><td>${item.created_date}</td></tr>`);
                  });
                }
                renderPagination(result.pagination);
                lucide.createIcons();
              }
            }).catch(err => console.error("Table Error:", err));
        }

        // 5. Pagination Renderer
        function renderPagination(pagination) {
          const pDiv = document.getElementById('recent-content-pagination');
          if (!pDiv) return;
          pDiv.innerHTML = '';

          const {
            current_page,
            total_pages
          } = pagination;
          if (total_pages <= 1) return;

          const prevDisabled = current_page === 1 ? 'disabled' : '';
          pDiv.innerHTML += `<a href="#" class="page-btn ${prevDisabled}" onclick="event.preventDefault(); loadRecentContent(1)"><i data-lucide="chevrons-left" style="width:16px;"></i> First</a>`;
          pDiv.innerHTML += `<a href="#" class="page-btn ${prevDisabled}" onclick="event.preventDefault(); loadRecentContent(${current_page - 1})"><i data-lucide="chevron-left" style="width:16px;"></i> Back</a>`;

          let startPage = Math.max(1, current_page - 1);
          let endPage = Math.min(total_pages, current_page + 1);

          if (startPage > 1) pDiv.innerHTML += `<span class="d-flex align-items-end px-1">...</span>`;
          for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === current_page ? 'active' : '';
            pDiv.innerHTML += `<a href="#" class="page-btn ${activeClass}" onclick="event.preventDefault(); loadRecentContent(${i})">${i}</a>`;
          }
          if (endPage < total_pages) pDiv.innerHTML += `<span class="d-flex align-items-end px-1">...</span>`;

          const nextDisabled = current_page === total_pages ? 'disabled' : '';
          pDiv.innerHTML += `<a href="#" class="page-btn ${nextDisabled}" onclick="event.preventDefault(); loadRecentContent(${current_page + 1})">Next <i data-lucide="chevron-right" style="width:16px;"></i></a>`;
          pDiv.innerHTML += `<a href="#" class="page-btn ${nextDisabled}" onclick="event.preventDefault(); loadRecentContent(${total_pages})">Last <i data-lucide="chevrons-right" style="width:16px;"></i></a>`;
        }

        // 6. Bind Date Dropdown Filters
        const dpBtn = document.getElementById('dateDropdownBtn');
        document.querySelectorAll('.date-filter').forEach(item => {
          item.addEventListener('click', (e) => {
            e.preventDefault();
            const range = e.target.getAttribute('data-range');
            dpBtn.innerHTML = `${e.target.innerText} <i data-lucide="chevron-down" style="width: 16px;"></i>`;
            lucide.createIcons();
            resetDateRangePicker();
            fetchDashboardStats(range);
          });
        });

        const customDateBtn = document.getElementById('customDateBtn');
        customDateBtn.addEventListener('click', (event) => {
          event.stopPropagation();
        });

        $(function() {
          $(customDateBtn).daterangepicker({
            opens: 'left'
          }, function(start, end) {
            const startDate = start.format('YYYY-MM-DD');
            const endDate = end.format('YYYY-MM-DD');

            dpBtn.innerHTML = `${startDate} - ${endDate} <i data-lucide="chevron-down" style="width: 16px;"></i>`;
            lucide.createIcons();

            fetchDashboardStats('custom', startDate, endDate);
          });

          $(customDateBtn).on('show.daterangepicker', function(ev, picker) {
            picker.container.find('.drp-calendar').on('click', function(e) {
              e.stopPropagation();
            });
          });
        });

        // 7. Initial Data Load for Dashboard
        fetchDashboardStats('today');
        loadRecentContent(1);

        function resetDateRangePicker() {
          const today = moment();
          const $button = $(customDateBtn);
          const picker = $button.data('daterangepicker');
          if (picker) {
            picker.setStartDate(today);
            picker.setEndDate(today);
          }
        }
      }
      // ---> Notice the Dashboard function cleanly ends here now! <---


      // ==========================================
      // --- VIEW: PROJECTS LOGIC ---
      // ==========================================
      function initProjectsScript() {
        window.loadProjects = function() {
          const tbody = document.getElementById('projects-tbody');
          if (!tbody) return;

          tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading projects...</td></tr>';

          fetch('actions/projects/fetch-projects.php')
            .then(res => res.json())
            .then(data => {
              const projects = Array.isArray(data) ? data : Object.values(data);
              tbody.innerHTML = '';

              if (projects.length === 0 || projects[0].error) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No projects found.</td></tr>';
                return;
              }

              projects.forEach(item => {
                let pillClass = (item.status || '').toLowerCase() === 'published' ? 'status-published' : 'status-draft';
                let createdDate = item.created_date ? item.created_date.split(' ')[0] : 'N/A';

                // Format the phase visually
                let phaseText = item.project_phase === 'past' ? '<span class="text-secondary fw-medium">Past</span>' : '<span class="text-success fw-medium">Ongoing</span>';

                const row = `
                  <tr>
                    <td class="fw-medium">${item.title}</td>
                    <td>${phaseText}</td> <!-- NEW PHASE COLUMN INJECTED -->
                    <td><span class="status-pill ${pillClass}">${(item.status || 'Draft').charAt(0).toUpperCase() + (item.status || 'draft').slice(1)}</span></td>
                    <td>${item.project_lead || 'System'}</td>
                    <td>${createdDate}</td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit"><i data-lucide="edit" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                    </td>
                  </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
              });
              lucide.createIcons();
            })
            .catch(err => {
              tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load projects.</td></tr>';
            });
        };
        loadProjects();
      }

      // ==========================================
      // --- VIEW: CREATE PROJECT LOGIC ---
      // ==========================================
      function initCreateProjectScript() {

        // Note for backend integration:
        // Final form submission will route files to:
        // Images -> ../../assets/media/img/projects
        // Videos -> ../../assets/media/videos/projects

        // 1. Toggle Phase Label & End Date Visibility
        const phaseToggle = document.getElementById('projectPhaseToggle');
        const phaseLabel = document.getElementById('phaseLabel');
        const startDateContainer = document.getElementById('startDateContainer');
        const endDateContainer = document.getElementById('endDateContainer');

        if (phaseToggle) {
          phaseToggle.addEventListener('change', (e) => {
            // Update Label text and color
            phaseLabel.innerText = e.target.checked ? 'Ongoing' : 'Past';
            phaseLabel.className = e.target.checked ? 'form-check-label fw-medium ms-2 text-success' : 'form-check-label fw-medium ms-2 text-secondary';

            // Handle Grid Widths and Visibility
            if (endDateContainer && startDateContainer) {
              if (e.target.checked) {
                // Ongoing State: Start Date takes full width (12 cols)
                startDateContainer.classList.remove('col-md-6');
                startDateContainer.classList.add('col-md-12');
                endDateContainer.style.display = 'none';

                // Clear the end date just in case
                document.getElementById('projectEndDate').value = '';
              } else {
                // Past State: Start Date shrinks to half width (6 cols) to make room
                startDateContainer.classList.remove('col-md-12');
                startDateContainer.classList.add('col-md-6');
                endDateContainer.style.display = 'block';
              }
            }
          });
        }

        // 2. Initialize Quill Rich Text Editor
        if (document.getElementById('editor')) {

          // Import and map the icons BEFORE initializing Quill
          const Icons = Quill.import('ui/icons');
          Icons.undo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
          Icons.redo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

          var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
              // The history module MUST be enabled for undo/redo to function
              history: {
                delay: 1000,
                maxStack: 100,
                userOnly: true
              },
              toolbar: {
                container: [
                  ['undo', 'redo'],
                  [{
                    'size': ['small', false, 'large', 'huge']
                  }],
                  ['bold', 'italic', 'underline', 'strike'],
                  [{
                    'align': ''
                  }, {
                    'align': 'center'
                  }, {
                    'align': 'right'
                  }, {
                    'align': 'justify'
                  }],
                  [{
                    'list': 'ordered'
                  }, {
                    'list': 'bullet'
                  }],
                  ['link']
                ],
                // Explicitly tell the toolbar what 'undo' and 'redo' should do
                handlers: {
                  'undo': function() {
                    this.quill.history.undo();
                  },
                  'redo': function() {
                    this.quill.history.redo();
                  }
                }
              }
            }
          });
        }

        // 3. Upload Progress Simulation Logic
        function simulateUpload(idPrefix, callback) {
          const contentDiv = document.getElementById(idPrefix + 'Content');
          const progressDiv = document.getElementById(idPrefix + 'Progress');
          const progressBar = document.getElementById(idPrefix + 'ProgressBar');
          const progressText = document.getElementById(idPrefix + 'ProgressText');

          if (!contentDiv || !progressDiv) {
            callback();
            return;
          } // Fallback if HTML wrapper missing

          // State 1: Uploading
          contentDiv.style.display = 'none';
          progressDiv.style.display = 'block';
          progressBar.className = 'progress-fill';
          progressBar.style.width = '0%';
          progressText.className = 'upload-status-text';

          let progress = 0;
          const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress >= 100) progress = 100;

            progressBar.style.width = progress + '%';
            progressText.innerText = `Uploading... ${Math.round(progress)}%`;

            if (progress === 100) {
              clearInterval(interval);
              // State 2: Success
              progressBar.classList.add('success');
              progressText.classList.add('success');
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
              lucide.createIcons();

              // State 3: Show Preview after delay
              setTimeout(() => {
                progressDiv.style.display = 'none';
                callback();
              }, 800);
            }
          }, 100);
        }

        // 4. Handle Standard Image Uploads (Cover, Sections, Subject, Lead)
        window.handleImageUpload = function(input, idPrefix) {
          if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
              simulateUpload(idPrefix, () => {
                document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'block';
                document.getElementById(idPrefix + 'PreviewImg').src = e.target.result;
              });
            }
            reader.readAsDataURL(input.files[0]);
          }
        };

        window.removeImage = function(event, idPrefix) {
          event.stopPropagation();
          document.getElementById(idPrefix + 'Input').value = "";
          document.getElementById(idPrefix + 'PreviewImg').src = "";
          document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'none';
          document.getElementById(idPrefix + 'Content').style.display = 'block';
        };

        // 5. Dynamic Metrics Logic (Drag & Drop + Icon Preview)
        let metricsCount = {
          1: 0,
          2: 0
        };

        window.addMetricRow = function(sectionId) {
          if (metricsCount[sectionId] >= 3) return;
          metricsCount[sectionId]++;
          const container = document.getElementById(`metricsContainerSec${sectionId}`);
          const rowId = `sec${sectionId}_metric${metricsCount[sectionId]}`;

          const rowHTML = `
            <div class="metric-row" id="row_${rowId}" draggable="true">
              <div class="drag-handle"><i data-lucide="grip-vertical" class="text-muted"></i></div>
              
              <!-- Mini Icon Upload -->
              <div class="metric-icon-box" onclick="document.getElementById('icon_${rowId}').click()">
                <i data-lucide="upload" id="icon_ph_${rowId}" class="text-muted" style="width:18px;"></i>
                <img src="" id="icon_preview_${rowId}">
                <button type="button" class="remove-icon-btn" onclick="removeMetricIcon(event, '${rowId}')"><i data-lucide="x" style="width:12px;"></i></button>
              </div>
              <input type="file" id="icon_${rowId}" class="d-none" accept="image/png" onchange="handleMetricIconUpload(this, '${rowId}')">
              
              <input type="text" class="form-control bg-white" placeholder="Metric Value (e.g. 1600+)">
              <input type="text" class="form-control bg-white" placeholder="Metric Label (e.g. Beneficiaries)">
              
              <button type="button" class="btn btn-link text-danger p-0 ms-auto me-2" onclick="removeMetricRow(${sectionId}, 'row_${rowId}')">
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          `;
          container.insertAdjacentHTML('beforeend', rowHTML);
          lucide.createIcons();
          initDragAndDrop(container);

          if (metricsCount[sectionId] >= 3) {
            document.getElementById(`addMetricSec${sectionId}Btn`).disabled = true;
          }
        };

        window.removeMetricRow = function(sectionId, rowId) {
          document.getElementById(rowId).remove();
          metricsCount[sectionId]--;
          document.getElementById(`addMetricSec${sectionId}Btn`).disabled = false;
        };

        window.handleMetricIconUpload = function(input, rowId) {
          if (input.files && input.files[0]) {
            if (input.files[0].type !== 'image/png') {
              alert("Only PNG images are allowed for icons.");
              return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('icon_ph_' + rowId).style.display = 'none';
              const img = document.getElementById('icon_preview_' + rowId);
              img.src = e.target.result;
              img.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
          }
        }

        window.removeMetricIcon = function(event, rowId) {
          event.stopPropagation();
          document.getElementById('icon_' + rowId).value = "";
          const img = document.getElementById('icon_preview_' + rowId);
          img.src = "";
          img.style.display = 'none';
          document.getElementById('icon_ph_' + rowId).style.display = 'block';
        }

        // Initialize Native Drag & Drop
        let draggedRow = null;

        function initDragAndDrop(container) {
          const rows = container.querySelectorAll('.metric-row');
          rows.forEach(row => {
            row.addEventListener('dragstart', () => {
              draggedRow = row;
              setTimeout(() => row.classList.add('dragging'), 0);
            });
            row.addEventListener('dragend', () => {
              row.classList.remove('dragging');
              draggedRow = null;
            });
          });

          container.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            if (draggedRow) {
              if (afterElement == null) container.appendChild(draggedRow);
              else container.insertBefore(draggedRow, afterElement);
            }
          });
        }

        function getDragAfterElement(container, y) {
          const draggableElements = [...container.querySelectorAll('.metric-row:not(.dragging)')];
          return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
              return {
                offset: offset,
                element: child
              };
            } else {
              return closest;
            }
          }, {
            offset: Number.NEGATIVE_INFINITY
          }).element;
        }

        addMetricRow(1);
        addMetricRow(2);


        // 6. Success Stories Logic
        let storyCount = 0;
        window.addStory = function() {
          storyCount++;
          const container = document.getElementById('storiesWrapper');
          const storyId = `story${storyCount}`;

          const storyHTML = `
            <div class="section-card position-relative" id="container_${storyId}">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 fs-6">Story ${storyCount}</h5>
                ${storyCount > 1 ? `<button type="button" class="btn btn-link text-danger p-0" onclick="removeStory('container_${storyId}')"><i data-lucide="minus-circle"></i></button>` : ''}
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-medium text-muted small">Subject Image</label>
                <div class="upload-area p-4" onclick="document.getElementById('${storyId}Input').click()">
                  <div class="upload-content" id="${storyId}Content">
                    <i data-lucide="upload" class="upload-icon"></i>
                    <p class="mb-0 text-muted small">Click to upload image</p>
                  </div>
                  <!-- Progress UI -->
                  <div class="upload-progress-wrapper" id="${storyId}Progress">
                    <div class="progress-bar-custom"><div class="progress-fill" id="${storyId}ProgressBar"></div></div>
                    <div class="upload-status-text" id="${storyId}ProgressText">Uploading... 0%</div>
                  </div>
                  <div class="image-preview-wrapper" id="${storyId}PreviewWrapper">
                    <img src="" class="image-preview" id="${storyId}PreviewImg">
                    <button type="button" class="remove-img-btn" onclick="removeImage(event, '${storyId}')"><i data-lucide="x"></i></button>
                  </div>
                  <input type="file" id="${storyId}Input" class="d-none" accept="image/*" onchange="handleStoryUpload(this, '${storyId}')">
                </div>
              </div>
              <div class="mb-3"><label class="form-label fw-medium text-muted small">Subject Description</label><textarea class="form-control" rows="3"></textarea></div>
              <div class="mb-0"><label class="form-label fw-medium text-muted small">Subject Name</label><input type="text" class="form-control"></div>
            </div>
          `;
          container.insertAdjacentHTML('beforeend', storyHTML);
          lucide.createIcons();
        };

        window.removeStory = function(containerId) {
          document.getElementById(containerId).remove();
        };

        window.handleStoryUpload = function(input, storyId) {
          if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
              // Now uses standard ID naming convention
              simulateUpload(storyId, () => {
                document.getElementById(storyId + 'PreviewWrapper').style.display = 'block';
                document.getElementById(storyId + 'PreviewImg').src = e.target.result;
              });
            }
            reader.readAsDataURL(input.files[0]);
          }
        }
        addStory();

        // 7. Media Gallery Multi-Upload (With Video Thumbnailing)
        window.handleGalleryUpload = function(input) {
          const container = document.getElementById('galleryPreviewContainer');

          if (input.files) {
            Array.from(input.files).forEach(file => {
              const uniqueId = 'gal_' + Math.random().toString(36).substr(2, 9);
              const isVideo = file.type.startsWith('video/');

              const injectHTML = (src, showPlayBtn) => {
                const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : '';
                const colHTML = `
                  <div class="col-xl-3 col-lg-4 col-md-6" id="${uniqueId}">
                    <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                      <img src="${src}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${showPlayBtn ? 0.7 : 1};">
                      ${playBtnHTML}
                      <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10;" onclick="document.getElementById('${uniqueId}').remove()">
                        <i data-lucide="x" style="width: 14px;"></i>
                      </button>
                    </div>
                  </div>
                `;
                container.insertAdjacentHTML('beforeend', colHTML);
                lucide.createIcons();
              };

              if (isVideo) {
                // Extract 1st frame of video using Canvas
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.src = URL.createObjectURL(file);
                video.onloadeddata = () => {
                  video.currentTime = 1;
                }; // Seek to 1 second to avoid black frames
                video.onseeked = () => {
                  const canvas = document.createElement('canvas');
                  canvas.width = video.videoWidth;
                  canvas.height = video.videoHeight;
                  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                  injectHTML(canvas.toDataURL('image/jpeg'), true);
                };
              } else {
                // Standard Image preview
                const reader = new FileReader();
                reader.onload = e => injectHTML(e.target.result, false);
                reader.readAsDataURL(file);
              }
            });
          }
        };
        // --- GLOBAL ARRAYS FOR FILES ---
        window.galleryFilesArray = [];

        // Update the Gallery Upload to store actual files
        window.handleGalleryUpload = function(input) {
          const container = document.getElementById('galleryPreviewContainer');
          if (input.files) {
            Array.from(input.files).forEach(file => {
              const uniqueId = 'gal_' + Math.random().toString(36).substr(2, 9);
              window.galleryFilesArray.push({
                id: uniqueId,
                file: file
              }); // Store for submission

              const isVideo = file.type.startsWith('video/');
              const injectHTML = (src, showPlayBtn) => {
                const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : '';
                const colHTML = `
                  <div class="col-xl-3 col-lg-4 col-md-6" id="${uniqueId}">
                    <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                      <img src="${src}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${showPlayBtn ? 0.7 : 1};">
                      ${playBtnHTML}
                      <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; z-index: 10;" onclick="removeGalleryItem('${uniqueId}')">
                        <i data-lucide="x" style="width: 14px;"></i>
                      </button>
                    </div>
                  </div>
                `;
                container.insertAdjacentHTML('beforeend', colHTML);
                lucide.createIcons();
              };

              if (isVideo) {
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.src = URL.createObjectURL(file);
                video.onloadeddata = () => {
                  video.currentTime = 1;
                };
                video.onseeked = () => {
                  const canvas = document.createElement('canvas');
                  canvas.width = video.videoWidth;
                  canvas.height = video.videoHeight;
                  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                  injectHTML(canvas.toDataURL('image/jpeg'), true);
                };
              } else {
                const reader = new FileReader();
                reader.onload = e => injectHTML(e.target.result, false);
                reader.readAsDataURL(file);
              }
            });
          }
          input.value = ""; // Reset input so same file can be selected again if needed
        };

        window.removeGalleryItem = function(id) {
          document.getElementById(id).remove();
          window.galleryFilesArray = window.galleryFilesArray.filter(item => item.id !== id);
        };

        // --- THE MASSIVE FORM SUBMIT HANDLER ---
        document.getElementById('createProjectForm').addEventListener('submit', async function(e) {
          e.preventDefault();

          // Use e.submitter to grab the exact button clicked
          const activeBtn = e.submitter;

          // DIAGNOSIS FIX: Simply assign 'draft' or 'published' based on the button clicked!
          const projectSubmitStatus = (activeBtn && activeBtn.id === 'draftBtn') ? 'draft' : 'published';

          // Handle UI Loading State
          const originalBtnText = activeBtn.innerHTML;
          activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

          // Safely disable buttons to prevent double-clicks
          const dBtn = document.getElementById('draftBtn');
          const pBtn = document.getElementById('publishBtn');
          if (dBtn) dBtn.disabled = true;
          if (pBtn) pBtn.disabled = true;

          const formData = new FormData();

          // 1. Basic Details
          formData.append('title', document.getElementById('projectTitle').value);
          formData.append('phase', document.getElementById('projectPhaseToggle').checked ? 'ongoing' : 'past');
          formData.append('impact_area', document.getElementById('projectImpactArea').value);
          formData.append('start_date', document.getElementById('projectStartDate').value);
          formData.append('end_date', document.getElementById('projectEndDate').value);
          formData.append('full_description', quill.root.innerHTML);

          // NEW: Append the exact status (draft or published)
          formData.append('status', projectSubmitStatus);

          // Cover Image
          const coverInput = document.getElementById('coverInput');
          if (coverInput.files[0]) formData.append('cover_image', coverInput.files[0]);

          // 2. Metrics Section 1
          const sec1Img = document.getElementById('sec1Input');
          if (sec1Img.files[0]) formData.append('sec1_image', sec1Img.files[0]);
          document.querySelectorAll('#metricsContainerSec1 .metric-row').forEach((row, index) => {
            const iconInput = row.querySelector('input[type="file"]');
            const inputs = row.querySelectorAll('input[type="text"]');
            if (iconInput.files[0]) formData.append(`sec1_metrics[${index}][icon]`, iconInput.files[0]);
            formData.append(`sec1_metrics[${index}][value]`, inputs[0].value);
            formData.append(`sec1_metrics[${index}][label]`, inputs[1].value);
          });

          // 3. Metrics Section 2
          const sec2Img = document.getElementById('sec2Input');
          if (sec2Img.files[0]) formData.append('sec2_image', sec2Img.files[0]);
          document.querySelectorAll('#metricsContainerSec2 .metric-row').forEach((row, index) => {
            const iconInput = row.querySelector('input[type="file"]');
            const inputs = row.querySelectorAll('input[type="text"]');
            if (iconInput.files[0]) formData.append(`sec2_metrics[${index}][icon]`, iconInput.files[0]);
            formData.append(`sec2_metrics[${index}][value]`, inputs[0].value);
            formData.append(`sec2_metrics[${index}][label]`, inputs[1].value);
          });

          // 4. Success Stories
          document.querySelectorAll('#storiesWrapper .section-card').forEach((card, index) => {
            const fileInput = card.querySelector('input[type="file"]');
            const desc = card.querySelector('textarea').value;
            const name = card.querySelector('input[type="text"]').value;
            if (fileInput.files[0]) formData.append(`stories[${index}][image]`, fileInput.files[0]);
            formData.append(`stories[${index}][description]`, desc);
            formData.append(`stories[${index}][name]`, name);
          });

          // 5. Project Leads
          const leadImg = document.getElementById('leadInput');
          if (leadImg.files[0]) formData.append('lead_image', leadImg.files[0]);
          formData.append('lead_name', document.getElementById('leadName').value);
          formData.append('lead_role', document.getElementById('leadRole').value);
          formData.append('lead_linkedin', document.getElementById('leadLinkedin').value);

          // 6. Media Gallery
          window.galleryFilesArray.forEach((item, index) => {
            formData.append(`gallery_files[]`, item.file);
          });

          // SEND TO SERVER
          try {
            const response = await fetch('actions/projects/create-project.php', {
              method: 'POST',
              body: formData
            });
            const result = await response.json();

            if (result.success) {
              alert('Project Published Successfully!');
              loadView('projects', 'Projects Management'); // Go back to list
            } else {
              alert('Error: ' + result.message);
              publishBtn.innerHTML = originalBtnText;
              publishBtn.disabled = false;
            }
          } catch (err) {
            console.error(err);
            alert('A network error occurred. Please try again.');
            publishBtn.innerHTML = originalBtnText;
            publishBtn.disabled = false;
          }
        });
      }
      // ==========================================
      // --- VIEW: POSTS LOGIC ---
      // ==========================================
      function initPostsScript() {
        window.loadPosts = function() {
          const tbody = document.getElementById('posts-tbody');
          if (!tbody) return;

          tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading posts...</td></tr>';

          fetch('actions/posts/fetch-posts.php')
            .then(res => res.json())
            .then(data => {
              const posts = Array.isArray(data) ? data : Object.values(data);
              tbody.innerHTML = '';

              if (posts.length === 0 || posts[0].error) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No posts found.</td></tr>';
                return;
              }

              posts.forEach(item => {
                let pillClass = (item.status || '').toLowerCase() === 'published' ? 'status-published' : 'status-draft';
                let pubDate = item.publish_date ? item.publish_date.split(' ')[0] : (item.created_at ? item.created_at.split(' ')[0] : 'N/A');

                const row = `
                  <tr>
                    <td class="fw-medium">${item.title}</td>
                    <td>${item.post_lead || item.created_by_name || 'System'}</td>
                    <td><span class="status-pill ${pillClass}">${(item.status || 'Draft').charAt(0).toUpperCase() + (item.status || 'draft').slice(1)}</span></td>
                    <td>${pubDate}</td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit" onclick="loadView('edit-post', 'Edit Post', {id: ${item.id}})"><i data-lucide="edit" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                    </td>
                  </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
              });
              lucide.createIcons();
            })
            .catch(err => {
              console.error("Posts Fetch Error:", err);
              tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load posts.</td></tr>';
            });
        };
        loadPosts();
      }

      // ==========================================
      // --- VIEW: CREATE POST LOGIC ---
      // ==========================================
      function initCreatePostScript() {
        // Reset Gallery Array specifically for this view
        window.galleryFilesArray = [];

        // --- 1. UPLOAD PREVIEW & PROGRESS FUNCTIONS ---
        // (Included here so they work even if you haven't visited Projects first)

        function simulateUpload(idPrefix, callback) {
          const contentDiv = document.getElementById(idPrefix + 'Content');
          const progressDiv = document.getElementById(idPrefix + 'Progress');
          const progressBar = document.getElementById(idPrefix + 'ProgressBar');
          const progressText = document.getElementById(idPrefix + 'ProgressText');

          if (!contentDiv || !progressDiv) {
            callback();
            return;
          }

          contentDiv.style.display = 'none';
          progressDiv.style.display = 'block';
          progressBar.className = 'progress-fill';
          progressBar.style.width = '0%';
          progressText.className = 'upload-status-text';

          let progress = 0;
          const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress >= 100) progress = 100;

            progressBar.style.width = progress + '%';
            progressText.innerText = `Uploading... ${Math.round(progress)}%`;

            if (progress === 100) {
              clearInterval(interval);
              progressBar.classList.add('success');
              progressText.classList.add('success');
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
              lucide.createIcons();

              setTimeout(() => {
                progressDiv.style.display = 'none';
                callback();
              }, 800);
            }
          }, 100);
        }

        window.handleImageUpload = function(input, idPrefix) {
          if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
              simulateUpload(idPrefix, () => {
                document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'block';
                document.getElementById(idPrefix + 'PreviewImg').src = e.target.result;
              });
            }
            reader.readAsDataURL(input.files[0]);
          }
        };

        window.removeImage = function(event, idPrefix) {
          event.stopPropagation();
          document.getElementById(idPrefix + 'Input').value = "";
          document.getElementById(idPrefix + 'PreviewImg').src = "";
          document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'none';
          document.getElementById(idPrefix + 'Content').style.display = 'block';
        };

        window.handleGalleryUpload = function(input) {
          const container = document.getElementById('galleryPreviewContainer');
          if (input.files) {
            Array.from(input.files).forEach(file => {
              const uniqueId = 'gal_' + Math.random().toString(36).substr(2, 9);
              window.galleryFilesArray.push({
                id: uniqueId,
                file: file
              });

              const isVideo = file.type.startsWith('video/');
              const injectHTML = (src, showPlayBtn) => {
                const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : '';
                const colHTML = `
                  <div class="col-xl-3 col-lg-4 col-md-6" id="${uniqueId}">
                    <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                      <img src="${src}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${showPlayBtn ? 0.7 : 1};">
                      ${playBtnHTML}
                      <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; z-index: 10;" onclick="removeGalleryItem('${uniqueId}')">
                        <i data-lucide="x" style="width: 14px;"></i>
                      </button>
                    </div>
                  </div>
                `;
                container.insertAdjacentHTML('beforeend', colHTML);
                lucide.createIcons();
              };

              if (isVideo) {
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.src = URL.createObjectURL(file);
                video.onloadeddata = () => {
                  video.currentTime = 1;
                };
                video.onseeked = () => {
                  const canvas = document.createElement('canvas');
                  canvas.width = video.videoWidth;
                  canvas.height = video.videoHeight;
                  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                  injectHTML(canvas.toDataURL('image/jpeg'), true);
                };
              } else {
                const reader = new FileReader();
                reader.onload = e => injectHTML(e.target.result, false);
                reader.readAsDataURL(file);
              }
            });
          }
          input.value = "";
        };

        window.removeGalleryItem = function(id) {
          document.getElementById(id).remove();
          window.galleryFilesArray = window.galleryFilesArray.filter(item => item.id !== id);
        };


        // --- 2. QUILL EDITOR INITIALIZATION ---
        if (document.getElementById('editor')) {
          const Icons = Quill.import('ui/icons');
          Icons.undo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
          Icons.redo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

          var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
              history: {
                delay: 1000,
                maxStack: 100,
                userOnly: true
              },
              toolbar: {
                container: [
                  ['undo', 'redo'],
                  [{
                    'size': ['small', false, 'large', 'huge']
                  }],
                  ['bold', 'italic', 'underline', 'strike'],
                  [{
                    'align': ''
                  }, {
                    'align': 'center'
                  }, {
                    'align': 'right'
                  }, {
                    'align': 'justify'
                  }],
                  [{
                    'list': 'ordered'
                  }, {
                    'list': 'bullet'
                  }],
                  ['link']
                ],
                handlers: {
                  'undo': function() {
                    this.quill.history.undo();
                  },
                  'redo': function() {
                    this.quill.history.redo();
                  }
                }
              }
            }
          });
        }


        // --- 3. FORM SUBMIT HANDLER ---
        document.getElementById('createPostForm').addEventListener('submit', async function(e) {
          e.preventDefault();

          const activeBtn = e.submitter;
          const postSubmitStatus = (activeBtn && activeBtn.id === 'draftBtn') ? 'draft' : 'published';

          const originalBtnText = activeBtn.innerHTML;
          activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

          const dBtn = document.getElementById('draftBtn');
          const pBtn = document.getElementById('publishBtn');
          if (dBtn) dBtn.disabled = true;
          if (pBtn) pBtn.disabled = true;

          const formData = new FormData();

          formData.append('title', document.getElementById('postTitle').value);
          formData.append('impact_area', document.getElementById('postImpactArea').value);
          formData.append('content', quill.root.innerHTML);
          formData.append('status', postSubmitStatus);

          // Cover Image
          const coverInput = document.getElementById('coverInput');
          if (coverInput.files[0]) formData.append('cover_image', coverInput.files[0]);

          // Media Gallery
          window.galleryFilesArray.forEach((item) => {
            formData.append(`gallery_files[]`, item.file);
          });

          try {
            const response = await fetch('actions/posts/create-post.php', {
              method: 'POST',
              body: formData
            });
            const rawText = await response.text();

            try {
              const result = JSON.parse(rawText);
              if (result.success) {
                alert('Post Saved Successfully!');
                loadView('posts', 'Posts Management');
              } else {
                alert('Error: ' + result.message);
                if (pBtn) pBtn.disabled = false;
                if (dBtn) dBtn.disabled = false;
                activeBtn.innerHTML = originalBtnText;
              }
            } catch (e) {
              console.error("Server Error: ", rawText);
              alert("A server error occurred. Check console for details.");
              if (pBtn) pBtn.disabled = false;
              if (dBtn) dBtn.disabled = false;
              activeBtn.innerHTML = originalBtnText;
            }
          } catch (err) {
            console.error(err);
            alert('A network error occurred. Please try again.');
            if (pBtn) pBtn.disabled = false;
            if (dBtn) dBtn.disabled = false;
            activeBtn.innerHTML = originalBtnText;
          }
        });
      }

      // ==========================================
      // --- VIEW: EDIT POST LOGIC ---
      // ==========================================
      function initEditPostScript(params = {}) {
        // Reset Gallery Array specifically for this view
        window.galleryFilesArray = [];
        window.galleryFilesDeletedArray = [];
        let quill = null;
        window.postData = {};

        // --- 1. UPLOAD PREVIEW & PROGRESS FUNCTIONS ---
        // (Included here so they work even if you haven't visited Projects first)
        function simulateUpload(idPrefix, callback, isLoading = false) {
          const contentDiv = document.getElementById(idPrefix + 'Content');
          const progressDiv = document.getElementById(idPrefix + 'Progress');
          const progressBar = document.getElementById(idPrefix + 'ProgressBar');
          const progressText = document.getElementById(idPrefix + 'ProgressText');

          if (!contentDiv || !progressDiv) {
            callback();
            return;
          }

          contentDiv.style.display = 'none';
          progressDiv.style.display = 'block';
          progressBar.className = 'progress-fill';
          progressBar.style.width = '0%';
          progressText.className = 'upload-status-text';

          let progress = 0;
          const interval = setInterval(() => {
            progress += Math.random() * 20;
            if (progress >= 100) progress = 100;

            progressBar.style.width = progress + '%';

            const status = (isLoading) ? "Load" : "Upload"
            progressText.innerText = `${status}ing... ${Math.round(progress)}%`;

            if (progress === 100) {
              clearInterval(interval);
              progressBar.classList.add('success');
              progressText.classList.add('success');
              progressText.innerHTML = `<i data-lucide="check-circle" style="width:14px;"></i> ${status} Complete!`;
              lucide.createIcons();

              setTimeout(() => {
                progressDiv.style.display = 'none';
                callback();
              }, 800);
            }
          }, 100);
        }

        window.handleImageUpload = function(input, idPrefix) {
          if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
              simulateUpload(idPrefix, () => {
                document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'block';
                document.getElementById(idPrefix + 'PreviewImg').src = e.target.result;
              });
            }
            reader.readAsDataURL(input.files[0]);
          }
        };

        window.removeImage = function(event, idPrefix) {
          event.stopPropagation();
          const input = document.getElementById(idPrefix + 'Input');
          if (input.hasAttribute('data-cover-image')) {
            delete input.dataset.coverImage;
            window.postData.isCoverDeleted = true;
          }
          input.value = "";
          document.getElementById(idPrefix + 'PreviewImg').src = "";
          document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'none';
          document.getElementById(idPrefix + 'Content').style.display = 'block';
        };

        window.handleGalleryUpload = function(input) {
          const container = document.getElementById('galleryPreviewContainer');
          if (input.files) {
            Array.from(input.files).forEach(file => {
              const uniqueId = 'gal_' + Math.random().toString(36).substr(2, 9);
              window.galleryFilesArray.push({
                id: uniqueId,
                file: file
              });

              const isVideo = file.type.startsWith('video/');
              const injectHTML = (src, showPlayBtn) => {
                const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : '';
                const colHTML = `
                  <div class="col-xl-3 col-lg-4 col-md-6" id="${uniqueId}">
                    <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                      <img src="${src}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${showPlayBtn ? 0.7 : 1};">
                      ${playBtnHTML}
                      <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; z-index: 10;" onclick="removeGalleryItem('${uniqueId}')">
                        <i data-lucide="x" style="width: 14px;"></i>
                      </button>
                    </div>
                  </div>
                `;
                container.insertAdjacentHTML('beforeend', colHTML);
                lucide.createIcons();
              };

              if (isVideo) {
                const video = document.createElement('video');
                video.preload = 'metadata';
                video.src = URL.createObjectURL(file);
                video.onloadeddata = () => {
                  video.currentTime = 1;
                };
                video.onseeked = () => {
                  const canvas = document.createElement('canvas');
                  canvas.width = video.videoWidth;
                  canvas.height = video.videoHeight;
                  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                  injectHTML(canvas.toDataURL('image/jpeg'), true);
                };
              } else {
                const reader = new FileReader();
                reader.onload = e => injectHTML(e.target.result, false);
                reader.readAsDataURL(file);
              }
            });
          }
          input.value = "";
        };

        window.removeGalleryItem = function(id, isDeleted = false) {
          document.getElementById(id).remove();

          if (isDeleted) {
            window.galleryFilesDeletedArray.push(id);
          } else {
            window.galleryFilesArray = window.galleryFilesArray.filter(item => item.id !== id);
          }
        };


        // --- 2. QUILL EDITOR INITIALIZATION ---
        if (document.getElementById('editor')) {
          const Icons = Quill.import('ui/icons');
          Icons.undo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
          Icons.redo = '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

          quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
              history: {
                delay: 1000,
                maxStack: 100,
                userOnly: true
              },
              toolbar: {
                container: [
                  ['undo', 'redo'],
                  [{
                    'size': ['small', false, 'large', 'huge']
                  }],
                  ['bold', 'italic', 'underline', 'strike'],
                  [{
                    'align': ''
                  }, {
                    'align': 'center'
                  }, {
                    'align': 'right'
                  }, {
                    'align': 'justify'
                  }],
                  [{
                    'list': 'ordered'
                  }, {
                    'list': 'bullet'
                  }],
                  ['link']
                ],
                handlers: {
                  'undo': function() {
                    this.quill.history.undo();
                  },
                  'redo': function() {
                    this.quill.history.redo();
                  }
                }
              }
            }
          });
        }

        // --- 3. FORM SUBMIT HANDLER ---
        document.getElementById('createPostForm').addEventListener('submit', async function(e) {
          e.preventDefault();
          // console.log(window.postData);

          const activeBtn = e.submitter;
          let postSubmitStatus = (activeBtn && activeBtn.id === 'statusBtn') ? activeBtn.dataset.action : null;

          const originalBtnText = activeBtn.innerHTML;
          activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

          const statusBtn = document.getElementById('statusBtn');
          const saveBtn = document.getElementById('saveBtn');
          if (statusBtn) statusBtn.disabled = true;
          if (saveBtn) saveBtn.disabled = true;

          const formData = new FormData();

          const id = window.postData.id ?? null;
          const title = document.getElementById('postTitle').value;
          const impactArea = document.getElementById('postImpactArea').value;
          const content = quill.root.innerHTML;
          const status = postSubmitStatus;

          formData.append('id', id);
          if (window.postData.title !== title) formData.append('title', title);
          if (window.postData.impactAreaIds[0] !== impactArea) formData.append('impact_area', impactArea);
          if (window.postData.content !== content) formData.append('content', quill.root.innerHTML);
          if (postSubmitStatus) formData.append('status', postSubmitStatus);

          // Cover Image
          const coverInput = document.getElementById('coverInput');
          const coverImage = coverInput.files[0] ? coverInput.files[0] : null;
          const isCoverDeleted = window.postData.isCoverDeleted;

          if (coverImage) formData.append('cover_image', coverImage);
          formData.append('is_cover_deleted', isCoverDeleted);

          // Media Gallery
          window.galleryFilesArray.forEach((item) => {
            formData.append(`gallery_files[]`, item.file);
          });

          window.galleryFilesDeletedArray.forEach((item) => {
            formData.append(`gallery_files_deleted[]`, item);
          });

          // console.log(formData);

          try {
            const response = await fetch('actions/posts/update-post.php', {
              method: 'POST',
              body: formData
            });
            const rawText = await response.text();

            try {
              const result = JSON.parse(rawText);
              if (result.success) {
                alert('Post Updated Successfully!');
                loadView('posts', 'Posts Management');
              } else {
                alert('Error: ' + result.message);
                if (saveBtn) saveBtn.disabled = false;
                if (statusBtn) statusBtn.disabled = false;
                activeBtn.innerHTML = originalBtnText;
              }
            } catch (e) {
              console.error("Server Error: ", rawText);
              alert("A server error occurred. Check console for details.");
              if (saveBtn) saveBtn.disabled = false;
              if (statusBtn) statusBtn.disabled = false;
              activeBtn.innerHTML = originalBtnText;
            }
          } catch (err) {
            console.error(err);
            alert('A network error occurred. Please try again.');
            if (saveBtn) saveBtn.disabled = false;
            if (statusBtn) statusBtn.disabled = false;
            activeBtn.innerHTML = originalBtnText;
          }
        });

        const urlParams = new URLSearchParams(params);
        fetch(`actions/posts/fetch-post.php?${urlParams}`)
          .then(res => res.json())
          .then(data => {

            if (!data) {
              alert("Somethin went wrong.");
              loadView('posts', 'Posts Management');
              return;
            }

            if (data.status === "error") {
              alert(data.message);
              loadView('posts', 'Posts Management');
              return;
            }

            console.log(data);

            const id = data.id ?? null;
            const title = data.title ?? null;
            const content = data.content ?? "";
            const coverImage = data.cover_image ?? null;
            const status = data.status ?? null;
            const publishedDate = data.published_date ?? null;
            const createdAt = data.created_at ?? null;
            const updatedAt = data.updated_at ?? null;
            const impactAreaIds = data.impact_area_ids ?? null;
            const media = data.post_media ?? null;

            const required = [id, title, status, createdAt, updatedAt];

            if (required.includes(null)) throw new Error("Missing required post data.");

            window.postData.id = id;
            window.postData.title = title;
            window.postData.content = content;
            window.postData.impactAreaIds = impactAreaIds;
            window.postData.isCoverDeleted = false;

            document.getElementById('postTitle').value = title;
            document.getElementById('postImpactArea').value = impactAreaIds[0]; // this is not a multi select.

            if (quill && content) quill.root.innerHTML = content;

            if (coverImage) {
              document.getElementById('coverInput').dataset.coverImage = coverImage;
              simulateUpload('cover', () => {
                document.getElementById('coverPreviewWrapper').style.display = 'block';
                document.getElementById('coverPreviewImg').src = `/project-sedna/${coverImage}`;
              }, true);
            }

            if (media) {
              const container = document.getElementById('galleryPreviewContainer');

              media.forEach(item => {
                const requiredFields = ["id", "type", "url"];
                const hasAllFields = requiredFields.every(field => Object.hasOwn(item, field));

                if (!hasAllFields) throw new Error("Missing required post media data.");
                const uniqueId = item.id;
                const url = `/project-sedna/${item.url}`;

                const isVideo = (item.type === "video") ? true : false;
                const injectHTML = (src, showPlayBtn) => {
                  const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : '';
                  const colHTML = `
                      <div class="col-xl-3 col-lg-4 col-md-6" id="${uniqueId}">
                        <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                          <img src="${src}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${showPlayBtn ? 0.7 : 1};">
                          ${playBtnHTML}
                          <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; z-index: 10;" onclick="removeGalleryItem('${uniqueId}', true)">
                            <i data-lucide="x" style="width: 14px;"></i>
                          </button>
                        </div>
                      </div>
                    `;
                  container.insertAdjacentHTML('beforeend', colHTML);
                  lucide.createIcons();
                };

                if (isVideo) {
                  const video = document.createElement('video');
                  video.preload = 'metadata';
                  video.src = url;
                  video.onloadeddata = () => {
                    video.currentTime = 1;
                  };
                  video.onseeked = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    injectHTML(canvas.toDataURL('image/jpeg'), true);
                  };
                } else {
                  injectHTML(url, false);
                }
              });
            }

            const statusBtn = document.getElementById('statusBtn');

            if (status === "draft" || status === "archived") {
              statusBtn.dataset.action = "publish";
              statusBtn.textContent = "Publish";
              statusBtn.classList.remove('d-none');
            } else if (status === "published") {
              statusBtn.dataset.action = "archive";
              statusBtn.textContent = "Archive";
              statusBtn.classList.remove('d-none');
            } else {
              throw new Error('Post status unknown.');
            }
          })
          .catch(err => {
            console.error("Post Fetch Error:", err);
            alert("Failed to load the post.");
            loadView('posts', 'Posts Management');
          });
      }

      // ==========================================
      // --- VIEW: PUBLICATIONS LOGIC ---
      // ==========================================
      function initPublicationsScript() {
        window.loadPublications = function() {
          const tbody = document.getElementById('publications-tbody');
          if (!tbody) return;

          tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading publications...</td></tr>';

          fetch('actions/publications/fetch-publications.php')
            .then(res => res.json())
            .then(data => {
              const pubs = Array.isArray(data) ? data : Object.values(data);
              tbody.innerHTML = '';

              if (pubs.length === 0 || pubs[0].error) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No publications found.</td></tr>';
                return;
              }

              pubs.forEach(item => {
                let pillClass = (item.status || '').toLowerCase() === 'published' ? 'status-published' : 'status-draft';

                const row = `
                  <tr>
                    <td class="fw-medium">${item.title}</td>
                    <td>${item.category || 'Uncategorized'}</td>
                    <td><span class="status-pill ${pillClass}">${(item.status || 'Draft').charAt(0).toUpperCase() + (item.status || 'draft').slice(1)}</span></td>
                    <td>${item.uploaded_by_name || 'System'}</td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit"><i data-lucide="edit" style="width: 16px;"></i></button>
                      <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                    </td>
                  </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
              });
              lucide.createIcons();
            })
            .catch(err => {
              console.error("Publications Fetch Error:", err);
              tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load publications.</td></tr>';
            });
        };
        loadPublications();
      }

      // ==========================================
      // --- VIEW: CREATE PUBLICATION LOGIC ---
      // ==========================================
      function initCreatePublicationScript() {

        let extractedPdfCoverBlob = null; // Store the auto-generated image

        // --- 1. TOGGLE LOGIC ---
        const radioAuto = document.getElementById('coverAuto');
        const radioCustom = document.getElementById('coverCustom');
        const autoSection = document.getElementById('autoCoverSection');
        const customSection = document.getElementById('customCoverSection');

        function updateCoverUI() {
          if (radioAuto.checked) {
            autoSection.style.display = 'block';
            customSection.style.display = 'none';
          } else {
            autoSection.style.display = 'none';
            customSection.style.display = 'block';
          }
        }
        if (radioAuto && radioCustom) {
          radioAuto.addEventListener('change', updateCoverUI);
          radioCustom.addEventListener('change', updateCoverUI);
        }

        // --- 2. PRO-TIER UPLOAD SIMULATOR (PROMISE BASED) ---
        // This ensures the progress bar finishes BEFORE showing the preview
        function simulateUploadAsync(idPrefix) {
          return new Promise((resolve) => {
            const contentDiv = document.getElementById(idPrefix + 'Content');
            const progressDiv = document.getElementById(idPrefix + 'Progress');
            const progressBar = document.getElementById(idPrefix + 'ProgressBar');
            const progressText = document.getElementById(idPrefix + 'ProgressText');

            if (!contentDiv || !progressDiv) {
              resolve();
              return;
            }

            // State 1: Uploading
            contentDiv.style.display = 'none';
            progressDiv.style.display = 'block';
            progressBar.className = 'progress-fill';
            progressBar.style.width = '0%';
            progressText.className = 'upload-status-text';

            let progress = 0;
            const interval = setInterval(() => {
              progress += Math.random() * 25; // Speed up slightly for PDFs
              if (progress >= 100) progress = 100;

              progressBar.style.width = progress + '%';
              progressText.innerText = `Uploading & Processing... ${Math.round(progress)}%`;

              if (progress === 100) {
                clearInterval(interval);
                // State 2: Success
                progressBar.classList.add('success');
                progressText.classList.add('success');
                progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Processing Complete!';
                if (window.lucide) lucide.createIcons();

                // State 3: Resolve promise to trigger preview
                setTimeout(() => {
                  progressDiv.style.display = 'none';
                  resolve();
                }, 700);
              }
            }, 100);
          });
        }

        // --- 3. PDF UPLOAD & EXTRACTION ---
        window.handlePdfUpload = async function(input) {
          if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.type !== 'application/pdf') {
              alert("Please upload a valid PDF file.");
              return;
            }

            // 1. Wait for the visual upload progress to finish first
            await simulateUploadAsync('pdf');

            // 2. Show the PDF Document Preview Box
            document.getElementById('pdfPreviewWrapper').style.display = 'block';
            document.getElementById('pdfFileNameDisplay').innerText = file.name;

            // 3. Extract PDF Cover using pdf.js
            try {
              // Failsafe to ensure worker is loaded
              if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
              }

              const fileURL = URL.createObjectURL(file);
              const pdf = await pdfjsLib.getDocument(fileURL).promise;
              const page = await pdf.getPage(1);

              const viewport = page.getViewport({
                scale: 1.5
              }); // High resolution
              const canvas = document.createElement('canvas');
              const ctx = canvas.getContext('2d');
              canvas.height = viewport.height;
              canvas.width = viewport.width;

              await page.render({
                canvasContext: ctx,
                viewport: viewport
              }).promise;

              // Convert Canvas to Blob for form submission
              canvas.toBlob((blob) => {
                extractedPdfCoverBlob = blob; // Save it to memory

                // Show preview in the UI Auto section
                const imgUrl = URL.createObjectURL(blob);
                document.getElementById('autoCoverPlaceholder').style.display = 'none';
                const imgEl = document.getElementById('autoCoverImg');
                imgEl.src = imgUrl;
                imgEl.style.display = 'block';
              }, 'image/jpeg', 0.8);

            } catch (err) {
              console.error("PDF Extraction Failed:", err);
              document.getElementById('autoCoverPlaceholder').innerText = "Preview extraction failed. A default cover will be used.";
            }
          }
        };

        // --- DRAG AND DROP FILE UPLOAD INTERCEPTOR ---
        function setupFileUploadDragAndDrop() {
          const uploadAreas = document.querySelectorAll('.upload-area');

          uploadAreas.forEach(area => {
            // 1. Prevent default browser file opening
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
              area.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
              }, false);
            });

            // 2. Add visual feedback when dragging over the box
            ['dragenter', 'dragover'].forEach(eventName => {
              area.addEventListener(eventName, () => {
                area.style.borderColor = 'var(--sapsri-red)';
                area.style.backgroundColor = '#F9E7EC'; // sapsri-red-light
              }, false);
            });

            // 3. Remove visual feedback when leaving or dropping
            ['dragleave', 'drop'].forEach(eventName => {
              area.addEventListener(eventName, () => {
                area.style.borderColor = '#D6D6D6';
                area.style.backgroundColor = '#FDF4F6';
              }, false);
            });

            // 4. Handle the actual file drop
            area.addEventListener('drop', (e) => {
              const files = e.dataTransfer.files;

              if (files && files.length > 0) {
                // Find the hidden input inside this specific upload area
                const fileInput = area.querySelector('input[type="file"]');

                if (fileInput) {
                  // Assign the dropped files to the input
                  fileInput.files = files;

                  // Manually trigger the 'change' event so your existing preview functions run!
                  fileInput.dispatchEvent(new Event('change', {
                    bubbles: true
                  }));
                }
              }
            }, false);
          });
        }

        // Initialize the drag-and-drop logic immediately
        setupFileUploadDragAndDrop();

        window.removePdf = function(event) {
          event.stopPropagation();
          document.getElementById('pdfInput').value = "";
          document.getElementById('pdfPreviewWrapper').style.display = 'none';
          document.getElementById('pdfContent').style.display = 'block';

          // Reset extracted cover
          extractedPdfCoverBlob = null;
          document.getElementById('autoCoverImg').style.display = 'none';
          document.getElementById('autoCoverImg').src = "";
          document.getElementById('autoCoverPlaceholder').style.display = 'block';
          document.getElementById('autoCoverPlaceholder').innerText = "A preview will appear here once a PDF is uploaded.";
        };

        // --- 4. CUSTOM COVER UPLOAD LOGIC ---
        window.handleImageUpload = async function(input, idPrefix) {
          if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = async function(e) {
              await simulateUploadAsync(idPrefix);
              document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'block';
              document.getElementById(idPrefix + 'PreviewImg').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
          }
        };

        window.removeImage = function(event, idPrefix) {
          event.stopPropagation();
          document.getElementById(idPrefix + 'Input').value = "";
          document.getElementById(idPrefix + 'PreviewImg').src = "";
          document.getElementById(idPrefix + 'PreviewWrapper').style.display = 'none';
          document.getElementById(idPrefix + 'Content').style.display = 'block';
        };

        // --- 5. FORM SUBMIT HANDLER ---
        document.getElementById('createPublicationForm').addEventListener('submit', async function(e) {
          e.preventDefault();

          const activeBtn = e.submitter;
          const status = (activeBtn && activeBtn.id === 'draftBtn') ? 'draft' : 'published';

          const originalBtnText = activeBtn.innerHTML;
          activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
          document.getElementById('draftBtn').disabled = true;
          document.getElementById('publishBtn').disabled = true;

          const formData = new FormData();
          formData.append('title', document.getElementById('pubTitle').value);
          formData.append('category_id', document.getElementById('pubCategory').value);
          formData.append('description', document.getElementById('pubDescription').value);
          formData.append('status', status);

          const isCustom = document.getElementById('coverCustom').checked;
          formData.append('is_custom_cover', isCustom ? 1 : 0);

          // Append PDF Document
          const pdfInput = document.getElementById('pdfInput');
          if (pdfInput.files[0]) formData.append('pdf_file', pdfInput.files[0]);

          // Append Cover Image based on strategy
          if (isCustom) {
            const customInput = document.getElementById('pubCoverInput');
            if (customInput.files[0]) formData.append('cover_image', customInput.files[0]);
          } else {
            // Append the extracted Blob from PDF.js
            if (extractedPdfCoverBlob) {
              formData.append('cover_image', extractedPdfCoverBlob, 'auto-cover.jpg');
            }
          }

          try {
            const response = await fetch('actions/publications/create-publication.php', {
              method: 'POST',
              body: formData
            });
            const rawText = await response.text();

            try {
              const result = JSON.parse(rawText);
              if (result.success) {
                alert('Publication Saved Successfully!');
                loadView('publications', 'Publications');
              } else {
                alert('Error: ' + result.message);
              }
            } catch (e) {
              console.error("Server Error: ", rawText);
              alert("A server error occurred. Check console for details.");
            }
          } catch (err) {
            console.error(err);
            alert('A network error occurred. Check the server response.');
          } finally {
            document.getElementById('draftBtn').disabled = false;
            document.getElementById('publishBtn').disabled = false;
            activeBtn.innerHTML = originalBtnText;
          }
        });
      }

      // ==========================================
      // --- VIEW: USERS LOGIC ---
      // ==========================================
      function initUsersScript() {
        fetchUserManagementData();

        // 1. Fetch all data for the 3 tabs
        async function fetchUserManagementData() {
          try {
            const response = await fetch('actions/users/fetch-users.php');
            const data = await response.json();

            if (data.success) {
              renderActiveUsers(data.active_users);
              renderPendingUsers(data.pending_users, data.roles);
              renderRoles(data.roles);
              lucide.createIcons(); // Refresh icons after rendering
            } else {
              console.error("Failed to load user data:", data.message);
            }
          } catch (error) {
            console.error("Error fetching user data:", error);
          }
        }

        // 2. Render Active Users Tab
        function renderActiveUsers(users) {
          const tbody = document.getElementById('active-users-tbody');
          tbody.innerHTML = '';

          if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>';
            return;
          }

          users.forEach(user => {
            const fullName = `${user.first_name} ${user.last_name}`;
            const isSuspended = user.status === 'suspended';
            const statusPill = isSuspended ?
              `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2">Suspended</span>` :
              `<span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2">Active</span>`;

            const actionBtn = isSuspended ?
              `<button class="btn btn-sm btn-light text-success border-0 me-1 shadow-sm" onclick="handleUserSuspendAction(${user.id}, 'activate')" title="Remove Suspension"><i data-lucide="user-check" style="width: 16px;"></i></button>` :
              `<button class="btn btn-sm btn-light text-danger border-0 me-1 shadow-sm" onclick="openSuspendModal(${user.id}, '${fullName.replace(/'/g, "\\'")}')" title="Suspend"><i data-lucide="user-minus" style="width: 16px;"></i></button>`;

            tbody.insertAdjacentHTML('beforeend', `
              <tr>
                <td class="fw-medium ${isSuspended ? 'text-muted' : ''}">${fullName}</td>
                <td class="${isSuspended ? 'text-muted' : ''}">${user.email}</td>
                <td class="${isSuspended ? 'text-muted' : ''}">${user.role_name || 'No Role'}</td>
                <td>${statusPill}</td>
                <td class="text-end">${actionBtn}</td>
              </tr>
            `);
          });
        }

        // 3. Render Incoming Acceptance Tab
        function renderPendingUsers(users, roles) {
          const tbody = document.getElementById('pending-users-tbody');
          tbody.innerHTML = '';

          if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No pending requests.</td></tr>';
            return;
          }

          // Build Roles Dropdown Options
          let roleOptions = '<option disabled selected value="">Select Role...</option>';
          roles.forEach(role => {
            roleOptions += `<option value="${role.id}">${role.name}</option>`;
          });

          users.forEach(user => {
            const fullName = `${user.first_name} ${user.last_name}`;
            // Format date string
            const reqDate = new Date(user.created_at).toLocaleDateString('en-GB');

            tbody.insertAdjacentHTML('beforeend', `
              <tr id="pending-row-${user.id}">
                <td class="fw-medium">${fullName}</td>
                <td>${user.email}</td>
                <td>${reqDate}</td>
                <td class="text-end d-flex justify-content-end align-items-center gap-2">
                  <select class="form-select form-select-sm role-select" id="role-select-${user.id}">
                    ${roleOptions}
                  </select>
                  <button class="btn btn-sm btn-success shadow-sm" onclick="handleUserRequest(${user.id}, 'accept')">Accept</button>
                  <button class="btn btn-sm btn-outline-danger shadow-sm" onclick="handleUserRequest(${user.id}, 'reject')">Reject</button>
                </td>
              </tr>
            `);
          });
        }

        // 4. Render Roles Tab
        function renderRoles(roles) {
          const tbody = document.getElementById('roles-tbody');
          tbody.innerHTML = '';

          if (roles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No roles found.</td></tr>';
            return;
          }

          roles.forEach(role => {
            tbody.insertAdjacentHTML('beforeend', `
              <tr>
                <td class="fw-medium">${role.name}</td>
                <td class="text-muted">${role.description || 'No description provided.'}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-light border-0 shadow-sm" title="Edit Role"><i data-lucide="edit" style="width: 16px;"></i></button>
                </td>
              </tr>
            `);
          });
        }

        // 5. Handle Accept/Reject Action
        window.handleUserRequest = async function(userId, action) {
          const roleSelect = document.getElementById(`role-select-${userId}`);
          const roleId = roleSelect ? roleSelect.value : null;

          if (action === 'accept' && !roleId) {
            alert('Please select a role before accepting the user.');
            return;
          }

          if (!confirm(`Are you sure you want to ${action} this user?`)) return;

          const formData = new FormData();
          formData.append('user_id', userId);
          formData.append('action', action);
          if (roleId) formData.append('role_id', roleId);

          try {
            const response = await fetch('actions/users/handle-request.php', {
              method: 'POST',
              body: formData
            });
            const result = await response.json();

            if (result.success) {
              alert(result.message);
              // Remove the row visually without reloading the whole page
              const row = document.getElementById(`pending-row-${userId}`);
              if (row) row.remove();

              // If accepted, refresh the whole data set so they appear in Active Users
              if (action === 'accept') {
                fetchUserManagementData();
              }
            } else {
              alert('Error: ' + result.message);
            }
          } catch (error) {
            console.error(error);
            alert('A network error occurred processing the request.');
          }
        };

        // UI Modal Trigger Script (Attached to window so inline onclick works)
        window.openSuspendModal = function(userId, userName) {
          document.getElementById('suspendUserNameText').innerText = `Are You Sure Want to Suspend This User "${userName}"?`;
          document.getElementById('confirmSuspendBtn').setAttribute('data-target-user', userId);

          const suspendModal = new bootstrap.Modal(document.getElementById('suspendUserModal'));
          suspendModal.show();
        };

        // 6. Handle Suspend / Activate Action
        window.handleUserSuspendAction = async function(userId, action, duration = '') {
          if (action === 'activate' && !confirm("Are you sure you want to remove the suspension for this user?")) return;

          const formData = new FormData();
          formData.append('user_id', userId);
          formData.append('action', action);
          if (action === 'suspend') formData.append('duration', duration);

          try {
            const response = await fetch('actions/users/suspend-user.php', {
              method: 'POST',
              body: formData
            });
            const result = await response.json();

            if (result.success) {
              alert(result.message);
              fetchUserManagementData(); // Instantly refresh the table to swap the icons
            } else {
              alert('Error: ' + result.message);
            }
          } catch (error) {
            console.error(error);
            alert('A network error occurred.');
          }
        };

        // 7. Wire up the Modal Confirm Button
        const confirmSuspendBtn = document.getElementById('confirmSuspendBtn');
        if (confirmSuspendBtn) {
          // Clone and replace to prevent multiple event listeners stacking up if you change tabs
          const newConfirmBtn = confirmSuspendBtn.cloneNode(true);
          confirmSuspendBtn.parentNode.replaceChild(newConfirmBtn, confirmSuspendBtn);

          newConfirmBtn.addEventListener('click', function() {
            const userId = this.getAttribute('data-target-user');
            const duration = document.getElementById('suspendDuration').value;

            if (!duration) {
              showAlert("error", 'Please select a suspension duration.');
              return;
            }

            // Hide the Bootstrap modal cleanly
            const modalEl = document.getElementById('suspendUserModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            // Trigger the backend process
            handleUserSuspendAction(userId, 'suspend', duration);

            // Reset the dropdown for next time
            document.getElementById('suspendDuration').value = '';
          });
        }
        // ==========================================
        // 8. Create New Role Modal Logic (Bulletproof)
        // ==========================================

        // Expose function globally so the HTML button can trigger it
        window.openCreateRoleModal = function() {
          document.getElementById('createRoleForm').reset();
          const roleModal = new bootstrap.Modal(document.getElementById('createRoleModal'));
          roleModal.show();
        };

        // Listen globally for ANY form submission, and intercept if it's our Role Form
        document.addEventListener('submit', async function(e) {
          if (e.target && e.target.id === 'createRoleForm') {
            e.preventDefault(); // Stop the page from reloading!

            const submitBtn = document.getElementById('saveRoleBtn');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Saving...';
            submitBtn.disabled = true;

            try {
              const formData = new FormData(e.target);
              const response = await fetch('actions/roles/create-role.php', {
                method: 'POST',
                body: formData
              });

              // 🚨 Crucial Fix: Read as text first to catch any hidden PHP/SQL errors that break JSON
              const rawText = await response.text();

              try {
                const result = JSON.parse(rawText);

                if (result.success) {
                  showAlert("success", result.message);


                  // Hide modal cleanly
                  const modalEl = document.getElementById('createRoleModal');
                  const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                  modalInstance.hide();

                  // Refresh the tables/UI instantly
                  if (typeof fetchUserManagementData === 'function') {
                    fetchUserManagementData();
                  }
                } else {
                  showAlert("error", 'Database Error: ' + result.message);

                }
              } catch (parseError) {
                // If the response wasn't valid JSON, it means PHP threw a fatal SQL error.
                console.error("PHP/SQL Error Output:", rawText);
                showAlert("error", "The server encountered an error. Please try again.");
              }
            } catch (error) {
              console.error('Role Creation Error:', error);
              showAlert("error", "A network error occurred while saving the role.");
            } finally {
              if (submitBtn) {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
              }
            }
          }
        });
      }

    });
  </script>

  <!-- Handle quick actions -->
  <script>
    function handleQuickAction(action) {

      const navLinks = document.querySelectorAll('.spa-link');
      navLinks.forEach(l => l.classList.remove('active-primary'));

      switch (action) {
        case 'create-new-project':
          document.querySelector("[data-view='projects']").classList.add('active-primary');
          loadView('create-project', 'Create New Project');
          break;

        case 'create-new-post':
          document.querySelector("[data-view='posts']").classList.add('active-primary');
          loadView('create-post', 'Create New Post');
          break;

        case 'add-new-publication':
          document.querySelector("[data-view='publications']").classList.add('active-primary');
          loadView('create-publication', 'Create Publication');
          break;

        default:
          document.querySelector("[data-view='publications']").classList.add('active-primary');
          loadView('dashboard', 'Dashboard');
          showAlert("error", "Something went wrong.");
          break;
      }

      if (window.innerWidth <= 991) {
        toggleMobileMenu();
      }

    }
  </script>

  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="alert" class="toast rounded-3 bg-white" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body d-flex gap-3 p-3">
        <span id="alert-icon-bg" class="rounded-circle d-grid text-white" style="width: 36px; height: 36px; place-items: center; aspect-ratio: 1;">
          <i id="alert-icon" data-lucide="check" width="18" height="18"></i>
        </span>
        <div class="w-100">
          <div class="d-flex justify-content-between">
            <strong id="alert-title" class="fs-6">Success!</strong>
            <i data-lucide="x" width="24" height="24" data-bs-dismiss="toast" aria-label="Close" style="cursor: pointer;"></i>
          </div>
          <span id="alert-message">Message.</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const toastEl = document.getElementById('alert');
    const toast = new bootstrap.Toast(toastEl);

    function showAlert(type, message) {
      const iconEl = toastEl.querySelector("#alert-icon");
      const iconBgEl = toastEl.querySelector("#alert-icon-bg");
      const titleEl = toastEl.querySelector("#alert-title");
      const messageEl = toastEl.querySelector("#alert-message");

      if (type === "success") {
        iconEl.dataset.lucide = "check";
        iconBgEl.style.backgroundColor = "#12B76A";
        titleEl.textContent = "Success!"
        messageEl.textContent = message;
        toast.show();
        return;
      }

      if (type === "error") {
        iconEl.dataset.lucide = "x";
        iconBgEl.style.backgroundColor = "#CB2045";
        titleEl.textContent = "Error!"
        messageEl.textContent = message;
        toast.show();
        return;
      }
    }
  </script>

</body>

</html>