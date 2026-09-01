<?php

class Partido extends Model
{
    protected string $table = 'partidos';

    public function proximos(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo, r.escudo AS rival_escudo
             FROM partidos p
             LEFT JOIN equipos e ON e.id = p.equipo_id
             LEFT JOIN equipos r ON r.nombre = p.rival
             WHERE p.estado = 'programado'
             ORDER BY p.fecha ASC, p.hora ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function ultimosResultados(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.nombre AS equipo_nombre, e.escudo AS equipo_escudo, r.escudo AS rival_escudo
             FROM partidos p
             LEFT JOIN equipos e ON e.id = p.equipo_id
             LEFT JOIN equipos r ON r.nombre = p.rival
             WHERE p.estado = 'jugado'
             ORDER BY p.fecha DESC, p.hora DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

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

    public function contarTodos(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM partidos')->fetchColumn();
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
