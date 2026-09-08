<?php

class JugadorBadgeTemporada extends Model
{
    protected string $table = 'jugador_badge_temporadas';

    /**
     * Registra (o mejora) el nivel de una familia de badge alcanzado por un
     * jugador durante una temporada (liga). Solo actualiza si el nuevo
     * nivel es mayor al ya registrado para esa temporada.
     */
    public function registrar(int $jugadorId, int $ligaId, string $familia, int $badgeId, int $nivel): void
    {
        $sql = "INSERT INTO jugador_badge_temporadas (jugador_id, liga_id, familia, badge_id, nivel)
                VALUES (:jugador_id, :liga_id, :familia, :badge_id, :nivel)
                ON DUPLICATE KEY UPDATE
                    badge_id = IF(VALUES(nivel) > nivel, VALUES(badge_id), badge_id),
                    nivel = GREATEST(nivel, VALUES(nivel))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'jugador_id' => $jugadorId,
            'liga_id' => $ligaId,
            'familia' => $familia,
            'badge_id' => $badgeId,
            'nivel' => $nivel,
        ]);
    }

    /**
     * Historial de niveles de badge alcanzados por un jugador, por temporada.
     */
    public function historialDeJugador(int $jugadorId): array
    {
        $sql = "SELECT jbt.*, c.nombre AS badge_nombre, c.icono, c.imagen,
                       l.nombre AS liga_nombre, l.temporada, l.fecha_inicio, l.fecha_fin
                FROM jugador_badge_temporadas jbt
                JOIN cat_badges c ON c.id = jbt.badge_id
                JOIN ligas l ON l.id = jbt.liga_id
                WHERE jbt.jugador_id = :jugador_id
                ORDER BY l.fecha_inicio DESC, jbt.familia ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Historial completo de una liga/temporada: qué jugadores alcanzaron
     * qué nivel de qué familia de badge durante ese periodo.
     */
    public function historialDeLiga(int $ligaId): array
    {
        $sql = "SELECT jbt.*, c.nombre AS badge_nombre, c.icono, c.imagen,
                       j.nombre AS jugador_nombre, j.apellido AS jugador_apellido
                FROM jugador_badge_temporadas jbt
                JOIN cat_badges c ON c.id = jbt.badge_id
                JOIN jugadores j ON j.id = jbt.jugador_id
                WHERE jbt.liga_id = :liga_id
                ORDER BY jbt.familia ASC, jbt.nivel DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['liga_id' => $ligaId]);

        return $stmt->fetchAll();
    }
}
