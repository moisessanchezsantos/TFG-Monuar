<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DestinoController extends AbstractController
{
    #[Route('/api/destinos', name: 'api_destinos', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $destinos = [
            [
                'id' => 1,
                'nombre' => 'Francia',
                'pais' => 'Francia',
                'clima' => 'Templado',
                'mejor_epoca' => 'Primavera',
                'descripcion' => 'País conocido por su patrimonio, gastronomía y ciudades históricas.',
                'visitado' => true
            ],
            [
                'id' => 2,
                'nombre' => 'Japón',
                'pais' => 'Japón',
                'clima' => 'Húmedo subtropical',
                'mejor_epoca' => 'Primavera y otoño',
                'descripcion' => 'País moderno y tradicional a la vez, con gran riqueza cultural y tecnológica.',
                'visitado' => false
            ],
            [
                'id' => 3,
                'nombre' => 'España',
                'pais' => 'España',
                'clima' => 'Mediterráneo',
                'mejor_epoca' => 'Primavera y otoño',
                'descripcion' => 'País con gran diversidad cultural, gastronómica y paisajística.',
                'visitado' => true
            ]
        ];

        return $this->json($destinos);
    }
}