// Unit & Integration Test Suite untuk PayCom PC Builder Engine
import { HARDWARE_DATABASE, COMPONENT_CATEGORIES } from "../js/data/components.js";
import { filterAvailableComponents, validateBuild } from "../js/engine/compatibility.js";
import { calculateWattage } from "../js/engine/wattage.js";
import {
  calculateTotalPrice,
  getComponentPriceInfo,
  setApiStatus,
  setManualPrice,
  clearManualPrice
} from "../js/engine/pricing.js";
import { generateBuildText } from "../js/engine/exporter.js";

let passed = 0;
let failed = 0;

function assert(condition, testName) {
  if (condition) {
    console.log(`PASS: ${testName}`);
    passed++;
  } else {
    console.error(`FAIL: ${testName}`);
    failed++;
  }
}

console.log("=== MEMULAI PENGUJIAN ENGINE PAYCOM ===");

// 1. Test FR1: Smart Component Selector & Filtering
const intelCpu = HARDWARE_DATABASE.cpu.find(c => c.socket === "LGA1700");
const amdCpu = HARDWARE_DATABASE.cpu.find(c => c.socket === "AM4");

const mobosForIntel = filterAvailableComponents("motherboard", { cpu: intelCpu }, HARDWARE_DATABASE);
assert(mobosForIntel.length > 0 && mobosForIntel.every(m => m.socket === "LGA1700"), "Filter Motherboard: hanya menampilkan socket LGA1700 saat CPU LGA1700 dipilih");

const mobosForAmd = filterAvailableComponents("motherboard", { cpu: amdCpu }, HARDWARE_DATABASE);
assert(mobosForAmd.length > 0 && mobosForAmd.every(m => m.socket === "AM4"), "Filter Motherboard: hanya menampilkan socket AM4 saat CPU AM4 dipilih");

const ddr4Mobo = HARDWARE_DATABASE.motherboard.find(m => m.ram_type === "DDR4");
const ramsForDdr4 = filterAvailableComponents("ram", { motherboard: ddr4Mobo }, HARDWARE_DATABASE);
assert(ramsForDdr4.length > 0 && ramsForDdr4.every(r => r.ram_type === "DDR4"), "Filter RAM: hanya menampilkan DDR4 saat Motherboard DDR4 dipilih");

// 2. Test FR3: Wattage Calculator
// i5-12400F (TDP 65) + RTX 4060 (TDP 115) + Baseline (50) = 230 Watt
const testBuildWattage = {
  cpu: { tdp: 65 },
  gpu: { tdp: 115 }
};
const wattageResult = calculateWattage(testBuildWattage);
assert(wattageResult.totalWattage === 230, `Kalkulator Wattage: totalWattage harus 230W (Didapat: ${wattageResult.totalWattage}W)`);
assert(wattageResult.recommendedPsuWattage >= 350, `Kalkulator Rekomendasi PSU: harus >= 350W (Didapat: ${wattageResult.recommendedPsuWattage}W)`);

// 3. Test FR4: Hardware Warnings Engine
// CPU tanpa iGPU (i3-12100F, has_igpu: false) dan tanpa VGA tambahan
const noGpuBuild = {
  cpu: { id: "cpu-f", socket: "LGA1700", tdp: 58, has_igpu: false },
  motherboard: { id: "mobo-1", socket: "LGA1700", ram_type: "DDR4", form_factor: "mATX" },
  gpu: { id: "gpu-none", base_price: 0, tdp: 0 }
};
const validationNoGpu = validateBuild(noGpuBuild, calculateWattage(noGpuBuild));
const hasNoDisplayNotice = validationNoGpu.errors.some(e => e.type === "no_display_output");
assert(hasNoDisplayNotice, "FR4 Hardware Warning: terdeteksi peringatan kritis jika CPU tanpa iGPU dan tidak ada kartu grafis");

// Tambahkan GPU eksternal, peringatan no_display_output harus hilang
noGpuBuild.gpu = { id: "gpu-rtx4060", base_price: 4850000, tdp: 115 };
const validationWithGpu = validateBuild(noGpuBuild, calculateWattage(noGpuBuild));
const stillHasNoDisplay = validationWithGpu.errors.some(e => e.type === "no_display_output");
assert(!stillHasNoDisplay, "FR4 Hardware Warning: peringatan display hilang setelah GPU eksternal ditambahkan");

// Test Edge Case: Motherboard ATX di Casing ITX
const incompatibleCaseBuild = {
  cpu: { socket: "LGA1700", has_igpu: true, tdp: 65 },
  motherboard: { socket: "LGA1700", ram_type: "DDR5", form_factor: "ATX" },
  case: { supported_form_factors: ["ITX"] }
};
const valCase = validateBuild(incompatibleCaseBuild, calculateWattage(incompatibleCaseBuild));
assert(valCase.errors.some(e => e.type === "form_factor_incompatible"), "Edge Case: Terdeteksi error form factor jika motherboard ATX dipasang di casing Mini-ITX");

// 4. Test Failure State 1: API Down & Manual Price Override
setApiStatus("down");
const priceInfoDown = getComponentPriceInfo("cpu", intelCpu);
assert(priceInfoDown.isApiDown === true, "Failure State 1: status isApiDown bernilai true");

setManualPrice("cpu", 1500000);
const priceInfoManual = getComponentPriceInfo("cpu", intelCpu);
assert(priceInfoManual.price === 1500000, "Failure State 1: harga manual berhasil disimpan dan dihitung");
clearManualPrice("cpu");
setApiStatus("online");

// 5. Test Failure State 2: Custom Component
const customBuild = {
  cpu: { id: "custom-cpu", name: "Custom AMD Ryzen 5", socket: "AM4", has_igpu: true, is_custom: true },
  motherboard: { id: "mobo-am4", socket: "AM4", ram_type: "DDR4", form_factor: "mATX" },
  ram: { id: "ram-ddr4", ram_type: "DDR4" },
  storage: { id: "ssd-1" },
  psu: { id: "psu-1", wattage: 650 },
  case: { id: "case-1", supported_form_factors: ["mATX"] }
};
const valCustom = validateBuild(customBuild, calculateWattage(customBuild));
assert(valCustom.isCustom === true, "Failure State 2: status isCustom bernilai true");
assert(valCustom.compatibilityStatus.badgeText.includes("Custom Tidak Dijamin"), "Failure State 2: badge status menampilkan 'Kompatibilitas Custom Tidak Dijamin'");

// 6. Test FR5: Exporter
const exportText = generateBuildText(customBuild, calculateWattage(customBuild), valCustom);
assert(exportText.includes("DAFTAR RAKITAN PC"), "FR5 Exporter: format teks rakitan mencakup judul");
assert(exportText.includes("STATUS SISTEM"), "FR5 Exporter: format teks mencakup status kompatibilitas");
assert(exportText.includes("ESTIMASI KEBUTUHAN DAYA LISTRIK"), "FR5 Exporter: format teks mencakup total daya & rekomendasi PSU");

console.log(`\n=== HASIL PENGUJIAN: ${passed} PASS, ${failed} FAIL ===`);
if (failed > 0) process.exit(1);

