<!-- WORDPRESS-STYLE MEDIA LIBRARY MODAL COMPONENT -->
<div id="wpMediaModal" class="wp-media-modal-backdrop" style="display: none;">
  <div class="wp-media-modal-dialog">
    
    <!-- Modal Header -->
    <div class="wp-media-modal-header">
      <div class="wp-media-modal-title">
        <i class="fas fa-photo-video text-primary mr-2"></i> Media Library
      </div>
      <div class="wp-media-modal-nav">
        <button type="button" class="wp-media-tab-btn" id="tabUploadBtn" onclick="switchMediaTab('upload')">
          <i class="fas fa-upload mr-1"></i> Upload files
        </button>
        <button type="button" class="wp-media-tab-btn active" id="tabLibraryBtn" onclick="switchMediaTab('library')">
          <i class="fas fa-images mr-1"></i> Media Library
        </button>
      </div>
      <button type="button" class="wp-media-modal-close" onclick="closeMediaModal()">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="wp-media-modal-body">

      <!-- TAB 1: UPLOAD FILES -->
      <div id="mediaUploadTab" class="wp-media-tab-content" style="display: none;">
        <div class="wp-upload-dropzone" id="wpDropzone">
          <div class="wp-upload-dropzone-inner">
            <div class="wp-upload-icon-wrapper mb-3">
              <i class="fas fa-cloud-upload-alt text-primary fa-4x"></i>
            </div>
            <h4 class="font-weight-bold text-dark">Drop files anywhere to upload</h4>
            <p class="text-muted">or</p>
            <label for="wpFileInput" class="btn btn-primary btn-lg px-4 shadow-sm" style="cursor: pointer;">
              <i class="fas fa-folder-open mr-2"></i> Select Files
            </label>
            <input type="file" id="wpFileInput" multiple accept="image/*" style="display: none;" onchange="handleMediaFilesSelect(this.files)">
            <p class="text-muted small mt-3 mb-0">Maximum upload file size: 20 MB. Supported formats: JPG, PNG, WEBP, GIF, SVG.</p>
          </div>
        </div>

        <!-- UPLOADING PROGRESS BAR SECTION -->
        <div id="wpUploadProgressContainer" class="mt-4 p-4 border rounded bg-white shadow-sm" style="display: none;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold text-dark" id="wpUploadStatusText"><i class="fas fa-spinner fa-spin text-primary mr-2"></i> Uploading file...</span>
            <span class="badge badge-primary font-weight-bold text-white px-2 py-1" id="wpUploadPercentText">0%</span>
          </div>
          <div class="progress" style="height: 18px; border-radius: 9px; background-color: #e9ecef;">
            <div id="wpProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%; font-weight: bold; line-height: 18px; transition: width 0.2s ease;">0%</div>
          </div>
        </div>
      </div>

      <!-- TAB 2: MEDIA LIBRARY GRID & DETAILS -->
      <div id="mediaLibraryTab" class="wp-media-tab-content active d-flex h-100">
        
        <!-- Left Side: Media Grid -->
        <div class="wp-media-grid-container flex-grow-1 p-3 d-flex flex-column" style="overflow-y: auto;">
          
          <!-- Search & Filter Toolbar -->
          <div class="wp-media-toolbar d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center">
              <div class="input-group input-group-sm mr-2" style="width: 260px;">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                </div>
                <input type="text" id="wpMediaSearchInput" class="form-control border-left-0" placeholder="Search media items..." onkeyup="filterMediaItems()">
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadMediaLibrary()" title="Refresh Media Library">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
            <div class="text-muted small">
              Showing <span id="wpMediaCountBadge" class="font-weight-bold text-dark">0</span> items
            </div>
          </div>

          <!-- Media Thumbnails Grid -->
          <div id="wpMediaGrid" class="wp-media-grid flex-grow-1">
            <div class="text-center py-5 text-muted w-100" id="wpMediaLoadingSpinner">
              <i class="fas fa-circle-notch fa-spin fa-2x text-primary mb-2"></i>
              <p>Loading media library...</p>
            </div>
          </div>

        </div>

        <!-- Right Side: Attachment Details Sidebar -->
        <div class="wp-media-sidebar border-left bg-light p-3" id="wpMediaSidebar">
          <h6 class="font-weight-bold text-uppercase text-secondary mb-3 pb-2 border-bottom" style="letter-spacing: 0.5px;">
            Attachment Details
          </h6>
          
          <!-- Placeholder when no image is selected -->
          <div id="wpSidebarEmptyState" class="text-center text-muted py-5">
            <i class="far fa-image fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
            <p class="small mb-0">Select an image from the grid to view details and options.</p>
          </div>

          <!-- Active Selected Image Details Container -->
          <div id="wpSidebarDetails" style="display: none;">
            <div class="wp-sidebar-preview mb-3 text-center p-2 bg-white rounded border shadow-sm">
              <img id="wpSidebarImgPreview" src="" alt="Selected Preview" class="img-fluid rounded" style="max-height: 160px; object-fit: contain;">
            </div>

            <div class="wp-sidebar-meta small mb-3 text-dark">
              <div class="font-weight-bold text-truncate" id="wpSidebarFilename" title="">filename.jpg</div>
              <div class="text-muted" id="wpSidebarDate">Date</div>
              <div class="text-muted" id="wpSidebarSize">Size</div>
              <div class="text-muted" id="wpSidebarDimensions">Dimensions</div>
            </div>

            <hr class="my-2">

            <!-- URL Input Box with Copy Button -->
            <div class="form-group mb-2">
              <label class="small font-weight-bold text-secondary mb-1">File URL:</label>
              <div class="input-group input-group-sm">
                <input type="text" id="wpSidebarUrlInput" class="form-control form-control-sm bg-white" readonly>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyMediaUrlToClipboard()">
                    <i class="far fa-copy"></i> Copy
                  </button>
                </div>
              </div>
            </div>

            <!-- Alt Text / Description -->
            <div class="form-group mb-2">
              <label class="small font-weight-bold text-secondary mb-1">Alt Text / Description:</label>
              <input type="text" id="wpSidebarAltInput" class="form-control form-control-sm" placeholder="Image description for accessibility">
            </div>

            <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
              <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="deleteSelectedMediaItem()">
                <i class="far fa-trash-alt mr-1"></i> Delete Permanently
              </button>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Modal Footer -->
    <div class="wp-media-modal-footer">
      <div class="wp-media-selection-info" id="wpSelectionInfo">
        <span class="text-muted small">No items selected</span>
      </div>
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="closeMediaModal()">Cancel</button>
        <button type="button" class="btn btn-primary font-weight-bold" id="wpSelectBtn" disabled onclick="confirmMediaSelection()">
          <i class="fas fa-check mr-1"></i> Use Selected Media
        </button>
      </div>
    </div>

  </div>
