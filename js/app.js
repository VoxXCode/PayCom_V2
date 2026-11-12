// Main Application Controller untuk Platform Perakit PC (PayCom)
// Mengimplementasikan seluruh Functional Requirements (FR1 - FR5) dan Design Brief

import { HARDWARE_DATABASE, COMPONENT_CATEGORIES, COMPONENT_IMAGES, PREBUILT_TEMPLATES } from "./data/components.js";
import { filterAvailableComponents, validateBuild } from "./engine/compatibility.js";
import { calculateWattage } from "./engine/wattage.js";
import {
  calculateTotalPrice,
  getComponentPriceInfo,
  setApiStatus,
  getApiStatus,
  setManualPrice,
  clearManualPrice,
  formatRupiah
} from "./engine/pricing.js";
import { generateBuildText, copyBuildToClipboard, printBuild } from "./engine/exporter.js";
import { ICONS } from "./icons.js";

// State Aplikasi
const state = {
  build: {
    cpu: null,
    motherboard: null,
    ram: null,
    gpu: null,
    storage: null,
    psu: null,
    case: null
  },
  modal: {
    isOpen: false,
    currentCategory: null,
    searchQuery: "",
    isCustomFormVisible: false
  },
  toastMessage: null,
  toastTimeout: null
};

// Mengambil katalog aktif (MySQL via PHP jika tersedia, atau fallback data statis)
function getActiveDatabase() {
  const dbSource = typeof window !== "undefined" && window.__PAYCOM_DB_COMPONENTS__
    ? window.__PAYCOM_DB_COMPONENTS__
    : HARDWARE_DATABASE;

  const normalized = {};
  Object.keys(dbSource).forEach((cat) => {
    normalized[cat] = (dbSource[cat] || []).map((item) => ({
      ...item,
      image: item.image || COMPONENT_IMAGES[cat] || null
    }));
  });
  return normalized;
}

// Inisialisasi DOM setelah halaman selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
  initApp();
});

function initApp() {
  hydratePreloadedBuild();
  renderComponentSlots();
  updateSummary();
  setupEventListeners();
  setupKeyboardAccessibility();
}

function hydratePreloadedBuild() {
  if (typeof window === "undefined" || !window.__PAYCOM_PRELOADED_BUILD__) {
    return;
  }

  const loaded = window.__PAYCOM_PRELOADED_BUILD__;
  const db = getActiveDatabase();

  if (loaded.items) {
    Object.entries(loaded.items).forEach(([cat, item]) => {
      const matched = (db[cat] || []).find((c) => c.id === item.slug || String(c.db_id) === String(item.component_id));
      if (matched) {
        state.build[cat] = { ...matched };
        if (item.custom_price !== null && item.custom_price !== undefined) {
          state.build[cat].manual_price = parseInt(item.custom_price, 10) || 0;
          setManualPrice(cat, state.build[cat].manual_price);
        }
      }
    });
  }

  const nameInput = document.getElementById("builder-save-name");
  const authorInput = document.getElementById("builder-save-author");
  const purposeSelect = document.getElementById("builder-save-purpose");
  const notesInput = document.getElementById("builder-save-notes");

  if (nameInput && !nameInput.value) nameInput.value = `${loaded.build_name || ""} (Salinan)`;
  if (authorInput && !authorInput.value) authorInput.value = loaded.builder_name || "";
  if (purposeSelect && loaded.purpose) purposeSelect.value = loaded.purpose;
  if (notesInput && !notesInput.value) notesInput.value = loaded.notes || "";
}

