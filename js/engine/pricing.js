// FR2 - Live Price Integration & Failure State 1
// Menangani format mata uang Rupiah, kalkulasi total estimasi, dan penanganan gangguan API

let apiStatus = "online"; // 'online' | 'down'
let manualPriceOverrides = {};

export function setApiStatus(status) {
  apiStatus = status;
}

export function getApiStatus() {
  return apiStatus;
}

export function setManualPrice(componentType, price) {
  manualPriceOverrides[componentType] = Math.max(0, parseInt(price, 10) || 0);
}

export function getManualPrice(componentType) {
  return manualPriceOverrides[componentType];
}

export function clearManualPrice(componentType) {
  delete manualPriceOverrides[componentType];
}

export function formatRupiah(amount) {
  if (amount === null || amount === undefined || isNaN(amount)) {
    return "Rp 0";
  }
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0
  }).format(amount);
}

// Mengambil estimasi harga untuk komponen tertentu dengan memperhitungkan status API & manual override
export function getComponentPriceInfo(type, component) {
  if (!component) {
    return {
      price: 0,
      formatted: "-",
      isManual: false,
      isApiDown: apiStatus === "down"
    };
  }

  // Jika user menginput override manual
  if (manualPriceOverrides[type] !== undefined) {
    const val = manualPriceOverrides[type];
    return {
      price: val,
      formatted: `Est. ${formatRupiah(val)} (Manual)`,
      isManual: true,
      isApiDown: apiStatus === "down"
    };
  }

  // Jika API sedang down (Failure State 1)
  if (apiStatus === "down") {
    return {
      price: 0,
      formatted: "Est. Rp - (Isi Manual)",
      isManual: true,
      isApiDown: true
    };
  }

  // Kondisi normal: harga estimasi dari data komponen
  const price = component.base_price || 0;
  return {
    price,
    formatted: `Est. ${formatRupiah(price)}`,
    isManual: false,
    isApiDown: false
  };
}

// Menghitung total harga seluruh build
export function calculateTotalPrice(currentBuild) {
  let total = 0;
  let hasManualPending = false;

  Object.entries(currentBuild).forEach(([type, comp]) => {
    if (comp) {
      const priceInfo = getComponentPriceInfo(type, comp);
      if (priceInfo.isApiDown && manualPriceOverrides[type] === undefined) {
        hasManualPending = true;
      }
      total += priceInfo.price;
    }
  });

  return {
    total,
    formattedTotal: `Est. ${formatRupiah(total)}`,
    hasManualPending,
    isApiDown: apiStatus === "down"
  };
}