</div>

<!-- WORDPRESS MEDIA MODAL STYLES -->
<style>
.wp-media-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.75);
  z-index: 10500;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.wp-media-modal-dialog {
  background: #ffffff;
  width: 92vw;
  height: 88vh;
  max-width: 1250px;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 15px 35px rgba(0,0,0,0.3);
  overflow: hidden;
}
.wp-media-modal-header {
  height: 54px;
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}
.wp-media-modal-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #2c3e50;
}
.wp-media-modal-nav {
  display: flex;
  gap: 8px;
}
.wp-media-tab-btn {
  background: transparent;
  border: 1px solid transparent;
  border-bottom: none;
  padding: 8px 16px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #6c757d;
  border-radius: 6px 6px 0 0;
  cursor: pointer;
  transition: all 0.2s ease;
}
.wp-media-tab-btn:hover {
  color: #007bff;
}
.wp-media-tab-btn.active {
  background: #ffffff;
  color: #007bff;
  border-color: #dee2e6 #dee2e6 #ffffff #dee2e6;
  font-weight: 700;
}
.wp-media-modal-close {
  background: none;
  border: none;
  font-size: 1.8rem;
  line-height: 1;
  color: #6c757d;
  cursor: pointer;
}
.wp-media-modal-close:hover {
  color: #dc3545;
}
.wp-media-modal-body {
  flex: 1;
  overflow: hidden;
  position: relative;
  background: #fdfdfd;
}
.wp-media-tab-content {
  width: 100%;
  height: 100%;
}
.wp-upload-dropzone {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}
.wp-upload-dropzone-inner {
  border: 3px dashed #007bff;
  border-radius: 12px;
  padding: 50px 40px;
  text-align: center;
  width: 100%;
  max-width: 650px;
  background-color: #f8faff;
  transition: all 0.2s ease;
}
.wp-upload-dropzone-inner.drag-over {
  background-color: #e6f0ff;
  border-color: #28a745;
  transform: scale(1.02);
}
.wp-media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  grid-gap: 12px;
}
.wp-media-thumb {
  position: relative;
  aspect-ratio: 1 / 1;
  border-radius: 6px;
  border: 2px solid #e9ecef;
  background: #f8f9fa;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.15s ease-in-out;
}
.wp-media-thumb:hover {
  border-color: #007bff;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.wp-media-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.wp-media-thumb.selected {
  border: 3px solid #007bff;
  box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}