// ----------------------------------------------------
// Rendering Component Rows (Area Kiri - 8 Kolom)
// ----------------------------------------------------
function renderComponentSlots() {
  const container = document.getElementById("component-slots-container");
  if (!container) return;

  container.innerHTML = "";

  COMPONENT_CATEGORIES.forEach((cat, index) => {
    const selectedItem = state.build[cat.id];
    const isMoboLocked = cat.id === "motherboard" && !state.build.cpu;
    const isRamLocked = cat.id === "ram" && !state.build.motherboard;

    const row = document.createElement("div");
    row.className = `component-row ${selectedItem ? "filled" : "empty"}`;
    row.setAttribute("data-category", cat.id);

    // Nomor urut dan info kategori
    const orderNum = index + 1;
    let lockHelpText = "";
    if (isMoboLocked) {
      lockHelpText = `<span class="lock-hint">${ICONS.lock(12)} (Kunci: Pilih CPU terlebih dahulu)</span>`;
    } else if (isRamLocked) {
      lockHelpText = `<span class="lock-hint">${ICONS.lock(12)} (Kunci: Pilih Motherboard terlebih dahulu)</span>`;
    }

    if (!selectedItem) {
      // Empty state slot (Border dashed sesuai Design Brief)
      const isDisabled = isMoboLocked || isRamLocked;
      row.innerHTML = `
        <div class="slot-info">
          <div class="slot-header">
            <span class="slot-number">${orderNum}</span>
            <h3 class="slot-title">${cat.label}</h3>
            ${lockHelpText}
          </div>
          <p class="slot-desc">${cat.description}</p>
        </div>
        <div class="slot-actions">
          <button 
            type="button" 
            class="btn btn-primary btn-select"
            data-action="select" 
            data-category="${cat.id}"
            ${isDisabled ? "disabled" : ""}
            aria-label="Pilih ${cat.label}"
          >
            ${ICONS.plus(14)} <span>Pilih ${cat.id.toUpperCase()}</span>
          </button>
        </div>
      `;
    } else {
      // Filled state slot (Surface #1E1E1E, gambar hardware, spesifikasi kunci, harga estimasi, tombol Ganti & Hapus)
      const priceInfo = getComponentPriceInfo(cat.id, selectedItem);
      const isCustomBadge = selectedItem.is_custom ? `<span class="tag-custom">Custom</span>` : "";
      const itemImgHtml = selectedItem.image
        ? `<img src="${selectedItem.image}" alt="${escapeHtml(selectedItem.name)}" class="component-thumb w-14 h-14 object-contain rounded bg-zinc-900/90 p-1.5 border border-zinc-700/60 shrink-0" />`
        : "";

      let priceDisplayHtml = `
        <div class="price-container">
          <span class="price-label">Estimasi Harga:</span>
          <span class="price-value ${priceInfo.isManual ? "price-manual" : ""}">${priceInfo.formatted}</span>
        </div>
      `;

      // Jika API harga down, tampilkan form input harga manual inline
      if (priceInfo.isApiDown) {
        priceDisplayHtml = `
          <div class="manual-price-box">
            <label for="price-input-${cat.id}" class="manual-label">Input Harga Manual (Rp):</label>
            <div class="manual-input-group">
              <input 
                type="number" 
                id="price-input-${cat.id}" 
                class="input-manual-price" 
                placeholder="Contoh: 1500000" 
                value="${selectedItem.manual_price || (priceInfo.price > 0 ? priceInfo.price : "")}"
                data-category="${cat.id}"
              />
              <button type="button" class="btn btn-secondary btn-sm btn-save-price" data-category="${cat.id}">Simpan</button>
            </div>
          </div>
        `;
      }

      row.innerHTML = `
        <div class="slot-content-wrapper flex items-center gap-3.5 flex-1 min-w-0">
          ${itemImgHtml}
          <div class="slot-info flex-1 min-w-0">
            <div class="slot-header flex items-center gap-2 flex-wrap">
              <span class="slot-number">${orderNum}</span>
              <div class="slot-title-group">
                <h3 class="slot-title">${cat.label} ${isCustomBadge}</h3>
                <div class="selected-name">${escapeHtml(selectedItem.name)}</div>
              </div>
            </div>
            <div class="selected-specs">${escapeHtml(selectedItem.specs_summary || "")}</div>
            ${priceDisplayHtml}
          </div>
        </div>
        <div class="slot-actions filled-actions">
          <button 
            type="button" 
            class="btn btn-secondary btn-sm" 
            data-action="change" 
            data-category="${cat.id}"
            aria-label="Ganti ${cat.label}"
          >
            Ganti
          </button>
          <button 
            type="button" 
            class="btn btn-danger-icon btn-sm" 
            data-action="remove" 
            data-category="${cat.id}"
            title="Hapus ${cat.label}"
            aria-label="Hapus ${cat.label}"
          >
            ${ICONS.close(14)}
          </button>
        </div>
      `;
    }

    container.appendChild(row);
  });
}

