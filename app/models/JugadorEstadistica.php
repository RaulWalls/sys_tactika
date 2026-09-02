<?php

class JugadorEstadistica extends Model
{
    protected string $table = 'jugador_estadisticas';

    /**
     * Todas las estadísticas de rendimiento del catálogo para un jugador,
     * con su valor actual (0 si aún no se ha registrado).
     */
    public function porJugador(int $jugadorId): array
    {
        $sql = "SELECT c.id AS estadistica_id, c.nombre, c.abreviatura,
                       COALESCE(je.valor, 0) AS valor
                FROM cat_estadisticas_rendimiento c
                LEFT JOIN jugador_estadisticas je
                    ON je.estadistica_id = c.id AND je.jugador_id = :jugador_id
                ORDER BY c.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Guarda (crea o actualiza) los valores de un jugador para cada
     * estadística de rendimiento: ['estadistica_id' => valor].
     */
    public function guardar(int $jugadorId, array $valores): void
    {
        $sql = "INSERT INTO jugador_estadisticas (jugador_id, estadistica_id, valor)
                VALUES (:jugador_id, :estadistica_id, :valor)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)";

        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();

        try {
            foreach ($valores as $estadisticaId => $valor) {
                $stmt->execute([
                    'jugador_id' => $jugadorId,
                    'estadistica_id' => (int) $estadisticaId,
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