.wp-media-thumb.selected::after {
  content: "✓";
  position: absolute;
  top: 4px;
  right: 4px;
  background: #007bff;
  color: #fff;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}
.wp-media-sidebar {
  width: 320px;
  min-width: 320px;
  overflow-y: auto;
}
.wp-media-modal-footer {
  height: 60px;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}
</style>

<!-- WORDPRESS MEDIA MODAL JAVASCRIPT LOGIC -->
<script>
let wpMediaState = {
  mode: 'single', // 'single' or 'multiple'
  selectedItems: [], // array of media items
  allMedia: [],
  onConfirmCallback: null
};

// Open Modal Function
function openMediaModal(options = {}) {
  wpMediaState.mode = options.mode || 'single';
  wpMediaState.selectedItems = options.preselected ? [...options.preselected] : [];
  wpMediaState.onConfirmCallback = options.onSelect || null;

  // Set action button text
  const btnText = wpMediaState.mode === 'multiple' ? 'Add to Gallery' : 'Set Main Image';
  document.getElementById('wpSelectBtn').innerHTML = `<i class="fas fa-check mr-1"></i> ${btnText}`;

  document.getElementById('wpMediaModal').style.display = 'flex';
  switchMediaTab('library');
  loadMediaLibrary();
}

// Close Modal Function
function closeMediaModal() {
  document.getElementById('wpMediaModal').style.display = 'none';
  resetMediaUploadProgress();
}

// Switch Tabs
function switchMediaTab(tab) {
  const uploadBtn = document.getElementById('tabUploadBtn');
  const libraryBtn = document.getElementById('tabLibraryBtn');
  const uploadTab = document.getElementById('mediaUploadTab');
  const libraryTab = document.getElementById('mediaLibraryTab');

  if (tab === 'upload') {
    uploadBtn.classList.add('active');
    libraryBtn.classList.remove('active');
    uploadTab.style.display = 'block';
    libraryTab.style.display = 'none';
  } else {
    libraryBtn.classList.add('active');
    uploadBtn.classList.remove('active');
    libraryTab.style.display = 'flex';
    uploadTab.style.display = 'none';
  }
}

// Fetch Media Library from Server
function loadMediaLibrary() {
  const grid = document.getElementById('wpMediaGrid');
  grid.innerHTML = `
    <div class="text-center py-5 text-muted w-100">
      <i class="fas fa-circle-notch fa-spin fa-2x text-primary mb-2"></i>
      <p>Loading media library...</p>
    </div>
  `;

  fetch('/admin/media/library')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        wpMediaState.allMedia = data.media || [];
        renderMediaGrid(wpMediaState.allMedia);
      } else {
        grid.innerHTML = `<div class="alert alert-danger w-100">Failed to load media items.</div>`;
      }
    })
    .catch(err => {
      console.error(err);
      grid.innerHTML = `<div class="alert alert-danger w-100">Error connecting to server.</div>`;
    });
}

// Render Grid Thumbnails
function renderMediaGrid(items) {
  const grid = document.getElementById('wpMediaGrid');
  const countBadge = document.getElementById('wpMediaCountBadge');
  countBadge.textContent = items.length;

  if (items.length === 0) {
    grid.innerHTML = `
      <div class="text-center py-5 text-muted w-100" style="grid-column: 1 / -1;">
        <i class="far fa-folder-open fa-3x mb-2 text-secondary"></i>
        <p>No media files found. Upload some images to get started!</p>
      </div>
    `;
    updateSidebarDetails(null);
    return;
  }

  let html = '';
  items.forEach(item => {
    const isSelected = wpMediaState.selectedItems.some(s => s.url === item.url || s.relative_path === item.relative_path);
    const selectedClass = isSelected ? 'selected' : '';

    html += `
      <div class="wp-media-thumb ${selectedClass}" 
           data-id="${item.id}"
           onclick="handleMediaItemClick('${item.id}')">
        <img src="${item.url}" alt="${item.filename}" loading="lazy">
      </div>
    `;
  });

  grid.innerHTML = html;

  // If items selected, show sidebar details of last selected item
  if (wpMediaState.selectedItems.length > 0) {
    const lastItem = wpMediaState.selectedItems[wpMediaState.selectedItems.length - 1];
    updateSidebarDetails(lastItem);
  } else {
    updateSidebarDetails(null);
  }

  updateSelectionFooter();
}

