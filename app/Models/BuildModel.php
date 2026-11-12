<?php
/**
 * Model Rakitan PC (`pc_builds` & `build_items`)
 * Menggunakan PDO Prepared Statements dan Database Transactions.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

class BuildModel
{
    public const ALLOWED_PURPOSES = [
        'Gaming',
        'Produktivitas',
        'Editing & 3D',
        'Kantor / Umum',
        'Lainnya',
    ];

    /**
     * Mengambil daftar seluruh rakitan PC beserta jumlah komponennya.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(string $search = '', string $purpose = ''): array
    {
        $pdo = Database::requireConnection();
        $sql = 'SELECT b.*, COUNT(bi.id) AS items_count
                FROM pc_builds b
                LEFT JOIN build_items bi ON bi.build_id = b.id
                WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (b.build_name LIKE :search_build OR b.builder_name LIKE :search_builder)';
            $like = '%' . $search . '%';
            $params[':search_build'] = $like;
            $params[':search_builder'] = $like;
        }

        if ($purpose !== '' && in_array($purpose, self::ALLOWED_PURPOSES, true)) {
            $sql .= ' AND b.purpose = :purpose';
            $params[':purpose'] = $purpose;
        }

        $sql .= ' GROUP BY b.id ORDER BY b.updated_at DESC, b.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil detail satu rakitan PC beserta daftar komponen (`build_items` JOIN `components`).
     *
     * @return array<string, mixed>|null
     */
    public function findWithItems(int $id): ?array
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('SELECT * FROM pc_builds WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $build = $stmt->fetch();

        if (!$build) {
            return null;
        }

        $itemsStmt = $pdo->prepare(
            'SELECT bi.id AS item_id, bi.category, bi.custom_price,
                    c.id AS component_id, c.slug, c.name, c.base_price, c.socket,
                    c.tdp, c.has_igpu, c.form_factor, c.ram_type, c.capacity_gb,
                    c.speed_mhz, c.wattage, c.supported_form_factors, c.specs_summary, c.is_custom
             FROM build_items bi
             INNER JOIN components c ON c.id = bi.component_id
             WHERE bi.build_id = :build_id
             ORDER BY FIELD(bi.category, "cpu", "motherboard", "ram", "gpu", "storage", "psu", "case")'
        );
        $itemsStmt->execute([':build_id' => $id]);

        $itemsByCategory = [];
        foreach ($itemsStmt->fetchAll() as $item) {
            $itemsByCategory[$item['category']] = $item;
        }

        $build['items'] = $itemsByCategory;
        return $build;
    }

    /**
     * Menyimpan rakitan PC baru beserta relasi komponennya dalam satu transaksi atomik (CREATE).
     *
     * @param array<string, mixed> $buildData
     * @param array<string, array{component_id: int, custom_price: ?int}> $items
     */
    public function createWithItems(array $buildData, array $items): int
    {
        $pdo = Database::requireConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO pc_builds (
                    build_name, builder_name, purpose, notes,
                    total_price, total_wattage, recommended_psu, compatibility_status
                ) VALUES (
                    :build_name, :builder_name, :purpose, :notes,
                    :total_price, :total_wattage, :recommended_psu, :compatibility_status
                )'
            );

            $stmt->execute([
                ':build_name'           => $buildData['build_name'],
                ':builder_name'         => $buildData['builder_name'],
                ':purpose'              => $buildData['purpose'],
                ':notes'                => $buildData['notes'] ?: null,
                ':total_price'          => (int) $buildData['total_price'],
                ':total_wattage'        => (int) $buildData['total_wattage'],
                ':recommended_psu'      => (int) $buildData['recommended_psu'],
                ':compatibility_status' => $buildData['compatibility_status'],
            ]);

            $buildId = (int) $pdo->lastInsertId();
            $this->insertItems($pdo, $buildId, $items);

            $pdo->commit();
            return $buildId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Memperbarui rakitan PC beserta daftar komponennya secara atomik (UPDATE).
     *
     * @param array<string, mixed> $buildData
     * @param array<string, array{component_id: int, custom_price: ?int}> $items
     */
    public function updateWithItems(int $id, array $buildData, array $items): bool
    {
        $pdo = Database::requireConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'UPDATE pc_builds SET
                    build_name = :build_name,
                    builder_name = :builder_name,
                    purpose = :purpose,
                    notes = :notes,
                    total_price = :total_price,
                    total_wattage = :total_wattage,
                    recommended_psu = :recommended_psu,
                    compatibility_status = :compatibility_status
                 WHERE id = :id'
            );

            $stmt->execute([
                ':id'                   => $id,
                ':build_name'           => $buildData['build_name'],
                ':builder_name'         => $buildData['builder_name'],
                ':purpose'              => $buildData['purpose'],
                ':notes'                => $buildData['notes'] ?: null,
                ':total_price'          => (int) $buildData['total_price'],
                ':total_wattage'        => (int) $buildData['total_wattage'],
                ':recommended_psu'      => (int) $buildData['recommended_psu'],
                ':compatibility_status' => $buildData['compatibility_status'],
            ]);

            $delStmt = $pdo->prepare('DELETE FROM build_items WHERE build_id = :build_id');
            $delStmt->execute([':build_id' => $id]);

            $this->insertItems($pdo, $id, $items);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Menghapus rakitan PC (DELETE - cascade otomatis menghapus build_items).
     */
    public function delete(int $id): bool
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->prepare('DELETE FROM pc_builds WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countTotal(): int
    {
        $pdo = Database::requireConnection();
        $stmt = $pdo->query('SELECT COUNT(*) FROM pc_builds');
        return (int) $stmt->fetchColumn();
    }

    /**
     * @param array<string, array{component_id: int, custom_price: ?int}> $items
     */
    private function insertItems(\PDO $pdo, int $buildId, array $items): void
    {
        $itemStmt = $pdo->prepare(
            'INSERT INTO build_items (build_id, component_id, category, custom_price)
             VALUES (:build_id, :component_id, :category, :custom_price)'
        );

        foreach ($items as $category => $item) {
            $itemStmt->execute([
                ':build_id'     => $buildId,
                ':component_id' => (int) $item['component_id'],
                ':category'     => $category,
                ':custom_price' => $item['custom_price'],
            ]);
        }
    }
}

