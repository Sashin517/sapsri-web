<style>
  .upload-area { border: 2px dashed #D6D6D6; background-color: #FDF4F6; border-radius: 8px; height: 240px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; cursor: pointer; transition: all 0.2s ease; position: relative; overflow: hidden; }
  .upload-area:hover { border-color: var(--sapsri-red); }
  .upload-icon { color: var(--sapsri-red); width: 32px; height: 32px; margin-bottom: 0.5rem; }
  .image-preview-wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none; }
  .image-preview { width: 100%; height: 100%; object-fit: cover; }
  .remove-img-btn { position: absolute; top: 8px; right: 8px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; transition: opacity 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--sapsri-red); }
  .image-preview-wrapper:hover .remove-img-btn { opacity: 1; }
  .section-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; background: #fff; }
  .quill-resizer { resize: vertical; overflow: hidden; min-height: 250px; border: 1px solid var(--border-color); border-radius: 0 0 8px 8px; border-top: none; display: flex; flex-direction: column; background: #fff; }
  .quill-resizer .ql-container { flex: 1; border: none !important; font-family: 'Inter', sans-serif; font-size: 0.95rem; }
  .upload-progress-wrapper { display: none; width: 80%; margin: 0 auto; text-align: center; }
  .progress-bar-custom { height: 8px; border-radius: 4px; background-color: #E9ECEF; overflow: hidden; margin-bottom: 0.5rem; }
  .progress-fill { height: 100%; background-color: var(--sapsri-red); width: 0%; transition: width 0.1s linear; }
  .progress-fill.success { background-color: #147A42; }
  .upload-status-text { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); }
  .upload-status-text.success { color: #147A42; }
  .video-play-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.5); color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; pointer-events: none; }
</style>

<!-- Top Header / Breadcrumb -->
<div class="d-flex align-items-center justify-content-between mb-4 px-4 pt-4">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('posts', 'Posts Management')">
      <i data-lucide="arrow-left" style="width: 20px;"></i>
    </button>
    <span class="text-muted" style="font-size: 0.95rem;">Posts > </span> 
    <span class="fw-bold fs-4">Create New Post</span>
  </div>
  <button class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('posts', 'Posts Management')">
    <i data-lucide="x" style="width: 20px;"></i>
  </button>
</div>

<div class="px-4 pb-5">
  <form id="createPostForm">
    
    <!-- Post Details -->
    <div class="section-card">
      <div class="d-flex align-items-center mb-4">
        <h4 class="fw-bold mb-0 fs-5">Post Details</h4>
        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto d-flex gap-2 align-items-center">
          <i data-lucide="eye" style="width:16px;"></i> Preview
        </button>
      </div>

      <div class="mb-3">
        <label class="form-label fw-medium">Post Title</label>
        <input type="text" id="postTitle" class="form-control" placeholder="Type post title here..." required>
      </div>

      <!-- Impact Area -->
      <div class="mb-4">
        <label class="form-label fw-medium">Impact Area</label>
        <select class="form-select" id="postImpactArea" multiple>
          <option value="1">Climate & Biodiversity</option>
          <option value="2">Sustainable Agriculture</option>
          <option value="3">Finance & Governance</option>
          <option value="4">Gender Inclusion</option>
          <option value="5">Other</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label fw-medium">Cover Image</label>
        <div class="upload-area" id="coverUploadArea" onclick="document.getElementById('coverInput').click()">
          <div class="upload-content" id="coverContent">
            <i data-lucide="upload" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop image<br><small>(recommended resolution, 1307x535 px)</small></p>
          </div>
          <div class="upload-progress-wrapper" id="coverProgress">
            <div class="progress-bar-custom"><div class="progress-fill" id="coverProgressBar"></div></div>
            <div class="upload-status-text" id="coverProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper" id="coverPreviewWrapper">
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
          <p class="small mb-2">To ensure your post looks perfect on all devices, this tool extracts only your <strong>clean text, headings, and lists</strong>.</p>
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
        <label class="form-label fw-medium">Post Content</label>
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <div class="quill-resizer">
          <div id="editor"></div>
        </div>
      </div>
    </div>

    <!-- Media Gallery -->
    <h4 class="fw-bold mb-3 fs-5 mt-5">Media Gallery</h4>
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
        <!-- Note the 'gallery' parameter added to the function call -->
        <input type="file" id="galleryInput" class="d-none" accept="image/*,video/*" multiple onchange="handleGalleryUpload(this, 'gallery')">
      </div>
      <div class="row g-3" id="galleryPreviewContainer"></div>
    </div>

    <hr class="my-5">

    <!-- Footer Actions -->
    <div class="d-flex justify-content-end gap-3 pb-4">
      <button type="button" class="btn btn-light border px-4" onclick="loadView('posts', 'Posts Management')">Cancel</button>
      <button type="submit" id="draftBtn" class="btn btn-outline-danger px-4">Save as Draft</button>
      <button type="submit" id="publishBtn" class="btn px-4 text-white" style="background: var(--sapsri-red);">Publish Post</button>
    </div>

  </form>
</div>