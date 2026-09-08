<?php

/**
 * Motor de desbloqueo de logros y badges (jugador y equipo).
 *
 * Solo evalúa condition_type con datos reales disponibles hoy en el
 * sistema. Quedan sin evaluar (progreso/cantidad se mantienen en 0):
 * - Todo lo que depende de campeonatos/torneos/finales (no existe ese
 *   concepto en Táctika todavía: championships, championship_streak,
 *   unbeaten_championship, final_appearance, championship_streak_unbeaten_finals).
 * - Lo que requiere el marcador minuto a minuto de un partido (no solo el
 *   resultado final): late_goal, winning_goal, long_range_goal,
 *   comeback_win, late_winning_goal, decisive_save, decisive_actions_team,
 *   comebacks.
 * - Comparativas contra el resto del equipo/liga: best_rating_month,
 *   best_rating_season, rating_improvement_pct.
 * - Todo lo marcado como 'composite' en la base de datos (badges sin una
 *   fórmula definida todavía).
 */
class LogroEngine
{
    private PDO $db;

    private const MAPA_ESTADISTICA_JUGADOR = [
        'goals' => 'Goles',
        'goals_career' => 'Goles',
        'assists' => 'Asistencias',
        'key_passes' => 'Pases clave',
        'recoveries' => 'Recuperaciones',
        'tackles_won' => 'Entradas exitosas',
        'defensive_duels_won' => 'Duelos ganados',
        'clearances' => 'Despejes',
        'interceptions' => 'Intercepciones',
        'saves' => 'Atajadas',
        'clean_sheets' => 'Porterías en cero',
        'penalties_saved' => 'Penales atajados',
        'matches' => 'Partidos',
        'minutes' => 'Minutos',
    ];

    private const MENOR_ES_MEJOR = ['team_goals_conceded_avg_max'];

    private const MIN_PARTIDOS_PROMEDIO = 5;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // =========================================================
    // LOGROS DE JUGADOR
    // =========================================================

    /**
     * Evalúa todos los logros de un jugador y guarda su progreso.
     * Devuelve los nombres de los logros recién desbloqueados.
     */
    public function evaluarLogrosJugador(int $jugadorId): array
    {
        $logroModel = new JugadorLogro();
        $logros = $logroModel->porJugador($jugadorId);

        $stats = $this->estadisticasJugador($jugadorId);
        $eventos = (new JugadorPartidoEvento())->porJugadorOrdenado($jugadorId);

        $mvpCount = 0;
        foreach ($eventos as $e) {
            if ((int) $e['mvp'] === 1) {
                $mvpCount++;
            }
        }

        $metricas = [
            'defensive_action' => max($stats['Recuperaciones'] ?? 0, $stats['Entradas exitosas'] ?? 0),
            'goals_single_match' => $this->maxColumna($eventos, 'goles'),
            'assists_single_match' => $this->maxColumna($eventos, 'asistencias'),
            'goal_and_assist_single_match' => $this->existeFila($eventos, fn ($e) => (int) $e['goles'] >= 1 && (int) $e['asistencias'] >= 1) ? 1 : 0,
            'mvp' => $mvpCount,
            'scoring_streak' => $this->rachaMaxima($eventos, fn ($e) => (int) $e['goles'] >= 1),
            'assist_streak' => $this->rachaMaxima($eventos, fn ($e) => (int) $e['asistencias'] >= 1),
            'starter_streak' => $this->rachaMaxima($eventos, fn ($e) => (int) $e['titular'] === 1),
            'participation_streak' => $this->rachaMaxima($eventos, fn ($e) => (int) $e['minutos'] > 0),
            'rating_streak_min8' => $this->rachaMaxima($eventos, fn ($e) => $e['rating'] !== null && (float) $e['rating'] >= 8),
        ];

        $nuevos = [];

        foreach ($logros as $logro) {
            $tipo = $logro['condition_type'];
            $meta = (float) $logro['meta'];
            $valor = null;

            if (isset(self::MAPA_ESTADISTICA_JUGADOR[$tipo])) {
                $valor = (float) ($stats[self::MAPA_ESTADISTICA_JUGADOR[$tipo]] ?? 0);
            } elseif ($tipo === 'matches_and_goal_contributions') {
                $partidos = $stats['Partidos'] ?? 0;
                $contribuciones = ($stats['Goles'] ?? 0) + ($stats['Asistencias'] ?? 0);
                $valor = $partidos;
                $cumple = $partidos >= $meta && $contribuciones >= 50;
                $this->guardarLogro($logroModel, $jugadorId, $logro, (int) $valor, $cumple, $nuevos);
                continue;
            } elseif (array_key_exists($tipo, $metricas)) {
                $valor = (float) $metricas[$tipo];
            } else {
                continue; // sin datos suficientes (ver cabecera de la clase)
            }

            $this->guardarLogro($logroModel, $jugadorId, $logro, (int) $valor, $valor >= $meta, $nuevos);
        }

        return $nuevos;
    }

