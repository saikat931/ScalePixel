/**
 * ScalePixel - Interactive Studio, Navigation & UI Engine
 * Handles accessible mobile drawer, toast feedback, clipboard copy,
 * mode tabs, custom range sliders, real XHR upload progress, and comparison slider.
 */

(function () {
  'use strict';

  /* -------------------------------------------------------------------------
   * 1. Toast Notification Helper
   * ---------------------------------------------------------------------- */
  const toastEl = document.getElementById('sp-toast');
  let toastTimer = null;

  function showToast(message, variant = 'success') {
    if (!toastEl) return;
    if (toastTimer) clearTimeout(toastTimer);

    const iconSvg =
      variant === 'error'
        ? '<svg class="sp-icon sp-state-error" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        : '<svg class="sp-icon sp-state-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>';

    toastEl.innerHTML = `${iconSvg}<span>${escapeHtml(message)}</span>`;
    toastEl.hidden = false;

    toastTimer = setTimeout(() => {
      toastEl.hidden = true;
    }, 3200);
  }

  window.spToast = showToast;

  /* -------------------------------------------------------------------------
   * 2. Accessible Mobile Sidebar Drawer
   * ---------------------------------------------------------------------- */
  const sidebar = document.getElementById('sp-sidebar');
  const scrim = document.getElementById('sp-sidebar-scrim');
  const openBtn = document.getElementById('sp-mobile-toggle');
  const closeBtn = document.getElementById('sp-sidebar-close');

  function isMobileViewport() {
    return window.matchMedia('(max-width: 767px)').matches;
  }

  function getFocusableInSidebar() {
    if (!sidebar) return [];
    return Array.from(
      sidebar.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')
    ).filter((el) => el.offsetParent !== null);
  }

  function openDrawer() {
    if (!sidebar || !scrim || !openBtn) return;
    sidebar.classList.add('is-open');
    scrim.hidden = false;
    openBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';

    const focusTarget = closeBtn || getFocusableInSidebar()[0];
    if (focusTarget) {
      setTimeout(() => focusTarget.focus({ preventScroll: true }), 20);
    }
  }

  function closeDrawer(restoreFocus = true) {
    if (!sidebar || !scrim || !openBtn) return;
    const wasOpen = sidebar.classList.contains('is-open');
    sidebar.classList.remove('is-open');
    scrim.hidden = true;
    openBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    if (wasOpen && restoreFocus) {
      openBtn.focus();
    }
  }

  if (openBtn && sidebar && scrim) {
    openBtn.addEventListener('click', () => {
      const expanded = openBtn.getAttribute('aria-expanded') === 'true';
      if (expanded) {
        closeDrawer(true);
      } else {
        openDrawer();
      }
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', () => closeDrawer(true));
    }

    scrim.addEventListener('click', () => closeDrawer(true));

    document.addEventListener('keydown', (e) => {
      if (!sidebar.classList.contains('is-open')) return;

      if (e.key === 'Escape') {
        e.preventDefault();
        closeDrawer(true);
        return;
      }

      if (e.key === 'Tab' && isMobileViewport()) {
        const focusables = getFocusableInSidebar();
        if (!focusables.length) return;
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    });

    window.addEventListener('resize', () => {
      if (!isMobileViewport() && sidebar.classList.contains('is-open')) {
        closeDrawer(false);
      }
    });
  }

  /* -------------------------------------------------------------------------
   * 3. Copy-to-Clipboard Buttons (API Keys & Docs)
   * ---------------------------------------------------------------------- */
  document.addEventListener('click', async (e) => {
    const copyBtn = e.target.closest('[data-copy], [data-copy-target]');
    if (!copyBtn) return;

    let textToCopy = copyBtn.getAttribute('data-copy') || '';
    const targetId = copyBtn.getAttribute('data-copy-target');
    if (targetId) {
      const targetEl = document.getElementById(targetId);
      if (targetEl) textToCopy = targetEl.textContent.trim();
    }

    if (!textToCopy) return;

    try {
      await navigator.clipboard.writeText(textToCopy);
      showToast('Copied to clipboard.');
    } catch {
      const textarea = document.createElement('textarea');
      textarea.value = textToCopy;
      textarea.setAttribute('readonly', '');
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
      showToast('Copied to clipboard.');
    }
  });

  /* -------------------------------------------------------------------------
   * 4. Studio Interactive Engine (runs on Studio page when elements exist)
   * ---------------------------------------------------------------------- */
  const dropZone = document.getElementById('drop-zone');
  const fileInput = document.getElementById('file-input');
  const browseBtn = document.getElementById('browse-btn');
  const modeTabs = Array.from(document.querySelectorAll('.sp-tab[data-mode]'));
  const qualitySlider = document.getElementById('quality-slider');
  const qualityVal = document.getElementById('quality-val');
  const formatSelect = document.getElementById('format-select');
  const prefixInput = document.getElementById('prefix-input');
  const includeSecondsCheck = document.getElementById('include-seconds');
  const modeHint = document.getElementById('mode-hint');
  const resultsContainer = document.getElementById('results-container');
  const queueEmpty = document.getElementById('queue-empty');
  const clearQueueBtn = document.getElementById('clear-queue-btn');

  const metricCount = document.getElementById('metric-count');
  const metricSaved = document.getElementById('metric-saved');
  const metricSpeed = document.getElementById('metric-speed');

  const compareEmpty = document.getElementById('compare-empty');
  const compareBox = document.getElementById('compare-box');
  const compareRange = document.getElementById('compare-range');
  const beforeImg = document.getElementById('before-img');
  const afterImg = document.getElementById('after-img');
  const compareBeforeMeta = document.getElementById('compare-before-meta');
  const compareAfterMeta = document.getElementById('compare-after-meta');

  if (!dropZone || !fileInput) return;

  let currentMode = 'auto';
  const sessionItems = [];
  const objectUrls = [];

  const MODE_DESCRIPTIONS = {
    auto: 'Auto Smart balances file size and clarity (WebP for PNG transparency, JPEG for photos).',
    avatar: 'Profile Avatar crops to a 600x600 1:1 square JPEG at 80% quality with timestamp naming.',
    showcase: 'Web Showcase encodes high-efficiency WebP up to 1920px at 75% quality.',
    convert: 'Format Converter transcodes directly to your selected output format via /api/v1/convert.',
    custom: 'Custom Parameters applies your exact format, quality, and prefix settings.',
  };

  function syncRangeProgress(slider) {
    if (!slider) return;
    const min = Number(slider.min || 0);
    const max = Number(slider.max || 100);
    const val = Number(slider.value || 0);
    const pct = max > min ? ((val - min) / (max - min)) * 100 : 0;
    slider.style.setProperty('--sp-range-progress', `${pct}%`);
  }

  function updateControlsForMode(mode) {
    if (modeHint && MODE_DESCRIPTIONS[mode]) {
      modeHint.textContent = MODE_DESCRIPTIONS[mode];
    }
    if (!qualitySlider || !formatSelect) return;

    if (mode === 'avatar') {
      formatSelect.value = 'jpeg';
      qualitySlider.value = '80';
      if (prefixInput && (!prefixInput.value.trim() || prefixInput.value === 'shop_19')) {
        prefixInput.value = 'provider_19';
      }
    } else if (mode === 'showcase') {
      formatSelect.value = 'webp';
      qualitySlider.value = '75';
      if (prefixInput && prefixInput.value === 'provider_19') {
        prefixInput.value = 'shop_19';
      }
    } else if (mode === 'convert') {
      if (formatSelect.value === 'auto') {
        formatSelect.value = 'webp';
      }
    } else if (mode === 'auto') {
      formatSelect.value = 'auto';
      qualitySlider.value = '80';
    }

    if (qualityVal) qualityVal.textContent = `${qualitySlider.value}%`;
    syncRangeProgress(qualitySlider);
  }

  function selectTab(tab) {
    modeTabs.forEach((t) => {
      const selected = t === tab;
      t.setAttribute('aria-selected', selected ? 'true' : 'false');
      t.setAttribute('tabindex', selected ? '0' : '-1');
    });
    currentMode = tab.dataset.mode || 'auto';
    updateControlsForMode(currentMode);
  }

  modeTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectTab(tab));

    tab.addEventListener('keydown', (e) => {
      let nextIndex = null;
      if (e.key === 'ArrowRight') nextIndex = (index + 1) % modeTabs.length;
      else if (e.key === 'ArrowLeft') nextIndex = (index - 1 + modeTabs.length) % modeTabs.length;
      else if (e.key === 'Home') nextIndex = 0;
      else if (e.key === 'End') nextIndex = modeTabs.length - 1;

      if (nextIndex !== null) {
        e.preventDefault();
        const nextTab = modeTabs[nextIndex];
        selectTab(nextTab);
        nextTab.focus();
      }
    });
  });

  if (qualitySlider) {
    syncRangeProgress(qualitySlider);
    qualitySlider.addEventListener('input', (e) => {
      if (qualityVal) qualityVal.textContent = `${e.target.value}%`;
      syncRangeProgress(e.target);
    });
  }

  // Dropzone events (keyboard + click + drag & drop)
  dropZone.addEventListener('click', (e) => {
    if (e.target.closest('#browse-btn')) return;
    fileInput.click();
  });

  if (browseBtn) {
    browseBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fileInput.click();
    });
  }

  dropZone.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      fileInput.click();
    }
  });

  ['dragenter', 'dragover'].forEach((evt) => {
    dropZone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropZone.classList.add('is-dragover');
    });
  });

  ['dragleave', 'drop'].forEach((evt) => {
    dropZone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropZone.classList.remove('is-dragover');
    });
  });

  dropZone.addEventListener('drop', (e) => {
    const files = e.dataTransfer ? e.dataTransfer.files : null;
    if (files && files.length > 0) {
      handleFiles(files);
    }
  });

  fileInput.addEventListener('change', (e) => {
    if (e.target.files && e.target.files.length > 0) {
      handleFiles(e.target.files);
      fileInput.value = '';
    }
  });

  if (clearQueueBtn) {
    clearQueueBtn.addEventListener('click', () => {
      sessionItems.length = 0;
      while (objectUrls.length) {
        URL.revokeObjectURL(objectUrls.pop());
      }
      if (resultsContainer) resultsContainer.innerHTML = '';
      if (queueEmpty) queueEmpty.hidden = false;
      clearQueueBtn.disabled = true;
      updateSessionMetrics();
      if (compareBox && compareEmpty) {
        compareBox.hidden = true;
        compareEmpty.hidden = false;
      }
      showToast('Compression queue cleared.');
    });
  }

  async function handleFiles(files) {
    if (queueEmpty) queueEmpty.hidden = true;
    if (clearQueueBtn) clearQueueBtn.disabled = false;

    for (let i = 0; i < files.length; i++) {
      await processSingleFile(files[i]);
    }
  }

  function updateSessionMetrics() {
    const completed = sessionItems.filter((item) => item.status === 'done');
    if (metricCount) metricCount.textContent = String(completed.length);

    if (completed.length === 0) {
      if (metricSaved) metricSaved.textContent = '—';
      if (metricSpeed) metricSpeed.textContent = '—';
      return;
    }

    const totalOriginalKb = completed.reduce((sum, item) => sum + item.originalKb, 0);
    const totalOutputKb = completed.reduce((sum, item) => sum + item.outputKb, 0);
    const totalSavedKb = Math.max(0, totalOriginalKb - totalOutputKb);
    const pctSaved = totalOriginalKb > 0 ? ((totalSavedKb / totalOriginalKb) * 100).toFixed(1) : '0.0';
    const avgMs = completed.reduce((sum, item) => sum + item.execMs, 0) / completed.length;

    if (metricSaved) {
      const savedFormatted = totalSavedKb >= 1024 ? `${(totalSavedKb / 1024).toFixed(2)} MB` : `${totalSavedKb.toFixed(1)} KB`;
      metricSaved.textContent = `${savedFormatted} (-${pctSaved}%)`;
    }
    if (metricSpeed) {
      metricSpeed.textContent = `${Math.round(avgMs)} ms`;
    }
  }

  function processSingleFile(file) {
    return new Promise((resolve) => {
      const itemId = 'sp_item_' + Math.random().toString(36).slice(2, 9);
      const originalPreviewUrl = URL.createObjectURL(file);
      objectUrls.push(originalPreviewUrl);

      const originalKbEstimate = Math.max(0.1, file.size / 1024);

      const rowWrap = document.createElement('div');
      rowWrap.className = 'sp-queue-item';
      rowWrap.id = itemId;
      rowWrap.innerHTML = `
        <div class="sp-queue-row">
          <div class="sp-file">
            <img src="${originalPreviewUrl}" class="sp-file-thumb" alt="${escapeHtml(file.name)}">
            <div class="sp-file-details">
              <div class="sp-file-name">${escapeHtml(file.name)}</div>
              <div class="sp-file-size">${originalKbEstimate.toFixed(1)} KB &bull; Uploading...</div>
            </div>
          </div>
          <progress class="sp-progress" value="8" max="100" aria-label="Progress for ${escapeHtml(file.name)}"></progress>
          <span class="sp-queue-percent">8%</span>
          <span class="sp-queue-status" aria-label="Processing"><span class="sp-spinner"></span></span>
        </div>
      `;

      if (resultsContainer) {
        resultsContainer.prepend(rowWrap);
      }

      const progressEl = rowWrap.querySelector('.sp-progress');
      const percentEl = rowWrap.querySelector('.sp-queue-percent');
      const statusEl = rowWrap.querySelector('.sp-queue-status');
      const sizeEl = rowWrap.querySelector('.sp-file-size');

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
      if (includeSecondsCheck) {
        formData.append('include_seconds', includeSecondsCheck.checked ? '1' : '0');
      }

      const endpoint = currentMode === 'convert' ? 'api/v1/convert.php' : 'api/v1/compress.php';

      const xhr = new XMLHttpRequest();
      xhr.open('POST', endpoint, true);

      xhr.upload.addEventListener('progress', (evt) => {
        if (!evt.lengthComputable) return;
        const uploadPct = Math.min(85, Math.max(10, Math.round((evt.loaded / evt.total) * 85)));
        if (progressEl) progressEl.value = uploadPct;
        if (percentEl) percentEl.textContent = `${uploadPct}%`;
        if (uploadPct >= 85 && sizeEl) {
          sizeEl.textContent = `${originalKbEstimate.toFixed(1)} KB • Optimizing...`;
        }
      });

      xhr.addEventListener('load', () => {
        let json = null;
        try {
          json = JSON.parse(xhr.responseText);
        } catch {
          json = null;
        }

        if (xhr.status >= 200 && xhr.status < 300 && json && json.status === 'success' && json.data) {
          const d = json.data;
          const outputKb = Number(d.compressed_size_kb ?? d.converted_size_kb ?? 0);
          const originalKb = Number(d.original_size_kb ?? originalKbEstimate);
          const savedPct = Number(d.saved_percent ?? 0);
          const execMs = Number(d.execution_time_ms ?? 0);

          if (progressEl) progressEl.value = 100;
          if (percentEl) percentEl.textContent = '100%';
          if (statusEl) {
            statusEl.innerHTML =
              '<svg class="sp-icon sp-state-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>';
            statusEl.setAttribute('aria-label', 'Completed');
          }

          const thumbImg = rowWrap.querySelector('.sp-file-thumb');
          if (thumbImg) {
            thumbImg.src = d.download_url;
            thumbImg.alt = d.filename;
          }

          const nameEl = rowWrap.querySelector('.sp-file-name');
          if (nameEl) nameEl.textContent = d.filename;

          if (sizeEl) {
            const signLabel = savedPct >= 0 ? `-${savedPct}%` : `+${Math.abs(savedPct)}%`;
            sizeEl.innerHTML = `${originalKb} KB &rarr; <strong>${outputKb} KB</strong> <span class="sp-state-success">(${signLabel})</span> &bull; ${execMs} ms`;
          }

          const actionsDiv = document.createElement('div');
          actionsDiv.className = 'sp-queue-actions';
          const dimsText = d.new_dimensions ? `${d.original_dimensions} → ${d.new_dimensions}` : (d.target_format ? d.target_format.toUpperCase() : '');
          actionsDiv.innerHTML = `
            <span class="sp-helper">${escapeHtml(dimsText)}</span>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
              <button type="button" class="sp-btn sp-btn-sm js-compare-btn">Compare</button>
              <a href="${escapeHtml(d.direct_download_url || d.download_url)}" download="${escapeHtml(d.filename)}" class="sp-btn sp-btn-primary sp-btn-sm">Download</a>
            </div>
          `;
          rowWrap.appendChild(actionsDiv);

          const comparePayload = {
            beforeUrl: originalPreviewUrl,
            afterUrl: d.download_url,
            beforeMeta: `${originalKb} KB${d.original_dimensions ? ' • ' + d.original_dimensions : ''}`,
            afterMeta: `${outputKb} KB (${savedPct >= 0 ? '-' + savedPct : '+' + Math.abs(savedPct)}%)${d.new_dimensions ? ' • ' + d.new_dimensions : ''}`,
          };

          const compareBtn = actionsDiv.querySelector('.js-compare-btn');
          if (compareBtn) {
            compareBtn.addEventListener('click', () => loadComparison(comparePayload, true));
          }

          sessionItems.push({
            status: 'done',
            originalKb,
            outputKb,
            execMs,
          });
          updateSessionMetrics();
          loadComparison(comparePayload, false);
        } else {
          const errorMsg = (json && json.message) || `Request failed (${xhr.status})`;
          markItemError(rowWrap, progressEl, percentEl, statusEl, sizeEl, errorMsg);
          sessionItems.push({ status: 'error', originalKb: originalKbEstimate, outputKb: 0, execMs: 0 });
          updateSessionMetrics();
        }
        resolve();
      });

      xhr.addEventListener('error', () => {
        markItemError(rowWrap, progressEl, percentEl, statusEl, sizeEl, 'Network error while uploading file.');
        sessionItems.push({ status: 'error', originalKb: originalKbEstimate, outputKb: 0, execMs: 0 });
        updateSessionMetrics();
        resolve();
      });

      xhr.send(formData);
    });
  }

  function markItemError(rowWrap, progressEl, percentEl, statusEl, sizeEl, message) {
    if (progressEl) {
      progressEl.value = 100;
      progressEl.classList.add('is-error');
    }
    if (percentEl) {
      percentEl.textContent = 'Err';
      percentEl.classList.add('sp-state-error');
    }
    if (statusEl) {
      statusEl.innerHTML =
        '<svg class="sp-icon sp-state-error" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
      statusEl.setAttribute('aria-label', 'Error');
    }
    if (sizeEl) {
      sizeEl.className = 'sp-file-size sp-state-error';
      sizeEl.textContent = message;
    }
  }

  function loadComparison(payload, shouldScroll) {
    if (!compareBox || !beforeImg || !afterImg) return;
    if (compareEmpty) compareEmpty.hidden = true;
    compareBox.hidden = false;

    beforeImg.src = payload.beforeUrl;
    afterImg.src = payload.afterUrl;

    if (compareBeforeMeta) compareBeforeMeta.textContent = payload.beforeMeta;
    if (compareAfterMeta) compareAfterMeta.textContent = payload.afterMeta;

    if (compareRange) {
      compareRange.value = '50';
      compareBox.style.setProperty('--sp-split', '50%');
    }

    if (shouldScroll) {
      compareBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  if (compareRange && compareBox) {
    compareRange.addEventListener('input', (e) => {
      compareBox.style.setProperty('--sp-split', `${e.target.value}%`);
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
  }
})();
