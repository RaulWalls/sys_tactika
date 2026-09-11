<?php

class JugadorPartidoEvento extends Model
{
    protected string $table = 'jugador_partido_eventos';

    /**
     * Jugadores del equipo dueño del partido, con su evento registrado
     * en ese partido (o valores en cero si aún no se ha capturado).
     */
    public function porPartido(int $partidoId, int $equipoId): array
    {
        $sql = "SELECT j.id AS jugador_id, j.nombre, j.apellido, j.numero,
                       COALESCE(e.goles, 0) AS goles,
                       COALESCE(e.asistencias, 0) AS asistencias,
                       COALESCE(e.minutos, 0) AS minutos,
                       COALESCE(e.titular, 0) AS titular,
                       e.rating,
                       COALESCE(e.mvp, 0) AS mvp
                FROM jugadores j
                JOIN jugador_equipos je ON je.jugador_id = j.id AND je.equipo_id = :equipo_id
                LEFT JOIN jugador_partido_eventos e ON e.jugador_id = j.id AND e.partido_id = :partido_id
                ORDER BY j.numero ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId, 'partido_id' => $partidoId]);

        return $stmt->fetchAll();
    }

    /**
     * Guarda (crea o actualiza) los eventos de varios jugadores para un
     * mismo partido: ['jugador_id' => ['goles'=>, 'asistencias'=>, 'minutos'=>, 'titular'=>, 'rating'=>, 'mvp'=>]].
     */
    public function guardarLote(int $partidoId, array $filas): void
    {
        $sql = "INSERT INTO jugador_partido_eventos
                    (jugador_id, partido_id, goles, asistencias, minutos, titular, rating, mvp)
                VALUES
                    (:jugador_id, :partido_id, :goles, :asistencias, :minutos, :titular, :rating, :mvp)
                ON DUPLICATE KEY UPDATE
                    goles = VALUES(goles),
                    asistencias = VALUES(asistencias),
                    minutos = VALUES(minutos),
                    titular = VALUES(titular),
                    rating = VALUES(rating),
                    mvp = VALUES(mvp)";

        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();

        try {
            foreach ($filas as $jugadorId => $datos) {
                $stmt->execute([
                    'jugador_id' => (int) $jugadorId,
                    'partido_id' => $partidoId,
                    'goles' => (int) ($datos['goles'] ?? 0),
                    'asistencias' => (int) ($datos['asistencias'] ?? 0),
                    'minutos' => (int) ($datos['minutos'] ?? 0),
                    'titular' => !empty($datos['titular']) ? 1 : 0,
                    'rating' => ($datos['rating'] ?? '') !== '' ? (float) $datos['rating'] : null,
                    'mvp' => !empty($datos['mvp']) ? 1 : 0,
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Totales acumulados de un jugador a partir de todos sus eventos de
     * partido registrados (fuente de verdad para goles/asistencias/minutos
     * en las estadísticas de rendimiento).
     */
    public function totalesDe(int $jugadorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(goles), 0) AS goles,
                    COALESCE(SUM(asistencias), 0) AS asistencias,
                    COALESCE(SUM(minutos), 0) AS minutos
             FROM jugador_partido_eventos
             WHERE jugador_id = :jugador_id'
        );
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetch() ?: ['goles' => 0, 'asistencias' => 0, 'minutos' => 0];
    }

    /**
     * Cantidad de partidos realmente disputados por un jugador (con
     * minutos > 0), para sincronizar la estadística "Partidos".
     */
    public function contarPartidos(int $jugadorId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM jugador_partido_eventos WHERE jugador_id = :jugador_id AND minutos > 0'
        );
        $stmt->execute(['jugador_id' => $jugadorId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Eventos de un jugador ordenados cronológicamente por fecha del
     * partido (para calcular rachas y máximos por partido). Solo incluye
     * partidos donde se capturó explícitamente su evento.
     */
    public function porJugadorOrdenado(int $jugadorId): array
    {
        $sql = "SELECT e.*, p.liga_id, p.fecha
                FROM jugador_partido_eventos e
                JOIN partidos p ON p.id = e.partido_id
                WHERE e.jugador_id = :jugador_id
                ORDER BY p.fecha ASC, p.hora ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['jugador_id' => $jugadorId]);

        return $stmt->fetchAll();
    }

    /**
     * Cantidad de jugadores del plantel actual de un equipo que tienen al
     * menos un gol o una asistencia registrada (para el badge "Profundidad").
     */
    public function contarConContribuciones(int $equipoId): int
    {
        $sql = "SELECT COUNT(DISTINCT j.id)
                FROM jugadores j
                JOIN jugador_equipos je ON je.jugador_id = j.id AND je.equipo_id = :equipo_id
                JOIN jugador_partido_eventos e ON e.jugador_id = j.id
                WHERE e.goles > 0 OR e.asistencias > 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipo_id' => $equipoId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Goleadores (jugadores con más goles anotados) dentro de una liga o
     * un torneo, para el reporte de goleo. El equipo mostrado es el
     * primero vinculado al jugador (aproximado: no se guarda con cuál
     * equipo disputó cada partido puntual).
     */
    public function goleadores(string $columna, int $valor, int $limite = 10): array
    {
        if (!in_array($columna, ['liga_id', 'torneo_id'], true)) {
            throw new InvalidArgumentException('Columna de filtro inválida.');
        }

        $sql = "SELECT j.id AS jugador_id, j.nombre, j.apellido, goleo.goles,
                       (SELECT e2.id FROM jugador_equipos je2 JOIN equipos e2 ON e2.id = je2.equipo_id
                        WHERE je2.jugador_id = j.id LIMIT 1) AS equipo_id,
                       (SELECT e2.nombre FROM jugador_equipos je2 JOIN equipos e2 ON e2.id = je2.equipo_id
                        WHERE je2.jugador_id = j.id LIMIT 1) AS equipo_nombre
                FROM (
                    SELECT ev.jugador_id, SUM(ev.goles) AS goles
                    FROM jugador_partido_eventos ev
                    JOIN partidos p ON p.id = ev.partido_id
                    WHERE p.{$columna} = :valor
                    GROUP BY ev.jugador_id
                    HAVING goles > 0
                ) goleo
                JOIN jugadores j ON j.id = goleo.jugador_id
                ORDER BY goleo.goles DESC
                LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['valor' => $valor]);

        return $stmt->fetchAll();
    }
}
