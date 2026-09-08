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
     * Tabla de posiciones de una liga (puntos: victoria=3, empate=1),
     * calculada a partir de los partidos jugados. Cada partido solo se
     * guarda desde la perspectiva del equipo local (equipo_id), así que
     * el rival se acredita resolviendo su nombre contra la tabla equipos.
     */
    public function tablaPosiciones(int $ligaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.equipo_id, e.nombre AS equipo_nombre, p.rival, p.goles_favor, p.goles_contra
             FROM partidos p
             LEFT JOIN equipos e ON e.id = p.equipo_id
             WHERE p.liga_id = :liga_id AND p.estado = 'jugado'
                   AND p.goles_favor IS NOT NULL AND p.goles_contra IS NOT NULL"
        );
        $stmt->execute(['liga_id' => $ligaId]);
        $partidos = $stmt->fetchAll();

        $equipoModel = new Equipo();
        $tabla = [];

        $inicializar = function (int $id, string $nombre) use (&$tabla): void {
            if (!isset($tabla[$id])) {
                $tabla[$id] = ['equipo_id' => $id, 'nombre' => $nombre, 'pj' => 0, 'g' => 0, 'e' => 0, 'p' => 0, 'gf' => 0, 'gc' => 0];
            }
        };

        $acreditar = function (int $id, int $gf, int $gc) use (&$tabla): void {
            $tabla[$id]['pj']++;
            $tabla[$id]['gf'] += $gf;
            $tabla[$id]['gc'] += $gc;
            if ($gf > $gc) {
                $tabla[$id]['g']++;
            } elseif ($gf === $gc) {
                $tabla[$id]['e']++;
            } else {
                $tabla[$id]['p']++;
            }
        };

        foreach ($partidos as $p) {
            $gf = (int) $p['goles_favor'];
            $gc = (int) $p['goles_contra'];

            if ($p['equipo_id']) {
                $inicializar((int) $p['equipo_id'], $p['equipo_nombre']);
                $acreditar((int) $p['equipo_id'], $gf, $gc);
            }

            if (!empty($p['rival'])) {
                $rivalEquipo = $equipoModel->porNombre($p['rival']);
                if ($rivalEquipo) {
                    $inicializar((int) $rivalEquipo['id'], $rivalEquipo['nombre']);
                    $acreditar((int) $rivalEquipo['id'], $gc, $gf);
                }
            }
        }

        foreach ($tabla as &$fila) {
            $fila['dg'] = $fila['gf'] - $fila['gc'];
            $fila['pts'] = $fila['g'] * 3 + $fila['e'];
        }
        unset($fila);

        $filas = array_values($tabla);
        usort($filas, fn ($a, $b) => $b['pts'] <=> $a['pts'] ?: $b['dg'] <=> $a['dg'] ?: $b['gf'] <=> $a['gf']);

        return $filas;
    }
}
