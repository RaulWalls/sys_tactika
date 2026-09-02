<?php

class JugadorAtributo extends Model
{
    protected string $table = 'jugador_atributos';

    /**
     * Todas las estadísticas del catálogo para un jugador, con su valor
     * actual (0 si aún no se ha registrado).
     */
    public function porJugador(int $jugadorId): array
    {
        $sql = "SELECT c.id AS atributo_id, c.nombre, c.abreviatura,
                       COALESCE(je.valor, 0) AS valor
                FROM cat_atributos c
                LEFT JOIN jugador_atributos je
                    ON je.atributo_id = c.id AND je.jugador_id = :jugador_id
                ORDER BY c.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Guarda (crea o actualiza) los valores de un jugador para cada
     * estadística: ['estadistica_id' => valor].
     */
    public function guardar(int $jugadorId, array $valores): void
    {
        $sql = "INSERT INTO jugador_atributos (jugador_id, atributo_id, valor)
                VALUES (:jugador_id, :atributo_id, :valor)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)";

        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();

        try {
            foreach ($valores as $estadisticaId => $valor) {
                $stmt->execute([
                    'jugador_id' => $jugadorId,
                    'atributo_id' => (int) $estadisticaId,
                    'valor' => (int) $valor,
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