// ----------------------------------------------------
// Update Summary & Hardware Warnings (FR2, FR3, FR4, FR5)
// ----------------------------------------------------
function updateSummary() {
  const wattageInfo = calculateWattage(state.build);
  const compatibilityInfo = validateBuild(state.build, wattageInfo);
  const priceInfo = calculateTotalPrice(state.build);

  // Top Bar Wattage Display (Header)
  const topWattageEl = document.getElementById("top-wattage-display");
  if (topWattageEl) {
    if (wattageInfo.totalWattage > 0) {
      topWattageEl.innerHTML = `${ICONS.zap(16)} <span class="wattage-val">${wattageInfo.totalWattage}W</span> <span class="wattage-rec">(Saran PSU: min ${wattageInfo.recommendedPsuWattage}W)</span>`;
    } else {
      topWattageEl.innerHTML = `${ICONS.zap(16)} <span class="wattage-val">0W</span> <span class="wattage-rec">(Pilih CPU/GPU)</span>`;
    }
  }

  // Sidebar Summary Wattage Detail & Meter Bar
  const sidebarWattageEl = document.getElementById("summary-wattage-total");
  const sidebarPsuRecEl = document.getElementById("summary-psu-recommendation");
  const wattageGaugeBar = document.getElementById("summary-wattage-bar");
  if (sidebarWattageEl) {
    sidebarWattageEl.textContent = `${wattageInfo.totalWattage} Watt`;
  }
  if (sidebarPsuRecEl) {
    sidebarPsuRecEl.textContent = wattageInfo.recommendedPsuWattage > 0 
      ? `Minimal ${wattageInfo.recommendedPsuWattage} Watt` 
      : "-";
  }
  if (wattageGaugeBar) {
    const pct = Math.min(100, Math.round((wattageInfo.totalWattage / 850) * 100));
    wattageGaugeBar.style.width = `${pct}%`;
  }

  // Sidebar Total Price
  const totalPriceEl = document.getElementById("summary-total-price");
  if (totalPriceEl) {
    totalPriceEl.textContent = priceInfo.formattedTotal;
  }

  // Status Kompatibilitas Badge
  const statusBadgeContainer = document.getElementById("compatibility-status-badge");
  if (statusBadgeContainer) {
    const status = compatibilityInfo.compatibilityStatus;
    statusBadgeContainer.className = `status-badge ${status.badgeClass}`;
    
    let iconSymbol = ICONS.info(18);
    if (status.status === "compatible") iconSymbol = ICONS.checkCircle(18);
    else if (status.status === "incompatible") iconSymbol = ICONS.alertCircle(18);
    else if (status.status === "custom_warning") iconSymbol = ICONS.alertTriangle(18);
    else if (status.status === "progressing") iconSymbol = ICONS.check(18);

    statusBadgeContainer.innerHTML = `
      <span class="badge-icon">${iconSymbol}</span>
      <span class="badge-text">${status.badgeText}</span>
    `;
  }

  // Hardware Warnings Engine (FR4)
  const warningsContainer = document.getElementById("hardware-warnings-panel");
  if (warningsContainer) {
    const allNotices = [...compatibilityInfo.errors, ...compatibilityInfo.warnings];

    if (allNotices.length === 0) {
      warningsContainer.innerHTML = `
        <div class="warning-empty">
          <span class="icon">${ICONS.check(16)}</span>
          <span>Tidak ada masalah kompatibilitas terdeteksi.</span>
        </div>
      `;
    } else {
      warningsContainer.innerHTML = allNotices
        .map((notice) => {
          const isCritical = notice.severity === "critical";
          const icon = isCritical ? ICONS.alertCircle(16) : ICONS.alertTriangle(16);
          const boxClass = isCritical ? "notice-critical" : "notice-warning";
          return `
            <div class="warning-box ${boxClass}">
              <div class="warning-header">
                <span class="warning-icon">${icon}</span>
                <strong class="warning-title">${escapeHtml(notice.title)}</strong>
              </div>
              <p class="warning-text">${escapeHtml(notice.message)}</p>
            </div>
          `;
        })
        .join("");
    }
  }

  // Update Status Banner API Harga Down (Failure State 1)
  const apiBanner = document.getElementById("api-status-banner");
  if (apiBanner) {
    if (getApiStatus() === "down") {
      apiBanner.classList.remove("hidden");
    } else {
      apiBanner.classList.add("hidden");
    }
  }

  // Disable / Enable Export & Simpan DB Button jika build benar-benar kosong
  const exportBtn = document.getElementById("btn-export-build");
  const copyBtn = document.getElementById("btn-copy-build");
  const saveDbBtn = document.getElementById("btn-save-build-db");
  const hasAnyComponent = Object.values(state.build).some((c) => c !== null);

  if (exportBtn) exportBtn.disabled = !hasAnyComponent;
  if (copyBtn) copyBtn.disabled = !hasAnyComponent;
  if (saveDbBtn) {
    const hasDbSelected = Object.values(state.build).some((c) => c !== null && (c.db_id || !c.is_custom));
    saveDbBtn.disabled = !hasDbSelected;
  }

  // Sinkronkan komponen terpilih ke hidden inputs form simpan database MySQL
  Object.entries(state.build).forEach(([cat, comp]) => {
    const idInput = document.getElementById(`db-input-${cat}`);
    const priceInput = document.getElementById(`db-price-${cat}`);
    if (idInput) {
      idInput.value = comp && !comp.is_custom ? (comp.db_id || comp.id || "") : "";
    }
    if (priceInput) {
      priceInput.value = comp && comp.manual_price !== undefined ? String(comp.manual_price) : "";
    }
  });

  // Update printable preview elements jika sedang diprint
  updatePrintSheet(wattageInfo, compatibilityInfo, priceInfo);
}

