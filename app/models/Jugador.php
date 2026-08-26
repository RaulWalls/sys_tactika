<?php

class Jugador extends Model
{
    protected string $table = 'jugadores';

    public function porEquipo(int $equipoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM jugadores WHERE equipo_id = :equipo_id ORDER BY numero ASC');
        $stmt->execute(['equipo_id' => $equipoId]);

        return $stmt->fetchAll();
    }

    public function conEquipo(): array
    {
        $sql = "SELECT j.*, e.nombre AS equipo_nombre
                FROM jugadores j
                LEFT JOIN equipos e ON e.id = j.equipo_id
                ORDER BY j.apellido ASC";

        return $this->db->query($sql)->fetchAll();
    }
}
