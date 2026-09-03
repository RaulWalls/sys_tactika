<?php

class Jugador extends Model
{
    protected string $table = 'jugadores';

    public function porEquipo(int $equipoId): array
    {
        $sql = "SELECT j.*, p.nombre AS posicion_nombre, p.tag AS posicion_tag
                FROM jugadores j
                JOIN jugador_equipos je ON je.jugador_id = j.id
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                WHERE je.equipo_id = :equipo_id
                ORDER BY j.numero ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    public function conDetalle(int $id): ?array
    {
        $sql = "SELECT j.*, p.nombre AS posicion_nombre, p.tag AS posicion_tag
                FROM jugadores j
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                WHERE j.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $row['equipos'] = $this->equiposDe($id);

        return $row;
    }

    /**
     * Lista de jugadores con sus equipos (nombres concatenados).
     * Si se pasa $equipoIds, solo incluye jugadores que pertenezcan a
     * alguno de esos equipos (para el acotado por rol).
     */
    public function conEquipo(?array $equipoIds = null): array
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return [];
        }

        $sql = "SELECT j.*, p.nombre AS posicion_nombre, p.tag AS posicion_tag,
                       GROUP_CONCAT(DISTINCT e.nombre ORDER BY e.nombre SEPARATOR ', ') AS equipos_nombres
                FROM jugadores j
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                LEFT JOIN jugador_equipos je ON je.jugador_id = j.id
                LEFT JOIN equipos e ON e.id = je.equipo_id";

        $params = [];

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $sql .= " WHERE j.id IN (SELECT jugador_id FROM jugador_equipos WHERE equipo_id IN ({$in}))";
            $params = array_values($equipoIds);
        }

        $sql .= " GROUP BY j.id ORDER BY j.apellido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function equiposDe(int $jugadorId): array
    {
        $sql = "SELECT e.id, e.nombre, e.escudo
                FROM equipos e
                JOIN jugador_equipos je ON je.equipo_id = e.id
                WHERE je.jugador_id = :jugador_id
                ORDER BY e.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    public function perteneceAEquipo(int $jugadorId, int $equipoId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM jugador_equipos WHERE jugador_id = :jugador_id AND equipo_id = :equipo_id LIMIT 1');
        $stmt->execute(['jugador_id' => $jugadorId, 'equipo_id' => $equipoId]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Reemplaza los equipos a los que pertenece un jugador.
     */
    public function guardarEquipos(int $jugadorId, array $equipoIds): void
    {
        $this->db->beginTransaction();

        try {
            $borrar = $this->db->prepare('DELETE FROM jugador_equipos WHERE jugador_id = :jugador_id');
            $borrar->execute(['jugador_id' => $jugadorId]);

            $insertar = $this->db->prepare('INSERT INTO jugador_equipos (jugador_id, equipo_id) VALUES (:jugador_id, :equipo_id)');

            foreach (array_unique(array_map('intval', $equipoIds)) as $equipoId) {
                if ($equipoId > 0) {
                    $insertar->execute(['jugador_id' => $jugadorId, 'equipo_id' => $equipoId]);
                }
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
