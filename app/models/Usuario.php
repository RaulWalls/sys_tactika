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

    /**
     * Equipos que un usuario (entrenador) tiene asignados.
     */
    public function equiposDe(int $usuarioId): array
    {
        $sql = "SELECT e.id, e.nombre
                FROM equipos e
                JOIN usuario_equipos ue ON ue.equipo_id = e.id
                WHERE ue.usuario_id = :usuario_id
                ORDER BY e.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);

        return $stmt->fetchAll();
    }

    /**
     * Reemplaza los equipos asignados a un usuario (entrenador).
     */
    public function guardarEquipos(int $usuarioId, array $equipoIds): void
    {
        $this->db->beginTransaction();

        try {
            $borrar = $this->db->prepare('DELETE FROM usuario_equipos WHERE usuario_id = :usuario_id');
            $borrar->execute(['usuario_id' => $usuarioId]);

            $insertar = $this->db->prepare('INSERT INTO usuario_equipos (usuario_id, equipo_id) VALUES (:usuario_id, :equipo_id)');

            foreach (array_unique(array_map('intval', $equipoIds)) as $equipoId) {
                if ($equipoId > 0) {
                    $insertar->execute(['usuario_id' => $usuarioId, 'equipo_id' => $equipoId]);
                }
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
