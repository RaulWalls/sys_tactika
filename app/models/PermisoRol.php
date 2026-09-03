<?php

class PermisoRol extends Model
{
    protected string $table = 'permisos_rol';

    private const ACCIONES = ['ver', 'crear', 'editar', 'eliminar'];

    public function tienePermiso(int $rolId, string $controlador, string $accion): bool
    {
        if (!in_array($accion, self::ACCIONES, true)) {
            return false;
        }

        $sql = "SELECT p.{$accion}
                FROM permisos_rol p
                JOIN cat_modulos m ON m.id = p.modulo_id
                WHERE p.rol_id = :rol_id AND m.controlador = :controlador
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rol_id' => $rolId, 'controlador' => $controlador]);
        $valor = $stmt->fetchColumn();

        return $valor !== false && (bool) $valor;
    }

    /**
     * Matriz completa rol x módulo, con los 4 flags, para la vista de
     * Configuración (una fila por cada combinación, incluso sin registrar).
     */
    public function matriz(): array
    {
        $sql = "SELECT r.id AS rol_id, r.nombre AS rol_nombre,
                       m.id AS modulo_id, m.nombre AS modulo_nombre,
                       COALESCE(p.ver, 0) AS ver,
                       COALESCE(p.crear, 0) AS crear,
                       COALESCE(p.editar, 0) AS editar,
                       COALESCE(p.eliminar, 0) AS eliminar
                FROM cat_roles r
                CROSS JOIN cat_modulos m
                LEFT JOIN permisos_rol p ON p.rol_id = r.id AND p.modulo_id = m.id
                ORDER BY r.id ASC, m.id ASC";

        return $this->db->query($sql)->fetchAll();
    }

    public function guardar(int $rolId, int $moduloId, array $valores): void
    {
        $sql = "INSERT INTO permisos_rol (rol_id, modulo_id, ver, crear, editar, eliminar)
                VALUES (:rol_id, :modulo_id, :ver, :crear, :editar, :eliminar)
                ON DUPLICATE KEY UPDATE
                    ver = VALUES(ver), crear = VALUES(crear),
                    editar = VALUES(editar), eliminar = VALUES(eliminar)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'rol_id' => $rolId,
            'modulo_id' => $moduloId,
            'ver' => !empty($valores['ver']) ? 1 : 0,
            'crear' => !empty($valores['crear']) ? 1 : 0,
            'editar' => !empty($valores['editar']) ? 1 : 0,
            'eliminar' => !empty($valores['eliminar']) ? 1 : 0,
        ]);
    }
}