    // =========================================================
    // LOGROS DE EQUIPO
    // =========================================================

    public function evaluarLogrosEquipo(int $equipoId): array
    {
        $logroModel = new EquipoLogro();
        $logros = $logroModel->porEquipo($equipoId);
        $partidos = (new Partido())->jugadosDeEquipo($equipoId);

        $metricas = $this->metricasEquipo($partidos);

        $nuevos = [];

        foreach ($logros as $logro) {
            $tipo = $logro['condition_type'];
            $meta = (float) $logro['meta'];

            if ($tipo === 'team_matches_and_wins') {
                $valor = $metricas['team_matches'];
                $cumple = $valor >= $meta && $metricas['wins'] >= 100;
            } elseif (array_key_exists($tipo, $metricas)) {
                $valor = $metricas[$tipo];
                $cumple = $valor >= $meta;
            } else {
                continue; // championships, comeback_win, late_winning_goal, etc.
            }

            $this->guardarLogro($logroModel, $equipoId, $logro, (int) $valor, $cumple, $nuevos);
        }

        return $nuevos;
    }

    private function guardarLogro(object $modelo, int $sujetoId, array $logro, int $progreso, bool $cumple, array &$nuevos): void
    {
        $yaDesbloqueado = !empty($logro['desbloqueado_en']);
        $modelo->registrarProgreso($sujetoId, $logro['logro_id'], $progreso, $cumple);

        if ($cumple && !$yaDesbloqueado) {
            $nuevos[] = $logro['nombre'];
        }
    }

    // =========================================================
    // BADGES CON NIVEL: JUGADOR
    // =========================================================

    /**
     * Evalúa las familias de badges con nivel de un jugador (Francotirador,
     * Depredador, Incansable, Parapenales, MVP). Son permanentes: al
     * superar un umbral se conservan todos los tiers alcanzados.
     * Si se pasa $ligaId, registra el nivel más alto alcanzado en el
     * historial de esa temporada.
     */
    public function evaluarBadgesJugador(int $jugadorId, ?int $ligaId = null): array
    {
        $badgeModel = new JugadorBadge();
        $badges = $badgeModel->porJugador($jugadorId);
        $stats = $this->estadisticasJugador($jugadorId);

        $partidos = $stats['Partidos'] ?? 0;
        $mvpCount = (new JugadorPartidoEvento())->porJugadorOrdenado($jugadorId);
        $totalMvp = 0;
        foreach ($mvpCount as $e) {
            if ((int) $e['mvp'] === 1) {
                $totalMvp++;
            }
        }

        $metricas = [
            'goals' => (float) ($stats['Goles'] ?? 0),
            'minutes' => (float) ($stats['Minutos'] ?? 0),
            'penalties_saved' => (float) ($stats['Penales atajados'] ?? 0),
            'mvp' => (float) $totalMvp,
        ];

        // Igual que en equipo: un promedio por partido necesita una muestra
        // mínima para no dispararse con uno o dos partidos sueltos.
        if ($partidos >= self::MIN_PARTIDOS_PROMEDIO) {
            $metricas['goals_per_match'] = ($stats['Goles'] ?? 0) / $partidos;
        }

        $temporadaModel = $ligaId ? new JugadorBadgeTemporada() : null;

        return $this->evaluarFamiliasBadge($badgeModel, $badges, $metricas, $jugadorId, $ligaId, $temporadaModel, false);
    }

    // =========================================================
    // BADGES CON NIVEL: EQUIPO
    // =========================================================

    /**
     * Evalúa las familias de badges con nivel de un equipo (Artilleros,
     * Pólvora, Muralla, Candado, Dominante, Invencible, Imparable,
     * Profundidad). A diferencia del jugador, son una identidad que puede
     * evolucionar: solo se conserva el tier vigente de cada familia.
     * Si se pasa $ligaId, registra el nivel más alto alcanzado en el
     * historial de esa temporada.
     */
    public function evaluarBadgesEquipo(int $equipoId, ?int $ligaId = null): array
    {
        $badgeModel = new EquipoBadge();
        $badges = $badgeModel->porEquipo($equipoId);
        $partidos = (new Partido())->jugadosDeEquipo($equipoId);

        $metricas = $this->metricasEquipo($partidos);
        $totalPartidos = $metricas['team_matches'];

        $metricasBadge = [
            'win_streak' => $metricas['win_streak'],
            'unbeaten_streak' => $metricas['unbeaten_streak'],
            'scoring_streak_team' => $metricas['scoring_streak_team'],
            'team_clean_sheets' => $metricas['team_clean_sheets'],
            'players_with_contributions' => (float) (new JugadorPartidoEvento())->contarConContribuciones($equipoId),
        ];

        if ($totalPartidos >= self::MIN_PARTIDOS_PROMEDIO) {
            $metricasBadge['team_goals_per_match'] = $metricas['team_goals'] / $totalPartidos;
            $metricasBadge['team_goals_conceded_avg_max'] = $metricas['team_goles_recibidos'] / $totalPartidos;
            $metricasBadge['win_rate'] = ($metricas['wins'] / $totalPartidos) * 100;
        }

        $temporadaModel = $ligaId ? new EquipoBadgeTemporada() : null;

        return $this->evaluarFamiliasBadge($badgeModel, $badges, $metricasBadge, $equipoId, $ligaId, $temporadaModel, true);
    }