// ----------------------------------------------------
// Printable Sheet Update (Untuk PDF Export FR5)
// ----------------------------------------------------
function updatePrintSheet(wattageInfo, compatibilityInfo, priceInfo) {
  const printTbody = document.getElementById("print-table-body");
  const printTotalWattage = document.getElementById("print-total-wattage");
  const printPsuRec = document.getElementById("print-psu-rec");
  const printTotalPrice = document.getElementById("print-total-price");
  const printStatusBadge = document.getElementById("print-status-badge");
  const printDate = document.getElementById("print-date");

  if (!printTbody) return;

  if (printDate) {
    printDate.textContent = new Intl.DateTimeFormat("id-ID", {
      dateStyle: "full",
      timeStyle: "short"
    }).format(new Date());
  }

  if (printTotalWattage) printTotalWattage.textContent = `${wattageInfo.totalWattage} Watt`;
  if (printPsuRec) printPsuRec.textContent = `Minimal ${wattageInfo.recommendedPsuWattage} Watt`;
  if (printTotalPrice) printTotalPrice.textContent = priceInfo.formattedTotal;

  if (printStatusBadge) {
    printStatusBadge.textContent = compatibilityInfo.compatibilityStatus.badgeText;
    printStatusBadge.className = `print-badge ${compatibilityInfo.compatibilityStatus.badgeClass}`;
  }

  let html = "";
  COMPONENT_CATEGORIES.forEach((cat, idx) => {
    const comp = state.build[cat.id];
    const price = comp ? getComponentPriceInfo(cat.id, comp).formatted : "-";
    html += `
      <tr>
        <td class="col-num">${idx + 1}</td>
        <td class="col-cat">${cat.label}</td>
        <td class="col-name">
          <strong>${comp ? escapeHtml(comp.name) : "<em>[Belum Dipilih]</em>"}</strong>
          ${comp && comp.specs_summary ? `<div class="sub-spec">${escapeHtml(comp.specs_summary)}</div>` : ""}
        </td>
        <td class="col-price">${price}</td>
      </tr>
    `;
  });
  printTbody.innerHTML = html;
}

// ----------------------------------------------------
// Modal Selection & Custom Component Logic
// ----------------------------------------------------
function openComponentModal(category) {
  state.modal.isOpen = true;
  state.modal.currentCategory = category;
  state.modal.searchQuery = "";
  state.modal.isCustomFormVisible = false;

  const modalOverlay = document.getElementById("component-modal-overlay");
  const modalTitle = document.getElementById("modal-category-title");
  const searchInput = document.getElementById("modal-search-input");
  const customForm = document.getElementById("modal-custom-form");

  if (searchInput) searchInput.value = "";
  if (customForm) customForm.classList.add("hidden");

  const catMeta = COMPONENT_CATEGORIES.find((c) => c.id === category);
  if (modalTitle && catMeta) {
    modalTitle.textContent = `Pilih ${catMeta.label}`;
  }

  renderModalList();

  if (modalOverlay) {
    modalOverlay.classList.remove("hidden");
    document.body.classList.add("modal-open");
    // Auto focus search input untuk kemudahan navigasi keyboard
    setTimeout(() => {
      if (searchInput) searchInput.focus();
    }, 50);
  }
}

function closeComponentModal() {
  state.modal.isOpen = false;
  state.modal.currentCategory = null;
  state.modal.isCustomFormVisible = false;

  const modalOverlay = document.getElementById("component-modal-overlay");
  if (modalOverlay) {
    modalOverlay.classList.add("hidden");
    document.body.classList.remove("modal-open");
  }
}

function renderModalList() {
  const listContainer = document.getElementById("modal-component-list");
  if (!listContainer || !state.modal.currentCategory) return;

  const category = state.modal.currentCategory;
  
  // Terapkan Smart Filtering Compatibility (FR1 & Edge Cases)
  const filteredByCategory = filterAvailableComponents(category, state.build, getActiveDatabase());
  
  // Terapkan search query
  const query = state.modal.searchQuery.toLowerCase().trim();
  const matched = filteredByCategory.filter((item) => {
    return (
      item.name.toLowerCase().includes(query) ||
      (item.specs_summary && item.specs_summary.toLowerCase().includes(query))
    );
  });

  if (matched.length === 0) {
    // Empty State sesuai Design Brief section 7
    listContainer.innerHTML = `
      <div class="empty-state-modal">
        <div class="empty-icon">${ICONS.search(36)}</div>
        <h4 class="empty-title">Komponen tidak ditemukan</h4>
        <p class="empty-desc">
          Tidak ada ${category.toUpperCase()} yang cocok dengan kata kunci pencarian atau kompatibilitas komponen yang telah Anda pilih sebelumnya.
        </p>
        <button type="button" class="btn btn-secondary btn-sm" id="btn-show-custom-from-empty">
          ${ICONS.plus(14)} <span>Input Komponen Manual (${category.toUpperCase()})</span>
        </button>
      </div>
    `;

    const btnEmptyCustom = document.getElementById("btn-show-custom-from-empty");
    if (btnEmptyCustom) {
      btnEmptyCustom.addEventListener("click", () => toggleCustomComponentForm(true));
    }
    return;
  }

  let html = "";
  matched.forEach((item) => {
    const priceInfo = getComponentPriceInfo(category, item);
    const itemCardImgHtml = item.image
      ? `<img src="${item.image}" alt="${escapeHtml(item.name)}" class="w-12 h-12 object-contain rounded bg-zinc-900 p-1 border border-zinc-700/60 shrink-0" />`
      : "";

    html += `
      <div class="modal-item-card flex items-center gap-3 p-3 rounded-lg border border-zinc-700/60 bg-zinc-900/40 hover:bg-zinc-800/60 transition-colors" data-component-id="${item.id}">
        ${itemCardImgHtml}
        <div class="item-main-info flex-1 min-w-0">
          <h4 class="item-name text-white font-medium text-sm md:text-base">${escapeHtml(item.name)}</h4>
          <p class="item-specs text-xs text-zinc-400 mt-0.5">${escapeHtml(item.specs_summary || "")}</p>
          <div class="item-meta-tags flex flex-wrap gap-1.5 mt-2">
            ${item.socket ? `<span class="spec-tag text-xs px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">Socket: ${item.socket}</span>` : ""}
            ${item.tdp ? `<span class="spec-tag text-xs px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">TDP: ${item.tdp}W</span>` : ""}
            ${item.ram_type ? `<span class="spec-tag text-xs px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">RAM: ${item.ram_type}</span>` : ""}
            ${item.form_factor ? `<span class="spec-tag text-xs px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">Size: ${item.form_factor}</span>` : ""}
            ${item.wattage ? `<span class="spec-tag text-xs px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">Power: ${item.wattage}W</span>` : ""}
          </div>
        </div>
        <div class="item-action-area flex flex-col items-end gap-2 shrink-0">
          <span class="item-price text-sm md:text-base font-semibold text-emerald-400">${priceInfo.formatted}</span>
          <button type="button" class="btn btn-primary btn-sm btn-choose-item px-3 py-1.5 text-xs font-medium rounded-md bg-blue-600 hover:bg-blue-700 text-white" data-id="${item.id}">
            Pilih
          </button>
        </div>
      </div>
    `;
  });

  listContainer.innerHTML = html;

  // Bind click listener ke tiap kartu
  listContainer.querySelectorAll(".btn-choose-item").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-id");
      selectComponentById(category, id);
    });
  });
}

