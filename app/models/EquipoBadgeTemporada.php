<?php

class EquipoBadgeTemporada extends Model
{
    protected string $table = 'equipo_badge_temporadas';

    /**
     * Registra (o mejora) el nivel de una familia de badge alcanzado por un
     * equipo durante una temporada (liga). Solo actualiza si el nuevo
     * nivel es mayor al ya registrado para esa temporada.
     */
    public function registrar(int $equipoId, int $ligaId, string $familia, int $badgeId, int $nivel): void
    {
        $sql = "INSERT INTO equipo_badge_temporadas (equipo_id, liga_id, familia, badge_id, nivel)
                VALUES (:equipo_id, :liga_id, :familia, :badge_id, :nivel)
                ON DUPLICATE KEY UPDATE
                    badge_id = IF(VALUES(nivel) > nivel, VALUES(badge_id), badge_id),
                    nivel = GREATEST(nivel, VALUES(nivel))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'equipo_id' => $equipoId,
            'liga_id' => $ligaId,
            'familia' => $familia,
            'badge_id' => $badgeId,
            'nivel' => $nivel,
        ]);
    }

    /**
     * Historial completo de una liga/temporada: qué equipos alcanzaron
     * qué nivel de qué familia de badge durante ese periodo.
     */
    public function historialDeLiga(int $ligaId): array
    {
        $sql = "SELECT ebt.*, c.nombre AS badge_nombre, c.icono, c.imagen,
                       e.nombre AS equipo_nombre
                FROM equipo_badge_temporadas ebt
                JOIN cat_badges c ON c.id = ebt.badge_id
                JOIN equipos e ON e.id = ebt.equipo_id
                WHERE ebt.liga_id = :liga_id
                ORDER BY ebt.familia ASC, ebt.nivel DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['liga_id' => $ligaId]);

        return $stmt->fetchAll();
    }
}