// Filter Grid Items via Search Box
function filterMediaItems() {
  const query = document.getElementById('wpMediaSearchInput').value.toLowerCase().trim();
  if (!query) {
    renderMediaGrid(wpMediaState.allMedia);
    return;
  }
  const filtered = wpMediaState.allMedia.filter(item => 
    (item.filename && item.filename.toLowerCase().includes(query)) ||
    (item.relative_path && item.relative_path.toLowerCase().includes(query))
  );
  renderMediaGrid(filtered);
}

// Handle Click on Thumbnails
function handleMediaItemClick(itemId) {
  const item = wpMediaState.allMedia.find(m => m.id === itemId);
  if (!item) return;

  if (wpMediaState.mode === 'single') {
    wpMediaState.selectedItems = [item];
  } else {
    // Multiple Mode Toggle
    const index = wpMediaState.selectedItems.findIndex(s => s.url === item.url || s.relative_path === item.relative_path);
    if (index > -1) {
      wpMediaState.selectedItems.splice(index, 1);
    } else {
      wpMediaState.selectedItems.push(item);
    }
  }

  // Update DOM classes
  const thumbs = document.querySelectorAll('.wp-media-thumb');
  thumbs.forEach(t => {
    const tid = t.getAttribute('data-id');
    const isSel = wpMediaState.selectedItems.some(s => s.id === tid);
    if (isSel) {
      t.classList.add('selected');
    } else {
      t.classList.remove('selected');
    }
  });

  updateSidebarDetails(item);
  updateSelectionFooter();
}

// Update Right Sidebar Details
function updateSidebarDetails(item) {
  const emptyState = document.getElementById('wpSidebarEmptyState');
  const details = document.getElementById('wpSidebarDetails');

  if (!item) {
    emptyState.style.display = 'block';
    details.style.display = 'none';
    return;
  }

  emptyState.style.display = 'none';
  details.style.display = 'block';

  document.getElementById('wpSidebarImgPreview').src = item.url;
  document.getElementById('wpSidebarFilename').textContent = item.filename;
  document.getElementById('wpSidebarFilename').title = item.filename;
  document.getElementById('wpSidebarDate').textContent = 'Uploaded on: ' + item.date;
  document.getElementById('wpSidebarSize').textContent = 'File size: ' + item.size;
  document.getElementById('wpSidebarDimensions').textContent = 'Dimensions: ' + item.dimensions;
  document.getElementById('wpSidebarUrlInput').value = item.url;
  document.getElementById('wpSidebarAltInput').value = item.filename;
}

// Copy URL to Clipboard
function copyMediaUrlToClipboard() {
  const input = document.getElementById('wpSidebarUrlInput');
  input.select();
  document.execCommand('copy');
  alert('Media URL copied to clipboard!');
}

// Update Footer Selection Info & Enable/Disable Select Button
function updateSelectionFooter() {
  const info = document.getElementById('wpSelectionInfo');
  const btn = document.getElementById('wpSelectBtn');
  const count = wpMediaState.selectedItems.length;

  if (count === 0) {
    info.innerHTML = `<span class="text-muted small">No items selected</span>`;
    btn.disabled = true;
  } else {
    info.innerHTML = `
      <span class="font-weight-bold text-dark small">${count} item${count > 1 ? 's' : ''} selected</span>
      <a href="javascript:void(0)" class="text-danger small ml-2" onclick="clearMediaSelection()">Clear</a>
    `;
    btn.disabled = false;
  }
}

// Clear Selection
function clearMediaSelection() {
  wpMediaState.selectedItems = [];
  document.querySelectorAll('.wp-media-thumb').forEach(t => t.classList.remove('selected'));
  updateSidebarDetails(null);
  updateSelectionFooter();
}