function selectComponentById(category, id) {
  const item = (getActiveDatabase()[category] || []).find((c) => c.id === id);
  if (!item) return;

  // Proteksi & Penanganan Edge Case PRD:
  // Jika CPU diganti / dipilih baru, periksa apakah Motherboard yang sudah ada masih kompatibel.
  // Rekomendasi UI: Reset otomatis komponen terkait jika socket berbeda untuk menghindari kebingungan pemula.
  if (category === "cpu" && state.build.motherboard) {
    if (state.build.motherboard.socket !== item.socket) {
      showToast("Motherboard & RAM sebelumnya direset otomatis karena perbedaan socket prosesor baru.");
      state.build.motherboard = null;
      state.build.ram = null;
      clearManualPrice("motherboard");
      clearManualPrice("ram");
    }
  }

  // Jika Motherboard diganti dan RAM yang sudah dipilih berbeda tipe DDR
  if (category === "motherboard" && state.build.ram) {
    if (state.build.ram.ram_type !== item.ram_type) {
      showToast("RAM sebelumnya direset otomatis karena tipe DDR motherboard berbeda.");
      state.build.ram = null;
      clearManualPrice("ram");
    }
  }

  state.build[category] = item;
  closeComponentModal();
  renderComponentSlots();
  updateSummary();
  showToast(`Berhasil memilih ${item.name}`);
}

function removeComponent(category) {
  const removedName = state.build[category] ? state.build[category].name : "";
  state.build[category] = null;
  clearManualPrice(category);

  // Jika CPU dihapus, peringatkan atau kosongkan motherboard jika diinginkan
  if (category === "cpu" && state.build.motherboard) {
    showToast("CPU dihapus. Motherboard & RAM ikut dikosongkan untuk memastikan kompatibilitas.");
    state.build.motherboard = null;
    state.build.ram = null;
  }

  renderComponentSlots();
  updateSummary();
  if (removedName) {
    showToast(`Komponen telah dihapus.`);
  }
}

// ----------------------------------------------------
// Failure State 2: Custom Component Handling
// ----------------------------------------------------
function toggleCustomComponentForm(show) {
  state.modal.isCustomFormVisible = show;
  const customForm = document.getElementById("modal-custom-form");
  const modalList = document.getElementById("modal-component-list");
  const searchBar = document.querySelector(".modal-search-box");

  if (!customForm) return;

  if (show) {
    customForm.classList.remove("hidden");
    if (modalList) modalList.classList.add("hidden");
    if (searchBar) searchBar.classList.add("hidden");

    // Sesuaikan form fields dengan kategori aktif
    setupCustomFormFields(state.modal.currentCategory);
  } else {
    customForm.classList.add("hidden");
    if (modalList) modalList.classList.remove("hidden");
    if (searchBar) searchBar.classList.remove("hidden");
    renderModalList();
  }
}

