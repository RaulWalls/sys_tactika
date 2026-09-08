<?php

class JugadorRango extends Model
{
    protected string $table = 'jugador_rango';

    /**
     * Nivel de progresión de un jugador. Si aún no tiene fila, se
     * inicializa en nivel 1 (Novato).
     */
    public function nivelDe(int $jugadorId): int
    {
        $stmt = $this->db->prepare('SELECT nivel FROM jugador_rango WHERE jugador_id = :jugador_id');
        $stmt->execute(['jugador_id' => $jugadorId]);
        $nivel = $stmt->fetchColumn();

        if ($nivel === false) {
            $ins = $this->db->prepare('INSERT INTO jugador_rango (jugador_id, nivel) VALUES (:jugador_id, 1)');
            $ins->execute(['jugador_id' => $jugadorId]);

            return 1;
        }

        return (int) $nivel;
    }
}
