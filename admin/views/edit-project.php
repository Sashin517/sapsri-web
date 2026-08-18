<style>
  /* --- REPLACED CSS TO FIX HEIGHTS --- */
  .upload-area {
    border: 2px dashed #D6D6D6;
    background-color: #FDF4F6;
    border-radius: 8px;
    height: 240px; /* Locks exact height for all standard uploads */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden; /* Ensures preview images don't spill out */
  }
  .upload-area:hover { border-color: var(--sapsri-red); }
  .upload-icon { color: var(--sapsri-red); width: 32px; height: 32px; margin-bottom: 0.5rem; }
  
  .image-preview-wrapper {
    position: absolute; /* Overlays perfectly on top of the fixed-height container */
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    display: none; 
  }
  .image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  /* Centers the round lead profile image perfectly in the absolute container */
  .image-preview-wrapper.round-preview .image-preview {
    border-radius: 50%;
    width: 160px;
    height: 160px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  .remove-img-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: var(--sapsri-red);
  }
  .image-preview-wrapper:hover .remove-img-btn { opacity: 1; }

  /* Toggles & Cards */
  .form-switch .form-check-input { width: 3rem; height: 1.5rem; }
  .form-switch .form-check-input:checked { background-color: var(--sapsri-red); border-color: var(--sapsri-red); }
  
  .section-card {
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    background: #fff;
  }
  .metric-row, .story-row {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    background: #FAFAFA;
  }
  .btn-outline-red {
    border: 1px solid var(--sapsri-red);
    color: var(--sapsri-red);
    background: transparent;
  }
  .btn-outline-red:hover { background: var(--sapsri-red-light); color: var(--sapsri-red); }
  .btn-outline-red:disabled { border-color: #ccc; color: #ccc; background: transparent; cursor: not-allowed; }

  /* --- NEW STYLES: Resizable Editor, Progress Bars, Drag & Drop --- */
  
  /* Quill Resizer */
  .quill-resizer {
    resize: vertical;
    overflow: hidden;
    min-height: 250px;
    border: 1px solid var(--border-color);
    border-radius: 0 0 8px 8px;
    border-top: none;
    display: flex;
    flex-direction: column;
    background: #fff;
  }
  .quill-resizer .ql-container { flex: 1; border: none !important; font-family: 'Inter', sans-serif; font-size: 0.95rem; }

  /* Upload Progress UI */
  .upload-progress-wrapper { display: none; width: 80%; margin: 0 auto; text-align: center; }
  .progress-bar-custom { height: 8px; border-radius: 4px; background-color: #E9ECEF; overflow: hidden; margin-bottom: 0.5rem; }
  .progress-fill { height: 100%; background-color: var(--sapsri-red); width: 0%; transition: width 0.1s linear; }
  .progress-fill.success { background-color: #147A42; }
  .upload-status-text { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); }
  .upload-status-text.success { color: #147A42; }

  /* Metric Icon Box */
  .metric-icon-box { 
    width: 42px; height: 42px; border: 1px dashed var(--border-color); border-radius: 6px; 
    display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; background: #fff; flex-shrink: 0;
  }
  .metric-icon-box:hover { border-color: var(--sapsri-red); }
  .metric-icon-box img { width: 100%; height: 100%; object-fit: contain; border-radius: 6px; display: none; }
  .remove-icon-btn { 
    position: absolute; top: -6px; right: -6px; background: #fff; border: 1px solid var(--sapsri-red); 
    color: var(--sapsri-red); border-radius: 50%; width: 18px; height: 18px; display: none; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; z-index: 10; padding: 0;
  }
  .metric-icon-box:hover .remove-icon-btn { display: flex; }

  /* Drag and Drop States */
  .metric-row { transition: transform 0.2s ease, box-shadow 0.2s ease; }
  .metric-row.dragging { opacity: 0.5; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 1px dashed var(--sapsri-red); }
  .drag-handle { cursor: grab; padding: 0.5rem; margin-left: -0.5rem; }
  .drag-handle:active { cursor: grabbing; }

  /* Video Gallery Overlays */
  .video-play-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.5); color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; pointer-events: none; }
</style>



<div class="d-flex align-items-center justify-content-between mb-4 px-4 pt-4">
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('projects', 'Projects Management')">
      <i data-lucide="arrow-left" style="width: 20px;"></i>
    </button>
    <span class="text-muted" style="font-size: 0.95rem;">Projects > </span> 
    <span class="fw-bold fs-4">Edit Project</span>
  </div>
  <button type="button" class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('projects', 'Projects Management')">
    <i data-lucide="x" style="width: 20px;"></i>
  </button>
</div>

<div class="px-4 pb-5">
  <form id="editProjectForm">
    <!-- Hidden input to pass Project ID -->
    <input type="hidden" id="projectId" name="project_id" value="">
    
    <!-- Project Details -->
    <div class="section-card">
      <div class="d-flex align-items-center gap-3 mb-4">
        <h4 class="fw-bold mb-0 fs-5">Project Details</h4>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="projectPhaseToggle" checked>
          <label class="form-check-label fw-medium ms-2" for="projectPhaseToggle" id="phaseLabel">Ongoing</label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto d-flex gap-2 align-items-center">
          <i data-lucide="eye" style="width:16px;"></i> Preview
        </button>
      </div>

      <div class="mb-3">
        <label class="form-label fw-medium">Title</label>
        <input type="text" id="projectTitle" class="form-control" placeholder="Type project title here..." required>
      </div>

      <div class="mb-4">
        <label class="form-label fw-medium">Impact Area</label>
        <select class="form-select" id="projectImpactArea" multiple>
          <option value="1">Climate & Biodiversity</option>
          <option value="2">Sustainable Agriculture</option>
          <option value="3">Finance & Governance</option>
          <option value="4">Gender Inclusion</option>
          <option value="5">Other</option>
        </select>
      </div>

      <!-- Date Row -->
      <div class="row mb-4">
        <div class="col-md-12" id="startDateContainer">
          <label class="form-label fw-medium">Start Date</label>
          <input type="date" id="projectStartDate" class="form-control" required>
        </div>
        <div class="col-md-6" id="endDateContainer" style="display: none;">
          <label class="form-label fw-medium">End Date</label>
          <input type="date" id="projectEndDate" class="form-control">
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-medium">Cover Image</label>
        <div class="upload-area" id="coverUploadArea" onclick="document.getElementById('coverInput').click()">
          <div class="upload-content" id="coverContent">
            <i data-lucide="upload" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop image<br><small>(recommended resolution, 1920x1080 px)</small></p>
          </div>
          <div class="upload-progress-wrapper" id="coverProgress" style="display:none;">
            <div class="progress-bar-custom"><div class="progress-fill" id="coverProgressBar"></div></div>
            <div class="upload-status-text" id="coverProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper" id="coverPreviewWrapper" style="display:none;">
            <img src="" class="image-preview" id="coverPreviewImg">
            <button type="button" class="remove-img-btn" onclick="removeImage(event, 'cover')"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="coverInput" class="d-none" accept="image/*" onchange="handleImageUpload(this, 'cover')">
        </div>
      </div>

      <!-- Content Strategy Toggle -->
      <div class="mb-3">
        <label class="form-label fw-medium">Content Strategy</label>
        <div class="d-flex gap-4 p-3 border rounded bg-light">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="descStrategy" id="descManual" value="manual" checked>
            <label class="form-check-label fw-medium" for="descManual">Write Manually</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="descStrategy" id="descWord" value="word">
            <label class="form-check-label fw-medium" for="descWord">Import from Word (.docx)</label>
          </div>
        </div>
      </div>

      <!-- Word Upload Section (Hidden by Default) -->
      <div class="mb-4" id="wordUploadSection" style="display: none;">
        <div class="alert border-0 mb-3" style="background-color: #E8F4FD; color: #0C4A6E;" role="alert">
          <h6 class="fw-bold mb-2"><i data-lucide="info" style="width: 18px; margin-top:-2px;"></i> Note on Word Imports</h6>
          <p class="small mb-2">Importing a Word document will <strong>replace the current text</strong> in the editor. This tool extracts only your <strong>clean text, headings, and lists</strong>.</p>
          <ul class="small mb-0 ps-3">
            <li><strong>Upload your .docx file</strong> below.</li>
            <li><strong>Review your text</strong> in the editor once it imports, and adjust any spacing if needed.</li>
            <li><strong>Upload images separately</strong> using the Media Gallery below (embedded Word images are ignored).</li>
          </ul>
        </div>
        
        <div class="upload-area" id="wordUploadArea" onclick="document.getElementById('wordInput').click()">
          <div class="upload-content" id="wordContent">
            <i data-lucide="file-text" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop your .docx file</p>
          </div>
          <div class="upload-progress-wrapper" id="wordProgress">
            <div class="progress-bar-custom"><div class="progress-fill" id="wordProgressBar"></div></div>
            <div class="upload-status-text" id="wordProgressText">Extracting Text... 0%</div>
          </div>
          <input type="file" id="wordInput" class="d-none" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" onchange="handleWordUpload(this)">
        </div>
      </div>

      <!-- Quill Editor Section -->
      <div class="mb-3" id="manualDescSection">
        <label class="form-label fw-medium">Full Description</label>
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <div class="quill-resizer">
          <div id="editor"></div>
        </div>
      </div>
    </div>

    <!-- Metrics Sections -->
    <h4 class="fw-bold mb-3 fs-5 mt-5">Project Metrics Sections</h4>
    
    <!-- Section 1 -->
    <div class="section-card">
      <h5 class="fw-bold mb-3 fs-6">Section One</h5>
      <div class="mb-4">
        <label class="form-label fw-medium">Metrics Image</label>
        <div class="upload-area" onclick="document.getElementById('sec1Input').click()">
          <div class="upload-content" id="sec1Content">
            <i data-lucide="upload" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop image<br><small>(1920x1080 px)</small></p>
          </div>
          <div class="upload-progress-wrapper" id="sec1Progress" style="display:none;">
            <div class="progress-bar-custom"><div class="progress-fill" id="sec1ProgressBar"></div></div>
            <div class="upload-status-text" id="sec1ProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper" id="sec1PreviewWrapper" style="display:none;">
            <img src="" class="image-preview" id="sec1PreviewImg">
            <button type="button" class="remove-img-btn" onclick="removeImage(event, 'sec1')"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="sec1Input" class="d-none" accept="image/*" onchange="handleImageUpload(this, 'sec1')">
        </div>
      </div>

      <h6 class="fw-bold mb-3 fs-6">Metrics</h6>
      <div class="d-flex align-items-center gap-3 mb-3">
        <button type="button" class="btn btn-outline-red d-flex gap-2 align-items-center" id="addMetricSec1Btn" onclick="addMetricRow(1)">
          <i data-lucide="plus" style="width:16px;"></i> Add Metric
        </button>
        <span class="text-muted small">maximum 3 metrics</span>
      </div>
      <div id="metricsContainerSec1"></div>
    </div>

    <!-- Section 2 -->
    <div class="section-card">
      <h5 class="fw-bold mb-3 fs-6">Section Two</h5>
      <div class="mb-4">
        <label class="form-label fw-medium">Metrics Image</label>
        <div class="upload-area" onclick="document.getElementById('sec2Input').click()">
          <div class="upload-content" id="sec2Content">
            <i data-lucide="upload" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop image</p>
          </div>
          <div class="upload-progress-wrapper" id="sec2Progress" style="display:none;">
            <div class="progress-bar-custom"><div class="progress-fill" id="sec2ProgressBar"></div></div>
            <div class="upload-status-text" id="sec2ProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper" id="sec2PreviewWrapper" style="display:none;">
            <img src="" class="image-preview" id="sec2PreviewImg">
            <button type="button" class="remove-img-btn" onclick="removeImage(event, 'sec2')"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="sec2Input" class="d-none" accept="image/*" onchange="handleImageUpload(this, 'sec2')">
        </div>
      </div>

      <h6 class="fw-bold mb-3 fs-6">Metrics</h6>
      <div class="d-flex align-items-center gap-3 mb-3">
        <button type="button" class="btn btn-outline-red d-flex gap-2 align-items-center" id="addMetricSec2Btn" onclick="addMetricRow(2)">
          <i data-lucide="plus" style="width:16px;"></i> Add Metric
        </button>
        <span class="text-muted small">maximum 3 metrics</span>
      </div>
      <div id="metricsContainerSec2"></div>
    </div>

    <!-- Success Stories -->
    <h4 class="fw-bold mb-3 fs-5 mt-5">Success Stories</h4>
    <div id="storiesWrapper"></div>
    <div class="text-end mb-5">
      <button type="button" class="btn btn-outline-red d-inline-flex gap-2 align-items-center" onclick="addStory()">
        <i data-lucide="plus" style="width:16px;"></i>Add Story
      </button>
    </div>

    <!-- Project Leads -->
    <h4 class="fw-bold mb-3 fs-5">Project Leads</h4>
    <div class="section-card d-flex flex-column flex-lg-row gap-4">
      <input type="hidden" id="leadId" value="">
      <div style="flex: 1;">
        <label class="form-label fw-medium">Profile Photo</label>
        <div class="upload-area" style="padding: 2rem;" onclick="document.getElementById('leadInput').click()">
          <div class="upload-content" id="leadContent">
            <i data-lucide="upload" class="upload-icon"></i>
            <p class="mb-0 text-muted small">Upload photo</p>
          </div>
          <div class="upload-progress-wrapper" id="leadProgress" style="display:none;">
            <div class="progress-bar-custom"><div class="progress-fill" id="leadProgressBar"></div></div>
            <div class="upload-status-text" id="leadProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper round-preview" id="leadPreviewWrapper" style="display:none;">
            <img src="" class="image-preview" id="leadPreviewImg">
            <button type="button" class="remove-img-btn" onclick="removeImage(event, 'lead')"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="leadInput" class="d-none" accept="image/*" onchange="handleImageUpload(this, 'lead')">
        </div>
      </div>
      <div style="flex: 2;">
        <div class="mb-3">
          <label class="form-label fw-medium">Name</label>
          <input type="text" id="leadName" class="form-control" placeholder="Type name here...">
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Role/Designation</label>
          <input type="text" id="leadRole" class="form-control" placeholder="Type designation here...">
        </div>
        <div class="mb-0">
          <label class="form-label fw-medium">LinkedIn Profile</label>
          <input type="url" id="leadLinkedin" class="form-control" placeholder="Paste URL here...">
        </div>
      </div>
    </div>

    <!-- Media Gallery -->
    <h4 class="fw-bold mb-3 fs-5 mt-5">Media Gallery (Relevant Photos)</h4>
    <div class="section-card">
      <div class="upload-area mb-3" onclick="document.getElementById('galleryInput').click()">
        <div class="upload-content" id="galleryContent">
          <i data-lucide="upload" class="upload-icon"></i>
          <p class="mb-0 text-muted">Click to upload or drag and drop images/videos</p>
        </div>
        <!-- Progress Bar Elements -->
        <div class="upload-progress-wrapper" id="galleryProgress" style="display:none;">
          <div class="progress-bar-custom"><div class="progress-fill" id="galleryProgressBar"></div></div>
          <div class="upload-status-text" id="galleryProgressText">Uploading... 0%</div>
        </div>
        <input type="file" id="galleryInput" class="d-none" accept="image/*,video/*" multiple onchange="handleGalleryUpload(this, 'gallery')">
      </div>
      <div class="row g-3" id="galleryPreviewContainer"></div>
    </div>

    <hr class="my-5">

    <!-- Footer Actions -->
    <div class="d-flex justify-content-end gap-3 pb-4">
      <button type="button" class="btn btn-light border px-4" onclick="loadView('projects', 'Projects Management')">Cancel</button>
      <button type="submit" id="statusBtn" class="btn btn-outline-danger px-4">--</button>
      <button type="submit" id="saveBtn" class="btn px-4 text-white" style="background: var(--sapsri-red);">Save</button>
    </div>

  </form>
</div>