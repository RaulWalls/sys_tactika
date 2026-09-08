<?php

class JugadorLogro extends Model
{
    protected string $table = 'jugador_logros';

    /**
     * Todos los logros activos aplicables a jugadores, con el progreso
     * de $jugadorId (0 si aún no ha avanzado en ese logro).
     */
    public function porJugador(int $jugadorId): array
    {
        $sql = "SELECT c.id AS logro_id, c.nombre, c.descripcion, c.icono, c.imagen, c.categoria,
                       c.id_cat_logro, c.condition_type, c.condition_value AS meta,
                       COALESCE(jl.progreso, 0) AS progreso, jl.desbloqueado_en
                FROM cat_logros c
                LEFT JOIN jugador_logros jl ON jl.logro_id = c.id AND jl.jugador_id = :jugador_id
                WHERE c.estatus = 'activo' AND c.aplica_a IN ('jugador', 'ambos')
                ORDER BY FIELD(c.categoria, 'comun', 'raro', 'epico', 'legendario', 'mitico'), c.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Total de logros desbloqueados por un jugador.
     */
    public function contarDesbloqueados(int $jugadorId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM jugador_logros WHERE jugador_id = :jugador_id AND desbloqueado_en IS NOT NULL'
        );
        $stmt->execute(['jugador_id' => $jugadorId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Últimos logros desbloqueados por un jugador (más reciente primero).
     */
    public function recientes(int $jugadorId, int $limite = 4): array
    {
        $sql = "SELECT c.nombre, c.icono, c.imagen, jl.desbloqueado_en
                FROM jugador_logros jl
                JOIN cat_logros c ON c.id = jl.logro_id
                WHERE jl.jugador_id = :jugador_id AND jl.desbloqueado_en IS NOT NULL
                ORDER BY jl.desbloqueado_en DESC
                LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Guarda el progreso de un jugador en un logro. Si $cumple es true y
     * aún no tenía fecha de desbloqueo, la fija (los logros son
     * permanentes: una vez desbloqueados no se revierten).
     */
    public function registrarProgreso(int $jugadorId, int $logroId, int $progreso, bool $cumple): void
    {
        $sql = "INSERT INTO jugador_logros (jugador_id, logro_id, progreso, desbloqueado_en)
                VALUES (:jugador_id, :logro_id, :progreso, :desbloqueado_en)
                ON DUPLICATE KEY UPDATE
                    progreso = GREATEST(progreso, VALUES(progreso)),
                    desbloqueado_en = COALESCE(desbloqueado_en, VALUES(desbloqueado_en))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'jugador_id' => $jugadorId,
            'logro_id' => $logroId,
            'progreso' => $progreso,
            'desbloqueado_en' => $cumple ? date('Y-m-d H:i:s') : null,
        ]);
    }
}
