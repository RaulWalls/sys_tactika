<?php

class EquipoLogro extends Model
{
    protected string $table = 'equipo_logros';

    /**
     * Todos los logros activos aplicables a equipos, con el progreso
     * de $equipoId (0 si aún no ha avanzado en ese logro).
     */
    public function porEquipo(int $equipoId): array
    {
        $sql = "SELECT c.id AS logro_id, c.nombre, c.descripcion, c.icono, c.imagen, c.categoria,
                       c.id_cat_logro, c.condition_type, c.condition_value AS meta,
                       COALESCE(el.progreso, 0) AS progreso, el.desbloqueado_en
                FROM cat_logros c
                LEFT JOIN equipo_logros el ON el.logro_id = c.id AND el.equipo_id = :equipo_id
                WHERE c.estatus = 'activo' AND c.aplica_a IN ('equipo', 'ambos')
                ORDER BY FIELD(c.categoria, 'comun', 'raro', 'epico', 'legendario', 'mitico'), c.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    /**
     * Total de logros desbloqueados por un equipo.
     */
    public function contarDesbloqueados(int $equipoId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM equipo_logros WHERE equipo_id = :equipo_id AND desbloqueado_en IS NOT NULL'
        );
        $stmt->execute(['equipo_id' => $equipoId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Últimos logros desbloqueados por un equipo (más reciente primero).
     */
    public function recientes(int $equipoId, int $limite = 4): array
    {
        $sql = "SELECT c.nombre, c.icono, c.imagen, el.desbloqueado_en
                FROM equipo_logros el
                JOIN cat_logros c ON c.id = el.logro_id
                WHERE el.equipo_id = :equipo_id AND el.desbloqueado_en IS NOT NULL
                ORDER BY el.desbloqueado_en DESC
                LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    /**
     * Guarda el progreso de un equipo en un logro. Si $cumple es true y
     * aún no tenía fecha de desbloqueo, la fija (los logros son
     * permanentes: una vez desbloqueados no se revierten).
     */
    public function registrarProgreso(int $equipoId, int $logroId, int $progreso, bool $cumple): void
    {
        $sql = "INSERT INTO equipo_logros (equipo_id, logro_id, progreso, desbloqueado_en)
                VALUES (:equipo_id, :logro_id, :progreso, :desbloqueado_en)
                ON DUPLICATE KEY UPDATE
                    progreso = GREATEST(progreso, VALUES(progreso)),
                    desbloqueado_en = COALESCE(desbloqueado_en, VALUES(desbloqueado_en))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'equipo_id' => $equipoId,
            'logro_id' => $logroId,
            'progreso' => $progreso,
            'desbloqueado_en' => $cumple ? date('Y-m-d H:i:s') : null,
        ]);
    }
}