function setupCustomFormFields(category) {
  const dynamicFieldsContainer = document.getElementById("custom-dynamic-fields");
  if (!dynamicFieldsContainer) return;

  let fieldsHtml = "";

  if (category === "cpu") {
    fieldsHtml = `
      <div class="form-group">
        <label for="custom-cpu-socket">Socket CPU:</label>
        <select id="custom-cpu-socket" class="form-input">
          <option value="LGA1700">Intel LGA1700</option>
          <option value="AM5">AMD AM5</option>
          <option value="AM4">AMD AM4</option>
          <option value="LGA1200">Intel LGA1200</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label for="custom-cpu-tdp">TDP Daya (Watt):</label>
        <input type="number" id="custom-cpu-tdp" class="form-input" value="65" />
      </div>
      <div class="form-group checkbox-group">
        <label class="checkbox-label">
          <input type="checkbox" id="custom-cpu-igpu" checked />
          <span>Memiliki Grafis Internal (Integrated GPU)</span>
        </label>
      </div>
    `;
  } else if (category === "motherboard") {
    fieldsHtml = `
      <div class="form-group">
        <label for="custom-mobo-socket">Socket Motherboard:</label>
        <select id="custom-mobo-socket" class="form-input">
          <option value="LGA1700">Intel LGA1700</option>
          <option value="AM5">AMD AM5</option>
          <option value="AM4">AMD AM4</option>
          <option value="LGA1200">Intel LGA1200</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label for="custom-mobo-ram">Dukungan Tipe RAM:</label>
        <select id="custom-mobo-ram" class="form-input">
          <option value="DDR4">DDR4</option>
          <option value="DDR5">DDR5</option>
        </select>
      </div>
      <div class="form-group">
        <label for="custom-mobo-form">Ukuran Form Factor:</label>
        <select id="custom-mobo-form" class="form-input">
          <option value="mATX">Micro-ATX (mATX)</option>
          <option value="ATX">Standard ATX</option>
          <option value="ITX">Mini-ITX (ITX)</option>
        </select>
      </div>
    `;
  } else if (category === "ram") {
    fieldsHtml = `
      <div class="form-group">
        <label for="custom-ram-type">Tipe RAM:</label>
        <select id="custom-ram-type" class="form-input">
          <option value="DDR4">DDR4</option>
          <option value="DDR5">DDR5</option>
        </select>
      </div>
      <div class="form-group">
        <label for="custom-ram-cap">Kapasitas (GB):</label>
        <input type="number" id="custom-ram-cap" class="form-input" value="16" />
      </div>
    `;
  } else if (category === "gpu") {
    fieldsHtml = `
      <div class="form-group">
        <label for="custom-gpu-tdp">Estimasi Daya TDP GPU (Watt):</label>
        <input type="number" id="custom-gpu-tdp" class="form-input" value="150" />
      </div>
    `;
  } else if (category === "psu") {
    fieldsHtml = `
      <div class="form-group">
        <label for="custom-psu-wattage">Kapasitas Watt PSU:</label>
        <input type="number" id="custom-psu-wattage" class="form-input" value="650" />
      </div>
    `;
  } else if (category === "case") {
    fieldsHtml = `
      <div class="form-group">
        <label>Mendukung Form Factor Motherboard:</label>
        <div class="checkbox-options">
          <label class="checkbox-label"><input type="checkbox" id="case-support-atx" checked /> ATX</label>
          <label class="checkbox-label"><input type="checkbox" id="case-support-matx" checked /> mATX</label>
          <label class="checkbox-label"><input type="checkbox" id="case-support-itx" checked /> Mini-ITX</label>
        </div>
      </div>
    `;
  }

  dynamicFieldsContainer.innerHTML = fieldsHtml;
}

