<?php

class Liga extends Model
{
    protected string $table = 'ligas';

    public function totalLigas(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
