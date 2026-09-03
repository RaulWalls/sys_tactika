<?php

class ConfiguracionController extends Controller
{
    private const TEMAS = [
        'default' => 'Táctika (predeterminado)',
        'barcelona' => 'Barcelona',
        'real_madrid' => 'Real Madrid',
        'manchester_city' => 'Manchester City',
        'psg' => 'PSG',
        'juventus' => 'Juventus',
        'bayern_munich' => 'Bayern Múnich',
        'liverpool' => 'Liverpool',
        'chelsea' => 'Chelsea',
        'america' => 'Club América',
    ];

    public function index(): void
    {
        $data = [
            'titulo' => 'Configuración',
            'activeMenu' => 'configuracion',
            'temasDisponibles' => self::TEMAS,
        ];

        if ($this->esAdmin()) {
            $permisoModel = $this->model('PermisoRol');
            $data['matriz'] = $permisoModel->matriz();
        }

        $this->view('configuracion/index', $data);
    }

    public function permisos(): void
    {
        if (!$this->esAdmin()) {
            http_response_code(403);
            die('Solo un administrador puede modificar los permisos.');
        }

        $permisoModel = $this->model('PermisoRol');

        foreach ($_POST['permiso'] ?? [] as $rolId => $modulos) {
            foreach ($modulos as $moduloId => $valores) {
                $permisoModel->guardar((int) $rolId, (int) $moduloId, $valores);
            }
        }

        $this->redirect('configuracion');
    }
}
