<style>
  .upload-area { border: 2px dashed #D6D6D6; background-color: #FDF4F6; border-radius: 8px; height: 240px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; cursor: pointer; transition: all 0.2s ease; position: relative; overflow: hidden; }
  .upload-area:hover { border-color: var(--sapsri-red); }
  .upload-icon { color: var(--sapsri-red); width: 32px; height: 32px; margin-bottom: 0.5rem; }
  .image-preview-wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none; background: #fff; }
  .image-preview { width: 100%; height: 100%; object-fit: contain; }
  .remove-img-btn { position: absolute; top: 8px; right: 8px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; transition: opacity 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--sapsri-red); }
  .image-preview-wrapper:hover .remove-img-btn { opacity: 1; }
  
  .section-card { border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; background: #fff; }
  
  .upload-progress-wrapper { display: none; width: 80%; margin: 0 auto; text-align: center; }
  .progress-bar-custom { height: 8px; border-radius: 4px; background-color: #E9ECEF; overflow: hidden; margin-bottom: 0.5rem; }
  .progress-fill { height: 100%; background-color: var(--sapsri-red); width: 0%; transition: width 0.1s linear; }
  .progress-fill.success { background-color: #147A42; }
  .upload-status-text { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); }
  .upload-status-text.success { color: #147A42; }
  
  /* PDF specific preview styles */
  .pdf-preview-box { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; background: #F8F9FA; }
  .pdf-preview-box i { color: #dc3545; width: 48px; height: 48px; margin-bottom: 10px; }
  .pdf-filename { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); word-break: break-all; padding: 0 1rem; }
</style>

<!-- Include PDF.js for Client-Side Extraction -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';</script>

<!-- Top Header / Breadcrumb -->
<div class="d-flex align-items-center justify-content-between mb-4 px-4 pt-4">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-light border-0 p-2 rounded-circle" onclick="loadView('publications', 'Publications')">
      <i data-lucide="arrow-left" style="width: 20px;"></i>
    </button>
    <span class="text-muted" style="font-size: 0.95rem;">Publications > </span> 
    <span class="fw-bold fs-4">Create New Publication</span>
  </div>
</div>

<div class="px-4 pb-5">
  <form id="createPublicationForm">
    
    <div class="section-card">
      <h4 class="fw-bold mb-4 fs-5">Publication Details</h4>

      <div class="mb-3">
        <label class="form-label fw-medium">Title</label>
        <input type="text" id="pubTitle" class="form-control" placeholder="Type publication title here..." required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-medium">Category</label>
        <select class="form-select" id="pubCategory" required>
          <option selected disabled value="">Select Category...</option>
          <option value="1">Organisational policies</option>
          <option value="2">Annual Reports</option>
          <option value="3">Reports & Case Studies</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label fw-medium">Description</label>
        <textarea id="pubDescription" class="form-control" rows="4" placeholder="Brief description of the publication..."></textarea>
      </div>
    </div>

    <!-- File Uploads -->
    <div class="section-card">
      <h4 class="fw-bold mb-4 fs-5">Publication File & Cover</h4>
      
      <!-- PDF Upload -->
      <div class="mb-4">
        <label class="form-label fw-medium">Upload Document (PDF only)</label>
        <div class="upload-area" id="pdfUploadArea" onclick="document.getElementById('pdfInput').click()">
          <div class="upload-content" id="pdfContent">
            <i data-lucide="file-text" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload or drag and drop your PDF file</p>
          </div>
          <div class="upload-progress-wrapper" id="pdfProgress">
            <div class="progress-bar-custom"><div class="progress-fill" id="pdfProgressBar"></div></div>
            <div class="upload-status-text" id="pdfProgressText">Uploading... 0%</div>
          </div>
          <!-- PDF UI Preview -->
          <div class="image-preview-wrapper" id="pdfPreviewWrapper">
            <div class="pdf-preview-box">
              <i data-lucide="file-check-2"></i>
              <span class="pdf-filename" id="pdfFileNameDisplay">document.pdf</span>
            </div>
            <button type="button" class="remove-img-btn" onclick="removePdf(event)"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="pdfInput" class="d-none" accept="application/pdf" required onchange="handlePdfUpload(this)">
        </div>
      </div>

      <!-- Cover Image Toggle -->
      <div class="mb-3">
        <label class="form-label fw-medium">Cover Image Strategy</label>
        <div class="d-flex gap-4 p-3 border rounded bg-light">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="coverStrategy" id="coverAuto" value="auto" checked>
            <label class="form-check-label fw-medium" for="coverAuto">Auto-Extract from PDF (First Page)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="coverStrategy" id="coverCustom" value="custom">
            <label class="form-check-label fw-medium" for="coverCustom">Upload Custom Cover Image</label>
          </div>
        </div>
      </div>

      <!-- Auto Extracted Preview (Read-Only) -->
      <div class="mb-4" id="autoCoverSection">
        <label class="form-label fw-medium text-muted">Extracted Cover Preview</label>
        <div class="border rounded bg-light d-flex align-items-center justify-content-center" style="height: 240px; overflow: hidden;">
          <span id="autoCoverPlaceholder" class="text-muted small">A preview will appear here once a PDF is uploaded.</span>
          <img src="" id="autoCoverImg" style="display: none; height: 100%; object-fit: contain;">
        </div>
      </div>

      <!-- Custom Cover Upload (Hidden initially) -->
      <div class="mb-4" id="customCoverSection" style="display: none;">
        <label class="form-label fw-medium">Custom Cover Image</label>
        <div class="upload-area" id="pubCoverUploadArea" onclick="document.getElementById('pubCoverInput').click()">
          <div class="upload-content" id="pubCoverContent">
            <i data-lucide="image" class="upload-icon"></i>
            <p class="mb-0 text-muted">Click to upload custom cover image</p>
          </div>
          <div class="upload-progress-wrapper" id="pubCoverProgress">
            <div class="progress-bar-custom"><div class="progress-fill" id="pubCoverProgressBar"></div></div>
            <div class="upload-status-text" id="pubCoverProgressText">Uploading... 0%</div>
          </div>
          <div class="image-preview-wrapper" id="pubCoverPreviewWrapper">
            <img src="" class="image-preview" id="pubCoverPreviewImg">
            <button type="button" class="remove-img-btn" onclick="removeImage(event, 'pubCover')"><i data-lucide="x"></i></button>
          </div>
          <input type="file" id="pubCoverInput" class="d-none" accept="image/*" onchange="handleImageUpload(this, 'pubCover')">
        </div>
      </div>
      
    </div>

    <hr class="my-5">

    <!-- Footer Actions -->
    <div class="d-flex justify-content-end gap-3 pb-4">
      <button type="button" class="btn btn-light border px-4" onclick="loadView('publications', 'Publications')">Cancel</button>
      <button type="submit" id="draftBtn" class="btn btn-outline-danger px-4">Save as Draft</button>
      <button type="submit" id="publishBtn" class="btn px-4 text-white" style="background: var(--sapsri-red);">Publish</button>
    </div>

  </form>
</div>