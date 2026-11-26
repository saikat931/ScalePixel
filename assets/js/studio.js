/**
 * ScalePixel - Interactive Studio Engine
 * Handles Drag & Drop, Multi-file batch processing, Mode switching,
 * and Interactive Before/After Split Comparison Slider.
 */

(function () {
  // DOM Elements
  const dropZone = document.getElementById('drop-zone');
  const fileInput = document.getElementById('file-input');
  const modeTabs = document.querySelectorAll('.mode-tab');
  const qualitySlider = document.getElementById('quality-slider');
  const qualityVal = document.getElementById('quality-val');
  const formatSelect = document.getElementById('format-select');
  const resultsContainer = document.getElementById('results-container');
  const prefixInput = document.getElementById('prefix-input');

  let currentMode = 'auto'; // auto, avatar, showcase, convert, custom

  // Mode Tabs Switching
  modeTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      modeTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentMode = tab.dataset.mode;
      updateControlsForMode(currentMode);
    });
  });

  function updateControlsForMode(mode) {
    if (!qualitySlider || !formatSelect) return;

    if (mode === 'avatar') {
      formatSelect.value = 'jpeg';
      qualitySlider.value = 80;
      if (qualityVal) qualityVal.textContent = '80%';
      if (prefixInput) prefixInput.value = 'provider_19';
    } else if (mode === 'showcase') {
      formatSelect.value = 'webp';
      qualitySlider.value = 75;
      if (qualityVal) qualityVal.textContent = '75%';
      if (prefixInput) prefixInput.value = 'shop_19';
    } else if (mode === 'convert') {
      formatSelect.value = 'webp';
    } else if (mode === 'auto') {
      formatSelect.value = 'auto';
      qualitySlider.value = 80;
      if (qualityVal) qualityVal.textContent = '80%';
    }
  }

  // Quality Slider change
  if (qualitySlider && qualityVal) {
    qualitySlider.addEventListener('input', (e) => {
      qualityVal.textContent = `${e.target.value}%`;
    });
  }

  // Drag and Drop Event Handlers
  if (dropZone && fileInput) {
    dropZone.addEventListener('click', () => fileInput.click());

    ['dragenter', 'dragover'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
      });
    });

    dropZone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        handleFiles(files);
      }
    });

    fileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        handleFiles(e.target.files);
      }
    });
  }

  // Process files
  async function handleFiles(files) {
    for (let i = 0; i < files.length; i++) {
      await processSingleFile(files[i]);
    }
  }

  async function processSingleFile(file) {
    const cardId = 'res_' + Math.random().toString(36).substring(2, 9);

    // Initial loading card
    const card = document.createElement('div');
    card.className = 'result-card';
    card.id = cardId;
    card.innerHTML = `
      <div class="result-left">
        <div class="result-thumb" style="display:flex;align-items:center;justify-content:center;color:#00F2FE;">⚡</div>
        <div class="result-info">
          <div class="result-name">${escapeHtml(file.name)}</div>
          <div class="result-meta">Optimizing through ScalePixel Quantum Engine...</div>
        </div>
      </div>
      <div class="result-actions">
        <span style="font-size:12px;color:var(--cyan);font-weight:700;">Processing...</span>
      </div>
    `;

    resultsContainer.prepend(card);

    const formData = new FormData();
    formData.append('file', file);
    formData.append('mode', currentMode);

    if (qualitySlider) formData.append('quality', qualitySlider.value);
    if (formatSelect) {
      if (currentMode === 'convert') {
        formData.append('to', formatSelect.value);
      } else {
        formData.append('format', formatSelect.value);
      }
    }
    if (prefixInput && prefixInput.value.trim()) {
      formData.append('prefix', prefixInput.value.trim());
    }

    const endpoint = currentMode === 'convert' ? 'api/v1/convert.php' : 'api/v1/compress.php';

    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        body: formData,
      });

      const json = await res.json();

      if (json.status === 'success' && json.data) {
        const d = json.data;
        card.innerHTML = `
          <div class="result-left">
            <img src="${d.download_url}" class="result-thumb" alt="${escapeHtml(d.filename)}" />
            <div class="result-info">
              <div class="result-name">${escapeHtml(d.filename)}</div>
              <div class="result-meta">
                <span>${d.original_size_kb} KB ➔ <strong style="color:#FFF;">${d.compressed_size_kb} KB</strong></span>
                <span class="result-savings-badge">-${d.saved_percent}%</span>
                <span style="color:var(--text-muted);">${d.execution_time_ms}ms</span>
              </div>
            </div>
          </div>
          <div class="result-actions">
            <button class="btn-download" style="background:rgba(255,255,255,0.1);color:#FFF;border:none;cursor:pointer;" onclick="previewInComparison('${d.download_url}')">Compare</button>
            <a href="${d.download_url}" download="${d.filename}" class="btn-download">Download</a>
          </div>
        `;
      } else {
        card.innerHTML = `
          <div class="result-left">
            <div class="result-thumb" style="display:flex;align-items:center;justify-content:center;color:#FF5E62;">✕</div>
            <div class="result-info">
              <div class="result-name">${escapeHtml(file.name)}</div>
              <div class="result-meta" style="color:#FF5E62;">${escapeHtml(json.message || 'Compression failed')}</div>
            </div>
          </div>
        `;
      }
    } catch (err) {
      card.innerHTML = `
        <div class="result-left">
          <div class="result-thumb" style="display:flex;align-items:center;justify-content:center;color:#FF5E62;">✕</div>
          <div class="result-info">
            <div class="result-name">${escapeHtml(file.name)}</div>
            <div class="result-meta" style="color:#FF5E62;">Network error: ${escapeHtml(err.message)}</div>
          </div>
        </div>
      `;
    }
  }

  // Before / After Split Slider
  const compareBox = document.getElementById('compare-box');
  const compareAfter = document.getElementById('compare-after');
  const compareHandle = document.getElementById('compare-handle');

  if (compareBox && compareAfter && compareHandle) {
    let isDragging = false;

    function setSliderPosition(x) {
      const rect = compareBox.getBoundingClientRect();
      let pos = (x - rect.left) / rect.width;
      pos = Math.max(0.05, Math.min(0.95, pos));
      const percentage = pos * 100;

      compareAfter.style.width = percentage + '%';
      compareHandle.style.left = percentage + '%';
    }

    compareHandle.addEventListener('mousedown', () => (isDragging = true));
    window.addEventListener('mouseup', () => (isDragging = false));
    window.addEventListener('mousemove', (e) => {
      if (isDragging) setSliderPosition(e.clientX);
    });

    // Touch support
    compareHandle.addEventListener('touchstart', () => (isDragging = true));
    window.addEventListener('touchend', () => (isDragging = false));
    window.addEventListener('touchmove', (e) => {
      if (isDragging && e.touches[0]) setSliderPosition(e.touches[0].clientX);
    });
  }

  window.previewInComparison = function (imgUrl) {
    const afterImg = document.getElementById('after-img');
    if (afterImg) {
      afterImg.src = imgUrl;
      const compareBox = document.getElementById('compare-box');
      if (compareBox) {
        compareBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  };

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }
})();