// Delete Selected Media Item
function deleteSelectedMediaItem() {
  if (wpMediaState.selectedItems.length === 0) return;
  const item = wpMediaState.selectedItems[wpMediaState.selectedItems.length - 1];

  if (!confirm(`Are you sure you want to permanently delete "${item.filename}" from the server?`)) {
    return;
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
  const formData = new FormData();
  formData.append('relative_path', item.relative_path);
  formData.append('_token', csrfToken);

  fetch('/admin/media/delete', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      wpMediaState.selectedItems = wpMediaState.selectedItems.filter(s => s.id !== item.id);
      loadMediaLibrary();
    } else {
      alert(data.message || 'Error deleting media item.');
    }
  })
  .catch(err => {
    console.error(err);
    alert('Failed to connect to server.');
  });
}

// Confirm Media Selection Action
function confirmMediaSelection() {
  if (wpMediaState.selectedItems.length === 0) return;

  if (typeof wpMediaState.onConfirmCallback === 'function') {
    wpMediaState.onConfirmCallback(wpMediaState.selectedItems);
  }
  closeMediaModal();
}

// Drag & Drop Upload Handlers
document.addEventListener('DOMContentLoaded', () => {
  const dropzone = document.getElementById('wpDropzone');
  if (!dropzone) return;

  ['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropzone.querySelector('.wp-upload-dropzone-inner').classList.add('drag-over');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropzone.querySelector('.wp-upload-dropzone-inner').classList.remove('drag-over');
    }, false);
  });

  dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files && files.length > 0) {
      handleMediaFilesSelect(files);
    }
  });
});

// Upload Files via AJAX with Live Progress Bar
function handleMediaFilesSelect(files) {
  if (!files || files.length === 0) return;

  const progressContainer = document.getElementById('wpUploadProgressContainer');
  const progressBar = document.getElementById('wpProgressBar');
  const percentText = document.getElementById('wpUploadPercentText');
  const statusText = document.getElementById('wpUploadStatusText');

  progressContainer.style.display = 'block';

  let totalFiles = files.length;
  let uploadedCount = 0;
  let newlyUploadedItems = [];

  const uploadFileIndex = (index) => {
    if (index >= totalFiles) {
      statusText.innerHTML = `<i class="fas fa-check-circle text-success mr-2"></i> All ${totalFiles} file(s) uploaded successfully!`;
      setTimeout(() => {
        progressContainer.style.display = 'none';
        resetMediaUploadProgress();
        switchMediaTab('library');
        // Preselect newly uploaded item(s)
        if (newlyUploadedItems.length > 0) {
          if (wpMediaState.mode === 'single') {
            wpMediaState.selectedItems = [newlyUploadedItems[newlyUploadedItems.length - 1]];
          } else {
            wpMediaState.selectedItems = [...wpMediaState.selectedItems, ...newlyUploadedItems];
          }
        }
        loadMediaLibrary();
      }, 1000);
      return;
    }

    const file = files[index];
    statusText.innerHTML = `<i class="fas fa-spinner fa-spin text-primary mr-2"></i> Uploading ${file.name} (${index + 1} of ${totalFiles})...`;

    const formData = new FormData();
    formData.append('file', file);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    formData.append('_token', csrfToken);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/media/upload', true);
    if (csrfToken) {
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    }

    xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percentComplete = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = percentComplete + '%';
        progressBar.textContent = percentComplete + '%';
        percentText.textContent = percentComplete + '%';
      }
    };

    xhr.onload = () => {
      if (xhr.status === 200) {
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.status === 'success' && res.media) {
            newlyUploadedItems.push(res.media);
          } else {
            alert(`Error uploading ${file.name}: ${res.message || 'Unknown error'}`);
          }
        } catch(err) {
          console.error(err);
          alert(`Error uploading ${file.name}`);
        }
      } else {
        let errDesc = `Error uploading ${file.name}`;
        try {
          const errRes = JSON.parse(xhr.responseText);
          if (errRes.message) {
            errDesc += `: ${errRes.message}`;
          }
        } catch(e) {}
        alert(errDesc);
      }
      uploadedCount++;
      uploadFileIndex(index + 1);
    };

    xhr.onerror = () => {
      alert(`Network error while uploading ${file.name}`);
      uploadFileIndex(index + 1);
    };

    xhr.send(formData);
  };

  uploadFileIndex(0);
}

function resetMediaUploadProgress() {
  const progressContainer = document.getElementById('wpUploadProgressContainer');
  const progressBar = document.getElementById('wpProgressBar');
  const percentText = document.getElementById('wpUploadPercentText');
  if (progressContainer) progressContainer.style.display = 'none';
  if (progressBar) {
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
  }
  if (percentText) percentText.textContent = '0%';
}
</script>
