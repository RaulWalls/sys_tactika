<?php

class Equipo extends Model
{
    protected string $table = 'equipos';

    public function conLiga(): array
    {
        $sql = "SELECT e.*, l.nombre AS liga_nombre
                FROM equipos e
                LEFT JOIN ligas l ON l.id = e.liga_id
                ORDER BY e.id DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function porLiga(int $ligaId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM equipos WHERE liga_id = :liga_id ORDER BY nombre ASC');
        $stmt->execute(['liga_id' => $ligaId]);

        return $stmt->fetchAll();
    }

    public function conStaff(int $id): ?array
    {
        $sql = "SELECT e.*,
                       l.nombre AS liga_nombre,
                       r.nombre AS representante_nombre, r.apellido AS representante_apellido, r.telefono AS representante_telefono,
                       d.nombre AS dt_nombre, d.apellido AS dt_apellido, d.telefono AS dt_telefono
                FROM equipos e
                LEFT JOIN ligas l ON l.id = e.liga_id
                LEFT JOIN staff r ON r.id = e.representante_id
                LEFT JOIN staff d ON d.id = e.dt_id
                WHERE e.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
