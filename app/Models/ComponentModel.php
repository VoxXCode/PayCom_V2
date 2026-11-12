<?php
/**
 * Model Katalog Komponen Hardware (`components`)
 * Menggunakan PDO & Prepared Statements untuk seluruh operasi database.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class ComponentModel
{
    public const ALLOWED_TYPES = ['cpu', 'motherboard', 'ram', 'gpu', 'storage', 'psu', 'case'];

    public const TYPE_LABELS = [
        'cpu'         => 'CPU (Prosesor)',
        'motherboard' => 'Motherboard',
        'ram'         => 'RAM (Memori)',
        'gpu'         => 'GPU (Kartu Grafis)',
        'storage'     => 'Storage (SSD/HDD)',
        'psu'         => 'Power Supply (PSU)',
        'case'        => 'Casing Komputer',
    ];

    /**
     * Mengambil daftar komponen dengan filter kategori & pencarian nama/spesifikasi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(?string $type = null, string $search = ''): array
    {
        $pdo = Database::requireConnection();
        $sql = 'SELECT * FROM components WHERE 1=1';
        $params = [];

        if ($type !== null && $type !== '' && in_array($type, self::ALLOWED_TYPES, true)) {
            $sql .= ' AND type = :type';
            $params[':type'] = $type;
        }

        if ($search !== '') {
            $sql .= ' AND (name LIKE :search_name OR specs_summary LIKE :search_specs OR socket LIKE :search_socket)';
            $like = '%' . $search . '%';
            $params[':search_name'] = $like;
            $params[':search_specs'] = $like;
            $params[':search_socket'] = $like;
        }

        $sql .= ' ORDER BY FIELD(type, "cpu", "motherboard", "ram", "gpu", "storage", "psu", "case"), base_price ASC, id ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil seluruh komponen yang dikelompokkan per kategori (untuk Konfigurator JS).
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getGroupedByType(): array
    {
        $grouped = [];
        foreach (self::ALLOWED_TYPES as $type) {
            $grouped[$type] = [];
        }

        foreach ($this->getAll() as $row) {
            $type = $row['type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }

            $grouped[$type][] = [
                'id'                     => $row['slug'],
                'db_id'                  => (int) $row['id'],
                'type'                   => $row['type'],
                'name'                   => $row['name'],
                'base_price'             => (int) $row['base_price'],
                'socket'                 => $row['socket'],
                'tdp'                    => (int) $row['tdp'],
                'has_igpu'               => (bool) $row['has_igpu'],
                'form_factor'            => $row['form_factor'],
                'ram_type'               => $row['ram_type'],
                'capacity_gb'            => $row['capacity_gb'] !== null ? (int) $row['capacity_gb'] : null,
                'speed_mhz'              => $row['speed_mhz'] !== null ? (int) $row['speed_mhz'] : null,
                'wattage'                => $row['wattage'] !== null ? (int) $row['wattage'] : null,
                'supported_form_factors' => $row['supported_form_factors']
                    ? array_map('trim', explode(',', (string) $row['supported_form_factors']))
                    : null,
                'specs_summary'          => $row['specs_summary'],
                'is_custom'              => (bool) $row['is_custom'],
            ];
        }

        return $grouped;
    }

    /**
     * Mencari satu komponen berdasarkan ID integer.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('SELECT * FROM components WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Mencari satu komponen berdasarkan slug unik.
     *
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('SELECT * FROM components WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Mengambil banyak komponen berdasarkan daftar ID.
     *
     * @param array<int, int> $ids
     * @return array<int, array<string, mixed>> Terindeks oleh ID komponen
     */
    public function findManyByIds(array $ids): array
    {
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
        if (empty($cleanIds)) {
            return [];
        }

        $pdo = Database::requireConnection();
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM components WHERE id IN ($placeholders)");
        $stmt->execute($cleanIds);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int) $row['id']] = $row;
        }
        return $result;
    }

    /**
     * Menyimpan komponen baru (CREATE).
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO components (
                slug, type, name, base_price, socket, tdp, has_igpu,
                form_factor, ram_type, capacity_gb, speed_mhz, wattage,
                supported_form_factors, specs_summary, is_custom
            ) VALUES (
                :slug, :type, :name, :base_price, :socket, :tdp, :has_igpu,
                :form_factor, :ram_type, :capacity_gb, :speed_mhz, :wattage,
                :supported_form_factors, :specs_summary, :is_custom
            )'
        );

        $stmt->execute([
            ':slug'                   => $data['slug'],
            ':type'                   => $data['type'],
            ':name'                   => $data['name'],
            ':base_price'             => (int) $data['base_price'],
            ':socket'                 => $data['socket'] ?: null,
            ':tdp'                    => (int) ($data['tdp'] ?? 0),
            ':has_igpu'               => !empty($data['has_igpu']) ? 1 : 0,
            ':form_factor'            => $data['form_factor'] ?: null,
            ':ram_type'               => $data['ram_type'] ?: null,
            ':capacity_gb'            => isset($data['capacity_gb']) && $data['capacity_gb'] !== '' ? (int) $data['capacity_gb'] : null,
            ':speed_mhz'              => isset($data['speed_mhz']) && $data['speed_mhz'] !== '' ? (int) $data['speed_mhz'] : null,
            ':wattage'                => isset($data['wattage']) && $data['wattage'] !== '' ? (int) $data['wattage'] : null,
            ':supported_form_factors' => $data['supported_form_factors'] ?: null,
            ':specs_summary'          => $data['specs_summary'],
            ':is_custom'              => !empty($data['is_custom']) ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Memperbarui data komponen (UPDATE).
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare(
            'UPDATE components SET
                type = :type,
                name = :name,
                base_price = :base_price,
                socket = :socket,
                tdp = :tdp,
                has_igpu = :has_igpu,
                form_factor = :form_factor,
                ram_type = :ram_type,
                capacity_gb = :capacity_gb,
                speed_mhz = :speed_mhz,
                wattage = :wattage,
                supported_form_factors = :supported_form_factors,
                specs_summary = :specs_summary
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id'                     => $id,
            ':type'                   => $data['type'],
            ':name'                   => $data['name'],
            ':base_price'             => (int) $data['base_price'],
            ':socket'                 => $data['socket'] ?: null,
            ':tdp'                    => (int) ($data['tdp'] ?? 0),
            ':has_igpu'               => !empty($data['has_igpu']) ? 1 : 0,
            ':form_factor'            => $data['form_factor'] ?: null,
            ':ram_type'               => $data['ram_type'] ?: null,
            ':capacity_gb'            => isset($data['capacity_gb']) && $data['capacity_gb'] !== '' ? (int) $data['capacity_gb'] : null,
            ':speed_mhz'              => isset($data['speed_mhz']) && $data['speed_mhz'] !== '' ? (int) $data['speed_mhz'] : null,
            ':wattage'                => isset($data['wattage']) && $data['wattage'] !== '' ? (int) $data['wattage'] : null,
            ':supported_form_factors' => $data['supported_form_factors'] ?: null,
            ':specs_summary'          => $data['specs_summary'],
        ]);
    }

    /**
     * Mengecek apakah komponen masih dipakai di salah satu rakitan (`build_items`).
     */
    public function isUsedInBuilds(int $id): bool
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM build_items WHERE component_id = :id');
        $stmt->execute([':id' => $id]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Menghapus komponen dari database (DELETE).
     */
    public function delete(int $id): bool
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('DELETE FROM components WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countTotal(): int
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->query('SELECT COUNT(*) FROM components');
        return (int) $stmt->fetchColumn();
    }

    /**
     * Membuat slug unik untuk komponen baru.
     */
    public function generateUniqueSlug(string $type, string $name): string
    {
        $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name) ?? '', '-'));
        $slug = $type . '-' . ($base !== '' ? substr($base, 0, 60) : 'item');

        $candidate = $slug;
        $counter = 1;
        while ($this->findBySlug($candidate) !== null) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }
        return $candidate;
    }
}

