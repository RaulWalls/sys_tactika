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

    public function conJugador(): array
    {
        $sql = "SELECT p.*,
                       j.nombre AS jugador_nombre, j.apellido AS jugador_apellido,
                       e.nombre AS equipo_nombre,
                       c.nombre AS concepto_nombre,
                       m.nombre AS metodo_nombre
                FROM pagos p
                LEFT JOIN jugadores j ON j.id = p.jugador_id
                LEFT JOIN equipos e ON e.id = p.equipo_id
                LEFT JOIN cat_pagos c ON c.id = p.concepto_id
                LEFT JOIN cat_metodo_pagos m ON m.id = p.metodo_pago_id
                ORDER BY p.fecha DESC";

        return $this->db->query($sql)->fetchAll();
    }
}
