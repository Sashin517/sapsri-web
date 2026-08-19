// Initialize Icons for static elements
lucide.createIcons();

// Global Elements
const sidebar = document.getElementById("sidebar");
const mainContent = document.getElementById("main-content");
const sidebarLogo = document.getElementById("sidebar-logo");
const mobileOverlay = document.getElementById("mobile-overlay");

// Logic: Toggle Sidebar (Desktop)
function toggleSidebar() {
  if (window.innerWidth <= 991) return;
  sidebar.classList.toggle("collapsed");
  mainContent.classList.toggle("expanded");
  if (sidebar.classList.contains("collapsed")) {
    sidebarLogo.src = "assets/img/sapsri-logo-icon.png";
  } else {
    sidebarLogo.src = "assets/img/sapsri-logo.png";
  }
}

// Logic: Toggle Sidebar (Mobile)
function toggleMobileMenu() {
  sidebar.classList.toggle("mobile-open");
  if (sidebar.classList.contains("mobile-open")) {
    mobileOverlay.classList.remove("d-none");
    sidebarLogo.src = "assets/img/sapsri-logo.png";
  } else {
    mobileOverlay.classList.add("d-none");
  }
}

// Logic: Handle quick actions links
function handleQuickAction(action) {
  const navLinks = document.querySelectorAll(".spa-link");
  navLinks.forEach((l) => l.classList.remove("active-primary"));

  switch (action) {
    case "create-new-project":
      document.querySelector("[data-view='projects']").classList.add("active-primary");
      loadView("create-project", "Create New Project");
      break;

    case "create-new-post":
      document.querySelector("[data-view='posts']").classList.add("active-primary");
      loadView("create-post", "Create New Post");
      break;

    case "add-new-publication":
      document.querySelector("[data-view='publications']").classList.add("active-primary");
      loadView("create-publication", "Create Publication");
      break;

    default:
      document.querySelector("[data-view='dashboard']").classList.add("active-primary");
      loadView("dashboard", "Dashboard");
      showAlert("error", "An unexpected action occurred.");
      break;
  }

  if (window.innerWidth <= 991) {
    toggleMobileMenu();
  }
}
// ==========================================
// UNIVERSAL ALERT MODAL LOGIC
// ==========================================
const toastEl = document.getElementById("alert");
const toast = new bootstrap.Toast(toastEl);

function showAlert(type, message) {
  const iconEl = toastEl.querySelector("#alert-icon");
  const iconBgEl = toastEl.querySelector("#alert-icon-bg");
  const titleEl = toastEl.querySelector("#alert-title");
  const messageEl = toastEl.querySelector("#alert-message");

  if (type === "success") {
    iconEl.dataset.lucide = "check";
    iconBgEl.style.backgroundColor = "#12B76A";
    titleEl.textContent = "Success!";
    messageEl.textContent = message;
    toast.show();
    return;
  }

  if (type === "error") {
    iconEl.dataset.lucide = "x";
    iconBgEl.style.backgroundColor = "#CB2045";
    titleEl.textContent = "Error!";
    messageEl.textContent = message;
    toast.show();
    return;
  }
}

// ==========================================
// UNIVERSAL DELETE MODAL LOGIC
// ==========================================
function openDeleteModal(itemId, itemName, itemType, endpointUrl, callbackFunctionName) {
  const typeCapitalized = itemType.charAt(0).toUpperCase() + itemType.slice(1);

  // Set dynamic text matching the design
  document.getElementById("deleteModalTitle").innerText = `Delete ${typeCapitalized} “${itemName}”`;
  document.getElementById("deleteModalText").innerText =
    `Are you sure want to delete this ${itemType.toLowerCase()}? ${typeCapitalized} will delete permanently.`;

  const confirmBtn = document.getElementById("confirmDeleteBtn");

  // Clone the button to wipe out any old event listeners from previous clicks
  const newConfirmBtn = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

  // Attach new deletion logic
  newConfirmBtn.addEventListener("click", async function () {
    this.innerText = "Deleting...";
    this.disabled = true;

    try {
      const formData = new FormData();
      formData.append("id", itemId);

      const response = await fetch(endpointUrl, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Hide modal
        const modalEl = document.getElementById("universalDeleteModal");
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        modalInstance.hide();

        // Trigger the table refresh function dynamically
        if (typeof window[callbackFunctionName] === "function") {
          window[callbackFunctionName]();
        } else if (typeof window[callbackFunctionName] === "string") {
          eval(callbackFunctionName + "()");
        }
        showAlert("success", `${typeCapitalized} deleted successfully.`);
      } else {
        console.error("Backend Error:", result.message);
        showAlert("error", "Deletion failed. Please try again.");
      }
    } catch (error) {
      console.error("Delete Error:", error);
      showAlert("error", "A network error occurred while deleting.");
    } finally {
      this.innerText = "Yes, Delete";
      this.disabled = false;
    }
  });

  // Show Modal
  const deleteModal = new bootstrap.Modal(document.getElementById("universalDeleteModal"));
  deleteModal.show();
}

