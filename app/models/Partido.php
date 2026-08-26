<?php

class Partido extends Model
{
    protected string $table = 'partidos';

    public function proximos(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM partidos WHERE estado = 'programado' ORDER BY fecha ASC, hora ASC LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function ultimosResultados(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM partidos WHERE estado = 'jugado' ORDER BY fecha DESC, hora DESC LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function todosOrdenados(): array
    {
        return $this->db->query('SELECT * FROM partidos ORDER BY fecha DESC, hora DESC')->fetchAll();
    }
}
