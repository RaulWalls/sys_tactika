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

    public function todosOrdenados(): array
    {
        $sql = "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo
                FROM partidos p
                LEFT JOIN equipos e ON e.id = p.equipo_id
                ORDER BY p.fecha DESC, p.hora DESC";

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