function handleSaveCustomComponent() {
  const category = state.modal.currentCategory;
  if (!category) return;

  const nameInput = document.getElementById("custom-comp-name");
  const priceInput = document.getElementById("custom-comp-price");

  const name = nameInput ? nameInput.value.trim() : "";
  const price = priceInput ? parseInt(priceInput.value, 10) || 0 : 0;

  if (!name) {
    alert("Harap masukkan nama komponen custom.");
    return;
  }

  const newComp = {
    id: `custom-${category}-${Date.now()}`,
    type: category,
    name: `[Custom] ${name}`,
    base_price: price,
    is_custom: true,
    specs_summary: "Komponen manual yang diinput oleh pengguna",
    image: COMPONENT_IMAGES[category] || null
  };

  // Baca spesifikasi dinamis
  if (category === "cpu") {
    const socketEl = document.getElementById("custom-cpu-socket");
    const tdpEl = document.getElementById("custom-cpu-tdp");
    const igpuEl = document.getElementById("custom-cpu-igpu");
    newComp.socket = socketEl ? socketEl.value : "LGA1700";
    newComp.tdp = tdpEl ? parseInt(tdpEl.value, 10) || 65 : 65;
    newComp.has_igpu = igpuEl ? igpuEl.checked : true;
    newComp.specs_summary = `Socket ${newComp.socket}, TDP ${newComp.tdp}W, ${newComp.has_igpu ? "With iGPU" : "No iGPU"}`;
  } else if (category === "motherboard") {
    const socketEl = document.getElementById("custom-mobo-socket");
    const ramEl = document.getElementById("custom-mobo-ram");
    const formEl = document.getElementById("custom-mobo-form");
    newComp.socket = socketEl ? socketEl.value : "LGA1700";
    newComp.ram_type = ramEl ? ramEl.value : "DDR4";
    newComp.form_factor = formEl ? formEl.value : "mATX";
    newComp.specs_summary = `Socket ${newComp.socket}, ${newComp.form_factor}, RAM ${newComp.ram_type}`;
  } else if (category === "ram") {
    const ramEl = document.getElementById("custom-ram-type");
    const capEl = document.getElementById("custom-ram-cap");
    newComp.ram_type = ramEl ? ramEl.value : "DDR4";
    const cap = capEl ? parseInt(capEl.value, 10) || 16 : 16;
    newComp.specs_summary = `${cap}GB ${newComp.ram_type}`;
  } else if (category === "gpu") {
    const tdpEl = document.getElementById("custom-gpu-tdp");
    newComp.tdp = tdpEl ? parseInt(tdpEl.value, 10) || 150 : 150;
    newComp.specs_summary = `Custom GPU, TDP ${newComp.tdp}W`;
  } else if (category === "psu") {
    const wattEl = document.getElementById("custom-psu-wattage");
    newComp.wattage = wattEl ? parseInt(wattEl.value, 10) || 650 : 650;
    newComp.specs_summary = `Kapasitas ${newComp.wattage}W`;
  } else if (category === "case") {
    const supp = [];
    if (document.getElementById("case-support-atx")?.checked) supp.push("ATX");
    if (document.getElementById("case-support-matx")?.checked) supp.push("mATX");
    if (document.getElementById("case-support-itx")?.checked) supp.push("ITX");
    newComp.supported_form_factors = supp.length > 0 ? supp : ["mATX", "ITX"];
    newComp.specs_summary = `Mendukung Form Factor: ${newComp.supported_form_factors.join(", ")}`;
  }

  state.build[category] = newComp;
  closeComponentModal();
  renderComponentSlots();
  updateSummary();
  showToast(`Komponen custom ditambahkan: ${name}. Status: Kompatibilitas Custom Tidak Dijamin.`);
}

