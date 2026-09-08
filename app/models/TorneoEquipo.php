<?php

class TorneoEquipo extends Model
{
    protected string $table = 'torneo_equipos';

    /**
     * Equipos participantes de un torneo, ordenados por semilla (posición
     * en la tabla de la liga al momento de generar el torneo).
     */
    public function porTorneo(int $torneoId): array
    {
        $sql = "SELECT te.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo
                FROM torneo_equipos te
                JOIN equipos e ON e.id = te.equipo_id
                WHERE te.torneo_id = :torneo_id
                ORDER BY te.semilla ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['torneo_id' => $torneoId]);

        return $stmt->fetchAll();
    }
}
