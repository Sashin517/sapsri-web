<style>
  .user-tabs { border-bottom: 2px solid var(--border-color); }
  .user-tabs .nav-link { 
    color: var(--text-muted); font-weight: 500; border: none; border-bottom: 2px solid transparent; 
    padding: 0.75rem 1.5rem; margin-bottom: -2px; transition: all 0.2s ease; background: transparent; 
  }
  .user-tabs .nav-link:hover { color: var(--text-dark); }
  .user-tabs .nav-link.active { color: var(--sapsri-red); border-bottom: 2px solid var(--sapsri-red); background: transparent; }
  .role-select { max-width: 200px; display: inline-block; }
</style>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3 px-4 pt-4">
  <div>
    <h1 class="mb-1 fw-bold fs-3">Manage Users</h1>
    <p class="mb-0 text-muted" style="font-size: 0.95rem;">Manage admins, roles, and access requests</p>
  </div>
  <button class="btn btn-create-project d-flex align-items-center gap-2" onclick="openCreateRoleModal()">
    <i data-lucide="user-plus" style="width: 18px;"></i> Create New Role
  </button>
</div>

<!-- Tabs -->
<div class="px-4 mb-4">
  <ul class="nav nav-tabs user-tabs" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-users" type="button">Users</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#incoming-req" type="button">Incoming Acceptance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#roles" type="button">Roles</button>
    </li>
  </ul>
</div>

<!-- Tab Content -->
<div class="tab-content px-4 pb-4" id="userTabsContent">
  
  <!-- Tab 1: Active Users -->
  <div class="tab-pane fade show active" id="active-users">
    <div class="content-card card-edge-table h-100 mb-0">
      <div class="table-responsive mb-0">
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody id="active-users-tbody">
            <!-- Injected via JS -->
            <tr><td colspan="5" class="text-center text-muted py-4">Loading users...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab 2: Incoming Acceptance -->
  <div class="tab-pane fade" id="incoming-req">
    <div class="content-card card-edge-table h-100 mb-0">
      <div class="table-responsive mb-0">
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Request Date</th><th class="text-end">Assign Role & Action</th></tr>
          </thead>
          <tbody id="pending-users-tbody">
             <!-- Injected via JS -->
             <tr><td colspan="4" class="text-center text-muted py-4">Loading requests...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab 3: Roles -->
  <div class="tab-pane fade" id="roles">
    <div class="content-card card-edge-table h-100 mb-0">
      <div class="table-responsive mb-0">
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr><th>Role Name</th><th>Description</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody id="roles-tbody">
            <!-- Injected via JS -->
            <tr><td colspan="3" class="text-center text-muted py-4">Loading roles...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- EXACT Suspend User UI Modal -->
<div class="modal fade" id="suspendUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
      
      <div class="modal-header border-bottom-0 pb-0 align-items-start gap-3 pt-4 px-4">
        <div class="text-warning mt-1">
          <i data-lucide="triangle-alert" style="width: 28px; height: 28px;"></i>
        </div>
        <h4 class="modal-title fw-bold mt-1" style="color: #000; font-size: 1.25rem;">Suspend User</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body px-4 pt-3 pb-4">
        <div class="rounded-3 p-3 mb-4" style="background-color: #EFEFEF;">
          <h5 class="fw-bold mb-2 text-dark" style="font-size: 1rem;" id="suspendUserNameText">Are You Sure Want to Suspend This User?</h5>
          <p class="mb-0 text-dark" style="font-size: 0.9rem;">This will instantly revoke the user's access to the admin app. Their historical data will remain intact.</p>
        </div>
        
        <div class="mb-2">
          <label class="form-label fw-medium text-dark" style="font-size: 0.95rem;">Please select a duration.</label>
          <select class="form-select text-muted" id="suspendDuration" style="border-radius: 8px; font-size: 0.95rem; padding: 0.75rem 1rem;">
            <option value="" selected disabled>Select Duration of Suspension</option>
            <option value="24_hours">24 Hours</option>
            <option value="3_days">3 Days</option>
            <option value="1_week">1 Week (7 Days)</option>
            <option value="1_month">1 Month (30 Days)</option>
            <option value="indefinite">Until Further Activation</option>
          </select>
        </div>
      </div>
      
      <div class="modal-footer border-top-0 px-4 pb-4 d-flex gap-3">
        <button type="button" class="btn flex-grow-1" data-bs-dismiss="modal" style="background-color: #EAEAEA; color: #000; border-radius: 8px; font-weight: 500; padding: 0.7rem;">Cancel</button>
        <button type="button" class="btn flex-grow-1 text-white" id="confirmSuspendBtn" style="background-color: #E31837; border-radius: 8px; font-weight: 500; padding: 0.7rem;">Suspend</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Custom CSS for the Role Modal (Can be moved to your main stylesheet later) -->
