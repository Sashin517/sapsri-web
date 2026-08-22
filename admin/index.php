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

  <!-- GLightbox CSS & JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/project-sedna/vendor/bootstrap/bootstrap-custom.css">
  <link rel="stylesheet" href="/project-sedna/vendor/daterangepicker/daterangepicker-bs5.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <!-- Load Mammoth.js Library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Stylesheet for the Admin Panel -->
  <link rel="stylesheet" href="assets/css/admin-style.css">

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
        <a href="#" class="nav-link spa-link" data-view="projects" data-title="Projects">
          <i data-lucide="folder"></i> <span class="nav-link-text">Projects</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="posts" data-title="Posts">
          <i data-lucide="file-text"></i> <span class="nav-link-text">Posts</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="publications" data-title="Publications">
          <i data-lucide="book-open"></i> <span class="nav-link-text">Publications</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-link spa-link" data-view="users" data-title="Users">
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
    <header class="top-navbar px-4">
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

  <!-- Universal Delete Modal -->
  <div class="modal fade" id="universalDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">

        <!-- Close Button -->
        <div class="modal-header border-bottom-0 pb-0 justify-content-end pt-3 px-3">
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body text-center px-4 pt-0 pb-4">

          <!-- Warning Icon Circle -->
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background-color: #FFF3CD;">
            <i data-lucide="triangle-alert" style="width: 32px; height: 32px; color: #FFA000;"></i>
          </div>

          <!-- Dynamic Text -->
          <h4 class="fw-bold mb-2 text-dark" id="deleteModalTitle" style="font-size: 1.25rem;">Delete Item</h4>
          <p class="text-muted mb-4" id="deleteModalText" style="font-size: 0.95rem;">Are you sure want to delete this item? It will delete permanently.</p>

          <!-- Action Buttons -->
          <div class="d-flex gap-3">
            <button type="button" class="btn flex-grow-1 bg-white" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500; padding: 0.7rem; border: 1px solid #ced4da; color: #212529;">Cancel</button>

            <button type="button" class="btn flex-grow-1 text-white shadow-sm" id="confirmDeleteBtn" style="background-color: #C9184A; border-radius: 8px; font-weight: 500; padding: 0.7rem; border: none;">Yes, Delete</button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Universal Alert Modal -->
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

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Quill Rich Text Editor -->
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <!-- PDF JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" integrity="sha512-q+4liFwdPC/bNdhUpZx6aXDx/h77yEQtn4I1slHydcbZK34nLaR3cAeYSJshoxIOq3mjEf7xJE8YWIUHMn+oCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  </script>

  <!-- IMPORTANT: Pass PHP session variables safely to JavaScript -->
  <script>
    window.sessionData = {
      userName: "<?php echo addslashes($adminName); ?>",
      userRole: "<?php echo addslashes($adminRole); ?>"
    };
  </script>

  <!-- EXTERNAL JS -->
  <script src="assets/js/admin-script.js?v=1.1"></script>

</body>

</html>