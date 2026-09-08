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
        $sql = "SELECT c.id AS estadistica_id, c.nombre, c.abreviatura, c.categoria, c.estatus,
                       COALESCE(je.valor, 0) AS valor
                FROM cat_estadisticas_rendimiento c
                LEFT JOIN jugador_estadisticas je
                    ON je.estadistica_id = c.id AND je.jugador_id = :jugador_id
                ORDER BY FIELD(c.categoria, 'ofensiva', 'defensiva', 'disciplina', 'portero'), c.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Total de goles anotados por un jugador (estadística "Goles").
     */
    public function golesDe(int $jugadorId): int
    {
        $sql = "SELECT COALESCE(je.valor, 0)
                FROM cat_estadisticas_rendimiento c
                LEFT JOIN jugador_estadisticas je
                    ON je.estadistica_id = c.id AND je.jugador_id = :jugador_id
                WHERE c.nombre = 'Goles'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Total de asistencias de un jugador (estadística "Asistencias").
     */
    public function asistenciasDe(int $jugadorId): int
    {
        $sql = "SELECT COALESCE(je.valor, 0)
                FROM cat_estadisticas_rendimiento c
                LEFT JOIN jugador_estadisticas je
                    ON je.estadistica_id = c.id AND je.jugador_id = :jugador_id
                WHERE c.nombre = 'Asistencias'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Sincroniza estadísticas de rendimiento a partir de un mapa
     * ['NombreEstadistica' => valor]. Usado por la captura de eventos por
     * partido, que tiene mayor jerarquía que la edición manual: sobrescribe
     * el valor existente para dejar un solo registro de la verdad.
     */
    public function sincronizarPorNombre(int $jugadorId, array $valoresPorNombre): void
    {
        if (!$valoresPorNombre) {
            return;
        }

        $in = implode(',', array_fill(0, count($valoresPorNombre), '?'));
        $stmt = $this->db->prepare("SELECT nombre, id FROM cat_estadisticas_rendimiento WHERE nombre IN ({$in})");
        $stmt->execute(array_keys($valoresPorNombre));

        $valores = [];
        foreach ($stmt->fetchAll(PDO::FETCH_KEY_PAIR) as $nombre => $estadisticaId) {
            $valores[$estadisticaId] = $valoresPorNombre[$nombre];
        }

        $this->guardar($jugadorId, $valores);
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
