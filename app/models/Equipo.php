<?php

class Equipo extends Model
{
    protected string $table = 'equipos';

    /**
     * Lista de equipos. Si se pasa $ids (acotado por rol), solo incluye
     * esos equipos; null significa "sin restricción" (administrador).
     */
    public function conLiga(?array $ids = null): array
    {
        if ($ids !== null && empty($ids)) {
            return [];
        }

        $sql = "SELECT e.*, l.nombre AS liga_nombre
                FROM equipos e
                LEFT JOIN ligas l ON l.id = e.liga_id";

        $params = [];

        if ($ids !== null) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " WHERE e.id IN ({$in})";
            $params = array_values($ids);
        }

        $sql .= " ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
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
