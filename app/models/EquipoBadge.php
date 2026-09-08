<?php

class EquipoBadge extends Model
{
    protected string $table = 'equipo_badges';

    /**
     * Todas las badges activas aplicables a equipos, con el total
     * acumulado por $equipoId (0 si nunca la ha obtenido).
     */
    public function porEquipo(int $equipoId): array
    {
        $sql = "SELECT c.id AS badge_id, c.nombre, c.descripcion, c.icono, c.imagen, c.categoria,
                       c.id_cat_badge, c.condition_type, c.condition_value, c.nivel, c.familia,
                       COALESCE(eb.cantidad, 0) AS cantidad, eb.ultima_obtencion
                FROM cat_badges c
                LEFT JOIN equipo_badges eb ON eb.badge_id = c.id AND eb.equipo_id = :equipo_id
                WHERE c.estatus = 'activo' AND c.aplica_a IN ('equipo', 'ambos')
                ORDER BY FIELD(c.categoria, 'comun', 'raro', 'epico', 'legendario', 'mitico'), c.id_cat_badge ASC, c.nivel ASC, c.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    /**
     * Total de badges vigentes de un equipo.
     */
    public function contarObtenidas(int $equipoId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM equipo_badges WHERE equipo_id = :equipo_id AND cantidad > 0'
        );
        $stmt->execute(['equipo_id' => $equipoId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Badges vigentes de un equipo (más reciente primero), para mostrar
     * su fila de íconos en el dashboard.
     */
    public function obtenidas(int $equipoId, int $limite = 6): array
    {
        $sql = "SELECT c.nombre, c.icono, c.imagen
                FROM equipo_badges eb
                JOIN cat_badges c ON c.id = eb.badge_id
                WHERE eb.equipo_id = :equipo_id AND eb.cantidad > 0
                ORDER BY eb.ultima_obtencion DESC
                LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    /**
     * Los badges de equipo son una "identidad" que puede evolucionar (subir
     * o bajar), no un logro permanente: fija el nivel actualmente vigente
     * de una familia (p. ej. "artilleros") y limpia los demás tiers de esa
     * misma familia para ese equipo.
     */
    public function actualizarFamilia(int $equipoId, string $familia, ?int $badgeIdVigente): void
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM cat_badges WHERE familia = :familia'
        );
        $stmt->execute(['familia' => $familia]);
        $badgeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$badgeIds) {
            return;
        }

        $this->db->beginTransaction();

        try {
            $limpiar = $this->db->prepare(
                'UPDATE equipo_badges SET cantidad = 0 WHERE equipo_id = :equipo_id AND badge_id = :badge_id'
            );
            foreach ($badgeIds as $badgeId) {
                if ($badgeId !== $badgeIdVigente) {
                    $limpiar->execute(['equipo_id' => $equipoId, 'badge_id' => $badgeId]);
                }
            }

            if ($badgeIdVigente !== null) {
                $marcar = $this->db->prepare(
                    "INSERT INTO equipo_badges (equipo_id, badge_id, cantidad, ultima_obtencion)
                     VALUES (:equipo_id, :badge_id, 1, :fecha)
                     ON DUPLICATE KEY UPDATE cantidad = 1, ultima_obtencion = VALUES(ultima_obtencion)"
                );
                $marcar->execute([
                    'equipo_id' => $equipoId,
                    'badge_id' => $badgeIdVigente,
                    'fecha' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
