<?php

class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function porEmail(string $email): ?array
    {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                LEFT JOIN cat_roles r ON r.id = u.rol_id
                WHERE u.email = :email
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function conRol(): array
    {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                LEFT JOIN cat_roles r ON r.id = u.rol_id
                ORDER BY u.nombre ASC";

        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return parent::create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        return parent::update($id, $data);
    }
}
