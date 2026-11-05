// Engine validasi kompatibilitas perangkat keras (FR1, FR4, dan Edge Cases PRD)

export function filterAvailableComponents(category, currentBuild, allComponents) {
  const list = allComponents[category] || [];

  switch (category) {
    case "motherboard":
      // FR1: Filter Motherboard berdasarkan socket CPU yang dipilih
      if (currentBuild.cpu) {
        return list.filter((mobo) => mobo.socket === currentBuild.cpu.socket);
      }
      return list;

    case "ram":
      // FR1: Filter RAM berdasarkan tipe RAM motherboard (DDR4 atau DDR5)
      if (currentBuild.motherboard) {
        return list.filter((ram) => ram.ram_type === currentBuild.motherboard.ram_type);
      }
      return list;

    case "case":
      // Edge Case PRD: Filter casing berdasarkan form factor motherboard
      if (currentBuild.motherboard && currentBuild.motherboard.form_factor) {
        return list.filter((c) => {
          if (!c.supported_form_factors) return true;
          return c.supported_form_factors.includes(currentBuild.motherboard.form_factor);
        });
      }
      return list;

    default:
      return list;
  }
}

export function validateBuild(currentBuild, wattageInfo) {
  const warnings = [];
  const errors = [];
  let isCustom = false;

  // Cek apakah ada komponen custom
  Object.values(currentBuild).forEach((comp) => {
    if (comp && comp.is_custom) {
      isCustom = true;
    }
  });

  // Validasi Socket CPU vs Motherboard
  if (currentBuild.cpu && currentBuild.motherboard) {
    if (currentBuild.cpu.socket !== currentBuild.motherboard.socket) {
      errors.push({
        type: "socket_mismatch",
        severity: "critical",
        title: "Ketidakcocokan Socket",
        message: `CPU menggunakan socket ${currentBuild.cpu.socket}, sedangkan Motherboard menggunakan socket ${currentBuild.motherboard.socket}. Keduanya tidak dapat dipasang bersama.`
      });
    }
  }

  // Validasi RAM Type vs Motherboard
  if (currentBuild.motherboard && currentBuild.ram) {
    if (currentBuild.motherboard.ram_type !== currentBuild.ram.ram_type) {
      errors.push({
        type: "ram_type_mismatch",
        severity: "critical",
        title: "Tipe RAM Tidak Cocok",
        message: `Motherboard mendukung memori tipe ${currentBuild.motherboard.ram_type}, tetapi RAM yang dipilih adalah ${currentBuild.ram.ram_type}. Slot fisik berbeda dan tidak kompatibel.`
      });
    }
  }

  // Validasi Form Factor Motherboard vs Casing
  if (currentBuild.motherboard && currentBuild.case) {
    if (
      currentBuild.case.supported_form_factors &&
      !currentBuild.case.supported_form_factors.includes(currentBuild.motherboard.form_factor)
    ) {
      errors.push({
        type: "form_factor_incompatible",
        severity: "critical",
        title: "Ukuran Motherboard Terlalu Besar untuk Casing",
        message: `Motherboard berukuran ${currentBuild.motherboard.form_factor}, sedangkan casing ini hanya mendukung: ${currentBuild.case.supported_form_factors.join(", ")}.`
      });
    }
  }

  // FR4: Hardware Warnings Engine
  // Jika cpu.has_igpu == false DAN gpu == null atau gpu-none
  if (currentBuild.cpu) {
    const hasExternalGpu = currentBuild.gpu && currentBuild.gpu.id !== "gpu-none" && currentBuild.gpu.base_price > 0;
    if (!currentBuild.cpu.has_igpu && !hasExternalGpu) {
      errors.push({
        type: "no_display_output",
        severity: "critical",
        title: "Peringatan Grafis: Tidak Ada Output Layar",
        message: "PC Anda tidak akan menampilkan gambar tanpa VGA tambahan (CPU yang dipilih tidak memiliki grafis internal)."
      });
    }
  }

  // Peringatan Kapasitas PSU vs Rekomendasi Daya
  if (currentBuild.psu && wattageInfo && wattageInfo.recommendedPsuWattage > 0) {
    if (currentBuild.psu.wattage < wattageInfo.recommendedPsuWattage) {
      warnings.push({
        type: "psu_wattage_low",
        severity: "warning",
        title: "Kapasitas PSU di Bawah Saran Minimum",
        message: `Kapasitas PSU terpilih (${currentBuild.psu.wattage}W) lebih rendah dari rekomendasi daya aman sistem (${wattageInfo.recommendedPsuWattage}W). Sistem rentan mati mendadak saat beban puncak.`
      });
    }
  }

  // Pengecekan kelengkapan komponen inti
  const coreKeys = ["cpu", "motherboard", "ram", "storage", "psu", "case"];
  const missingCores = coreKeys.filter((k) => !currentBuild[k]);
  const isComplete = missingCores.length === 0;

  // Status Kompatibilitas Keseluruhan
  let compatibilityStatus = {
    status: "incomplete",
    badgeText: "Konfigurasi Belum Lengkap",
    badgeClass: "badge-incomplete",
    icon: "info",
    is100Percent: false,
    isCustomGuaranteed: false
  };

  if (errors.length > 0) {
    compatibilityStatus = {
      status: "incompatible",
      badgeText: "Konfigurasi Tidak Kompatibel",
      badgeClass: "badge-danger",
      icon: "alert-circle",
      is100Percent: false,
      isCustomGuaranteed: false
    };
  } else if (isCustom) {
    compatibilityStatus = {
      status: "custom_warning",
      badgeText: "Kompatibilitas Custom Tidak Dijamin",
      badgeClass: "badge-warning",
      icon: "alert-triangle",
      is100Percent: false,
      isCustomGuaranteed: false
    };
  } else if (isComplete && errors.length === 0) {
    compatibilityStatus = {
      status: "compatible",
      badgeText: "100% Kompatibel",
      badgeClass: "badge-success",
      icon: "check-circle",
      is100Percent: true,
      isCustomGuaranteed: true
    };
  } else {
    compatibilityStatus = {
      status: "progressing",
      badgeText: "Kompatibel Sementara (Belum Lengkap)",
      badgeClass: "badge-neutral",
      icon: "check",
      is100Percent: false,
      isCustomGuaranteed: false
    };
  }

  return {
    errors,
    warnings,
    isCustom,
    isComplete,
    missingCores,
    compatibilityStatus
  };
}

