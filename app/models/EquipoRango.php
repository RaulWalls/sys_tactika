<?php

class EquipoRango extends Model
{
    protected string $table = 'equipo_rango';

    /**
     * Nivel de progresión de un equipo. Si aún no tiene fila, se
     * inicializa en nivel 1 (Novato).
     */
    public function nivelDe(int $equipoId): int
    {
        $stmt = $this->db->prepare('SELECT nivel FROM equipo_rango WHERE equipo_id = :equipo_id');
        $stmt->execute(['equipo_id' => $equipoId]);
        $nivel = $stmt->fetchColumn();

        if ($nivel === false) {
            $ins = $this->db->prepare('INSERT INTO equipo_rango (equipo_id, nivel) VALUES (:equipo_id, 1)');
            $ins->execute(['equipo_id' => $equipoId]);

            return 1;
        }

        return (int) $nivel;
    }
}