    /**
     * Lógica compartida de evaluación por familia de badge (agrupa los
     * tiers I-IV, calcula el tier más alto que cumple y lo guarda).
     */
    private function evaluarFamiliasBadge(object $badgeModel, array $badges, array $metricas, int $sujetoId, ?int $ligaId, ?object $temporadaModel, bool $esEquipo): array
    {
        $porFamilia = [];
        foreach ($badges as $b) {
            if ($b['familia'] !== null) {
                $porFamilia[$b['familia']][] = $b;
            }
        }

        $nuevos = [];

        foreach ($porFamilia as $familia => $tiers) {
            usort($tiers, fn ($a, $b) => $a['nivel'] <=> $b['nivel']);

            $tipo = $tiers[0]['condition_type'];
            if (!array_key_exists($tipo, $metricas)) {
                continue; // composite, o no hay partidos suficientes todavía
            }

            $metrica = $metricas[$tipo];
            $invertido = in_array($tipo, self::MENOR_ES_MEJOR, true);

            $nivelVigente = 0;
            $badgeIdVigente = null;
            $nombreVigente = null;

            foreach ($tiers as $tier) {
                $umbral = (float) $tier['condition_value'];
                $cumpleTier = $invertido ? ($metrica <= $umbral) : ($metrica >= $umbral);

                if ($cumpleTier && (int) $tier['nivel'] > $nivelVigente) {
                    $nivelVigente = (int) $tier['nivel'];
                    $badgeIdVigente = (int) $tier['badge_id'];
                    $nombreVigente = $tier['nombre'];
                }
            }

            if ($esEquipo) {
                // Identidad evolutiva: fija el tier vigente y limpia el resto.
                $teniaVigente = $badgeIdVigente !== null && array_reduce(
                    $tiers,
                    fn ($carry, $t) => $carry || ((int) $t['badge_id'] === $badgeIdVigente && $t['cantidad'] > 0),
                    false
                );

                $badgeModel->actualizarFamilia($sujetoId, $familia, $badgeIdVigente);

                if ($badgeIdVigente !== null && !$teniaVigente) {
                    $nuevos[] = $nombreVigente;
                }
            } else {
                // Permanente: conserva todos los tiers ya alcanzados.
                foreach ($tiers as $tier) {
                    $umbral = (float) $tier['condition_value'];
                    if ($metrica >= $umbral) {
                        $yaTenia = $tier['cantidad'] > 0;
                        $badgeModel->registrarObtenida($sujetoId, (int) $tier['badge_id']);
                        if (!$yaTenia) {
                            $nuevos[] = $tier['nombre'];
                        }
                    }
                }
            }

            if ($ligaId && $badgeIdVigente !== null && $temporadaModel) {
                $temporadaModel->registrar($sujetoId, $ligaId, $familia, $badgeIdVigente, $nivelVigente);
            }
        }

        return $nuevos;
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function estadisticasJugador(int $jugadorId): array
    {
        $rows = (new JugadorEstadistica())->porJugador($jugadorId);

        $stats = [];
        foreach ($rows as $r) {
            $stats[$r['nombre']] = (int) $r['valor'];
        }

        return $stats;
    }

    /**
     * Agregados de un equipo a partir de sus partidos jugados: totales,
     * rachas y, cuando aplica, el máximo alcanzado dentro de una sola
     * temporada (liga).
     */
    private function metricasEquipo(array $partidos): array
    {
        $totalPartidos = count($partidos);
        $totalVictorias = 0;
        $totalGoles = 0;
        $totalGolesRecibidos = 0;
        $totalPorteriasCero = 0;
        $maxGolesPartido = 0;
        $maxMargen = 0;
        $porLiga = [];

        foreach ($partidos as $p) {
            $gf = (int) $p['goles_favor'];
            $gc = (int) $p['goles_contra'];

            if ($gf > $gc) {
                $totalVictorias++;
            }

            $totalGoles += $gf;
            $totalGolesRecibidos += $gc;
            $maxGolesPartido = max($maxGolesPartido, $gf);
            $maxMargen = max($maxMargen, $gf - $gc);

            if ($gc === 0) {
                $totalPorteriasCero++;
            }

            $ligaId = $p['liga_id'];
            if ($ligaId) {
                $porLiga[$ligaId]['partidos'] = ($porLiga[$ligaId]['partidos'] ?? 0) + 1;
                $porLiga[$ligaId]['goles'] = ($porLiga[$ligaId]['goles'] ?? 0) + $gf;
                $porLiga[$ligaId]['porterias_cero'] = ($porLiga[$ligaId]['porterias_cero'] ?? 0) + ($gc === 0 ? 1 : 0);
                $porLiga[$ligaId]['derrotas'] = ($porLiga[$ligaId]['derrotas'] ?? 0) + ($gf < $gc ? 1 : 0);
            }
        }

        $rachaVictorias = $this->rachaMaxima($partidos, fn ($p) => (int) $p['goles_favor'] > (int) $p['goles_contra']);
        $rachaInvicto = $this->rachaMaxima($partidos, fn ($p) => (int) $p['goles_favor'] >= (int) $p['goles_contra']);
        $rachaGoleadora = $this->rachaMaxima($partidos, fn ($p) => (int) $p['goles_favor'] >= 1);
        $rachaPorteriaCero = $this->rachaMaxima($partidos, fn ($p) => (int) $p['goles_contra'] === 0);
        $rachaPocosGoles = $this->rachaMaxima($partidos, fn ($p) => (int) $p['goles_contra'] <= 1);

        $existeVictoriaSinGolesRecibidos = $this->existeFila(
            $partidos,
            fn ($p) => (int) $p['goles_favor'] > (int) $p['goles_contra'] && (int) $p['goles_contra'] === 0
        );

        $maxGolesTemporada = 0;
        $maxPorteriasCeroTemporada = 0;
        $maxPromedioGolesTemporada = 0.0;
        $temporadaInvicta = false;

        if ($porLiga) {
            $ligaModel = new Liga();

            foreach ($porLiga as $ligaId => $datos) {
                $maxGolesTemporada = max($maxGolesTemporada, $datos['goles']);
                $maxPorteriasCeroTemporada = max($maxPorteriasCeroTemporada, $datos['porterias_cero']);

                if ($datos['partidos'] > 0) {
                    $maxPromedioGolesTemporada = max($maxPromedioGolesTemporada, $datos['goles'] / $datos['partidos']);
                }

                if (!$temporadaInvicta && $datos['derrotas'] === 0) {
                    $liga = $ligaModel->find((int) $ligaId);
                    if ($liga && !empty($liga['fecha_fin']) && strtotime($liga['fecha_fin']) < time()) {
                        $temporadaInvicta = true;
                    }
                }
            }
        }

        return [
            'wins' => (float) $totalVictorias,
            'win_streak' => (float) $rachaVictorias,
            'unbeaten_streak' => (float) $rachaInvicto,
            'unbeaten_season' => $temporadaInvicta ? 1.0 : 0.0,
            'win_clean_sheet' => $existeVictoriaSinGolesRecibidos ? 1.0 : 0.0,
            'team_goals' => (float) $totalGoles,
            'team_goals_season' => (float) $maxGolesTemporada,
            'team_goals_career' => (float) $totalGoles,
            'team_goals_single_match' => (float) $maxGolesPartido,
            'win_margin' => (float) $maxMargen,
            'scoring_streak_team' => (float) $rachaGoleadora,
            'team_goals_per_match_season' => $maxPromedioGolesTemporada,
            'team_clean_sheets' => (float) $totalPorteriasCero,
            'clean_sheet_streak' => (float) $rachaPorteriaCero,
            'team_clean_sheets_season' => (float) $maxPorteriasCeroTemporada,
            'low_conceding_streak' => (float) $rachaPocosGoles,
            'team_matches' => (float) $totalPartidos,
            'team_goles_recibidos' => (float) $totalGolesRecibidos,
        ];
    }

    private function rachaMaxima(array $filas, callable $predicado): int
    {
        $max = 0;
        $actual = 0;

        foreach ($filas as $fila) {
            if ($predicado($fila)) {
                $actual++;
                $max = max($max, $actual);
            } else {
                $actual = 0;
            }
        }

        return $max;
    }

    private function maxColumna(array $filas, string $columna): int
    {
        $max = 0;

        foreach ($filas as $fila) {
            $max = max($max, (int) $fila[$columna]);
        }

        return $max;
    }

    private function existeFila(array $filas, callable $predicado): bool
    {
        foreach ($filas as $fila) {
            if ($predicado($fila)) {
                return true;
            }
        }

        return false;
    }
}
