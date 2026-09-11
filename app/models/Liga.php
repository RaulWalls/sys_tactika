<?php

class Liga extends Model
{
    protected string $table = 'ligas';

    public function totalLigas(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    /**
     * Temporadas (ligas) ya finalizadas, para el historial: aquellas cuya
     * fecha de fin ya pasó.
     */
    public function finalizadas(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM ligas WHERE fecha_fin IS NOT NULL AND fecha_fin < CURDATE() ORDER BY fecha_fin DESC"
        );

        return $stmt->fetchAll();
    }

    /**
     * Tabla de posiciones de una liga. Delegada a Partido::tablaPosiciones(),
     * que también sabe calcularla filtrando por torneo (para Reportes).
     */
    public function tablaPosiciones(int $ligaId): array
    {
        return (new Partido())->tablaPosiciones('liga_id', $ligaId);
    }
}
