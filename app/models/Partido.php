<?php

class Partido extends Model
{
    protected string $table = 'partidos';

    /**
     * Próximos partidos programados. Si se pasa $equipoIds (acotado por
     * rol), solo incluye partidos de esos equipos; null = sin restricción.
     */
    public function proximos(int $limite = 5, ?array $equipoIds = null): array
    {
        return $this->porEstado('programado', 'p.fecha ASC, p.hora ASC', $limite, $equipoIds);
    }

    public function ultimosResultados(int $limite = 5, ?array $equipoIds = null): array
    {
        return $this->porEstado('jugado', 'p.fecha DESC, p.hora DESC', $limite, $equipoIds);
    }

    private function porEstado(string $estado, string $orden, int $limite, ?array $equipoIds): array
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return [];
        }

        $sql = "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo, r.escudo AS rival_escudo
                FROM partidos p
                LEFT JOIN equipos e ON e.id = p.equipo_id
                LEFT JOIN equipos r ON r.nombre = p.rival
                WHERE p.estado = ?";

        $params = [$estado];

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $sql .= " AND p.equipo_id IN ({$in})";
            $params = array_merge($params, array_values($equipoIds));
        }

        $sql .= " ORDER BY {$orden} LIMIT " . (int) $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function todosOrdenados(?int $limite = null, int $offset = 0): array
    {
        $sql = "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo
                FROM partidos p
                LEFT JOIN equipos e ON e.id = p.equipo_id
                ORDER BY p.fecha DESC, p.hora DESC";

        if ($limite !== null) {
            $sql .= ' LIMIT ' . (int) $limite . ' OFFSET ' . (int) $offset;
        }

        return $this->db->query($sql)->fetchAll();
    }

    public function contarTodos(?array $equipoIds = null): int
    {
        return $this->contarPorEstado(null, $equipoIds);
    }

    public function contarJugados(?array $equipoIds = null): int
    {
        return $this->contarPorEstado('jugado', $equipoIds);
    }

    private function contarPorEstado(?string $estado, ?array $equipoIds): int
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return 0;
        }

        $sql = 'SELECT COUNT(*) FROM partidos WHERE 1 = 1';
        $params = [];

        if ($estado !== null) {
            $sql .= ' AND estado = ?';
            $params[] = $estado;
        }

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $sql .= " AND equipo_id IN ({$in})";
            $params = array_merge($params, array_values($equipoIds));
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Partidos jugados de un equipo, ordenados cronológicamente (para
     * calcular rachas, promedios y máximos por partido en el motor de
     * logros/badges de equipo). Cada partido solo se guarda una vez desde
     * la perspectiva del equipo local (equipo_id); los partidos donde este
     * equipo fue el rival (visitante) se incluyen reflejando el marcador
     * para que también cuenten en sus rachas, goles y porterías en cero.
     */
    public function jugadosDeEquipo(int $equipoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM partidos
             WHERE equipo_id = :equipo_id AND estado = 'jugado' AND goles_favor IS NOT NULL AND goles_contra IS NOT NULL"
        );
        $stmt->execute(['equipo_id' => $equipoId]);
        $propios = $stmt->fetchAll();

        $comoRival = [];
        $equipo = (new Equipo())->find($equipoId);

        if ($equipo) {
            $stmt2 = $this->db->prepare(
                "SELECT * FROM partidos
                 WHERE rival = :nombre AND estado = 'jugado' AND goles_favor IS NOT NULL AND goles_contra IS NOT NULL"
            );
            $stmt2->execute(['nombre' => $equipo['nombre']]);

            foreach ($stmt2->fetchAll() as $p) {
                $temp = $p['goles_favor'];
                $p['goles_favor'] = $p['goles_contra'];
                $p['goles_contra'] = $temp;
                $comoRival[] = $p;
            }
        }

        $todos = array_merge($propios, $comoRival);
        usort($todos, fn ($a, $b) => [$a['fecha'], $a['hora']] <=> [$b['fecha'], $b['hora']]);

        return $todos;
    }

    /**
     * Partidos de un torneo (bracket), con escudos de ambos lados,
     * ordenados por ronda y posición dentro de la ronda.
     */
    public function deTorneo(int $torneoId): array
    {
        $sql = "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo,
                       r.id AS rival_equipo_id, r.nombre AS rival_nombre, r.escudo AS rival_escudo
                FROM partidos p
                LEFT JOIN equipos e ON e.id = p.equipo_id
                LEFT JOIN equipos r ON r.nombre = p.rival
                WHERE p.torneo_id = :torneo_id
                ORDER BY p.posicion ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['torneo_id' => $torneoId]);

        return $stmt->fetchAll();
    }

    /**
     * Localiza el partido de una ronda/posición específica dentro de un
     * torneo (para propagar el ganador de la ronda anterior).
     */
    public function deTorneoRonda(int $torneoId, string $ronda, int $posicion): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM partidos WHERE torneo_id = :torneo_id AND ronda = :ronda AND posicion = :posicion LIMIT 1'
        );
        $stmt->execute(['torneo_id' => $torneoId, 'ronda' => $ronda, 'posicion' => $posicion]);

        return $stmt->fetch() ?: null;
    }

    public function eliminarDeTorneo(int $torneoId): void
    {
        $stmt = $this->db->prepare('DELETE FROM partidos WHERE torneo_id = :torneo_id');
        $stmt->execute(['torneo_id' => $torneoId]);
    }

    public function regenerarCalendario(int $ligaId, array $partidos): int
    {
        $this->db->beginTransaction();

        try {
            $borrar = $this->db->prepare("DELETE FROM partidos WHERE liga_id = :liga_id AND estado = 'programado'");
            $borrar->execute(['liga_id' => $ligaId]);

            $insertar = $this->db->prepare(
                "INSERT INTO partidos (liga_id, equipo_id, rival, fecha, hora, sede, tipo, competencia, jornada, estado)
                 VALUES (:liga_id, :equipo_id, :rival, :fecha, :hora, :sede, :tipo, :competencia, :jornada, 'programado')"
            );

            foreach ($partidos as $p) {
                $insertar->execute([
                    'liga_id' => $ligaId,
                    'equipo_id' => $p['equipo_id'],
                    'rival' => $p['rival'],
                    'fecha' => $p['fecha'],
                    'hora' => $p['hora'],
                    'sede' => $p['sede'],
                    'tipo' => $p['tipo'],
                    'competencia' => $p['competencia'],
                    'jornada' => $p['jornada'],
                ]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return count($partidos);
    }
}
