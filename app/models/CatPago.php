<?php

class CatPago extends Model
{
    protected string $table = 'cat_pagos';

    /**
     * Conceptos activos, para el select de captura de pagos (los inactivos
     * se ocultan mas los pagos ya registrados con ellos no se pierden).
     */
    public function activos(): array
    {
        $stmt = $this->db->query("SELECT * FROM cat_pagos WHERE estatus = 'activo' ORDER BY nombre ASC");

        return $stmt->fetchAll();
    }
}
