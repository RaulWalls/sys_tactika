<?php

class JugadorBadge extends Model
{
    protected string $table = 'jugador_badges';

    /**
     * Todas las badges activas aplicables a jugadores, con el total
     * acumulado por $jugadorId (0 si nunca la ha obtenido).
     */
    public function porJugador(int $jugadorId): array
    {
        $sql = "SELECT c.id AS badge_id, c.nombre, c.descripcion, c.icono, c.imagen, c.categoria,
                       c.id_cat_badge, c.condition_type, c.condition_value, c.nivel, c.familia,
                       COALESCE(jb.cantidad, 0) AS cantidad, jb.ultima_obtencion
                FROM cat_badges c
                LEFT JOIN jugador_badges jb ON jb.badge_id = c.id AND jb.jugador_id = :jugador_id
                WHERE c.estatus = 'activo' AND c.aplica_a IN ('jugador', 'ambos')
                ORDER BY FIELD(c.categoria, 'comun', 'raro', 'epico', 'legendario', 'mitico'), c.id_cat_badge ASC, c.nivel ASC, c.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Total de badges obtenidas por un jugador.
     */
    public function contarObtenidas(int $jugadorId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM jugador_badges WHERE jugador_id = :jugador_id AND cantidad > 0'
        );
        $stmt->execute(['jugador_id' => $jugadorId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Badges obtenidas por un jugador (más reciente primero), para
     * mostrar su fila de íconos en el dashboard.
     */
    public function obtenidas(int $jugadorId, int $limite = 6): array
    {
        $sql = "SELECT c.nombre, c.icono, c.imagen
                FROM jugador_badges jb
                JOIN cat_badges c ON c.id = jb.badge_id
                WHERE jb.jugador_id = :jugador_id AND jb.cantidad > 0
                ORDER BY jb.ultima_obtencion DESC
                LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Marca una badge (tier) como obtenida por un jugador.
     */
    public function registrarObtenida(int $jugadorId, int $badgeId): void
    {
        $sql = "INSERT INTO jugador_badges (jugador_id, badge_id, cantidad, ultima_obtencion)
                VALUES (:jugador_id, :badge_id, 1, :fecha)
                ON DUPLICATE KEY UPDATE
                    cantidad = 1,
                    ultima_obtencion = COALESCE(ultima_obtencion, VALUES(ultima_obtencion))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'jugador_id' => $jugadorId,
            'badge_id' => $badgeId,
            'fecha' => date('Y-m-d H:i:s'),
        ]);
    }
}
