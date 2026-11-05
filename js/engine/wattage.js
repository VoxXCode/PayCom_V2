// FR3 - Wattage Calculator:
// Total Wattage = CPU TDP + GPU TDP + Baseline 50W (Mobo/Storage/Fan)
// Rekomendasi PSU minimum = (Total Wattage * 1.5)

export function calculateWattage(currentBuild) {
  const cpuTdp = currentBuild.cpu && currentBuild.cpu.tdp ? Number(currentBuild.cpu.tdp) : 0;
  const gpuTdp = currentBuild.gpu && currentBuild.gpu.tdp ? Number(currentBuild.gpu.tdp) : 0;
  const baselineTdp = 50; // Baseline untuk Motherboard, Storage, dan Kipas Casing

  // Jika belum ada komponen bertenaga yang dipilih, total wattage 0
  if (!currentBuild.cpu && !currentBuild.gpu) {
    return {
      cpuTdp: 0,
      gpuTdp: 0,
      baselineTdp: 0,
      totalWattage: 0,
      recommendedPsuWattage: 0,
      exactRecommended: 0
    };
  }

  const totalWattage = cpuTdp + gpuTdp + baselineTdp;
  const exactRecommended = Math.round(totalWattage * 1.5);

  // Bulatkan rekomendasi ke kelipatan 50W terdekat ke atas untuk ketersediaan PSU standar di pasaran (e.g. 500W, 550W, 650W)
  const roundedRecommended = Math.max(450, Math.ceil(exactRecommended / 50) * 50);

  return {
    cpuTdp,
    gpuTdp,
    baselineTdp,
    totalWattage,
    recommendedPsuWattage: roundedRecommended,
    exactRecommended
  };
}