// ----------------------------------------------------
// Setup Event Listeners
// ----------------------------------------------------
function setupEventListeners() {
  // Tombol Select / Change / Remove di Component List
  const container = document.getElementById("component-slots-container");
  if (container) {
    container.addEventListener("click", (e) => {
      const target = e.target.closest("button");
      if (!target) return;

      const action = target.getAttribute("data-action");
      const category = target.getAttribute("data-category");

      if (action === "select" || action === "change") {
        openComponentModal(category);
      } else if (action === "remove") {
        removeComponent(category);
      }
    });

    // Simpan harga manual jika API sedang down
    container.addEventListener("click", (e) => {
      if (e.target.classList.contains("btn-save-price")) {
        const cat = e.target.getAttribute("data-category");
        const input = document.getElementById(`price-input-${cat}`);
        if (input && cat) {
          const val = input.value;
          setManualPrice(cat, val);
          if (state.build[cat]) {
            state.build[cat].manual_price = parseInt(val, 10) || 0;
          }
          renderComponentSlots();
          updateSummary();
          showToast(`Harga manual untuk ${cat.toUpperCase()} berhasil disimpan.`);
        }
      }
    });
  }

  // Search input di dalam Modal
  const searchInput = document.getElementById("modal-search-input");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      state.modal.searchQuery = e.target.value;
      renderModalList();
    });
  }

  // Tombol Close Modal
  const btnCloseModal = document.getElementById("btn-close-modal");
  if (btnCloseModal) {
    btnCloseModal.addEventListener("click", closeComponentModal);
  }

  const modalOverlay = document.getElementById("component-modal-overlay");
  if (modalOverlay) {
    modalOverlay.addEventListener("click", (e) => {
      if (e.target === modalOverlay) {
        closeComponentModal();
      }
    });
  }

  // Tombol Buka Form Custom Komponen di Modal
  const btnOpenCustom = document.getElementById("btn-open-custom-form");
  if (btnOpenCustom) {
    btnOpenCustom.addEventListener("click", () => toggleCustomComponentForm(true));
  }

  const btnCancelCustom = document.getElementById("btn-cancel-custom");
  if (btnCancelCustom) {
    btnCancelCustom.addEventListener("click", () => toggleCustomComponentForm(false));
  }

  const btnSaveCustom = document.getElementById("btn-save-custom");
  if (btnSaveCustom) {
    btnSaveCustom.addEventListener("click", handleSaveCustomComponent);
  }

  // Export Buttons (FR5)
  const btnExportPdf = document.getElementById("btn-export-build");
  if (btnExportPdf) {
    btnExportPdf.addEventListener("click", () => {
      printBuild();
    });
  }

  const btnCopyText = document.getElementById("btn-copy-build");
  if (btnCopyText) {
    btnCopyText.addEventListener("click", () => {
      const wattageInfo = calculateWattage(state.build);
      const compatibilityInfo = validateBuild(state.build, wattageInfo);
      copyBuildToClipboard(state.build, wattageInfo, compatibilityInfo)
        .then(() => {
          showToast("Rincian rakitan berhasil disalin ke clipboard! Siap dikirim ke toko/teknisi.");
        })
        .catch(() => {
          alert("Gagal menyalin teks ke clipboard.");
        });
    });
  }

  // Reset Build Button
  const btnReset = document.getElementById("btn-reset-build");
  if (btnReset) {
    btnReset.addEventListener("click", () => {
      const hasAny = Object.values(state.build).some((c) => c !== null);
      if (!hasAny) return;
      if (confirm("Apakah Anda yakin ingin mengosongkan seluruh komponen rakitan?")) {
        Object.keys(state.build).forEach((k) => {
          state.build[k] = null;
          clearManualPrice(k);
        });
        renderComponentSlots();
        updateSummary();
        showToast("Seluruh komponen rakitan telah direset.");
      }
    });
  }

  // Toggle Simulasi API Harga (Failure State 1 Tester)
  const toggleApiBtn = document.getElementById("btn-toggle-api-status");
  if (toggleApiBtn) {
    toggleApiBtn.addEventListener("click", () => {
      const current = getApiStatus();
      const next = current === "online" ? "down" : "online";
      setApiStatus(next);
      toggleApiBtn.textContent = next === "online" ? "Simulasi: API Normal" : "Simulasi: API Down (Aktif)";
      toggleApiBtn.className = next === "online" ? "btn btn-secondary btn-sm" : "btn btn-warning-outline btn-sm";
      renderComponentSlots();
      updateSummary();
      showToast(next === "down" 
        ? "Mode gangguan API aktif: Menampilkan peringatan dan kolom input harga manual." 
        : "Mode API normal dipulihkan.");
    });
  }

  // Listener untuk Template Siap Pakai ala NZXT Player Series
  document.addEventListener("click", (e) => {
    const templateBtn = e.target.closest("[data-action='load-template']");
    if (templateBtn) {
      const templateId = templateBtn.getAttribute("data-template-id");
      loadPrebuiltTemplate(templateId);
    }
  });

  // Listener untuk Accordion FAQ Interaktif
  document.addEventListener("click", (e) => {
    const faqBtn = e.target.closest(".faq-question-btn");
    if (faqBtn) {
      const item = faqBtn.closest(".faq-item");
      if (!item) return;
      const answer = item.querySelector(".faq-answer");
      const chevron = item.querySelector(".faq-chevron");
      const isExpanded = answer && !answer.classList.contains("hidden");

      // Tutup accordion lain agar bersih dan fokus
      document.querySelectorAll(".faq-item").forEach((other) => {
        if (other !== item) {
          other.querySelector(".faq-answer")?.classList.add("hidden");
          other.querySelector(".faq-chevron")?.classList.remove("rotate-180");
        }
      });

      if (isExpanded) {
        answer?.classList.add("hidden");
        chevron?.classList.remove("rotate-180");
      } else {
        answer?.classList.remove("hidden");
        chevron?.classList.add("rotate-180");
      }
    }
  });
}

function loadPrebuiltTemplate(templateId) {
  const template = PREBUILT_TEMPLATES.find((t) => t.id === templateId);
  if (!template) return;

  // Reset rakitan saat ini
  Object.keys(state.build).forEach((k) => {
    state.build[k] = null;
    clearManualPrice(k);
  });

  // Masukkan komponen dari template
  const db = getActiveDatabase();
  Object.entries(template.components).forEach(([category, compId]) => {
    const item = (db[category] || []).find((c) => c.id === compId);
    if (item) {
      state.build[category] = item;
    }
  });

  renderComponentSlots();
  updateSummary();
  showToast(`Template ${template.badge} ("${template.title}") berhasil dimuat ke dalam builder!`);

  // Scroll halus ke area builder
  const builderEl = document.getElementById("builder-section") || document.getElementById("builder-container");
  if (builderEl) {
    builderEl.scrollIntoView({ behavior: "smooth", block: "start" });
  }
}

// ----------------------------------------------------
// Aksesibilitas Keyboard (R-32)
// ----------------------------------------------------
function setupKeyboardAccessibility() {
  document.addEventListener("keydown", (e) => {
    // Tombol Escape menutup modal yang terbuka
    if (e.key === "Escape" && state.modal.isOpen) {
      closeComponentModal();
    }
  });
}

// ----------------------------------------------------
// UI Notification / Toast Helper
// ----------------------------------------------------
function showToast(message) {
  const toast = document.getElementById("app-toast");
  if (!toast) return;

  toast.textContent = message;
  toast.classList.remove("hidden");
  toast.classList.add("visible");

  if (state.toastTimeout) {
    clearTimeout(state.toastTimeout);
  }

  state.toastTimeout = setTimeout(() => {
    toast.classList.remove("visible");
    setTimeout(() => toast.classList.add("hidden"), 300);
  }, 4000);
}

function escapeHtml(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

