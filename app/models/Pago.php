<?php

class Pago extends Model
{
    protected string $table = 'pagos';

    public function resumen(): array
    {
        $ingresos = (float) $this->db->query(
            "SELECT COALESCE(SUM(monto), 0) AS total FROM pagos WHERE tipo = 'ingreso'"
        )->fetch()['total'];

        $gastos = (float) $this->db->query(
            "SELECT COALESCE(SUM(monto), 0) AS total FROM pagos WHERE tipo = 'gasto'"
        )->fetch()['total'];

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
