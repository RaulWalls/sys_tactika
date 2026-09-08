<?php

class Pago extends Model
{
    protected string $table = 'pagos';

    /**
     * Resumen de ingresos/gastos/balance. Si se pasa $equipoIds (acotado
     * por rol), solo suma pagos de esos equipos; null = sin restricción.
     */
    public function resumen(?array $equipoIds = null): array
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return ['ingresos' => 0.0, 'gastos' => 0.0, 'balance' => 0.0];
        }

        $filtro = '';
        $params = [];

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $filtro = " AND equipo_id IN ({$in})";
            $params = array_values($equipoIds);
        }

        $stmtIngresos = $this->db->prepare("SELECT COALESCE(SUM(monto), 0) AS total FROM pagos WHERE tipo = 'ingreso'{$filtro}");
        $stmtIngresos->execute($params);
        $ingresos = (float) $stmtIngresos->fetch()['total'];

        $stmtGastos = $this->db->prepare("SELECT COALESCE(SUM(monto), 0) AS total FROM pagos WHERE tipo = 'gasto'{$filtro}");
        $stmtGastos->execute($params);
        $gastos = (float) $stmtGastos->fetch()['total'];

        return [
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'balance' => $ingresos - $gastos,
        ];
    }

    public function conJugador(?array $equipoIds = null, ?int $limite = null, int $offset = 0): array
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return [];
        }

        $sql = "SELECT p.*,
                       j.nombre AS jugador_nombre, j.apellido AS jugador_apellido,
                       e.nombre AS equipo_nombre,
                       c.nombre AS concepto_nombre,
                       m.nombre AS metodo_nombre
                FROM pagos p
                LEFT JOIN jugadores j ON j.id = p.jugador_id
                LEFT JOIN equipos e ON e.id = p.equipo_id
                LEFT JOIN cat_pagos c ON c.id = p.concepto_id
                LEFT JOIN cat_metodo_pagos m ON m.id = p.metodo_pago_id";

        $params = [];

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $sql .= " WHERE p.equipo_id IN ({$in})";
            $params = array_values($equipoIds);
        }

        $sql .= ' ORDER BY p.fecha DESC';

        if ($limite !== null) {
            $sql .= ' LIMIT ' . (int) $limite . ' OFFSET ' . (int) $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Total de pagos acotado por equipo(s), para el paginado de conJugador().
     */
    public function contarConJugador(?array $equipoIds = null): int
    {
        if ($equipoIds !== null && empty($equipoIds)) {
            return 0;
        }

        $sql = 'SELECT COUNT(*) FROM pagos p';
        $params = [];

        if ($equipoIds !== null) {
            $in = implode(',', array_fill(0, count($equipoIds), '?'));
            $sql .= " WHERE p.equipo_id IN ({$in})";
            $params = array_values($equipoIds);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
