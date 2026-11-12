<?php
/**
 * Service Kompatibilitas, Kalkulasi Wattage, & Estimasi Harga (Server-Side)
 * Menyinkronkan aturan bisnis FR1-FR4 pada sisi backend PHP.
 */

declare(strict_types=1);

namespace App\Core;

class CompatibilityService
{
    public const CATEGORIES = ['cpu', 'motherboard', 'ram', 'gpu', 'storage', 'psu', 'case'];

    /**
     * Menghitung ringkasan daya, total harga, dan status kompatibilitas rakitan.
     *
     * @param array<string, array<string, mixed>> $selectedByCategory
     * @return array{
     *   total_price: int,
     *   total_wattage: int,
     *   recommended_psu: int,
     *   compatibility_status: string,
     *   errors: array<int, string>,
     *   warnings: array<int, string>
     * }
     */
    public static function evaluate(array $selectedByCategory): array
    {
        $totalPrice = 0;
        $cpuTdp = 0;
        $gpuTdp = 0;
        $hasComponent = false;
        $hasCustom = false;

        foreach ($selectedByCategory as $cat => $item) {
            if (!is_array($item)) {
                continue;
            }
            $hasComponent = true;
            $price = isset($item['custom_price']) && $item['custom_price'] !== null
                ? (int) $item['custom_price']
                : (int) ($item['base_price'] ?? 0);
            $totalPrice += max(0, $price);

            if (!empty($item['is_custom'])) {
                $hasCustom = true;
            }
            if ($cat === 'cpu') {
                $cpuTdp = (int) ($item['tdp'] ?? 0);
            }
            if ($cat === 'gpu') {
                $gpuTdp = (int) ($item['tdp'] ?? 0);
            }
        }

        $totalWattage = $hasComponent ? ($cpuTdp + $gpuTdp + 50) : 0;
        $recommendedPsu = $totalWattage > 0
            ? (int) (ceil(($totalWattage * 1.5) / 50) * 50)
            : 0;

        $errors = [];
        $warnings = [];

        $cpu  = $selectedByCategory['cpu'] ?? null;
        $mobo = $selectedByCategory['motherboard'] ?? null;
        $ram  = $selectedByCategory['ram'] ?? null;
        $gpu  = $selectedByCategory['gpu'] ?? null;
        $psu  = $selectedByCategory['psu'] ?? null;
        $case = $selectedByCategory['case'] ?? null;

        // 1. Socket CPU vs Motherboard
        if ($cpu && $mobo && ($cpu['socket'] ?? '') !== ($mobo['socket'] ?? '')) {
            $errors[] = sprintf(
                'Ketidakcocokan Socket: CPU %s tidak cocok dengan Motherboard %s.',
                $cpu['socket'] ?? '-',
                $mobo['socket'] ?? '-'
            );
        }

        // 2. Tipe RAM vs Motherboard
        if ($mobo && $ram && ($mobo['ram_type'] ?? '') !== ($ram['ram_type'] ?? '')) {
            $errors[] = sprintf(
                'Tipe RAM Tidak Cocok: Motherboard %s vs RAM %s.',
                $mobo['ram_type'] ?? '-',
                $ram['ram_type'] ?? '-'
            );
        }

        // 3. Form Factor Motherboard vs Casing
        if ($mobo && $case && !empty($case['supported_form_factors'])) {
            $supported = array_map('trim', explode(',', (string) $case['supported_form_factors']));
            if (!in_array((string) ($mobo['form_factor'] ?? ''), $supported, true)) {
                $errors[] = sprintf(
                    'Ukuran Motherboard (%s) melebihi kapasitas Casing (%s).',
                    $mobo['form_factor'] ?? '-',
                    implode(', ', $supported)
                );
            }
        }

        // 4. CPU tanpa iGPU dan tanpa VGA diskrit
        if ($cpu) {
            $hasExternalGpu = $gpu && ($gpu['slug'] ?? '') !== 'gpu-none' && (int) ($gpu['base_price'] ?? 0) > 0;
            if ((int) ($cpu['has_igpu'] ?? 0) === 0 && !$hasExternalGpu) {
                $errors[] = 'PC tidak akan menampilkan gambar tanpa VGA tambahan (CPU tidak memiliki iGPU).';
            }
        }

        // 5. Kapasitas PSU vs Rekomendasi Minimum
        if ($psu && $recommendedPsu > 0) {
            $psuWatt = (int) ($psu['wattage'] ?? 0);
            if ($psuWatt > 0 && $psuWatt < $recommendedPsu) {
                $warnings[] = sprintf(
                    'Kapasitas PSU (%dW) di bawah rekomendasi aman (%dW).',
                    $psuWatt,
                    $recommendedPsu
                );
            }
        }

        // Menentukan label status akhir
        $allCoreSelected = isset($cpu, $mobo, $ram, $selectedByCategory['storage'], $psu, $case);
        if (!$hasComponent) {
            $status = 'Konfigurasi Belum Lengkap';
        } elseif (!empty($errors)) {
            $status = 'Tidak Kompatibel';
        } elseif ($hasCustom) {
            $status = 'Kompatibilitas Custom Tidak Dijamin';
        } elseif ($allCoreSelected) {
            $status = '100% Kompatibel';
        } else {
            $status = 'Dalam Progres (Kompatibel Sejauh Ini)';
        }

        return [
            'total_price'          => $totalPrice,
            'total_wattage'        => $totalWattage,
            'recommended_psu'      => $recommendedPsu,
            'compatibility_status' => $status,
            'errors'               => $errors,
            'warnings'             => $warnings,
        ];
    }
}

