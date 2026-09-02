<?php

class JugadorAmonestaciones extends Model
{
    protected string $table = 'jugador_amonestacion';

    /**
     * Total de amonestaciones de un jugador por tipo (0 si no tiene ninguna).
     */
    public function porJugador(int $jugadorId): array
    {
        $sql = "SELECT c.amonestacion, COALESCE(SUM(ja.valor), 0) AS total
                FROM cat_amonestaciones c
                LEFT JOIN jugador_amonestacion ja
                    ON ja.id_amonestacion = c.id AND ja.id_jugador = :jugador_id
                GROUP BY c.id, c.amonestacion";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        $resultado = [
            'tarjeta_amarilla' => 0,
            'tarjeta_roja' => 0,
        ];

        foreach ($stmt->fetchAll() as $fila) {
            $nombre = mb_strtolower($fila['amonestacion']);

            if (str_contains($nombre, 'amarilla')) {
                $resultado['tarjeta_amarilla'] = (int) $fila['total'];
            } elseif (str_contains($nombre, 'roja')) {
                $resultado['tarjeta_roja'] = (int) $fila['total'];
            }
        }

        return $resultado;
    }
}