// ==========================================
// MAIN SPA ROUTER & MODULE INITIALIZER
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  const appContent = document.getElementById("app-content");
  const pageTitle = document.getElementById("page-title");

  // --- SPA ROUTER FUNCTION ---
  async function loadView(viewName, title, params = {}) {
    appContent.innerHTML =
      '<div class="d-flex justify-content-center mt-5"><div class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status"></div></div>';

    try {
      const response = await fetch(`views/${viewName}.php`);
      if (!response.ok) throw new Error("View not found");

      const html = await response.text();
      appContent.innerHTML = html;

      if (pageTitle) pageTitle.textContent = title;
      lucide.createIcons();

      if (viewName === "dashboard") initDashboardScript();
      else if (viewName === "projects") initProjectsScript();
      else if (viewName === "posts") initPostsScript();
      else if (viewName === "publications") initPublicationsScript();
      else if (viewName === "users") initUsersScript();
      else if (viewName === "create-project") initCreateProjectScript();
      else if (viewName === "edit-project") initEditProjectScript(params);
      else if (viewName === "create-post") initCreatePostScript();
      else if (viewName === "edit-post") initEditPostScript(params);
      else if (viewName === "create-publication") initCreatePublicationScript();
      else if (viewName === "edit-publication") initEditPublicationScript(params);
    } catch (error) {
      appContent.innerHTML = `<div class="alert alert-danger m-4">Failed to load interface. Please try refreshing the page.</div>`;
      console.error("View Load Error:", error);
    }
  }

  window.loadView = loadView;

  // ==========================================
  // GLOBAL CHUNK UPLOADER (For Large Files)
  // ==========================================
  window.uploadedTempFiles = {}; // Store temp paths for form submission

  async function uploadFileInChunks(file, idPrefix, fileKey) {
    return new Promise((resolve, reject) => {
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      const progressBar = document.getElementById(idPrefix + "ProgressBar");
      const progressText = document.getElementById(idPrefix + "ProgressText");

      if (!contentDiv || !progressDiv) return resolve(null);

      // Show progress UI
      contentDiv.style.display = "none";
      progressDiv.style.display = "block";
      progressBar.className = "progress-fill";
      progressBar.style.width = "0%";
      progressText.className = "upload-status-text";
      progressText.innerText = `Uploading... 0%`;

      const chunkSize = 2 * 1024 * 1024; // Slice into 2MB chunks
      const totalChunks = Math.ceil(file.size / chunkSize);
      const uniqueFileName = Date.now() + "_" + file.name.replace(/[^a-zA-Z0-9.]/g, "_");
      let currentChunk = 0;

      function uploadNextChunk() {
        const start = currentChunk * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append("chunk", chunk);
        formData.append("chunkIndex", currentChunk);
        formData.append("totalChunks", totalChunks);
        formData.append("fileName", uniqueFileName);

        fetch("actions/upload-chunk.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (!data.success) throw new Error(data.message || "Chunk failed");

            currentChunk++;
            const progress = Math.round((currentChunk / totalChunks) * 100);
            progressBar.style.width = progress + "%";
            progressText.innerText = `Uploading... ${progress}%`;

            if (currentChunk < totalChunks) {
              uploadNextChunk(); // Call recursively until done
            } else {
              // Upload 100% Complete!
              progressBar.classList.add("success");
              progressText.classList.add("success");
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
              if (window.lucide) lucide.createIcons();

              // Save the server's temp path to our global object
              window.uploadedTempFiles[fileKey] = data.temp_path;

              setTimeout(() => {
                progressDiv.style.display = "none";
                resolve(data.temp_path);
              }, 800);
            }
          })
          .catch((error) => {
            console.error("Upload Error:", error);
            progressText.innerHTML = '<i data-lucide="x-circle" style="width:14px; color:red;"></i> Upload Failed!';
            progressBar.style.backgroundColor = "#CB2045";
            reject(error);
          });
      }

      uploadNextChunk(); // Start the loop
    });
  }

  // --- SIDEBAR NAVIGATION HANDLER ---
  const navLinks = document.querySelectorAll(".spa-link");

  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();

      navLinks.forEach((l) => l.classList.remove("active-primary"));
      e.currentTarget.classList.add("active-primary");

      const targetView = e.currentTarget.getAttribute("data-view");
      const targetTitle = e.currentTarget.getAttribute("data-title");

      loadView(targetView, targetTitle);

      if (window.innerWidth <= 991) {
        toggleMobileMenu();
      }
    });
  });

  // Load default view on start
  loadView("dashboard", "Dashboard");

  // ==========================================
  // --- VIEW: DASHBOARD LOGIC ---
  // ==========================================
  function initDashboardScript() {
    const nameEl = document.getElementById("dyn-user-name");
    if (nameEl) nameEl.innerText = window.sessionData.userName;

    function fetchDashboardStats(range = "today", start = "", end = "") {
      const modules = ["projects", "publications", "users", "posts"];
      const params = new URLSearchParams({ range: range, start: start, end: end });

      modules.forEach((mod) => {
        if (document.getElementById(`val-${mod}`)) {
          document.getElementById(`val-${mod}`).innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }
      });

      fetch(`actions/dashboard/fetch-stats.php?${params.toString()}`)
        .then((res) => res.json())
        .then((result) => {
          if (result.status === "success") {
            modules.forEach((mod) => {
              if (document.getElementById(`val-${mod}`)) {
                document.getElementById(`val-${mod}`).innerText = result.data[mod].count;
                document.getElementById(`trend-${mod}`).innerText = result.data[mod].trend;
                document.getElementById(`trend-${mod}`).className =
                  `badge-trend status-pill ${result.data[mod].trend_class}`;
                document.getElementById(`desc-${mod}`).innerText = result.period_text;
              }
            });
          }
        })
        .catch((err) => console.error("KPI Error:", err));
    }

    window.loadRecentContent = function (page = 1) {
      const tbody = document.getElementById("recent-content-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading content...</td></tr>';

      fetch(`actions/dashboard/fetch-recent-content.php?page=${page}`)
        .then((res) => res.json())
        .then((result) => {
          if (result.status === "success") {
            tbody.innerHTML = "";
            if (result.data.length === 0) {
              tbody.innerHTML =
                '<tr><td colspan="5" class="text-center text-muted py-4">No recent content found.</td></tr>';
            } else {
              result.data.forEach((item) => {
                let pillClass = ["published", "completed", "ongoing", "active"].includes(item.status.toLowerCase())
                  ? "status-published"
                  : "status-draft";
                tbody.insertAdjacentHTML(
                  "beforeend",
                  `<tr><td class="fw-medium">${item.title}</td><td>${item.type}</td><td><span class="status-pill ${pillClass}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td><td>${item.lead}</td><td>${item.created_date}</td></tr>`,
                );
              });
            }
            renderPagination(result.pagination);
            lucide.createIcons();
          }
        })
        .catch((err) => console.error("Table Error:", err));
    };

    function renderPagination(pagination) {
      const pDiv = document.getElementById("recent-content-pagination");
      if (!pDiv) return;
      pDiv.innerHTML = "";

      const { current_page, total_pages } = pagination;
      if (total_pages <= 1) return;

      const prevDisabled = current_page === 1 ? "disabled" : "";
      pDiv.innerHTML += `<a href="#" class="page-btn ${prevDisabled}" onclick="event.preventDefault(); loadRecentContent(1)"><i data-lucide="chevrons-left" style="width:16px;"></i> First</a>`;
      pDiv.innerHTML += `<a href="#" class="page-btn ${prevDisabled}" onclick="event.preventDefault(); loadRecentContent(${current_page - 1})"><i data-lucide="chevron-left" style="width:16px;"></i> Back</a>`;

      let startPage = Math.max(1, current_page - 1);
      let endPage = Math.min(total_pages, current_page + 1);

      if (startPage > 1) pDiv.innerHTML += `<span class="d-flex align-items-end px-1">...</span>`;
      for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === current_page ? "active" : "";
        pDiv.innerHTML += `<a href="#" class="page-btn ${activeClass}" onclick="event.preventDefault(); loadRecentContent(${i})">${i}</a>`;
      }
      if (endPage < total_pages) pDiv.innerHTML += `<span class="d-flex align-items-end px-1">...</span>`;

      const nextDisabled = current_page === total_pages ? "disabled" : "";
      pDiv.innerHTML += `<a href="#" class="page-btn ${nextDisabled}" onclick="event.preventDefault(); loadRecentContent(${current_page + 1})">Next <i data-lucide="chevron-right" style="width:16px;"></i></a>`;
      pDiv.innerHTML += `<a href="#" class="page-btn ${nextDisabled}" onclick="event.preventDefault(); loadRecentContent(${total_pages})">Last <i data-lucide="chevrons-right" style="width:16px;"></i></a>`;
    }

    const dpBtn = document.getElementById("dateDropdownBtn");
    const customDateBtn = document.getElementById("customDateBtn");

    // shoud be fixed. item class should set to active like projects.
    document.querySelectorAll(".date-filter").forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        const range = e.target.getAttribute("data-range");
        dpBtn.innerHTML = `${e.target.innerText} <i data-lucide="chevron-down" style="width: 16px;"></i>`;
        lucide.createIcons();
        resetDateRangePicker(customDateBtn);
        fetchDashboardStats(range);
      });
    });

    if (customDateBtn) {
      customDateBtn.addEventListener("click", (event) => {
        event.stopPropagation();
      });

      if (typeof $ !== "undefined") {
        $(function () {
          $(customDateBtn).daterangepicker(
            {
              opens: "left",
              drops: "auto",
            },
            function (start, end) {
              const startDate = start.format("YYYY-MM-DD");
              const endDate = end.format("YYYY-MM-DD");
              dpBtn.innerHTML = `${startDate} - ${endDate} <i data-lucide="chevron-down" style="width: 16px;"></i>`;
              lucide.createIcons();
              fetchDashboardStats("custom", startDate, endDate);
            },
          );
          $(customDateBtn).on("show.daterangepicker", function (ev, picker) {
            picker.container.find(".drp-calendar").on("click", function (e) {
              e.stopPropagation();
            });
          });
        });
      }
    }

    fetchDashboardStats("today");
    loadRecentContent(1);
  }

  function resetDateRangePicker(btn) {
    if (typeof moment !== "undefined" && typeof $ !== "undefined") {
      const today = moment();
      const $button = $(btn);
      const picker = $button.data("daterangepicker");
      if (picker) {
        picker.setStartDate(today);
        picker.setEndDate(today);
      }
    }
  }
  // ==========================================
  // --- VIEW: PROJECTS LOGIC ---
  // ==========================================
  function initProjectsScript_Old() {
    window.loadProjects = function () {
      const tbody = document.getElementById("projects-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading projects...</td></tr>';

      fetch("actions/projects/fetch-projects.php")
        .then((res) => res.json())
        .then((data) => {
          const projects = Array.isArray(data) ? data : Object.values(data);
          tbody.innerHTML = "";

          if (projects.length === 0 || projects[0].error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No projects found.</td></tr>';
            return;
          }

          projects.forEach((item) => {
            let pillClass = (item.status || "").toLowerCase() === "published" ? "status-published" : "status-draft";
            let createdDate = item.created_date ? item.created_date.split(" ")[0] : "N/A";
            let phaseText =
              item.project_phase === "past"
                ? '<span class="text-secondary fw-medium">Past</span>'
                : '<span class="text-success fw-medium">Ongoing</span>';

            const row = `
                <tr>
                  <td class="fw-medium">${item.title}</td>
                  <td>${phaseText}</td>
                  <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
                  <td>${item.project_lead || "System"}</td>
                  <td>${createdDate}</td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit" onclick="loadView('edit-project', 'Projects', {id: ${item.id}})"><i data-lucide="edit" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${item.title.replace(/'/g, "\\'")}', 'project', 'actions/projects/delete-project.php', 'loadProjects')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                  </td>
                </tr>
              `;
            tbody.insertAdjacentHTML("beforeend", row);
          });
          lucide.createIcons();
        })
        .catch((err) => {
          console.error("Projects Fetch Error:", err);
          tbody.innerHTML =
            '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load projects.</td></tr>';
        });
    };
    loadProjects();
  }

  function initProjectsScript() {
    // State configuration
    let state = {
      search: "",
      filter: "all", // Default selected button filter
      dateRange: "all_time", // Default selected date range
      startDate: "",
      endDate: "",
      page: 1,
      limit: 10, // Default selected rows per page
    };

    let searchTimeout = null;

    window.loadProjects = function () {
      const tbody = document.getElementById("projects-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading projects...</td></tr>';

      const params = new URLSearchParams({
        search: state.search,
        filter: state.filter,
        date_range: state.dateRange,
        start_date: state.startDate,
        end_date: state.endDate,
        page: state.page,
        limit: state.limit,
      });

      fetch(`actions/projects/fetch-projects.php?${params.toString()}`)
        .then((res) => res.json())
        .then((resData) => {
          const projects = resData.projects || [];
          const pagination = resData.pagination || { total_records: 0, total_pages: 1, current_page: 1, limit: 10 };

          tbody.innerHTML = "";

          if (projects.length === 0 || projects[0]?.error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No projects found.</td></tr>';
            renderPagination(pagination);
            return;
          }

          projects.forEach((item) => {
            let pillClass =
              item.status.toLowerCase() === "published"
                ? "status-published"
                : item.status.toLowerCase() === "archived"
                  ? "status-archived"
                  : "status-draft";
            let createdDate = item.created_date ? item.created_date.split(" ")[0] : "N/A";
            let phaseText =
              (item.project_phase || "").toLowerCase() === "past"
                ? '<span class="text-secondary fw-medium">Past</span>'
                : '<span class="text-success fw-medium">Ongoing</span>';

            const row = `
            <tr>
              <td class="fw-medium">${item.title}</td>
              <td>${phaseText}</td>
              <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
              <td>${item.project_lead || "System"}</td>
              <td>${createdDate}</td>
              <td>
                <div class="hstack gap-1 justify-content-end">
                  <a href="/project-sedna/${item.project_phase}-project?id=${item.id}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary border-0 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></a>
                  <button class="btn btn-sm btn-light text-warning border-0 shadow-sm" title="Edit" onclick="loadView('edit-project', 'Projects', {id: ${item.id}})"><i data-lucide="edit" style="width: 16px;"></i></button>
                  <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${(item.title || "").replace(/'/g, "\\'")}', 'project', 'actions/projects/delete-project.php', 'loadProjects')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                </div>
              </td>
            </tr>
          `;
            tbody.insertAdjacentHTML("beforeend", row);
          });

          if (window.lucide) {
            lucide.createIcons();
          }

          renderPagination(pagination);
        })
        .catch((err) => {
          console.error("Projects Fetch Error:", err);
          tbody.innerHTML =
            '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load projects.</td></tr>';
        });
    };

    // Render pagination buttons and entries counter
    function renderPagination(pagination) {
      const container = document.getElementById("projects-pagination");
      const infoText = document.getElementById("pagination-info-text");
      if (!container) return;

      const { total_records, total_pages, current_page, limit } = pagination;

      // Update record summary
      if (infoText) {
        const start = total_records === 0 ? 0 : (current_page - 1) * limit + 1;
        const end = Math.min(current_page * limit, total_records);
        infoText.innerText = `Showing ${start}-${end} of ${total_records}`;
      }

      if (total_pages <= 1) {
        container.innerHTML = "";
        return;
      }

      let html = `<nav><ul class="pagination pagination-sm table-pagination gap-1 mb-0">`;

      // First Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changeProjectPage(1)"><i data-lucide="chevrons-left" style="width: 16px;"></i>First</button>
      </li>
      `;

      // Back Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changeProjectPage(${current_page - 1})"><i data-lucide="chevron-left" style="width: 16px;"></i>Back</button>
      </li>
      `;

      // Page Number Buttons
      for (let i = 1; i <= total_pages; i++) {
        if (i === 1 || i === total_pages || (i >= current_page - 1 && i <= current_page + 1)) {
          html += `
          <li class="page-item ${i === current_page ? "active" : ""}">
            <button class="page-link" onclick="changeProjectPage(${i})">${i}</button>
          </li>
        `;
        } else if (i === current_page - 2 || i === current_page + 2) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }

      // Next Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changeProjectPage(${current_page + 1})">Next<i data-lucide="chevron-right" style="width: 16px;"></i></button>
      </li>
      `;

      // Last Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changeProjectPage(${total_pages})">Last<i data-lucide="chevrons-right" style="width: 16px;"></i></button>
      </li>
      `;

      html += `</ul></nav>`;
      container.innerHTML = html;
      lucide.createIcons();
    }

    // Page switcher attached to global scope
    window.changeProjectPage = function (pageNumber) {
      state.page = pageNumber;
      loadProjects();
    };

    // Event Listener Bindings
    const searchInput = document.getElementById("project-search-input");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          state.search = e.target.value.trim();
          state.page = 1;
          loadProjects();
        }, 300);
      });
    }

    const filterPills = document.querySelectorAll("#project-status-pills .filter-pill");
    filterPills.forEach((pill) => {
      pill.addEventListener("click", function () {
        filterPills.forEach((p) => {
          p.classList.remove("active");
          p.querySelector("[data-lucide]").classList.add("d-none");
        });
        this.classList.add("active");
        this.querySelector("[data-lucide]").classList.remove("d-none");
        state.filter = this.getAttribute("data-filter");
        state.page = 1;
        loadProjects();
      });
    });

    const dateOptions = document.querySelectorAll("#date-filter-options .dropdown-item");
    const dateLabel = document.getElementById("date-filter-label");
    const customDateItem = document.getElementById("customDateItem");

    dateOptions.forEach((option) => {
      option.addEventListener("click", function (e) {
        e.preventDefault();

        const selectedRange = this.getAttribute("data-range");
        if (!selectedRange) return;
        resetDateRangePicker(customDateItem);

        if (selectedRange === "custom") {
          e.stopPropagation();
        } else {
          dateOptions.forEach((opt) => opt.classList.remove("active"));
          this.classList.add("active");

          if (dateLabel) dateLabel.innerText = this.innerText;

          state.dateRange = selectedRange;
          state.startDate = "";
          state.endDate = "";
          state.page = 1;
          loadProjects();
        }
      });
    });

    // date range picker
    if (typeof $ !== "undefined") {
      $(function () {
        $(customDateItem).daterangepicker(
          {
            opens: "left",
            drops: "up",
          },
          function (start, end) {
            const startDate = start?.format("YYYY-MM-DD") || "";
            const endDate = end?.format("YYYY-MM-DD") || "";
            dateLabel.textContent = `${startDate} - ${endDate}`;

            state.startDate = startDate;
            state.endDate = endDate;
            state.dateRange = "custom";
            state.page = 1;
            loadProjects();
          },
        );
        $(customDateItem).on("show.daterangepicker", function (ev, picker) {
          picker.container.find(".drp-calendar").on("click", function (e) {
            e.stopPropagation();
          });
        });
      });
    }

    const rowsSelect = document.getElementById("projects-rows-per-page");
    if (rowsSelect) {
      rowsSelect.addEventListener("change", function () {
        state.limit = parseInt(this.value, 10);
        state.page = 1;
        loadProjects();
      });
    }

    // Execute initial load with default values
    loadProjects();
  }

  // ==========================================
  // --- VIEW: CREATE PROJECT LOGIC ---
  // ==========================================
  function initCreateProjectScript() {
    new SAPSRIMultiSelect("projectImpactArea", "Add Impact Areas...");
    const phaseToggle = document.getElementById("projectPhaseToggle");
    const phaseLabel = document.getElementById("phaseLabel");
    const startDateContainer = document.getElementById("startDateContainer");
    const endDateContainer = document.getElementById("endDateContainer");

    if (phaseToggle) {
      phaseToggle.addEventListener("change", (e) => {
        phaseLabel.innerText = e.target.checked ? "Ongoing" : "Past";
        phaseLabel.className = e.target.checked
          ? "form-check-label fw-medium ms-2 text-success"
          : "form-check-label fw-medium ms-2 text-secondary";
        if (endDateContainer && startDateContainer) {
          if (e.target.checked) {
            startDateContainer.classList.remove("col-md-6");
            startDateContainer.classList.add("col-md-12");
            endDateContainer.style.display = "none";
            document.getElementById("projectEndDate").value = "";
          } else {
            startDateContainer.classList.remove("col-md-12");
            startDateContainer.classList.add("col-md-6");
            endDateContainer.style.display = "block";
          }
        }
      });
    }

    if (document.getElementById("editor")) {
      const Icons = Quill.import("ui/icons");
      Icons.undo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
      Icons.redo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

      var quill = new Quill("#editor", {
        theme: "snow",
        modules: {
          history: { delay: 1000, maxStack: 100, userOnly: true },
          toolbar: {
            container: [
              ["undo", "redo"],
              [{ size: ["small", false, "large", "huge"] }],
              ["bold", "italic", "underline", "strike"],
              [{ align: "" }, { align: "center" }, { align: "right" }, { align: "justify" }],
              [{ list: "ordered" }, { list: "bullet" }],
              ["link"],
            ],
            handlers: {
              undo: function () {
                this.quill.history.undo();
              },
              redo: function () {
                this.quill.history.redo();
              },
            },
          },
        },
      });

      // -------------------------------------------------------------
      // Word Document Extractor Logic
      // -------------------------------------------------------------
      const radioManual = document.getElementById("descManual");
      const radioWord = document.getElementById("descWord");
      const manualSection = document.getElementById("manualDescSection");
      const wordSection = document.getElementById("wordUploadSection");

      // Toggle UI Elements
      function updateDescUI() {
        if (radioManual.checked) {
          wordSection.style.display = "none";
        } else {
          wordSection.style.display = "block";
        }
        if (window.lucide) lucide.createIcons();
      }
      
      if (radioManual && radioWord) {
        radioManual.addEventListener("change", updateDescUI);
        radioWord.addEventListener("change", updateDescUI);
      }

      // Handle Upload and Extraction
      window.handleWordUpload = function (input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        if (!file.name.toLowerCase().endsWith('.docx')) {
          showAlert('error', 'Please upload a valid modern Word document (.docx).');
          return;
        }

        // Show Progress UI
        const contentDiv = document.getElementById("wordContent");
        const progressDiv = document.getElementById("wordProgress");
        const progressBar = document.getElementById("wordProgressBar");
        const progressText = document.getElementById("wordProgressText");

        contentDiv.style.display = "none";
        progressDiv.style.display = "block";
        progressBar.className = "progress-fill";
        progressBar.style.width = "60%";
        progressText.className = "upload-status-text";
        progressText.innerText = `Extracting Text...`;

        // Read file into ArrayBuffer for Mammoth
        const reader = new FileReader();
        reader.onload = function(event) {
          const arrayBuffer = event.target.result;
          
          // Execute Mammoth.js Parser
          mammoth.convertToHtml({arrayBuffer: arrayBuffer})
            .then(function(result) {
              const html = result.value; // The pure, semantic HTML generated from Word
              
              // Finish Progress Bar
              progressBar.style.width = "100%";
              progressBar.classList.add("success");
              progressText.classList.add("success");
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Extraction Complete!';
              if (window.lucide) lucide.createIcons();

              setTimeout(() => {
                // 1. Inject Extracted HTML directly into Quill Editor
                quill.clipboard.dangerouslyPasteHTML(html);
                
                // 2. Programmatically switch the UI back to "Write Manually"
                radioManual.checked = true;
                updateDescUI();
                
                // 3. Reset the Upload Area for future use
                input.value = "";
                contentDiv.style.display = "block";
                progressDiv.style.display = "none";
                progressBar.classList.remove("success");
                progressText.classList.remove("success");
                progressBar.style.width = "0%";
                
                showAlert('success', 'Word document imported successfully! Please review formatting.');
              }, 800);
            })
            .catch(function(err) {
              console.error("Mammoth Parse Error:", err);
              showAlert('error', 'Failed to extract text. The document may be corrupted or password-protected.');
              contentDiv.style.display = "block";
              progressDiv.style.display = "none";
              input.value = "";
            });
        };
        
        reader.readAsArrayBuffer(file);
      };

    }

    function simulateUpload(idPrefix, callback) {
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      const progressBar = document.getElementById(idPrefix + "ProgressBar");
      const progressText = document.getElementById(idPrefix + "ProgressText");

      if (!contentDiv || !progressDiv) {
        callback();
        return;
      }

      contentDiv.style.display = "none";
      progressDiv.style.display = "block";
      progressBar.className = "progress-fill";
      progressBar.style.width = "0%";
      progressText.className = "upload-status-text";

      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress >= 100) progress = 100;
        progressBar.style.width = progress + "%";
        progressText.innerText = `Uploading... ${Math.round(progress)}%`;

        if (progress === 100) {
          clearInterval(interval);
          progressBar.classList.add("success");
          progressText.classList.add("success");
          progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
          lucide.createIcons();
          setTimeout(() => {
            progressDiv.style.display = "none";
            callback();
          }, 800);
        }
      }, 100);
    }

    window.handleImageUpload = function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(idPrefix, () => {
            document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
            document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      document.getElementById(idPrefix + "Input").value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";
    };

    let metricsCount = { 1: 0, 2: 0 };
    window.addMetricRow = function (sectionId) {
      if (metricsCount[sectionId] >= 3) return;
      metricsCount[sectionId]++;
      const container = document.getElementById(`metricsContainerSec${sectionId}`);
      const rowId = `sec${sectionId}_metric${metricsCount[sectionId]}`;

      const rowHTML = `
          <div class="metric-row" id="row_${rowId}" draggable="true">
            <div class="drag-handle"><i data-lucide="grip-vertical" class="text-muted"></i></div>
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
      container.insertAdjacentHTML("beforeend", rowHTML);
      lucide.createIcons();
      initDragAndDrop(container);
      if (metricsCount[sectionId] >= 3) document.getElementById(`addMetricSec${sectionId}Btn`).disabled = true;
    };

    window.removeMetricRow = function (sectionId, rowId) {
      document.getElementById(rowId).remove();
      metricsCount[sectionId]--;
      document.getElementById(`addMetricSec${sectionId}Btn`).disabled = false;
    };

    window.handleMetricIconUpload = function (input, rowId) {
      if (input.files && input.files[0]) {
        if (input.files[0].type !== "image/png") {
          showAlert("error", "Only PNG images are allowed for icons.");
          return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("icon_ph_" + rowId).style.display = "none";
          const img = document.getElementById("icon_preview_" + rowId);
          img.src = e.target.result;
          img.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeMetricIcon = function (event, rowId) {
      event.stopPropagation();
      document.getElementById("icon_" + rowId).value = "";
      const img = document.getElementById("icon_preview_" + rowId);
      img.src = "";
      img.style.display = "none";
      document.getElementById("icon_ph_" + rowId).style.display = "block";
    };

    let draggedRow = null;
    function initDragAndDrop(container) {
      const rows = container.querySelectorAll(".metric-row");
      rows.forEach((row) => {
        row.addEventListener("dragstart", () => {
          draggedRow = row;
          setTimeout(() => row.classList.add("dragging"), 0);
        });
        row.addEventListener("dragend", () => {
          row.classList.remove("dragging");
          draggedRow = null;
        });
      });

      container.addEventListener("dragover", (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        if (draggedRow) {
          if (afterElement == null) container.appendChild(draggedRow);
          else container.insertBefore(draggedRow, afterElement);
        }
      });
    }

    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll(".metric-row:not(.dragging)")];
      return draggableElements.reduce(
        (closest, child) => {
          const box = child.getBoundingClientRect();
          const offset = y - box.top - box.height / 2;
          if (offset < 0 && offset > closest.offset) return { offset: offset, element: child };
          else return closest;
        },
        { offset: Number.NEGATIVE_INFINITY },
      ).element;
    }

    addMetricRow(1);
    addMetricRow(2);

    let storyCount = 0;
    window.addStory = function () {
      storyCount++;
      const container = document.getElementById("storiesWrapper");
      const storyId = `story${storyCount}`;

      const storyHTML = `
          <div class="section-card position-relative" id="container_${storyId}">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0 fs-6">Story ${storyCount}</h5>
              ${storyCount > 1 ? `<button type="button" class="btn btn-link text-danger p-0" onclick="removeStory('container_${storyId}')"><i data-lucide="minus-circle"></i></button>` : ""}
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium text-muted small">Subject Image</label>
              <div class="upload-area p-4" onclick="document.getElementById('${storyId}Input').click()">
                <div class="upload-content" id="${storyId}Content">
                  <i data-lucide="upload" class="upload-icon"></i>
                  <p class="mb-0 text-muted small">Click to upload image</p>
                </div>
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
      container.insertAdjacentHTML("beforeend", storyHTML);
      lucide.createIcons();
    };

    window.removeStory = function (containerId) {
      document.getElementById(containerId).remove();
    };

    window.handleStoryUpload = function (input, storyId) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(storyId, () => {
            document.getElementById(storyId + "PreviewWrapper").style.display = "block";
            document.getElementById(storyId + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };
    addStory();

    window.galleryFilesArray = [];
    window.handleGalleryUpload = async function (input, idPrefix = "gallery") {
      const container = document.getElementById("galleryPreviewContainer");

      if (input.files) {
        for (const file of Array.from(input.files)) {
          const uniqueId = "gal_" + Math.random().toString(36).substr(2, 9);
          const isVideo = file.type.startsWith("video/");

          const injectHTML = (src, showPlayBtn) => {
            const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : "";
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
            container.insertAdjacentHTML("beforeend", colHTML);
            if (window.lucide) lucide.createIcons();
          };

          if (isVideo) {
            // Upload chunk immediately
            const tempPath = await uploadFileInChunks(file, idPrefix, uniqueId);
            
            // AWAIT the canvas extraction to guarantee the thumbnail exists
            const thumbBlob = await new Promise((resolve) => {
              const video = document.createElement("video");
              video.preload = "metadata";
              video.src = URL.createObjectURL(file);
              video.onloadeddata = () => { video.currentTime = 1; };
              video.onseeked = () => {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 360;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.85);
              };
              video.onerror = () => resolve(null); // Failsafe
            });

            window.galleryFilesArray.push({ id: uniqueId, tempPath: tempPath, thumbBlob: thumbBlob, type: 'video', fileName: file.name });
            if (thumbBlob) injectHTML(URL.createObjectURL(thumbBlob), true);
            
          } else {
            window.galleryFilesArray.push({ id: uniqueId, file: file, type: 'image' });
            const reader = new FileReader();
            reader.onload = (e) => injectHTML(e.target.result, false);
            reader.readAsDataURL(file);
          }
        }
      }
      
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      if (contentDiv && progressDiv) {
         setTimeout(() => { contentDiv.style.display = "block"; progressDiv.style.display = "none"; }, 1000);
      }
      input.value = "";
    };

    window.removeGalleryItem = function (id) {
      document.getElementById(id).remove();
      window.galleryFilesArray = window.galleryFilesArray.filter((item) => item.id !== id);
    };

    document.getElementById("createProjectForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const activeBtn = e.submitter;
      const projectSubmitStatus = activeBtn && activeBtn.id === "draftBtn" ? "draft" : "published";
      const originalBtnText = activeBtn.innerHTML;
      activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

      const dBtn = document.getElementById("draftBtn");
      const pBtn = document.getElementById("publishBtn");
      if (dBtn) dBtn.disabled = true;
      if (pBtn) pBtn.disabled = true;

      const formData = new FormData();
      formData.append("title", document.getElementById("projectTitle").value);
      formData.append("phase", document.getElementById("projectPhaseToggle").checked ? "ongoing" : "past");
      Array.from(document.getElementById("projectImpactArea").selectedOptions).forEach((option) => {
        formData.append("impact_area[]", option.value);
      });
      formData.append("start_date", document.getElementById("projectStartDate").value);
      formData.append("end_date", document.getElementById("projectEndDate").value);
      formData.append("full_description", quill.root.innerHTML);
      formData.append("status", projectSubmitStatus);

      const coverInput = document.getElementById("coverInput");
      if (coverInput.files[0]) formData.append("cover_image", coverInput.files[0]);

      const sec1Img = document.getElementById("sec1Input");
      if (sec1Img.files[0]) formData.append("sec1_image", sec1Img.files[0]);
      document.querySelectorAll("#metricsContainerSec1 .metric-row").forEach((row, index) => {
        const iconInput = row.querySelector('input[type="file"]');
        const inputs = row.querySelectorAll('input[type="text"]');
        if (iconInput.files[0]) formData.append(`sec1_metrics[${index}][icon]`, iconInput.files[0]);
        formData.append(`sec1_metrics[${index}][value]`, inputs[0].value);
        formData.append(`sec1_metrics[${index}][label]`, inputs[1].value);
      });

      const sec2Img = document.getElementById("sec2Input");
      if (sec2Img.files[0]) formData.append("sec2_image", sec2Img.files[0]);
      document.querySelectorAll("#metricsContainerSec2 .metric-row").forEach((row, index) => {
        const iconInput = row.querySelector('input[type="file"]');
        const inputs = row.querySelectorAll('input[type="text"]');
        if (iconInput.files[0]) formData.append(`sec2_metrics[${index}][icon]`, iconInput.files[0]);
        formData.append(`sec2_metrics[${index}][value]`, inputs[0].value);
        formData.append(`sec2_metrics[${index}][label]`, inputs[1].value);
      });

      document.querySelectorAll("#storiesWrapper .section-card").forEach((card, index) => {
        const fileInput = card.querySelector('input[type="file"]');
        const desc = card.querySelector("textarea").value;
        const name = card.querySelector('input[type="text"]').value;
        if (fileInput.files[0]) formData.append(`stories[${index}][image]`, fileInput.files[0]);
        formData.append(`stories[${index}][description]`, desc);
        formData.append(`stories[${index}][name]`, name);
      });

      const leadImg = document.getElementById("leadInput");
      if (leadImg.files[0]) formData.append("lead_image", leadImg.files[0]);
      formData.append("lead_name", document.getElementById("leadName").value);
      formData.append("lead_role", document.getElementById("leadRole").value);
      formData.append("lead_linkedin", document.getElementById("leadLinkedin").value);

      window.galleryFilesArray.forEach((item, index) => {
        if (item.type === "video") {
          // Send the temporary path created by chunk uploader
          formData.append(`gallery_videos_temp[${index}]`, item.tempPath);
          // Send original file name for extension mapping
          formData.append(`gallery_videos_names[${index}]`, item.fileName);
          // Send the canvas-extracted thumbnail
          if (item.thumbBlob) {
            formData.append(`gallery_thumbnails[${index}]`, item.thumbBlob, `thumb_${index}.jpg`);
          }
        } else {
          // Send standard image file
          formData.append(`gallery_files[${index}]`, item.file);
        }
      });

      try {
        const response = await fetch("actions/projects/create-project.php", { method: "POST", body: formData });
        const result = await response.json();
        if (result.success) {
          showAlert("success", "Project Saved Successfully!");
          loadView("projects", "Projects");
        } else {
          console.error("Backend Error:", result.message);
          showAlert("error", "Failed to save project. Please verify the details.");
          publishBtn.innerHTML = originalBtnText;
          publishBtn.disabled = false;
        }
      } catch (err) {
        console.error(err);
        showAlert("error", "A network error occurred. Please try again.");
        publishBtn.innerHTML = originalBtnText;
        publishBtn.disabled = false;
      }
    });
  }

  // ==========================================
  // --- VIEW: Edit PROJECT LOGIC ---
  // ==========================================

  function initEditProjectScript(params = {}) {
    if (!params.id) {
      loadView("projects", "Projects");
      showAlert("error", "Something went wrong.");
      return;
    }
    // Structure to track items removed from DB
    const removedItemTracker = {
      coverImageRemoved: false,
      leadImageRemoved: false,
      sec1ImageRemoved: false,
      sec2ImageRemoved: false,
      removedImpactAreaIds: [],
      removedMetricIds: [],
      removedMetricIconIds: [],
      removedStoryIds: [],
      removedStoryImageIds: [],
      removedMediaIds: [],
    };

    window.editProjectMultiSelect = new SAPSRIMultiSelect("projectImpactArea", "Add Impact Areas...");

    // Override MultiSelect removal to track deleted impact areas
    const originalSelectOption = window.editProjectMultiSelect.selectOption;
    if (originalSelectOption) {
      const selectContainer = document.getElementById("projectImpactArea");
      selectContainer.addEventListener("change", function () {
        // Check tracking on select change if needed
      });
    }

    const phaseToggle = document.getElementById("projectPhaseToggle");
    const phaseLabel = document.getElementById("phaseLabel");
    const startDateContainer = document.getElementById("startDateContainer");
    const endDateContainer = document.getElementById("endDateContainer");

    if (phaseToggle) {
      phaseToggle.addEventListener("change", (e) => {
        const isOngoing = e.target.checked;
        phaseLabel.innerText = isOngoing ? "Ongoing" : "Past";
        phaseLabel.className = isOngoing
          ? "form-check-label fw-medium ms-2 text-success"
          : "form-check-label fw-medium ms-2 text-secondary";
        if (endDateContainer && startDateContainer) {
          if (isOngoing) {
            startDateContainer.classList.remove("col-md-6");
            startDateContainer.classList.add("col-md-12");
            endDateContainer.style.display = "none";
            document.getElementById("projectEndDate").value = "";
          } else {
            startDateContainer.classList.remove("col-md-12");
            startDateContainer.classList.add("col-md-6");
            endDateContainer.style.display = "block";
          }
        }
      });
    }

    // Quill Setup
    let quill;
    if (document.getElementById("editor")) {
      const Icons = Quill.import("ui/icons");
      Icons.undo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
      Icons.redo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

      quill = new Quill("#editor", {
        theme: "snow",
        modules: {
          history: { delay: 1000, maxStack: 100, userOnly: true },
          toolbar: {
            container: [
              ["undo", "redo"],
              [{ size: ["small", false, "large", "huge"] }],
              ["bold", "italic", "underline", "strike"],
              [{ align: "" }, { align: "center" }, { align: "right" }, { align: "justify" }],
              [{ list: "ordered" }, { list: "bullet" }],
              ["link"],
            ],
            handlers: {
              undo: function () {
                this.quill.history.undo();
              },
              redo: function () {
                this.quill.history.redo();
              },
            },
          },
        },
      });

      // -------------------------------------------------------------
      // Word Document Extractor Logic (Edit Project)
      // -------------------------------------------------------------
      const radioManual = document.getElementById("descManual");
      const radioWord = document.getElementById("descWord");
      const manualSection = document.getElementById("manualDescSection");
      const wordSection = document.getElementById("wordUploadSection");

      // Toggle UI Elements
      function updateDescUI() {
        if (radioManual.checked) {
          wordSection.style.display = "none";
        } else {
          wordSection.style.display = "block";
        }
        if (window.lucide) lucide.createIcons();
      }
      
      if (radioManual && radioWord) {
        radioManual.addEventListener("change", updateDescUI);
        radioWord.addEventListener("change", updateDescUI);
      }

      // Handle Upload and Extraction
      window.handleWordUpload = function (input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        if (!file.name.toLowerCase().endsWith('.docx')) {
          showAlert('error', 'Please upload a valid modern Word document (.docx).');
          return;
        }

        // Show Progress UI
        const contentDiv = document.getElementById("wordContent");
        const progressDiv = document.getElementById("wordProgress");
        const progressBar = document.getElementById("wordProgressBar");
        const progressText = document.getElementById("wordProgressText");

        contentDiv.style.display = "none";
        progressDiv.style.display = "block";
        progressBar.className = "progress-fill";
        progressBar.style.width = "60%";
        progressText.className = "upload-status-text";
        progressText.innerText = `Extracting Text...`;

        const reader = new FileReader();
        reader.onload = function(event) {
          const arrayBuffer = event.target.result;
          
          mammoth.convertToHtml({arrayBuffer: arrayBuffer})
            .then(function(result) {
              const html = result.value; 
              
              progressBar.style.width = "100%";
              progressBar.classList.add("success");
              progressText.classList.add("success");
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Extraction Complete!';
              if (window.lucide) lucide.createIcons();

              setTimeout(() => {
                // Overwrite the Quill Editor with the new Word HTML
                quill.clipboard.dangerouslyPasteHTML(html);
                
                radioManual.checked = true;
                updateDescUI();
                
                input.value = "";
                contentDiv.style.display = "block";
                progressDiv.style.display = "none";
                progressBar.classList.remove("success");
                progressText.classList.remove("success");
                progressBar.style.width = "0%";
                
                showAlert('success', 'Word document imported successfully! Existing text has been replaced.');
              }, 800);
            })
            .catch(function(err) {
              console.error("Mammoth Parse Error:", err);
              showAlert('error', 'Failed to extract text. The document may be corrupted or password-protected.');
              contentDiv.style.display = "block";
              progressDiv.style.display = "none";
              input.value = "";
            });
        };
        
        reader.readAsArrayBuffer(file);
      };

    }

    // Single/Main Image Handling Functions
    function simulateUpload(idPrefix, callback) {
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      const progressBar = document.getElementById(idPrefix + "ProgressBar");
      const progressText = document.getElementById(idPrefix + "ProgressText");

      if (!contentDiv || !progressDiv) {
        callback();
        return;
      }

      contentDiv.style.display = "none";
      progressDiv.style.display = "block";
      progressBar.className = "progress-fill";
      progressBar.style.width = "0%";
      progressText.className = "upload-status-text";

      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress >= 100) progress = 100;
        progressBar.style.width = progress + "%";
        progressText.innerText = `Uploading... ${Math.round(progress)}%`;

        if (progress === 100) {
          clearInterval(interval);
          progressBar.classList.add("success");
          progressText.classList.add("success");
          progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
          if (window.lucide) lucide.createIcons();
          setTimeout(() => {
            progressDiv.style.display = "none";
            callback();
          }, 800);
        }
      }, 100);
    }

    window.handleImageUpload = function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(idPrefix, () => {
            document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
            document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      document.getElementById(idPrefix + "Input").value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";

      // Record standard image removals
      if (idPrefix === "cover") removedItemTracker.coverImageRemoved = true;
      if (idPrefix === "lead") removedItemTracker.leadImageRemoved = true;
      if (idPrefix === "sec1") removedItemTracker.sec1ImageRemoved = true;
      if (idPrefix === "sec2") removedItemTracker.sec2ImageRemoved = true;
    };

    // Metric Rows Handling
    let metricsCount = { 1: 0, 2: 0 };
    window.addMetricRow = function (sectionId, existingData = null) {
      if (metricsCount[sectionId] >= 3) return;
      metricsCount[sectionId]++;

      const container = document.getElementById(`metricsContainerSec${sectionId}`);
      const count = metricsCount[sectionId];
      const rowId = `sec${sectionId}_metric${count}`;
      const dbId = existingData ? existingData.id : "";
      const valueVal = existingData ? existingData.metric_value : "";
      const labelVal = existingData ? existingData.metric_label : "";
      const iconSrc = existingData ? existingData.icon_image : "";

      const rowHTML = `
            <div class="metric-row" id="row_${rowId}" data-db-id="${dbId}" draggable="true">
                <div class="drag-handle"><i data-lucide="grip-vertical" class="text-muted"></i></div>
                <div class="metric-icon-box" onclick="document.getElementById('icon_${rowId}').click()">
                    <i data-lucide="upload" id="icon_ph_${rowId}" class="text-muted" style="width:18px; display:${iconSrc ? "none" : "block"};"></i>
                    <img src="${iconSrc || ""}" id="icon_preview_${rowId}" style="display:${iconSrc ? "block" : "none"};">
                    <button type="button" class="remove-icon-btn" onclick="removeMetricIcon(event, '${rowId}', '${dbId}')"><i data-lucide="x" style="width:12px;"></i></button>
                </div>
                <input type="file" id="icon_${rowId}" class="d-none" accept="image/png" onchange="handleMetricIconUpload(this, '${rowId}')">
                <input type="text" class="form-control bg-white metric-val-input" placeholder="Metric Value (e.g. 1600+)" value="${valueVal}">
                <input type="text" class="form-control bg-white metric-lbl-input" placeholder="Metric Label (e.g. Beneficiaries)" value="${labelVal}">
                <button type="button" class="btn btn-link text-danger p-0 ms-auto me-2" onclick="removeMetricRow(${sectionId}, 'row_${rowId}', '${dbId}')">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        `;

      container.insertAdjacentHTML("beforeend", rowHTML);
      if (window.lucide) lucide.createIcons();
      initDragAndDrop(container);

      if (metricsCount[sectionId] >= 3) {
        document.getElementById(`addMetricSec${sectionId}Btn`).disabled = true;
      }
    };

    window.removeMetricRow = function (sectionId, rowId, dbId) {
      if (dbId) removedItemTracker.removedMetricIds.push(dbId);
      const el = document.getElementById(rowId);
      if (el) el.remove();
      metricsCount[sectionId]--;
      document.getElementById(`addMetricSec${sectionId}Btn`).disabled = false;
    };

    window.handleMetricIconUpload = function (input, rowId) {
      if (input.files && input.files[0]) {
        if (input.files[0].type !== "image/png") {
          if (typeof showAlert === "function") showAlert("error", "Only PNG images are allowed for icons.");
          else alert("Only PNG images are allowed for icons.");
          return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById("icon_ph_" + rowId).style.display = "none";
          const img = document.getElementById("icon_preview_" + rowId);
          img.src = e.target.result;
          img.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeMetricIcon = function (event, rowId, dbId) {
      event.stopPropagation();
      if (dbId) removedItemTracker.removedMetricIconIds.push(dbId);
      document.getElementById("icon_" + rowId).value = "";
      const img = document.getElementById("icon_preview_" + rowId);
      img.src = "";
      img.style.display = "none";
      document.getElementById("icon_ph_" + rowId).style.display = "block";
    };

    // Drag and Drop Helper
    let draggedRow = null;
    function initDragAndDrop(container) {
      const rows = container.querySelectorAll(".metric-row");
      rows.forEach((row) => {
        row.addEventListener("dragstart", () => {
          draggedRow = row;
          setTimeout(() => row.classList.add("dragging"), 0);
        });
        row.addEventListener("dragend", () => {
          row.classList.remove("dragging");
          draggedRow = null;
        });
      });

      container.addEventListener("dragover", (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        if (draggedRow) {
          if (afterElement == null) container.appendChild(draggedRow);
          else container.insertBefore(draggedRow, afterElement);
        }
      });
    }

    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll(".metric-row:not(.dragging)")];
      return draggableElements.reduce(
        (closest, child) => {
          const box = child.getBoundingClientRect();
          const offset = y - box.top - box.height / 2;
          if (offset < 0 && offset > closest.offset) return { offset: offset, element: child };
          else return closest;
        },
        { offset: Number.NEGATIVE_INFINITY },
      ).element;
    }

    // Success Story Handling
    let storyCount = 0;
    window.addStory = function (existingData = null) {
      storyCount++;
      const container = document.getElementById("storiesWrapper");
      const storyId = `story${storyCount}`;
      const dbId = existingData ? existingData.id : "";
      const nameVal = existingData ? existingData.subject_name : "";
      const descVal = existingData ? existingData.subject_description : "";
      const imgUrl = existingData ? existingData.subject_image : "";

      const storyHTML = `
            <div class="section-card position-relative" id="container_${storyId}" data-db-id="${dbId}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 fs-6">Story ${storyCount}</h5>
                    <button type="button" class="btn btn-link text-danger p-0" onclick="removeStory('container_${storyId}', '${dbId}')">
                        <i data-lucide="minus-circle"></i>
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium text-muted small">Subject Image</label>
                    <div class="upload-area p-4" onclick="document.getElementById('${storyId}Input').click()">
                        <div class="upload-content" id="${storyId}Content" style="display:${imgUrl ? "none" : "block"};">
                            <i data-lucide="upload" class="upload-icon"></i>
                            <p class="mb-0 text-muted small">Click to upload image</p>
                        </div>
                        <div class="upload-progress-wrapper" id="${storyId}Progress" style="display:none;">
                            <div class="progress-bar-custom"><div class="progress-fill" id="${storyId}ProgressBar"></div></div>
                            <div class="upload-status-text" id="${storyId}ProgressText">Uploading... 0%</div>
                        </div>
                        <div class="image-preview-wrapper" id="${storyId}PreviewWrapper" style="display:${imgUrl ? "block" : "none"};">
                            <img src="${imgUrl || ""}" class="image-preview" id="${storyId}PreviewImg">
                            <button type="button" class="remove-img-btn" onclick="removeStoryImage(event, '${storyId}', '${dbId}')"><i data-lucide="x"></i></button>
                        </div>
                        <input type="file" id="${storyId}Input" class="d-none" accept="image/*" onchange="handleStoryUpload(this, '${storyId}')">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium text-muted small">Subject Description</label>
                    <textarea class="form-control story-desc" rows="3">${descVal}</textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-medium text-muted small">Subject Name</label>
                    <input type="text" class="form-control story-name" value="${nameVal}">
                </div>
            </div>
        `;
      container.insertAdjacentHTML("beforeend", storyHTML);
      if (window.lucide) lucide.createIcons();
    };

    window.removeStory = function (containerId, dbId) {
      if (dbId) removedItemTracker.removedStoryIds.push(dbId);
      const el = document.getElementById(containerId);
      if (el) el.remove();
    };

    window.removeStoryImage = function (event, storyId, dbId) {
      event.stopPropagation();
      if (dbId) removedItemTracker.removedStoryImageIds.push(dbId);
      document.getElementById(storyId + "Input").value = "";
      document.getElementById(storyId + "PreviewImg").src = "";
      document.getElementById(storyId + "PreviewWrapper").style.display = "none";
      document.getElementById(storyId + "Content").style.display = "block";
    };

    window.handleStoryUpload = function (input, storyId) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(storyId, () => {
            document.getElementById(storyId + "PreviewWrapper").style.display = "block";
            document.getElementById(storyId + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    // Media Gallery Handling
    window.galleryFilesArray = [];
    window.handleGalleryUpload = async function (input, idPrefix = "gallery") {
      const container = document.getElementById("galleryPreviewContainer");

      if (input.files) {
        for (const file of Array.from(input.files)) {
          const uniqueId = "gal_" + Math.random().toString(36).substr(2, 9);
          const isVideo = file.type.startsWith("video/");

          const injectHTML = (src, showPlayBtn) => {
            const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : "";
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
            container.insertAdjacentHTML("beforeend", colHTML);
            if (window.lucide) lucide.createIcons();
          };

          if (isVideo) {
            // Upload chunk immediately
            const tempPath = await uploadFileInChunks(file, idPrefix, uniqueId);
            
            // AWAIT the canvas extraction to guarantee the thumbnail exists
            const thumbBlob = await new Promise((resolve) => {
              const video = document.createElement("video");
              video.preload = "metadata";
              video.src = URL.createObjectURL(file);
              video.onloadeddata = () => { video.currentTime = 1; };
              video.onseeked = () => {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 360;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.85);
              };
              video.onerror = () => resolve(null); // Failsafe
            });

            window.galleryFilesArray.push({ id: uniqueId, tempPath: tempPath, thumbBlob: thumbBlob, type: 'video', fileName: file.name });
            if (thumbBlob) injectHTML(URL.createObjectURL(thumbBlob), true);
            
          } else {
            window.galleryFilesArray.push({ id: uniqueId, file: file, type: 'image' });
            const reader = new FileReader();
            reader.onload = (e) => injectHTML(e.target.result, false);
            reader.readAsDataURL(file);
          }
        }
      }
      
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      if (contentDiv && progressDiv) {
         setTimeout(() => { contentDiv.style.display = "block"; progressDiv.style.display = "none"; }, 1000);
      }
      input.value = "";
    };

    window.removeGalleryItem = function (id, dbId) {
      if (dbId) removedItemTracker.removedMediaIds.push(dbId);
      const el = document.getElementById(id);
      if (el) el.remove();
      window.galleryFilesArray = window.galleryFilesArray.filter((item) => item.id !== id);
    };

    // Helper: Add server gallery items
    function renderServerMediaItem(media) {
      const container = document.getElementById("galleryPreviewContainer");
      const elementId = `server_media_${media.id}`;
      const isVideo = media.media_type === "video";
      const displaySrc = media.thumbnail_url || media.media_url;

      const colHTML = `
            <div class="col-xl-3 col-lg-4 col-md-6" id="${elementId}" data-db-id="${media.id}">
                <div class="position-relative" style="height: 150px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; background: #000;">
                    <img src="${displaySrc}" style="width: 100%; height: 100%; object-fit: cover; opacity: ${isVideo ? 0.7 : 1};">
                    ${isVideo ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : ""}
                    <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 8px; right: 8px; border-radius: 50%; z-index: 10;" onclick="removeGalleryItem('${elementId}', '${media.id}')">
                        <i data-lucide="x" style="width: 14px;"></i>
                    </button>
                </div>
            </div>
        `;
      container.insertAdjacentHTML("beforeend", colHTML);
    }

    // LOAD DATA VIA FETCH
    const projectIdParam = params.id;

    if (projectIdParam) {
      document.getElementById("projectId").value = projectIdParam;

      fetch(`actions/projects/fetch-project.php?id=${projectIdParam}`)
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "error") {
            if (typeof showAlert === "function") showAlert("error", data.message);
            return;
          }

          // 1. Populate Core Project Details
          const project = data.project;
          console.log(project);
          document.getElementById("projectTitle").value = project.title || "";
          if (quill && project.full_description) {
            quill.clipboard.dangerouslyPasteHTML(project.full_description);
          }

          // Phase & Dates
          const isOngoing = project.project_phase === "ongoing";
          phaseToggle.checked = isOngoing;
          phaseToggle.dispatchEvent(new Event("change"));

          document.getElementById("projectStartDate").value = project.start_date || "";
          if (!isOngoing && project.end_date) {
            document.getElementById("projectEndDate").value = project.end_date;
          }

          // Cover Image
          if (project.cover_image) {
            document.getElementById("coverContent").style.display = "none";
            document.getElementById("coverPreviewWrapper").style.display = "block";
            document.getElementById("coverPreviewImg").src = project.cover_image;
          }

          // 2. Select Impact Areas
          if (data.project_impact_areas && data.project_impact_areas.impact_area_ids) {
            const selectEl = document.getElementById("projectImpactArea");
            const loadedIds = data.project_impact_areas.impact_area_ids;
            Array.from(selectEl.options).forEach((opt) => {
              if (loadedIds.includes(parseInt(opt.value))) {
                opt.selected = true;
              }
            });
            if (window.editProjectMultiSelect && window.editProjectMultiSelect.refresh) {
              window.editProjectMultiSelect.refresh();
            }
          }

          // 3. Metrics (Section 1 and 2)
          if (data.project_metrics) {
            const sec1Metrics = data.project_metrics.filter((m) => String(m.section_number) === "1");
            const sec2Metrics = data.project_metrics.filter((m) => String(m.section_number) === "2");

            sec1Metrics.forEach((m) => addMetricRow(1, m));
            sec2Metrics.forEach((m) => addMetricRow(2, m));

            // Image for section 1/2 if provided in metrics object
            if (sec1Metrics[0] && sec1Metrics[0].section_image) {
              document.getElementById("sec1Content").style.display = "none";
              document.getElementById("sec1PreviewWrapper").style.display = "block";
              document.getElementById("sec1PreviewImg").src = sec1Metrics[0].section_image;
            }
            if (sec2Metrics[0] && sec2Metrics[0].section_image) {
              document.getElementById("sec2Content").style.display = "none";
              document.getElementById("sec2PreviewWrapper").style.display = "block";
              document.getElementById("sec2PreviewImg").src = sec2Metrics[0].section_image;
            }
          }

          // 4. Success Stories
          if (data.project_success_stories && data.project_success_stories.length > 0) {
            data.project_success_stories.forEach((story) => addStory(story));
          }

          // 5. Leads
          if (data.project_leads && data.project_leads.length > 0) {
            const lead = data.project_leads[0];
            document.getElementById("leadId").value = lead.id || "";
            document.getElementById("leadName").value = lead.name || "";
            document.getElementById("leadRole").value = lead.role_designation || "";
            document.getElementById("leadLinkedin").value = lead.linkedin_profile || "";

            if (lead.profile_photo) {
              document.getElementById("leadContent").style.display = "none";
              document.getElementById("leadPreviewWrapper").style.display = "block";
              document.getElementById("leadPreviewImg").src = lead.profile_photo;
            }
          }

          // 6. Media Gallery
          if (data.project_media && data.project_media.length > 0) {
            data.project_media.forEach((media) => renderServerMediaItem(media));
          }

          // 7. status
          const status = project.status ?? null;
          const statusBtn = document.getElementById("statusBtn");
          if (!status) throw new Error("Expected field 'status' was not found.");

          if (status === "draft" || status === "archived") {
            statusBtn.textContent = "Publish";
            statusBtn.dataset.action = "published";
          } else if (status === "published") {
            statusBtn.textContent = "Archive";
            statusBtn.dataset.action = "archived";
          } else {
            throw new Error("Invalid value for field 'status'.");
          }

          if (window.lucide) lucide.createIcons();
        })
        .catch((err) => {
          console.error("Project Fetch Error:", err);
          if (typeof showAlert === "function") showAlert("error", "Failed to load project details.");
        });
    }

    // FORM SUBMISSION (SAVE EDITS)
    document.getElementById("editProjectForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const activeBtn = e.submitter;
      let projectSubmitStatus = null;
      if (activeBtn && activeBtn.id === "statusBtn") {
        const action = activeBtn.dataset.action ?? null;
        projectSubmitStatus = action === "archived" ? "archived" : "published";
      }

      const originalBtnText = activeBtn ? activeBtn.innerHTML : "";
      if (activeBtn) activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

      const statusBtn = document.getElementById("statusBtn");
      const saveBtn = document.getElementById("saveBtn");
      if (statusBtn) saveBtn.disabled = true;
      if (saveBtn) statusBtn.disabled = true;

      const formData = new FormData();
      formData.append("project_id", document.getElementById("projectId").value);
      formData.append("title", document.getElementById("projectTitle").value);
      formData.append("phase", document.getElementById("projectPhaseToggle").checked ? "ongoing" : "past");

      Array.from(document.getElementById("projectImpactArea").selectedOptions).forEach((option) => {
        formData.append("impact_area[]", option.value);
      });

      formData.append("start_date", document.getElementById("projectStartDate").value);
      formData.append("end_date", document.getElementById("projectEndDate").value);
      formData.append("full_description", quill ? quill.root.innerHTML : "");
      if (projectSubmitStatus) formData.append("status", projectSubmitStatus);

      // Tracking Deleted Data payload
      formData.append("removed_items", JSON.stringify(removedItemTracker));

      // Single File Uploads
      const coverInput = document.getElementById("coverInput");
      if (coverInput.files[0]) formData.append("cover_image", coverInput.files[0]);

      const sec1Img = document.getElementById("sec1Input");
      if (sec1Img.files[0]) formData.append("sec1_image", sec1Img.files[0]);

      const sec2Img = document.getElementById("sec2Input");
      if (sec2Img.files[0]) formData.append("sec2_image", sec2Img.files[0]);

      // Section 1 Metrics
      document.querySelectorAll("#metricsContainerSec1 .metric-row").forEach((row, index) => {
        const dbId = row.getAttribute("data-db-id");
        const iconInput = row.querySelector('input[type="file"]');
        const valInput = row.querySelector(".metric-val-input");
        const lblInput = row.querySelector(".metric-lbl-input");

        if (dbId) formData.append(`sec1_metrics[${index}][id]`, dbId);
        if (iconInput && iconInput.files[0]) formData.append(`sec1_metrics[${index}][icon]`, iconInput.files[0]);
        formData.append(`sec1_metrics[${index}][value]`, valInput.value);
        formData.append(`sec1_metrics[${index}][label]`, lblInput.value);
      });

      // Section 2 Metrics
      document.querySelectorAll("#metricsContainerSec2 .metric-row").forEach((row, index) => {
        const dbId = row.getAttribute("data-db-id");
        const iconInput = row.querySelector('input[type="file"]');
        const valInput = row.querySelector(".metric-val-input");
        const lblInput = row.querySelector(".metric-lbl-input");

        if (dbId) formData.append(`sec2_metrics[${index}][id]`, dbId);
        if (iconInput && iconInput.files[0]) formData.append(`sec2_metrics[${index}][icon]`, iconInput.files[0]);
        formData.append(`sec2_metrics[${index}][value]`, valInput.value);
        formData.append(`sec2_metrics[${index}][label]`, lblInput.value);
      });

      // Stories
      document.querySelectorAll("#storiesWrapper .section-card").forEach((card, index) => {
        const dbId = card.getAttribute("data-db-id");
        const fileInput = card.querySelector('input[type="file"]');
        const desc = card.querySelector(".story-desc").value;
        const name = card.querySelector(".story-name").value;

        if (dbId) formData.append(`stories[${index}][id]`, dbId);
        if (fileInput && fileInput.files[0]) formData.append(`stories[${index}][image]`, fileInput.files[0]);
        formData.append(`stories[${index}][description]`, desc);
        formData.append(`stories[${index}][name]`, name);
      });

      // Lead Data
      const leadId = document.getElementById("leadId").value;
      if (leadId) formData.append("lead_id", leadId);
      const leadImg = document.getElementById("leadInput");
      if (leadImg.files[0]) formData.append("lead_image", leadImg.files[0]);
      formData.append("lead_name", document.getElementById("leadName").value);
      formData.append("lead_role", document.getElementById("leadRole").value);
      formData.append("lead_linkedin", document.getElementById("leadLinkedin").value);

      // Newly added Gallery Files
      window.galleryFilesArray.forEach((item, index) => {
        if (item.type === 'video') {
            formData.append(`gallery_videos_temp[${index}]`, item.tempPath);
            formData.append(`gallery_videos_names[${index}]`, item.fileName);
            if (item.thumbBlob) {
                formData.append(`gallery_thumbnails[${index}]`, item.thumbBlob, `thumb_${index}.jpg`);
            }
        } else {
            formData.append(`gallery_files[${index}]`, item.file);
        }
      });

      try {
        const response = await fetch("actions/projects/update-project.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.success || result.status === "success") {
          if (typeof showAlert === "function") showAlert("success", "Project Updated Successfully!");
          if (typeof loadView === "function") loadView("projects", "Projects Management");
        } else {
          console.error("Backend Error:", result.message);
          if (typeof showAlert === "function") showAlert("error", result.message || "Failed to update project.");
          if (activeBtn) {
            activeBtn.innerHTML = originalBtnText;
            activeBtn.disabled = false;
          }
        }
      } catch (err) {
        console.error("Network Error:", err);
        if (typeof showAlert === "function") showAlert("error", "A network error occurred. Please try again.");
        if (activeBtn) {
          activeBtn.innerHTML = originalBtnText;
          activeBtn.disabled = false;
        }
      } finally {
        if (statusBtn) statusBtn.disabled = false;
        if (saveBtn) saveBtn.disabled = false;
      }
    });
  }

  // ==========================================
  // --- VIEW: POSTS LOGIC ---
  // ==========================================
  function initPostsScript_Old() {
    window.loadPosts = function () {
      const tbody = document.getElementById("posts-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading posts...</td></tr>';

      fetch("actions/posts/fetch-posts.php")
        .then((res) => res.json())
        .then((data) => {
          const posts = Array.isArray(data) ? data : Object.values(data);
          tbody.innerHTML = "";

          if (posts.length === 0 || posts[0].error) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No posts found.</td></tr>';
            return;
          }

          posts.forEach((item) => {
            let pillClass = (item.status || "").toLowerCase() === "published" ? "status-published" : "status-draft";
            let pubDate = item.publish_date
              ? item.publish_date.split(" ")[0]
              : item.created_at
                ? item.created_at.split(" ")[0]
                : "N/A";

            const row = `
                <tr>
                  <td class="fw-medium">${item.title}</td>
                  <td>${item.post_lead || item.created_by_name || "System"}</td>
                  <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
                  <td>${pubDate}</td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit" onclick="loadView('edit-post', 'Posts', {id: ${item.id}})"><i data-lucide="edit" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${item.title.replace(/'/g, "\\'")}', 'post', 'actions/posts/delete-post.php', 'loadPosts')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                  </td>
                </tr>
              `;
            tbody.insertAdjacentHTML("beforeend", row);
          });
          lucide.createIcons();
        })
        .catch((err) => {
          console.error("Posts Fetch Error:", err);
          tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load posts.</td></tr>';
        });
    };
    loadPosts();
  }

  function initPostsScript() {
    // State configuration
    let state = {
      search: "",
      filter: "all", // Default selected button filter
      dateRange: "all_time", // Default selected date range
      startDate: "",
      endDate: "",
      page: 1,
      limit: 10, // Default selected rows per page
    };

    let searchTimeout = null;

    window.loadPosts = function () {
      const tbody = document.getElementById("posts-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading posts...</td></tr>';

      const params = new URLSearchParams({
        search: state.search,
        filter: state.filter,
        date_range: state.dateRange,
        start_date: state.startDate,
        end_date: state.endDate,
        page: state.page,
        limit: state.limit,
      });

      fetch(`actions/posts/fetch-posts.php?${params.toString()}`)
        .then((res) => res.json())
        .then((resData) => {
          const posts = resData.posts || [];
          const pagination = resData.pagination || { total_records: 0, total_pages: 1, current_page: 1, limit: 10 };

          tbody.innerHTML = "";

          if (posts.length === 0 || posts[0]?.error) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No posts found.</td></tr>';
            renderPagination(pagination);
            return;
          }

          posts.forEach((item) => {
            let statusLower = (item.status || "").toLowerCase();
            let pillClass =
              statusLower === "published"
                ? "status-published"
                : statusLower === "archived"
                  ? "status-archived"
                  : "status-draft";

            let pubDate = item.published_date
              ? item.published_date.split(" ")[0]
              : item.created_at
                ? item.created_at.split(" ")[0]
                : "N/A";

            const row = `
            <tr>
              <td class="fw-medium">${item.title}</td>
              <td>${item.post_lead || "System"}</td>
              <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
              <td>${pubDate}</td>
              <td class="text-end">
                <div class="hstack gap-1 justify-content-end">
                  <a href="/project-sedna/post?id=${item.id}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary border-0 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></a>
                  <button class="btn btn-sm btn-light text-warning border-0 shadow-sm" title="Edit" onclick="loadView('edit-post', 'Posts', {id: ${item.id}})"><i data-lucide="edit" style="width: 16px;"></i></button>
                  <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${(item.title || "").replace(/'/g, "\\'")}', 'post', 'actions/posts/delete-post.php', 'loadPosts')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                </div>
              </td>
            </tr>
          `;
            tbody.insertAdjacentHTML("beforeend", row);
          });

          if (window.lucide) {
            lucide.createIcons();
          }

          renderPagination(pagination);
        })
        .catch((err) => {
          console.error("Posts Fetch Error:", err);
          tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load posts.</td></tr>';
        });
    };

    // Render pagination buttons and entries counter
    function renderPagination(pagination) {
      const container = document.getElementById("posts-pagination");
      const infoText = document.getElementById("posts-pagination-info-text");
      if (!container) return;

      const { total_records, total_pages, current_page, limit } = pagination;

      // Update record summary
      if (infoText) {
        const start = total_records === 0 ? 0 : (current_page - 1) * limit + 1;
        const end = Math.min(current_page * limit, total_records);
        infoText.innerText = `Showing ${start}-${end} of ${total_records}`;
      }

      if (total_pages <= 1) {
        container.innerHTML = "";
        return;
      }

      let html = `<nav><ul class="pagination pagination-sm table-pagination gap-1 mb-0">`;

      // First Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changePostPage(1)"><i data-lucide="chevrons-left" style="width: 16px;"></i>First</button>
      </li>
    `;

      // Back Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changePostPage(${current_page - 1})"><i data-lucide="chevron-left" style="width: 16px;"></i>Back</button>
      </li>
    `;

      // Page Number Buttons
      for (let i = 1; i <= total_pages; i++) {
        if (i === 1 || i === total_pages || (i >= current_page - 1 && i <= current_page + 1)) {
          html += `
          <li class="page-item ${i === current_page ? "active" : ""}">
            <button class="page-link" onclick="changePostPage(${i})">${i}</button>
          </li>
        `;
        } else if (i === current_page - 2 || i === current_page + 2) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }

      // Next Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changePostPage(${current_page + 1})">Next<i data-lucide="chevron-right" style="width: 16px;"></i></button>
      </li>
    `;

      // Last Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changePostPage(${total_pages})">Last<i data-lucide="chevrons-right" style="width: 16px;"></i></button>
      </li>
    `;

      html += `</ul></nav>`;
      container.innerHTML = html;
      if (window.lucide) {
        lucide.createIcons();
      }
    }

    // Page switcher attached to global scope
    window.changePostPage = function (pageNumber) {
      state.page = pageNumber;
      loadPosts();
    };

    // Event Listener Bindings
    const searchInput = document.getElementById("post-search-input");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          state.search = e.target.value.trim();
          state.page = 1;
          loadPosts();
        }, 300);
      });
    }

    const filterPills = document.querySelectorAll("#post-status-pills .filter-pill");
    filterPills.forEach((pill) => {
      pill.addEventListener("click", function () {
        filterPills.forEach((p) => {
          p.classList.remove("active");
          const icon = p.querySelector("[data-lucide]");
          if (icon) icon.classList.add("d-none");
        });
        this.classList.add("active");
        const icon = this.querySelector("[data-lucide]");
        if (icon) icon.classList.remove("d-none");

        state.filter = this.getAttribute("data-filter");
        state.page = 1;
        loadPosts();
      });
    });

    const dateOptions = document.querySelectorAll("#date-filter-options .dropdown-item");
    const dateLabel = document.getElementById("date-filter-label");
    const customDateItem = document.getElementById("customDateItem");

    dateOptions.forEach((option) => {
      option.addEventListener("click", function (e) {
        e.preventDefault();

        const selectedRange = this.getAttribute("data-range");
        if (!selectedRange) return;

        if (typeof resetDateRangePicker === "function") {
          resetDateRangePicker(customDateItem);
        }

        if (selectedRange === "custom") {
          e.stopPropagation();
        } else {
          dateOptions.forEach((opt) => opt.classList.remove("active"));
          this.classList.add("active");

          if (dateLabel) dateLabel.innerText = this.innerText;

          state.dateRange = selectedRange;
          state.startDate = "";
          state.endDate = "";
          state.page = 1;
          loadPosts();
        }
      });
    });

    // Date range picker initialization
    if (typeof $ !== "undefined" && customDateItem) {
      $(function () {
        $(customDateItem).daterangepicker(
          {
            opens: "left",
            drops: "up",
          },
          function (start, end) {
            const startDate = start?.format("YYYY-MM-DD") || "";
            const endDate = end?.format("YYYY-MM-DD") || "";
            if (dateLabel) dateLabel.textContent = `${startDate} - ${endDate}`;

            state.startDate = startDate;
            state.endDate = endDate;
            state.dateRange = "custom";
            state.page = 1;
            loadPosts();
          },
        );
        $(customDateItem).on("show.daterangepicker", function (ev, picker) {
          picker.container.find(".drp-calendar").on("click", function (e) {
            e.stopPropagation();
          });
        });
      });
    }

    const rowsSelect = document.getElementById("posts-rows-per-page");
    if (rowsSelect) {
      rowsSelect.addEventListener("change", function () {
        state.limit = parseInt(this.value, 10);
        state.page = 1;
        loadPosts();
      });
    }

    // Execute initial load
    loadPosts();
  }

  // ==========================================
  // --- VIEW: CREATE POST LOGIC ---
  // ==========================================
  function initCreatePostScript() {
    new SAPSRIMultiSelect("postImpactArea", "Add Impact Areas...");
    window.galleryFilesArray = [];

    function simulateUpload(idPrefix, callback) {
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      const progressBar = document.getElementById(idPrefix + "ProgressBar");
      const progressText = document.getElementById(idPrefix + "ProgressText");
      if (!contentDiv || !progressDiv) return callback();

      contentDiv.style.display = "none";
      progressDiv.style.display = "block";
      progressBar.className = "progress-fill";
      progressBar.style.width = "0%";
      progressText.className = "upload-status-text";

      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress >= 100) progress = 100;
        progressBar.style.width = progress + "%";
        progressText.innerText = `Uploading... ${Math.round(progress)}%`;

        if (progress === 100) {
          clearInterval(interval);
          progressBar.classList.add("success");
          progressText.classList.add("success");
          progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Upload Complete!';
          lucide.createIcons();
          setTimeout(() => {
            progressDiv.style.display = "none";
            callback();
          }, 800);
        }
      }, 100);
    }

    window.handleImageUpload = function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(idPrefix, () => {
            document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
            document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      document.getElementById(idPrefix + "Input").value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";
    };

    window.galleryFilesArray = [];
    window.handleGalleryUpload = async function (input, idPrefix = 'gallery') {
      const container = document.getElementById("galleryPreviewContainer");

      if (input.files) {
        for (const file of Array.from(input.files)) {
          const uniqueId = "gal_" + Math.random().toString(36).substr(2, 9);
          const isVideo = file.type.startsWith("video/");

          const injectHTML = (src, showPlayBtn) => {
            const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : "";
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
            container.insertAdjacentHTML("beforeend", colHTML);
            if (window.lucide) lucide.createIcons();
          };

          if (isVideo) {
            // Upload chunk immediately
            const tempPath = await uploadFileInChunks(file, idPrefix, uniqueId);
            
            // AWAIT the canvas extraction to guarantee the thumbnail exists
            const thumbBlob = await new Promise((resolve) => {
              const video = document.createElement("video");
              video.preload = "metadata";
              video.src = URL.createObjectURL(file);
              video.onloadeddata = () => { video.currentTime = 1; };
              video.onseeked = () => {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 360;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.85);
              };
              video.onerror = () => resolve(null); // Failsafe
            });

            window.galleryFilesArray.push({ id: uniqueId, tempPath: tempPath, thumbBlob: thumbBlob, type: 'video', fileName: file.name });
            if (thumbBlob) injectHTML(URL.createObjectURL(thumbBlob), true);
            
          } else {
            window.galleryFilesArray.push({ id: uniqueId, file: file, type: 'image' });
            const reader = new FileReader();
            reader.onload = (e) => injectHTML(e.target.result, false);
            reader.readAsDataURL(file);
          }
        }
      }
      
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      if (contentDiv && progressDiv) {
         setTimeout(() => { contentDiv.style.display = "block"; progressDiv.style.display = "none"; }, 1000);
      }
      input.value = "";
    };

    window.removeGalleryItem = function (id) {
      document.getElementById(id).remove();
      window.galleryFilesArray = window.galleryFilesArray.filter((item) => item.id !== id);
    };

    let quill;
    if (document.getElementById("editor")) {
      const Icons = Quill.import("ui/icons");
      Icons.undo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
      Icons.redo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

      quill = new Quill("#editor", {
        theme: "snow",
        modules: {
          history: { delay: 1000, maxStack: 100, userOnly: true },
          toolbar: {
            container: [
              ["undo", "redo"],
              [{ size: ["small", false, "large", "huge"] }],
              ["bold", "italic", "underline", "strike"],
              [{ align: "" }, { align: "center" }, { align: "right" }, { align: "justify" }],
              [{ list: "ordered" }, { list: "bullet" }],
              ["link"],
            ],
            handlers: {
              undo: function () {
                this.quill.history.undo();
              },
              redo: function () {
                this.quill.history.redo();
              },
            },
          },
        },
      });

      // -------------------------------------------------------------
      // Word Document Extractor Logic (Create Post)
      // -------------------------------------------------------------
      const radioManual = document.getElementById("descManual");
      const radioWord = document.getElementById("descWord");
      const manualSection = document.getElementById("manualDescSection");
      const wordSection = document.getElementById("wordUploadSection");

      // Toggle UI Elements
      function updateDescUI() {
        if (radioManual.checked) {
          wordSection.style.display = "none";
        } else {
          wordSection.style.display = "block";
        }
        if (window.lucide) lucide.createIcons();
      }
      
      if (radioManual && radioWord) {
        radioManual.addEventListener("change", updateDescUI);
        radioWord.addEventListener("change", updateDescUI);
      }

      // Handle Upload and Extraction
      window.handleWordUpload = function (input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        if (!file.name.toLowerCase().endsWith('.docx')) {
          showAlert('error', 'Please upload a valid modern Word document (.docx).');
          return;
        }

        // Show Progress UI
        const contentDiv = document.getElementById("wordContent");
        const progressDiv = document.getElementById("wordProgress");
        const progressBar = document.getElementById("wordProgressBar");
        const progressText = document.getElementById("wordProgressText");

        contentDiv.style.display = "none";
        progressDiv.style.display = "block";
        progressBar.className = "progress-fill";
        progressBar.style.width = "60%";
        progressText.className = "upload-status-text";
        progressText.innerText = `Extracting Text...`;

        // Read file into ArrayBuffer for Mammoth
        const reader = new FileReader();
        reader.onload = function(event) {
          const arrayBuffer = event.target.result;
          
          // Execute Mammoth.js Parser
          mammoth.convertToHtml({arrayBuffer: arrayBuffer})
            .then(function(result) {
              const html = result.value; 
              
              // Finish Progress Bar
              progressBar.style.width = "100%";
              progressBar.classList.add("success");
              progressText.classList.add("success");
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Extraction Complete!';
              if (window.lucide) lucide.createIcons();

              setTimeout(() => {
                // 1. Inject Extracted HTML directly into Quill Editor
                quill.clipboard.dangerouslyPasteHTML(html);
                
                // 2. Programmatically switch the UI back to "Write Manually"
                radioManual.checked = true;
                updateDescUI();
                
                // 3. Reset the Upload Area for future use
                input.value = "";
                contentDiv.style.display = "block";
                progressDiv.style.display = "none";
                progressBar.classList.remove("success");
                progressText.classList.remove("success");
                progressBar.style.width = "0%";
                
                showAlert('success', 'Word document imported successfully! Please review formatting.');
              }, 800);
            })
            .catch(function(err) {
              console.error("Mammoth Parse Error:", err);
              showAlert('error', 'Failed to extract text. The document may be corrupted or password-protected.');
              contentDiv.style.display = "block";
              progressDiv.style.display = "none";
              input.value = "";
            });
        };
        
        reader.readAsArrayBuffer(file);
      };

    }

    document.getElementById("createPostForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const activeBtn = e.submitter;
      const postSubmitStatus = activeBtn && activeBtn.id === "draftBtn" ? "draft" : "published";

      const originalBtnText = activeBtn.innerHTML;
      activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

      const dBtn = document.getElementById("draftBtn");
      const pBtn = document.getElementById("publishBtn");
      if (dBtn) dBtn.disabled = true;
      if (pBtn) pBtn.disabled = true;

      const formData = new FormData();
      formData.append("title", document.getElementById("postTitle").value);
      Array.from(document.getElementById("postImpactArea").selectedOptions).forEach((option) => {
        formData.append("impact_area[]", option.value);
      });
      formData.append("content", quill.root.innerHTML);
      formData.append("status", postSubmitStatus);

      const coverInput = document.getElementById("coverInput");
      if (coverInput.files[0]) formData.append("cover_image", coverInput.files[0]);

      window.galleryFilesArray.forEach((item, index) => {
        if (item.type === 'video') {
            formData.append(`gallery_videos_temp[${index}]`, item.tempPath);
            formData.append(`gallery_videos_names[${index}]`, item.fileName);
            if (item.thumbBlob) {
                formData.append(`gallery_thumbnails[${index}]`, item.thumbBlob, `thumb_${index}.jpg`);
            }
        } else {
            formData.append(`gallery_files[${index}]`, item.file);
        }
      });

      try {
        const response = await fetch("actions/posts/create-post.php", { method: "POST", body: formData });
        const rawText = await response.text();
        try {
          const result = JSON.parse(rawText);
          if (result.success) {
            loadView("posts", "Posts");
            showAlert("success", "Post Saved Successfully!");
          } else {
            console.error("Backend Error:", result.message);
            showAlert("error", "Failed to save post. Please verify the details.");
            if (pBtn) pBtn.disabled = false;
            if (dBtn) dBtn.disabled = false;
            activeBtn.innerHTML = originalBtnText;
          }
        } catch (e) {
          console.error("Server Error: ", rawText);
          showAlert("error", "A server error occurred. Please try again later.");
          if (pBtn) pBtn.disabled = false;
          if (dBtn) dBtn.disabled = false;
          activeBtn.innerHTML = originalBtnText;
        }
      } catch (err) {
        console.error(err);
        showAlert("error", "A network error occurred. Please try again.");
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
    window.editPostMultiSelect = new SAPSRIMultiSelect("postImpactArea", "Add Impact Areas...");
    window.galleryFilesArray = [];
    window.galleryFilesDeletedArray = [];
    let quill = null;
    window.postData = {};

    function simulateUpload(idPrefix, callback, isLoading = false) {
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      const progressBar = document.getElementById(idPrefix + "ProgressBar");
      const progressText = document.getElementById(idPrefix + "ProgressText");
      if (!contentDiv || !progressDiv) return callback();

      contentDiv.style.display = "none";
      progressDiv.style.display = "block";
      progressBar.className = "progress-fill";
      progressBar.style.width = "0%";
      progressText.className = "upload-status-text";

      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress >= 100) progress = 100;
        progressBar.style.width = progress + "%";
        const status = isLoading ? "Load" : "Upload";
        progressText.innerText = `${status}ing... ${Math.round(progress)}%`;

        if (progress === 100) {
          clearInterval(interval);
          progressBar.classList.add("success");
          progressText.classList.add("success");
          progressText.innerHTML = `<i data-lucide="check-circle" style="width:14px;"></i> ${status} Complete!`;
          lucide.createIcons();
          setTimeout(() => {
            progressDiv.style.display = "none";
            callback();
          }, 800);
        }
      }, 100);
    }

    window.handleImageUpload = function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          simulateUpload(idPrefix, () => {
            document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
            document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
          });
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      const input = document.getElementById(idPrefix + "Input");
      if (input.hasAttribute("data-cover-image")) {
        delete input.dataset.coverImage;
        window.postData.isCoverDeleted = true;
      }
      input.value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";
    };

    window.galleryFilesArray = [];
    window.handleGalleryUpload = async function (input, idPrefix = 'gallery') {
      const container = document.getElementById("galleryPreviewContainer");

      if (input.files) {
        for (const file of Array.from(input.files)) {
          const uniqueId = "gal_" + Math.random().toString(36).substr(2, 9);
          const isVideo = file.type.startsWith("video/");

          const injectHTML = (src, showPlayBtn) => {
            const playBtnHTML = showPlayBtn ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>` : "";
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
            container.insertAdjacentHTML("beforeend", colHTML);
            if (window.lucide) lucide.createIcons();
          };

          if (isVideo) {
            // Upload chunk immediately
            const tempPath = await uploadFileInChunks(file, idPrefix, uniqueId);
            
            // AWAIT the canvas extraction to guarantee the thumbnail exists
            const thumbBlob = await new Promise((resolve) => {
              const video = document.createElement("video");
              video.preload = "metadata";
              video.src = URL.createObjectURL(file);
              video.onloadeddata = () => { video.currentTime = 1; };
              video.onseeked = () => {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 360;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.85);
              };
              video.onerror = () => resolve(null); // Failsafe
            });

            window.galleryFilesArray.push({ id: uniqueId, tempPath: tempPath, thumbBlob: thumbBlob, type: 'video', fileName: file.name });
            if (thumbBlob) injectHTML(URL.createObjectURL(thumbBlob), true);
            
          } else {
            window.galleryFilesArray.push({ id: uniqueId, file: file, type: 'image' });
            const reader = new FileReader();
            reader.onload = (e) => injectHTML(e.target.result, false);
            reader.readAsDataURL(file);
          }
        }
      }
      
      const contentDiv = document.getElementById(idPrefix + "Content");
      const progressDiv = document.getElementById(idPrefix + "Progress");
      if (contentDiv && progressDiv) {
         setTimeout(() => { contentDiv.style.display = "block"; progressDiv.style.display = "none"; }, 1000);
      }
      input.value = "";
    };

    window.removeGalleryItem = function (id, isDeleted = false) {
      document.getElementById(id).remove();

      if (isDeleted) {
        window.galleryFilesDeletedArray.push(id);
      } else {
        window.galleryFilesArray = window.galleryFilesArray.filter((item) => item.id !== id);
      }
    };

    if (document.getElementById("editor")) {
      const Icons = Quill.import("ui/icons");
      Icons.undo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="6 10 4 12 2 10"></polyline><path class="ql-stroke" d="M8.09,13.91A4.6,4.6,0,0,0,9,14,5,5,0,1,0,4,9"></path></svg>';
      Icons.redo =
        '<svg viewbox="0 0 18 18"><polyline class="ql-stroke" points="12 10 14 12 16 10"></polyline><path class="ql-stroke" d="M9.91,13.91A4.6,4.6,0,0,1,9,14a5,5,0,1,1,5-5"></path></svg>';

      quill = new Quill("#editor", {
        theme: "snow",
        modules: {
          history: { delay: 1000, maxStack: 100, userOnly: true },
          toolbar: {
            container: [
              ["undo", "redo"],
              [{ size: ["small", false, "large", "huge"] }],
              ["bold", "italic", "underline", "strike"],
              [{ align: "" }, { align: "center" }, { align: "right" }, { align: "justify" }],
              [{ list: "ordered" }, { list: "bullet" }],
              ["link"],
            ],
            handlers: {
              undo: function () {
                this.quill.history.undo();
              },
              redo: function () {
                this.quill.history.redo();
              },
            },
          },
        },
      });

      // -------------------------------------------------------------
      // Word Document Extractor Logic (Edit Post)
      // -------------------------------------------------------------
      const radioManual = document.getElementById("descManual");
      const radioWord = document.getElementById("descWord");
      const manualSection = document.getElementById("manualDescSection");
      const wordSection = document.getElementById("wordUploadSection");

      function updateDescUI() {
        if (radioManual.checked) {
          wordSection.style.display = "none";
        } else {
          wordSection.style.display = "block";
        }
        if (window.lucide) lucide.createIcons();
      }
      
      if (radioManual && radioWord) {
        radioManual.addEventListener("change", updateDescUI);
        radioWord.addEventListener("change", updateDescUI);
      }

      window.handleWordUpload = function (input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        if (!file.name.toLowerCase().endsWith('.docx')) {
          showAlert('error', 'Please upload a valid modern Word document (.docx).');
          return;
        }

        const contentDiv = document.getElementById("wordContent");
        const progressDiv = document.getElementById("wordProgress");
        const progressBar = document.getElementById("wordProgressBar");
        const progressText = document.getElementById("wordProgressText");

        contentDiv.style.display = "none";
        progressDiv.style.display = "block";
        progressBar.className = "progress-fill";
        progressBar.style.width = "60%";
        progressText.className = "upload-status-text";
        progressText.innerText = `Extracting Text...`;

        const reader = new FileReader();
        reader.onload = function(event) {
          const arrayBuffer = event.target.result;
          
          mammoth.convertToHtml({arrayBuffer: arrayBuffer})
            .then(function(result) {
              const html = result.value; 
              
              progressBar.style.width = "100%";
              progressBar.classList.add("success");
              progressText.classList.add("success");
              progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Extraction Complete!';
              if (window.lucide) lucide.createIcons();

              setTimeout(() => {
                quill.clipboard.dangerouslyPasteHTML(html);
                
                radioManual.checked = true;
                updateDescUI();
                
                input.value = "";
                contentDiv.style.display = "block";
                progressDiv.style.display = "none";
                progressBar.classList.remove("success");
                progressText.classList.remove("success");
                progressBar.style.width = "0%";
                
                showAlert('success', 'Word document imported successfully! Existing text has been replaced.');
              }, 800);
            })
            .catch(function(err) {
              console.error("Mammoth Parse Error:", err);
              showAlert('error', 'Failed to extract text. The document may be corrupted or password-protected.');
              contentDiv.style.display = "block";
              progressDiv.style.display = "none";
              input.value = "";
            });
        };
        
        reader.readAsArrayBuffer(file);
      };

    }

    document.getElementById("editPostForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const activeBtn = e.submitter;
      let postSubmitStatus = activeBtn && activeBtn.id === "statusBtn" ? activeBtn.dataset.action : null;
      console.log(postSubmitStatus);

      const originalBtnText = activeBtn.innerHTML;
      activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

      const statusBtn = document.getElementById("statusBtn");
      const saveBtn = document.getElementById("saveBtn");
      if (statusBtn) statusBtn.disabled = true;
      if (saveBtn) saveBtn.disabled = true;

      const formData = new FormData();
      const id = window.postData.id ?? null;
      const title = document.getElementById("postTitle").value;
      const content = quill.root.innerHTML;

      formData.append("id", id);
      if (window.postData.title !== title) formData.append("title", title);
      if (window.postData.content !== content) formData.append("content", content);
      if (postSubmitStatus) formData.append("status", postSubmitStatus);

      // Loop and append the array only ONCE
      Array.from(document.getElementById("postImpactArea").selectedOptions).forEach((option) => {
        formData.append("impact_area[]", option.value);
      });

      const coverInput = document.getElementById("coverInput");
      const coverImage = coverInput.files[0] ? coverInput.files[0] : null;
      const isCoverDeleted = window.postData.isCoverDeleted;

      if (coverImage) formData.append("cover_image", coverImage);
      formData.append("is_cover_deleted", isCoverDeleted);

      window.galleryFilesArray.forEach((item, index) => {
        if (item.type === 'video') {
            formData.append(`gallery_videos_temp[${index}]`, item.tempPath);
            formData.append(`gallery_videos_names[${index}]`, item.fileName);
            if (item.thumbBlob) {
                formData.append(`gallery_thumbnails[${index}]`, item.thumbBlob, `thumb_${index}.jpg`);
            }
        } else {
            formData.append(`gallery_files[${index}]`, item.file);
        }
      });

      window.galleryFilesDeletedArray.forEach((item) => {
        formData.append(`gallery_files_deleted[]`, item);
      });

      try {
        const response = await fetch("actions/posts/update-post.php", { method: "POST", body: formData });
        const rawText = await response.text();
        try {
          const result = JSON.parse(rawText);
          if (result.success) {
            showAlert("success", "Post Updated Successfully!");
            loadView("posts", "Posts");
          } else {
            console.error("Backend Error:", result.message);
            showAlert("error", "Failed to save post. Please verify the details.");
            if (saveBtn) saveBtn.disabled = false;
            if (statusBtn) statusBtn.disabled = false;
            activeBtn.innerHTML = originalBtnText;
          }
        } catch (e) {
          console.error("Server Error: ", rawText);
          showAlert("error", "A server error occurred. Please try again later.");
          if (saveBtn) saveBtn.disabled = false;
          if (statusBtn) statusBtn.disabled = false;
          activeBtn.innerHTML = originalBtnText;
        }
      } catch (err) {
        console.error(err);
        showAlert("error", "A network error occurred. Please try again.");
        if (saveBtn) saveBtn.disabled = false;
        if (statusBtn) statusBtn.disabled = false;
        activeBtn.innerHTML = originalBtnText;
      }
    });

    const urlParams = new URLSearchParams(params);
    fetch(`actions/posts/fetch-post.php?${urlParams}`)
      .then((res) => res.json())
      .then((data) => {
        if (!data || data.status === "error") {
          showAlert("error", data ? data.message : "Something went wrong.");
          loadView("posts", "Posts");
          return;
        }

        const id = data.id ?? null;
        const title = data.title ?? null;
        const content = data.content ?? "";
        const coverImage = data.cover_image ?? null;
        const status = data.status ?? null;
        const impactAreaIds = data.impact_area_ids ?? null;
        const media = data.post_media ?? null;

        if ([id, title, status].includes(null)) throw new Error("Missing required post data.");

        window.postData.id = id;
        window.postData.title = title;
        window.postData.content = content;
        window.postData.impactAreaIds = impactAreaIds;
        window.postData.isCoverDeleted = false;

        document.getElementById("postTitle").value = title;
        const postImpactAreaSelect = document.getElementById("postImpactArea");
        if (impactAreaIds && impactAreaIds.length > 0) {
          Array.from(postImpactAreaSelect.options).forEach((opt) => {
            if (impactAreaIds.includes(opt.value) || impactAreaIds.includes(parseInt(opt.value))) {
              opt.selected = true;
            }
          });

          if (window.editPostMultiSelect) window.editPostMultiSelect.refresh();
        }

        if (quill && content) quill.root.innerHTML = content;

        if (coverImage) {
          document.getElementById("coverInput").dataset.coverImage = coverImage;
          simulateUpload(
            "cover",
            () => {
              document.getElementById("coverPreviewWrapper").style.display = "block";
              document.getElementById("coverPreviewImg").src = `/project-sedna/${coverImage}`;
            },
            true,
          );
        }

        if (media) {
          const container = document.getElementById("galleryPreviewContainer");
          media.forEach((item) => {
            const uniqueId = item.id;
            const url = `/project-sedna/${item.url}`;
            const isVideo = item.type === "video" ? true : false;
            const injectHTML = (src, showPlayBtn) => {
              const playBtnHTML = showPlayBtn
                ? `<div class="video-play-overlay"><i data-lucide="play" style="width:20px; fill:#fff;"></i></div>`
                : "";
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
              container.insertAdjacentHTML("beforeend", colHTML);
              lucide.createIcons();
            };

            if (isVideo) {
              const video = document.createElement("video");
              video.preload = "metadata";
              video.src = url;
              video.onloadeddata = () => {
                video.currentTime = 1;
              };
              video.onseeked = () => {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                injectHTML(canvas.toDataURL("image/jpeg"), true);
              };
            } else {
              injectHTML(url, false);
            }
          });
        }

        const statusBtn = document.getElementById("statusBtn");
        if (status === "draft" || status === "archived") {
          statusBtn.dataset.action = "published";
          statusBtn.textContent = "Publish";
          statusBtn.classList.remove("d-none");
        } else if (status === "published") {
          statusBtn.dataset.action = "archived";
          statusBtn.textContent = "Archive";
          statusBtn.classList.remove("d-none");
        } else {
          throw new Error("Post status unknown.");
        }
      })
      .catch((err) => {
        console.error("Post Fetch Error:", err);
        showAlert("error", "Failed to load the post.");
        loadView("posts", "Posts");
      });
  }

  // ==========================================
  // --- VIEW: PUBLICATIONS LOGIC ---
  // ==========================================
  function initPublicationsScript_Old() {
    window.loadPublications = function () {
      const tbody = document.getElementById("publications-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading publications...</td></tr>';

      fetch("actions/publications/fetch-publications.php")
        .then((res) => res.json())
        .then((data) => {
          const pubs = Array.isArray(data) ? data : Object.values(data);
          tbody.innerHTML = "";

          if (pubs.length === 0 || pubs[0].error) {
            tbody.innerHTML =
              '<tr><td colspan="5" class="text-center text-muted py-4">No publications found.</td></tr>';
            return;
          }

          console.log(pubs);

          pubs.forEach((item) => {
            let pillClass = (item.status || "").toLowerCase() === "published" ? "status-published" : "status-draft";
            const row = `
                <tr>
                  <td class="fw-medium">${item.title}</td>
                  <td>${item.category || "Uncategorized"}</td>
                  <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
                  <td>${item.uploaded_by_name || "System"}</td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-light text-primary border-0 me-1 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-warning border-0 me-1 shadow-sm" title="Edit" onclick="loadView('edit-publication', 'Publications', { id: ${item.id} });"><i data-lucide="edit" style="width: 16px;"></i></button>
                    <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${item.title.replace(/'/g, "\\'")}', 'publication', 'actions/publications/delete-publication.php', 'loadPublications')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                  </td>
                </tr>
              `;
            tbody.insertAdjacentHTML("beforeend", row);
          });
          lucide.createIcons();
        })
        .catch((err) => {
          console.error("Publications Fetch Error:", err);
          tbody.innerHTML =
            '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load publications.</td></tr>';
        });
    };
    loadPublications();
  }

  function initPublicationsScript() {
    // State configuration
    let state = {
      search: "",
      filter: "all", // Default selected status filter
      dateRange: "all_time", // Default selected date range
      startDate: "",
      endDate: "",
      page: 1,
      limit: 10, // Default selected rows per page
    };

    let searchTimeout = null;

    window.loadPublications = function () {
      const tbody = document.getElementById("publications-tbody");
      if (!tbody) return;

      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Loading publications...</td></tr>';

      const params = new URLSearchParams({
        search: state.search,
        filter: state.filter,
        date_range: state.dateRange,
        start_date: state.startDate,
        end_date: state.endDate,
        page: state.page,
        limit: state.limit,
      });

      fetch(`actions/publications/fetch-publications.php?${params.toString()}`)
        .then((res) => res.json())
        .then((resData) => {
          const pubs = resData.publications || [];
          const pagination = resData.pagination || { total_records: 0, total_pages: 1, current_page: 1, limit: 10 };

          tbody.innerHTML = "";

          if (pubs.length === 0 || pubs[0]?.error) {
            tbody.innerHTML =
              '<tr><td colspan="5" class="text-center text-muted py-4">No publications found.</td></tr>';
            renderPagination(pagination);
            return;
          }

          pubs.forEach((item) => {
            let statusLower = (item.status || "").toLowerCase();
            let pillClass =
              statusLower === "published"
                ? "status-published"
                : statusLower === "archived"
                  ? "status-archived"
                  : "status-draft";

            const row = `
            <tr>
              <td class="fw-medium">${item.title}</td>
              <td>${item.category || "Uncategorized"}</td>
              <td><span class="status-pill ${pillClass}">${(item.status || "Draft").charAt(0).toUpperCase() + (item.status || "draft").slice(1)}</span></td>
              <td>${item.uploaded_by_name || "System"}</td>
              <td class="text-end">
                <div class="hstack gap-1 justify-content-end">
                  <button class="btn btn-sm btn-light text-primary border-0 shadow-sm" title="View"><i data-lucide="eye" style="width: 16px;"></i></button>
                  <button class="btn btn-sm btn-light text-warning border-0 shadow-sm" title="Edit" onclick="loadView('edit-publication', 'Publications', { id: ${item.id} });"><i data-lucide="edit" style="width: 16px;"></i></button>
                  <button class="btn btn-sm btn-light text-danger border-0 shadow-sm" title="Delete" onclick="openDeleteModal(${item.id}, '${(item.title || "").replace(/'/g, "\\'")}', 'publication', 'actions/publications/delete-publication.php', 'loadPublications')"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                </div>
              </td>
            </tr>
          `;
            tbody.insertAdjacentHTML("beforeend", row);
          });

          if (window.lucide) {
            lucide.createIcons();
          }

          renderPagination(pagination);
        })
        .catch((err) => {
          console.error("Publications Fetch Error:", err);
          tbody.innerHTML =
            '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load publications.</td></tr>';
        });
    };

    // Render pagination buttons and entries counter
    function renderPagination(pagination) {
      const container = document.getElementById("publications-pagination");
      const infoText = document.getElementById("publications-pagination-info-text");
      if (!container) return;

      const { total_records, total_pages, current_page, limit } = pagination;

      // Update record summary
      if (infoText) {
        const start = total_records === 0 ? 0 : (current_page - 1) * limit + 1;
        const end = Math.min(current_page * limit, total_records);
        infoText.innerText = `Showing ${start}-${end} of ${total_records}`;
      }

      if (total_pages <= 1) {
        container.innerHTML = "";
        return;
      }

      let html = `<nav><ul class="pagination pagination-sm table-pagination gap-1 mb-0">`;

      // First Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changePublicationPage(1)"><i data-lucide="chevrons-left" style="width: 16px;"></i>First</button>
      </li>
    `;

      // Back Button
      html += `
      <li class="page-item ${current_page === 1 ? "disabled" : ""}">
        <button class="page-link" onclick="changePublicationPage(${current_page - 1})"><i data-lucide="chevron-left" style="width: 16px;"></i>Back</button>
      </li>
    `;

      // Page Number Buttons
      for (let i = 1; i <= total_pages; i++) {
        if (i === 1 || i === total_pages || (i >= current_page - 1 && i <= current_page + 1)) {
          html += `
          <li class="page-item ${i === current_page ? "active" : ""}">
            <button class="page-link" onclick="changePublicationPage(${i})">${i}</button>
          </li>
        `;
        } else if (i === current_page - 2 || i === current_page + 2) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }

      // Next Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changePublicationPage(${current_page + 1})">Next<i data-lucide="chevron-right" style="width: 16px;"></i></button>
      </li>
    `;

      // Last Button
      html += `
      <li class="page-item ${current_page === total_pages ? "disabled" : ""}">
        <button class="page-link" onclick="changePublicationPage(${total_pages})">Last<i data-lucide="chevrons-right" style="width: 16px;"></i></button>
      </li>
    `;

      html += `</ul></nav>`;
      container.innerHTML = html;
      if (window.lucide) {
        lucide.createIcons();
      }
    }

    // Page switcher attached to global scope
    window.changePublicationPage = function (pageNumber) {
      state.page = pageNumber;
      loadPublications();
    };

    // Event Listener Bindings
    const searchInput = document.getElementById("publication-search-input");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          state.search = e.target.value.trim();
          state.page = 1;
          loadPublications();
        }, 300);
      });
    }

    const filterPills = document.querySelectorAll("#publication-status-pills .filter-pill");
    filterPills.forEach((pill) => {
      pill.addEventListener("click", function () {
        filterPills.forEach((p) => {
          p.classList.remove("active");
          const icon = p.querySelector("[data-lucide]");
          if (icon) icon.classList.add("d-none");
        });
        this.classList.add("active");
        const icon = this.querySelector("[data-lucide]");
        if (icon) icon.classList.remove("d-none");

        state.filter = this.getAttribute("data-filter");
        state.page = 1;
        loadPublications();
      });
    });

    const dateOptions = document.querySelectorAll("#pub-date-filter-options .dropdown-item");
    const dateLabel = document.getElementById("pub-date-filter-label");
    const customDateItem = document.getElementById("pubCustomDateItem");

    dateOptions.forEach((option) => {
      option.addEventListener("click", function (e) {
        e.preventDefault();

        const selectedRange = this.getAttribute("data-range");
        if (!selectedRange) return;

        if (typeof resetDateRangePicker === "function") {
          resetDateRangePicker(customDateItem);
        }

        if (selectedRange === "custom") {
          e.stopPropagation();
        } else {
          dateOptions.forEach((opt) => opt.classList.remove("active"));
          this.classList.add("active");

          if (dateLabel) dateLabel.innerText = this.innerText;

          state.dateRange = selectedRange;
          state.startDate = "";
          state.endDate = "";
          state.page = 1;
          loadPublications();
        }
      });
    });

    // Date range picker initialization
    if (typeof $ !== "undefined" && customDateItem) {
      $(function () {
        $(customDateItem).daterangepicker(
          {
            opens: "left",
            drops: "up",
          },
          function (start, end) {
            const startDate = start?.format("YYYY-MM-DD") || "";
            const endDate = end?.format("YYYY-MM-DD") || "";
            if (dateLabel) dateLabel.textContent = `${startDate} - ${endDate}`;

            state.startDate = startDate;
            state.endDate = endDate;
            state.dateRange = "custom";
            state.page = 1;
            loadPublications();
          },
        );
        $(customDateItem).on("show.daterangepicker", function (ev, picker) {
          picker.container.find(".drp-calendar").on("click", function (e) {
            e.stopPropagation();
          });
        });
      });
    }

    const rowsSelect = document.getElementById("publications-rows-per-page");
    if (rowsSelect) {
      rowsSelect.addEventListener("change", function () {
        state.limit = parseInt(this.value, 10);
        state.page = 1;
        loadPublications();
      });
    }

    // Execute initial load
    loadPublications();
  }

  // ==========================================
  // --- VIEW: CREATE PUBLICATION LOGIC ---
  // ==========================================
  function initCreatePublicationScript() {
    let extractedPdfCoverBlob = null;

    const radioAuto = document.getElementById("coverAuto");
    const radioCustom = document.getElementById("coverCustom");
    const autoSection = document.getElementById("autoCoverSection");
    const customSection = document.getElementById("customCoverSection");

    function updateCoverUI() {
      if (radioAuto.checked) {
        autoSection.style.display = "block";
        customSection.style.display = "none";
      } else {
        autoSection.style.display = "none";
        customSection.style.display = "block";
      }
    }
    if (radioAuto && radioCustom) {
      radioAuto.addEventListener("change", updateCoverUI);
      radioCustom.addEventListener("change", updateCoverUI);
    }

    window.handlePdfUpload = async function (input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type !== "application/pdf") {
          showAlert("error", "Please upload a valid PDF file.");
          return;
        }

        // --- NEW CHUNK UPLOAD CALL ---
        await uploadFileInChunks(file, "pdf", "publication_pdf");

        document.getElementById("pdfPreviewWrapper").style.display = "block";
        document.getElementById("pdfFileNameDisplay").innerText = file.name;

        try {
          if (!window.pdfjsLib) throw new Error("pdfjsLib is not loaded");
          if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
              "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";
          }

          const fileURL = URL.createObjectURL(file);
          const pdf = await pdfjsLib.getDocument(fileURL).promise;
          const page = await pdf.getPage(1);

          const viewport = page.getViewport({ scale: 1.5 });
          const canvas = document.createElement("canvas");
          const ctx = canvas.getContext("2d");
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          await page.render({ canvasContext: ctx, viewport: viewport }).promise;

          canvas.toBlob(
            (blob) => {
              extractedPdfCoverBlob = blob;
              const imgUrl = URL.createObjectURL(blob);
              document.getElementById("autoCoverPlaceholder").style.display = "none";
              const imgEl = document.getElementById("autoCoverImg");
              imgEl.src = imgUrl;
              imgEl.style.display = "block";
            },
            "image/jpeg",
            0.8,
          );
        } catch (err) {
          console.error("PDF Extraction Failed:", err);
          document.getElementById("autoCoverPlaceholder").innerText =
            "Preview extraction failed. A default cover will be used.";
        }
      }
    };

    function setupFileUploadDragAndDrop() {
      const uploadAreas = document.querySelectorAll(".upload-area");
      uploadAreas.forEach((area) => {
        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            (e) => {
              e.preventDefault();
              e.stopPropagation();
            },
            false,
          );
        });
        ["dragenter", "dragover"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            () => {
              area.style.borderColor = "var(--sapsri-red)";
              area.style.backgroundColor = "#F9E7EC";
            },
            false,
          );
        });
        ["dragleave", "drop"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            () => {
              area.style.borderColor = "#D6D6D6";
              area.style.backgroundColor = "#FDF4F6";
            },
            false,
          );
        });
        area.addEventListener(
          "drop",
          (e) => {
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
              const fileInput = area.querySelector('input[type="file"]');
              if (fileInput) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event("change", { bubbles: true }));
              }
            }
          },
          false,
        );
      });
    }
    setupFileUploadDragAndDrop();

    window.removePdf = function (event) {
      event.stopPropagation();
      document.getElementById("pdfInput").value = "";
      document.getElementById("pdfPreviewWrapper").style.display = "none";
      document.getElementById("pdfContent").style.display = "block";
      extractedPdfCoverBlob = null;
      document.getElementById("autoCoverImg").style.display = "none";
      document.getElementById("autoCoverImg").src = "";
      document.getElementById("autoCoverPlaceholder").style.display = "block";
      document.getElementById("autoCoverPlaceholder").innerText = "A preview will appear here once a PDF is uploaded.";
    };

    window.handleImageUpload = async function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = async function (e) {
          await simulateUploadAsync(idPrefix);
          document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
          document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      document.getElementById(idPrefix + "Input").value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";
    };

    document.getElementById("createPublicationForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const activeBtn = e.submitter;
      const draftBtn = document.getElementById("draftBtn");
      const publishBtn = document.getElementById("publishBtn");
      const status = activeBtn && activeBtn.id === "draftBtn" ? "draft" : "published";

      const originalBtnText = activeBtn.innerHTML;
      activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
      draftBtn.disabled = true;
      publishBtn.disabled = true;

      const formData = new FormData();
      formData.append("title", document.getElementById("pubTitle").value);
      formData.append("category_id", document.getElementById("pubCategory").value);
      formData.append("description", document.getElementById("pubDescription").value);
      formData.append("status", status);

      const isCustom = document.getElementById("coverCustom").checked;
      formData.append("is_custom_cover", isCustom ? 1 : 0);

      // Check if the chunk uploader saved a temporary path for our PDF
      if (window.uploadedTempFiles["publication_pdf"]) {
        formData.append("temp_pdf_path", window.uploadedTempFiles["publication_pdf"]);
      }

      if (isCustom) {
        const customInput = document.getElementById("pubCoverInput");
        if (customInput.files[0]) formData.append("cover_image", customInput.files[0]);
      } else {
        if (extractedPdfCoverBlob) {
          formData.append("cover_image", extractedPdfCoverBlob, "auto-cover.jpg");
        }
      }

      try {
        const response = await fetch("actions/publications/create-publication.php", { method: "POST", body: formData });
        const rawText = await response.text();
        try {
          const result = JSON.parse(rawText);
          if (result.success) {
            showAlert("success", "Publication Saved Successfully!");
            loadView("publications", "Publications");
          } else {
            console.error("Backend Error:", result.message);
            showAlert("error", "Failed to save publication. Please verify the details.");
          }
        } catch (e) {
          console.error("Server Error: ", rawText);
          showAlert("error", "A server error occurred. Check console for details.");
        }
      } catch (err) {
        console.error(err);
        showAlert("error", "A network error occurred. Check the server response.");
      } finally {
        if(draftBtn) draftBtn.disabled = false;
        if(publishBtn) publishBtn.disabled = false;
        if(activeBtn) activeBtn.innerHTML = originalBtnText;
      }
    });
  }

  // ==========================================
  // --- VIEW: EDIT PUBLICATION LOGIC ---
  // ==========================================
  async function initEditPublicationScript(params = {}) {
    const publicationId = params.id;
    if (!publicationId) {
      showAlert("error", "Invalid or missing publication ID.");
      return;
    }

    // State tracking variables
    let extractedPdfCoverBlob = null;
    let currentStatus = "draft";
    let existingPdfUrl = null;
    let existingCoverUrl = null;
    let isCustomCoverRemoved = false;
    let isPdfFileRemoved = false;

    const radioAuto = document.getElementById("coverAuto");
    const radioCustom = document.getElementById("coverCustom");
    const autoSection = document.getElementById("autoCoverSection");
    const customSection = document.getElementById("customCoverSection");
    const secondaryBtn = document.getElementById("secondaryActionBtn");
    const saveBtn = document.getElementById("saveBtn");

    document.getElementById("pubId").value = publicationId;

    // -------------------------------------------------------------
    // 1. Cover Strategy Toggle Listener
    // -------------------------------------------------------------
    function updateCoverUI() {
      if (radioAuto.checked) {
        autoSection.style.display = "block";
        customSection.style.display = "none";
      } else {
        autoSection.style.display = "none";
        customSection.style.display = "block";
      }
    }

    if (radioAuto && radioCustom) {
      radioAuto.addEventListener("change", updateCoverUI);
      radioCustom.addEventListener("change", updateCoverUI);
    }

    // -------------------------------------------------------------
    // 2. Load Existing Data from Backend
    // -------------------------------------------------------------
    try {
      const response = await fetch(`actions/publications/fetch-publication.php?id=${publicationId}`);
      if (!response.ok) throw new Error("Failed to load publication data");

      const data = await response.json();
      if (data.error) throw new Error(data.error);

      // Populate standard form fields
      document.getElementById("pubTitle").value = data.title || "";
      document.getElementById("pubCategory").value = data.category_id || "";
      document.getElementById("pubDescription").value = data.description || "";
      currentStatus = data.status || "draft";

      // Dynamic Action Buttons State
      updateActionButtons(currentStatus);

      // Populate File Preview (PDF)
      if (data.file_url) {
        existingPdfUrl = data.file_url;
        const fileName = data.file_url.split("/").pop() || "Uploaded Document.pdf";
        document.getElementById("pdfContent").style.display = "none";
        document.getElementById("pdfPreviewWrapper").style.display = "block";
        document.getElementById("pdfFileNameDisplay").innerText = fileName;
      }

      // Populate Strategy & Cover Image Preview
      const isCustom = Boolean(data.is_custom_cover);
      if (isCustom) {
        radioCustom.checked = true;
        if (data.cover_image) {
          existingCoverUrl = data.cover_image;
          document.getElementById("pubCoverContent").style.display = "none";
          document.getElementById("pubCoverPreviewWrapper").style.display = "block";
          document.getElementById("pubCoverPreviewImg").src = data.cover_image;
        }
      } else {
        radioAuto.checked = true;
        if (data.cover_image) {
          document.getElementById("autoCoverPlaceholder").style.display = "none";
          const imgEl = document.getElementById("autoCoverImg");
          imgEl.src = data.cover_image;
          imgEl.style.display = "block";
        }
      }

      updateCoverUI();

      if (window.lucide) lucide.createIcons();
    } catch (err) {
      console.error("Fetch Publication Error:", err);
      showAlert("error", err.message || "Failed to fetch publication details.");
    }

    // Helper function to update action button labels based on loaded status
    function updateActionButtons(status) {
      if (status === "draft") {
        secondaryBtn.innerText = "Publish";
        secondaryBtn.dataset.targetStatus = "published";
        // secondaryBtn.className = "btn btn-outline-success px-4";
      } else if (status === "published") {
        secondaryBtn.innerText = "Archive";
        secondaryBtn.dataset.targetStatus = "archived";
        // secondaryBtn.className = "btn btn-outline-warning px-4";
      } else if (status === "archived") {
        secondaryBtn.innerText = "Publish";
        secondaryBtn.dataset.targetStatus = "published";
        // secondaryBtn.className = "btn btn-outline-success px-4";
      }
    }

    // -------------------------------------------------------------
    // 3. Upload Simulation Helper
    // -------------------------------------------------------------
    function simulateUploadAsync(idPrefix) {
      return new Promise((resolve) => {
        const contentDiv = document.getElementById(idPrefix + "Content");
        const progressDiv = document.getElementById(idPrefix + "Progress");
        const progressBar = document.getElementById(idPrefix + "ProgressBar");
        const progressText = document.getElementById(idPrefix + "ProgressText");

        if (!contentDiv || !progressDiv) return resolve();

        contentDiv.style.display = "none";
        progressDiv.style.display = "block";
        progressBar.className = "progress-fill";
        progressBar.style.width = "0%";
        progressText.className = "upload-status-text";

        let progress = 0;
        const interval = setInterval(() => {
          progress += Math.random() * 25;
          if (progress >= 100) progress = 100;
          progressBar.style.width = progress + "%";
          progressText.innerText = `Uploading & Processing... ${Math.round(progress)}%`;

          if (progress === 100) {
            clearInterval(interval);
            progressBar.classList.add("success");
            progressText.classList.add("success");
            progressText.innerHTML = '<i data-lucide="check-circle" style="width:14px;"></i> Processing Complete!';
            if (window.lucide) lucide.createIcons();
            setTimeout(() => {
              progressDiv.style.display = "none";
              resolve();
            }, 700);
          }
        }, 100);
      });
    }

    // -------------------------------------------------------------
    // 4. File Upload & Extraction Handling
    // -------------------------------------------------------------
    window.handlePdfUpload = async function (input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type !== "application/pdf") {
          showAlert("error", "Please upload a valid PDF file.");
          return;
        }

        isPdfFileRemoved = false;
        await simulateUploadAsync("pdf");
        document.getElementById("pdfPreviewWrapper").style.display = "block";
        document.getElementById("pdfFileNameDisplay").innerText = file.name;

        try {
          if (!window.pdfjsLib) throw new Error("pdfjsLib is not loaded");
          if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
              "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";
          }

          const fileURL = URL.createObjectURL(file);
          const pdf = await pdfjsLib.getDocument(fileURL).promise;
          const page = await pdf.getPage(1);

          const viewport = page.getViewport({ scale: 1.5 });
          const canvas = document.createElement("canvas");
          const ctx = canvas.getContext("2d");
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          await page.render({ canvasContext: ctx, viewport: viewport }).promise;

          canvas.toBlob(
            (blob) => {
              extractedPdfCoverBlob = blob;
              const imgUrl = URL.createObjectURL(blob);
              document.getElementById("autoCoverPlaceholder").style.display = "none";
              const imgEl = document.getElementById("autoCoverImg");
              imgEl.src = imgUrl;
              imgEl.style.display = "block";
            },
            "image/jpeg",
            0.8,
          );
        } catch (err) {
          console.error("PDF Extraction Failed:", err);
          document.getElementById("autoCoverPlaceholder").innerText =
            "Preview extraction failed. Existing or default cover will be used.";
        }
      }
    };

    window.removePdf = function (event) {
      event.stopPropagation();
      document.getElementById("pdfInput").value = "";
      document.getElementById("pdfPreviewWrapper").style.display = "none";
      document.getElementById("pdfContent").style.display = "block";

      extractedPdfCoverBlob = null;
      isPdfFileRemoved = true;

      // Reset auto-cover preview if PDF removed
      document.getElementById("autoCoverImg").style.display = "none";
      document.getElementById("autoCoverImg").src = "";
      document.getElementById("autoCoverPlaceholder").style.display = "block";
      document.getElementById("autoCoverPlaceholder").innerText = "A preview will appear here once a PDF is uploaded.";
    };

    window.handleImageUpload = async function (input, idPrefix) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = async function (e) {
          isCustomCoverRemoved = false;
          await simulateUploadAsync(idPrefix);
          document.getElementById(idPrefix + "PreviewWrapper").style.display = "block";
          document.getElementById(idPrefix + "PreviewImg").src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeImage = function (event, idPrefix) {
      event.stopPropagation();
      document.getElementById(idPrefix + "Input").value = "";
      document.getElementById(idPrefix + "PreviewImg").src = "";
      document.getElementById(idPrefix + "PreviewWrapper").style.display = "none";
      document.getElementById(idPrefix + "Content").style.display = "block";
      isCustomCoverRemoved = true;
    };

    // Drag and drop helper
    function setupFileUploadDragAndDrop() {
      const uploadAreas = document.querySelectorAll(".upload-area");
      uploadAreas.forEach((area) => {
        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            (e) => {
              e.preventDefault();
              e.stopPropagation();
            },
            false,
          );
        });
        ["dragenter", "dragover"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            () => {
              area.style.borderColor = "var(--sapsri-red)";
              area.style.backgroundColor = "#F9E7EC";
            },
            false,
          );
        });
        ["dragleave", "drop"].forEach((eventName) => {
          area.addEventListener(
            eventName,
            () => {
              area.style.borderColor = "#D6D6D6";
              area.style.backgroundColor = "#FDF4F6";
            },
            false,
          );
        });
        area.addEventListener(
          "drop",
          (e) => {
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
              const fileInput = area.querySelector('input[type="file"]');
              if (fileInput) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event("change", { bubbles: true }));
              }
            }
          },
          false,
        );
      });
    }
    setupFileUploadDragAndDrop();

    // -------------------------------------------------------------
    // 5. Submit Handling
    // -------------------------------------------------------------
    document.getElementById("editPublicationForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const activeBtn = e.submitter;
      let targetStatus = currentStatus;

      // Check which submit button was clicked
      if (activeBtn && activeBtn.id === "secondaryActionBtn") {
        targetStatus = activeBtn.dataset.targetStatus || currentStatus;
      }

      const originalBtnText = activeBtn.innerHTML;
      activeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
      secondaryBtn.disabled = true;
      saveBtn.disabled = true;

      const formData = new FormData();
      formData.append("id", publicationId);
      formData.append("title", document.getElementById("pubTitle").value);
      formData.append("category_id", document.getElementById("pubCategory").value);
      formData.append("description", document.getElementById("pubDescription").value);
      formData.append("status", targetStatus);

      const isCustom = radioCustom.checked;
      formData.append("is_custom_cover", isCustom ? 1 : 0);

      // Track removal flags
      formData.append("is_pdf_removed", isPdfFileRemoved ? 1 : 0);
      formData.append("is_custom_cover_removed", isCustomCoverRemoved ? 1 : 0);

      // PDF upload handling
      const pdfInput = document.getElementById("pdfInput");
      if (pdfInput.files[0]) {
        formData.append("pdf_file", pdfInput.files[0]);
      }

      // Cover image upload handling according to chosen strategy
      if (isCustom) {
        const customInput = document.getElementById("pubCoverInput");
        if (customInput.files[0]) {
          formData.append("cover_image", customInput.files[0]);
        }
      } else {
        // Auto strategy selected
        if (extractedPdfCoverBlob) {
          // Newly generated blob from a newly uploaded PDF
          formData.append("cover_image", extractedPdfCoverBlob, "auto-cover.jpg");
        }
      }

      try {
        const response = await fetch("actions/publications/update-publication.php", {
          method: "POST",
          body: formData,
        });

        const rawText = await response.text();
        try {
          const result = JSON.parse(rawText);
          if (result.success) {
            showAlert("success", "Publication Updated Successfully!");
            loadView("publications", "Publications");
          } else {
            console.error("Backend Error:", result.message);
            showAlert("error", result.message || "Failed to update publication.");
          }
        } catch (err) {
          console.error("Server Error: ", rawText);
          showAlert("error", "A server error occurred. Check console for details.");
        }
      } catch (err) {
        console.error(err);
        showAlert("error", "A network error occurred. Check the server response.");
      } finally {
        if(secondaryBtn) secondaryBtn.disabled = false;
        if(saveBtn) saveBtn.disabled = false;
        if(activeBtn) activeBtn.innerHTML = originalBtnText;
      }
    });
  }

  // ==========================================
  // --- VIEW: USERS LOGIC ---
  // ==========================================
  function initUsersScript() {
    fetchUserManagementData();

    async function fetchUserManagementData() {
      try {
        const response = await fetch("actions/users/fetch-users.php");
        const data = await response.json();
        if (data.success) {
          renderActiveUsers(data.active_users);
          renderPendingUsers(data.pending_users, data.roles);
          renderRoles(data.roles);
          lucide.createIcons();
        } else {
          console.error("Failed to load user data:", data.message);
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    }

    function renderActiveUsers(users) {
      const tbody = document.getElementById("active-users-tbody");
      tbody.innerHTML = "";

      if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>';
        return;
      }

      users.forEach((user) => {
        const fullName = `${user.first_name} ${user.last_name}`;
        const isSuspended = user.status === "suspended";
        const statusPill = isSuspended
          ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2">Suspended</span>`
          : `<span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2">Active</span>`;

        const actionBtn = isSuspended
          ? `<button class="btn btn-sm btn-light text-success border-0 me-1 shadow-sm" onclick="handleUserSuspendAction(${user.id}, 'activate')" title="Remove Suspension"><i data-lucide="user-check" style="width: 16px;"></i></button>`
          : `<button class="btn btn-sm btn-light text-danger border-0 me-1 shadow-sm" onclick="openSuspendModal(${user.id}, '${fullName.replace(/'/g, "\\'")}')" title="Suspend"><i data-lucide="user-minus" style="width: 16px;"></i></button>`;

        tbody.insertAdjacentHTML(
          "beforeend",
          `
            <tr>
              <td class="fw-medium ${isSuspended ? "text-muted" : ""}">${fullName}</td>
              <td class="${isSuspended ? "text-muted" : ""}">${user.email}</td>
              <td class="${isSuspended ? "text-muted" : ""}">${user.role_name || "No Role"}</td>
              <td>${statusPill}</td>
              <td class="text-end">${actionBtn}</td>
            </tr>
          `,
        );
      });
    }

    function renderPendingUsers(users, roles) {
      const tbody = document.getElementById("pending-users-tbody");
      tbody.innerHTML = "";
      if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No pending requests.</td></tr>';
        return;
      }

      let roleOptions = '<option disabled selected value="">Select Role...</option>';
      roles.forEach((role) => {
        roleOptions += `<option value="${role.id}">${role.name}</option>`;
      });

      users.forEach((user) => {
        const fullName = `${user.first_name} ${user.last_name}`;
        const reqDate = new Date(user.created_at).toLocaleDateString("en-GB");

        tbody.insertAdjacentHTML(
          "beforeend",
          `
            <tr id="pending-row-${user.id}">
              <td class="fw-medium">${fullName}</td>
              <td>${user.email}</td>
              <td>${reqDate}</td>
              <td class="text-end d-flex justify-content-end align-items-center gap-2">
                <select class="form-select form-select-sm role-select" id="role-select-${user.id}">${roleOptions}</select>
                <button class="btn btn-sm btn-success shadow-sm" onclick="handleUserRequest(${user.id}, 'accept')">Accept</button>
                <button class="btn btn-sm btn-outline-danger shadow-sm" onclick="handleUserRequest(${user.id}, 'reject')">Reject</button>
              </td>
            </tr>
          `,
        );
      });
    }

    function renderRoles(roles) {
      const tbody = document.getElementById("roles-tbody");
      tbody.innerHTML = "";
      if (roles.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No roles found.</td></tr>';
        return;
      }
      roles.forEach((role) => {
        tbody.insertAdjacentHTML(
          "beforeend",
          `
            <tr>
              <td class="fw-medium">${role.name}</td>
              <td class="text-muted">${role.description || "No description provided."}</td>
              <td class="text-end"><button class="btn btn-sm btn-light border-0 shadow-sm" title="Edit Role"><i data-lucide="edit" style="width: 16px;"></i></button></td>
            </tr>
          `,
        );
      });
    }

    window.handleUserRequest = async function (userId, action) {
      const roleSelect = document.getElementById(`role-select-${userId}`);
      const roleId = roleSelect ? roleSelect.value : null;

      if (action === "accept" && !roleId) return showAlert("error", "Please select a role before accepting the user.");
      if (!confirm(`Are you sure you want to ${action} this user?`)) return;

      const formData = new FormData();
      formData.append("user_id", userId);
      formData.append("action", action);
      if (roleId) formData.append("role_id", roleId);

      try {
        const response = await fetch("actions/users/handle-request.php", { method: "POST", body: formData });
        const result = await response.json();

        if (result.success) {
          showAlert("success", result.message);
          const row = document.getElementById(`pending-row-${userId}`);
          if (row) row.remove();
          if (action === "accept") fetchUserManagementData();
        } else {
          console.error("Backend Error:", result.message);
          showAlert("error", "Action could not be completed.");
        }
      } catch (error) {
        console.error(error);
        showAlert("error", "A network error occurred processing the request.");
      }
    };

    window.openSuspendModal = function (userId, userName) {
      document.getElementById("suspendUserNameText").innerText =
        `Are You Sure Want to Suspend This User "${userName}"?`;
      document.getElementById("confirmSuspendBtn").setAttribute("data-target-user", userId);
      const suspendModal = new bootstrap.Modal(document.getElementById("suspendUserModal"));
      suspendModal.show();
    };

    window.handleUserSuspendAction = async function (userId, action, duration = "") {
      if (action === "activate" && !confirm("Are you sure you want to remove the suspension for this user?")) return;

      const formData = new FormData();
      formData.append("user_id", userId);
      formData.append("action", action);
      if (action === "suspend") formData.append("duration", duration);

      try {
        const response = await fetch("actions/users/suspend-user.php", { method: "POST", body: formData });
        const result = await response.json();
        if (result.success) {
          showAlert("success", result.message);
          fetchUserManagementData();
        } else {
          console.error("Backend Error:", result.message);
          showAlert("error", "Action could not be completed.");
        }
      } catch (error) {
        console.error(error);
        showAlert("error", "A network error occurred.");
      }
    };

    const confirmSuspendBtn = document.getElementById("confirmSuspendBtn");
    if (confirmSuspendBtn) {
      const newConfirmBtn = confirmSuspendBtn.cloneNode(true);
      confirmSuspendBtn.parentNode.replaceChild(newConfirmBtn, confirmSuspendBtn);

      newConfirmBtn.addEventListener("click", function () {
        const userId = this.getAttribute("data-target-user");
        const duration = document.getElementById("suspendDuration").value;

        if (!duration) return showAlert("error", "Please select a suspension duration.");

        const modalEl = document.getElementById("suspendUserModal");
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        handleUserSuspendAction(userId, "suspend", duration);
        document.getElementById("suspendDuration").value = "";
      });
    }

    window.openCreateRoleModal = function () {
      document.getElementById("createRoleForm").reset();
      const roleModal = new bootstrap.Modal(document.getElementById("createRoleModal"));
      roleModal.show();
    };

    document.addEventListener("submit", async function (e) {
      if (e.target && e.target.id === "createRoleForm") {
        e.preventDefault();
        const submitBtn = document.getElementById("saveRoleBtn");
        const originalText = submitBtn.innerText;
        submitBtn.innerText = "Saving...";
        submitBtn.disabled = true;

        try {
          const formData = new FormData(e.target);
          const response = await fetch("actions/roles/create-role.php", { method: "POST", body: formData });
          const rawText = await response.text();
          try {
            const result = JSON.parse(rawText);
            if (result.success) {
              showAlert("success", result.message);
              const modalEl = document.getElementById("createRoleModal");
              const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
              modalInstance.hide();
              if (typeof fetchUserManagementData === "function") fetchUserManagementData();
            } else {
              console.error("Backend Error:", result.message);
              showAlert("error", "Failed to save role. Please try again.");
            }
          } catch (parseError) {
            console.error("PHP/SQL Error Output:", rawText);
            showAlert("error", "A server error occurred while saving.");
          }
        } catch (error) {
          console.error("Role Creation Error:", error);
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
}); // END DOMContentLoaded

// ==========================================
// CUSTOM PILL MULTI-SELECT UI COMPONENT
// ==========================================
class SAPSRIMultiSelect {
  constructor(selectId, placeholderText = "Search and select...") {
    this.nativeSelect = document.getElementById(selectId);
    if (!this.nativeSelect) return;

    this.placeholderText = placeholderText;
    this.nativeSelect.style.display = "none"; // Hide native select

    this.buildUI();
    this.bindEvents();
    this.updateUI(); // Initial render
  }

  buildUI() {
    this.wrapper = document.createElement("div");
    this.wrapper.className = "ms-wrapper";

    this.inputBox = document.createElement("div");
    this.inputBox.className = "ms-input-box";

    this.pillsContainer = document.createElement("div");
    this.pillsContainer.style.display = "flex";
    this.pillsContainer.style.flexWrap = "wrap";
    this.pillsContainer.style.gap = "6px";

    this.searchInput = document.createElement("input");
    this.searchInput.type = "text";
    this.searchInput.className = "ms-search-input";
    this.searchInput.placeholder = this.placeholderText;

    const controls = document.createElement("div");
    controls.className = "ms-controls";

    this.clearBtn = document.createElement("div");
    this.clearBtn.className = "ms-clear-all";
    this.clearBtn.innerHTML =
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

    this.chevron = document.createElement("div");
    this.chevron.className = "ms-chevron";
    this.chevron.innerHTML =
      '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

    controls.appendChild(this.clearBtn);
    controls.appendChild(this.chevron);

    this.dropdown = document.createElement("div");
    this.dropdown.className = "ms-dropdown";

    this.inputBox.appendChild(this.pillsContainer);
    this.inputBox.appendChild(this.searchInput);
    this.inputBox.appendChild(controls);
    this.wrapper.appendChild(this.inputBox);
    this.wrapper.appendChild(this.dropdown);

    this.nativeSelect.parentNode.insertBefore(this.wrapper, this.nativeSelect.nextSibling);
  }

  bindEvents() {
    this.inputBox.addEventListener("click", () => {
      this.wrapper.classList.add("open");
      this.inputBox.classList.add("active");
      this.searchInput.focus();
      this.renderDropdown();
    });

    this.searchInput.addEventListener("input", () => {
      this.wrapper.classList.add("open");
      this.renderDropdown(this.searchInput.value);
    });

    document.addEventListener("click", (e) => {
      if (!this.wrapper.contains(e.target)) {
        this.wrapper.classList.remove("open");
        this.inputBox.classList.remove("active");
        this.searchInput.value = "";
      }
    });

    this.clearBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      Array.from(this.nativeSelect.options).forEach((opt) => (opt.selected = false));
      this.updateUI();
      this.wrapper.classList.remove("open");
    });
  }

  updateUI() {
    this.pillsContainer.innerHTML = "";
    let hasSelection = false;

    Array.from(this.nativeSelect.options).forEach((option) => {
      if (option.selected && option.value) {
        hasSelection = true;
        const pill = document.createElement("div");
        pill.className = "ms-pill";
        pill.innerHTML = `${option.text} <div class="remove-pill" data-val="${option.value}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>`;
        this.pillsContainer.appendChild(pill);
      }
    });

    this.pillsContainer.querySelectorAll(".remove-pill").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const opt = Array.from(this.nativeSelect.options).find((o) => o.value === btn.getAttribute("data-val"));
        if (opt) opt.selected = false;
        this.updateUI();
        if (this.wrapper.classList.contains("open")) this.renderDropdown(this.searchInput.value);
      });
    });

    this.searchInput.placeholder = hasSelection ? "" : this.placeholderText;
    this.clearBtn.style.display = hasSelection ? "flex" : "none";
  }

  renderDropdown(filterText = "") {
    this.dropdown.innerHTML = "";
    const lowerFilter = filterText.toLowerCase();
    let hasVisibleOptions = false;

    Array.from(this.nativeSelect.options).forEach((option) => {
      if (!option.value || option.selected) return;

      if (option.text.toLowerCase().includes(lowerFilter)) {
        hasVisibleOptions = true;
        const item = document.createElement("div");
        item.className = "ms-option";
        item.innerText = option.text;

        item.addEventListener("click", (e) => {
          e.stopPropagation();
          option.selected = true;
          this.searchInput.value = "";
          this.updateUI();
          this.renderDropdown();
          this.searchInput.focus(); // Keep open for multi-select
        });
        this.dropdown.appendChild(item);
      }
    });

    if (!hasVisibleOptions) {
      this.dropdown.innerHTML = `<div class="ms-empty-state">${filterText ? "No matches found" : "All selected"}</div>`;
    }
  }

  refresh() {
    this.updateUI();
  }
}