<style>
    .sapsri-checkbox:checked {
        background-color: var(--sapsri-red, #A20A35) !important;
        border-color: var(--sapsri-red, #A20A35) !important;
    }
    .permission-matrix-table {
        border-radius: 8px;
        overflow: hidden;
    }
    .permission-matrix-table th {
        background-color: #f4f5f6;
        color: #4a4a4a;
        font-weight: 600;
        text-align: center;
        padding: 1rem;
    }
    .permission-matrix-table td {
        vertical-align: middle;
        padding: 1rem;
    }
    .permission-matrix-table td:first-child, 
    .permission-matrix-table th:first-child {
        text-align: left;
    }
    .modal-xl-custom {
        max-width: 800px;
    }
</style>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl-custom">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
      
      <!-- Modal Header -->
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <h5 class="modal-title fw-bold fs-4">Create New Role</h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="createRoleForm">
        <!-- Modal Body -->
        <div class="modal-body px-4 pt-4 pb-2">
            
            <div class="mb-4">
                <label class="form-label fw-medium text-dark">Role Name</label>
                <input type="text" name="role_name" class="form-control form-control-lg fs-6" placeholder="Administrative Lead" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium text-dark">Role Description</label>
                <textarea name="role_description" class="form-control" rows="3" placeholder="Provide a brief description..."></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label fw-medium text-dark mb-3">Permission Matrix</label>
                <div class="border permission-matrix-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>View</th>
                                <th>Create</th>
                                <th>Edit</th>
                                <th>Delete/Suspend</th>
                                <th>Publish</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Matrix Rows injected via PHP/HTML -->
                            <?php 
                            $modules = ['Dashboard', 'Projects', 'Posts', 'Publications', 'Users'];
                            foreach($modules as $module): 
                                $mod_key = strtolower($module);
                            ?>
                            <tr>
                                <td class="fw-medium text-secondary"><?= $module ?></td>
                                <td class="text-center"><input class="form-check-input sapsri-checkbox fs-5 shadow-sm" type="checkbox" name="permissions[<?= $mod_key ?>][view]" value="1"></td>
                                <td class="text-center"><input class="form-check-input sapsri-checkbox fs-5 shadow-sm" type="checkbox" name="permissions[<?= $mod_key ?>][create]" value="1"></td>
                                <td class="text-center"><input class="form-check-input sapsri-checkbox fs-5 shadow-sm" type="checkbox" name="permissions[<?= $mod_key ?>][edit]" value="1"></td>
                                <td class="text-center"><input class="form-check-input sapsri-checkbox fs-5 shadow-sm" type="checkbox" name="permissions[<?= $mod_key ?>][delete]" value="1"></td>
                                <td class="text-center"><input class="form-check-input sapsri-checkbox fs-5 shadow-sm" type="checkbox" name="permissions[<?= $mod_key ?>][publish]" value="1"></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer border-top px-4 py-3 bg-white" style="border-radius: 0 0 12px 12px;">
            <button type="button" class="btn btn-light border px-4 py-2 fw-medium" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn px-4 py-2 text-white fw-medium shadow-sm" style="background-color: var(--sapsri-red, #A20A35);" id="saveRoleBtn">Save Role</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // UI Modal Trigger Script
  function openSuspendModal(userId, userName) {
    document.getElementById('suspendUserNameText').innerText = `Are You Sure Want to Suspend This User "${userName}"`;
    // Store user ID somewhere for the submission button
    document.getElementById('confirmSuspendBtn').setAttribute('data-target-user', userId);
    
    const suspendModal = new bootstrap.Modal(document.getElementById('suspendUserModal'));
    suspendModal.show();
  }
</script>