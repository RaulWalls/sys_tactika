<?php

class Jugador extends Model
{
    protected string $table = 'jugadores';

    public function porEquipo(int $equipoId): array
    {
        $sql = "SELECT j.*, p.nombre AS posicion_nombre, p.tag AS posicion_tag
                FROM jugadores j
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                WHERE j.equipo_id = :equipo_id
                ORDER BY j.numero ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    public function conDetalle(int $id): ?array
    {
        $sql = "SELECT j.*, e.nombre AS equipo_nombre, p.nombre AS posicion_nombre, p.tag AS posicion_tag
                FROM jugadores j
                LEFT JOIN equipos e ON e.id = j.equipo_id
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                WHERE j.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function conEquipo(): array
    {
        $sql = "SELECT j.*, e.nombre AS equipo_nombre, p.nombre AS posicion_nombre, p.tag AS posicion_tag
                FROM jugadores j
                LEFT JOIN equipos e ON e.id = j.equipo_id
                LEFT JOIN cat_posiciones p ON p.id = j.posicion_id
                ORDER BY j.apellido ASC";

        return $this->db->query($sql)->fetchAll();
    }
}
