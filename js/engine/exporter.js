// FR5 - Build Exporter: Teks Rapi & Cetak PDF siap bawa ke toko fisik/teknisi

import { COMPONENT_CATEGORIES } from "../data/components.js";
import { getComponentPriceInfo, formatRupiah } from "./pricing.js";

export function generateBuildText(currentBuild, wattageInfo, compatibilityInfo) {
  const dateStr = new Intl.DateTimeFormat("id-ID", {
    dateStyle: "full",
    timeStyle: "short"
  }).format(new Date());

  let lines = [];
  lines.push("==================================================");
  lines.push("          DAFTAR RAKITAN PC (PAYCOM V2)           ");
  lines.push(` Dibuat pada: ${dateStr}`);
  lines.push("==================================================");
  lines.push("");

  // Status Kompatibilitas
  lines.push(`STATUS SISTEM : ${compatibilityInfo.compatibilityStatus.badgeText.toUpperCase()}`);
  if (compatibilityInfo.compatibilityStatus.is100Percent) {
    lines.push("STATUS VALIDASI: [100% KOMPATIBEL - SIAP RAKIT]");
  } else if (compatibilityInfo.isCustom) {
    lines.push("STATUS VALIDASI: [KOMPATIBILITAS CUSTOM TIDAK DIJAMIN]");
  }
  lines.push("");

  // Ringkasan Daya (FR3)
  lines.push("--- ESTIMASI KEBUTUHAN DAYA LISTRIK ---");
  lines.push(`Total Estimasi Beban Daya : ${wattageInfo.totalWattage} Watt`);
  lines.push(`Rekomendasi Kapasitas PSU : Minimal ${wattageInfo.recommendedPsuWattage} Watt`);
  lines.push("");

  // Rincian Komponen (FR5)
  lines.push("--- RINCIAN KOMPONEN & ESTIMASI HARGA ---");
  let grandTotal = 0;

  COMPONENT_CATEGORIES.forEach((cat, index) => {
    const comp = currentBuild[cat.id];
    lines.push(`${index + 1}. ${cat.label}:`);
    if (comp) {
      const priceInfo = getComponentPriceInfo(cat.id, comp);
      grandTotal += priceInfo.price;
      lines.push(`   Nama : ${comp.name}`);
      if (comp.specs_summary) {
        lines.push(`   Spek : ${comp.specs_summary}`);
      }
      lines.push(`   Harga: ${priceInfo.formatted}`);
    } else {
      lines.push("   [Belum Dipilih]");
    }
    lines.push("");
  });

  lines.push("--------------------------------------------------");
  lines.push(`TOTAL ESTIMASI HARGA: Est. ${formatRupiah(grandTotal)}`);
  lines.push("--------------------------------------------------");
  lines.push("");
  lines.push("* Catatan untuk Penjual/Teknisi Toko:");
  lines.push("Daftar ini telah divalidasi kecocokan socket CPU, form factor motherboard, serta tipe RAM.");
  lines.push("Harga di atas adalah estimasi rata-rata online dan dapat berbeda di toko fisik.");
  lines.push("==================================================");

  return lines.join("\n");
}

export function copyBuildToClipboard(currentBuild, wattageInfo, compatibilityInfo) {
  const text = generateBuildText(currentBuild, wattageInfo, compatibilityInfo);
  if (navigator.clipboard && navigator.clipboard.writeText) {
    return navigator.clipboard.writeText(text);
  } else {
    // Fallback
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.style.position = "fixed";
    textarea.style.opacity = "0";
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
    return Promise.resolve();
  }
}

export function printBuild() {
  window.print();
}

