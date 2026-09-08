<?php

class CatRango extends Model
{
    protected string $table = 'cat_rangos';

    /**
     * Rango correspondiente a un nivel (el de mayor nivel_minimo <= $nivel).
     */
    public function paraNivel(int $nivel): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM cat_rangos WHERE nivel_minimo <= :nivel ORDER BY nivel_minimo DESC LIMIT 1'
        );
        $stmt->execute(['nivel' => $nivel]);

        return $stmt->fetch() ?: null;
    }
}
