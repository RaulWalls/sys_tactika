<?php

class Torneo extends Model
{
    protected string $table = 'torneos';

    public function conLiga(): array
    {
        $sql = "SELECT t.*, l.nombre AS liga_nombre
                FROM torneos t
                LEFT JOIN ligas l ON l.id = t.liga_id
                ORDER BY t.fecha_inicio DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function conDetalle(int $id): ?array
    {
        $sql = "SELECT t.*, l.nombre AS liga_nombre
                FROM torneos t
                LEFT JOIN ligas l ON l.id = t.liga_id
                WHERE t.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function totalTorneos(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM torneos')->fetchColumn();
    }
}
