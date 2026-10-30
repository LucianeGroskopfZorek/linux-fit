<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AcademiaController extends Controller
{
    private function listModalidades(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'musculacao',
                'nome' => 'Musculação',
                'descricao' => 'Treinamento de força e resistência.',
                'icone' => '💪',
                'professor' => 'Carlos Silva',
                'horarios' => ['Seg/qua/sex — 07:00', 'Ter/qui — 18:00'],
            ],
            [
                'id' => 2,
                'slug' => 'pilates',
                'nome' => 'Pilates',
                'descricao' => 'Fortalecimento, flexibilidade e consciência corporal.',
                'icone' => '🧘',
                'professor' => 'Ana Costa',
                'horarios' => ['Seg/qua — 08:00', 'Ter/qui — 19:00'],
            ],
            [
                'id' => 3,
                'slug' => 'funcional',
                'nome' => 'Funcional',
                'descricao' => 'Exercícios dinâmicos para o corpo todo e o condicionamento.',
                'icone' => '🔥',
                'professor' => 'Mariana Alves',
                'horarios' => ['Seg/qua/sex — 06:30', 'Ter/qui — 18:30'],
            ],
            [
                'id' => 4,
                'slug' => 'spinning',
                'nome' => 'Spinning',
                'descricao' => 'Aulas de ciclismo indoor com energia e ritmo intensos.',
                'icone' => '🚴',
                'professor' => 'Rafael Mendes',
                'horarios' => ['Seg/qua/sex — 19:00', 'Sábado — 09:00'],
            ],
        ];
    }

    public function modalidades(): JsonResponse
    {
        $modalidades = array_map(
            fn (array $modalidade): array => [
                'id' => $modalidade['id'],
                'slug' => $modalidade['slug'],
                'nome' => $modalidade['nome'],
                'descricao' => $modalidade['descricao'],
                'icone' => $modalidade['icone'],
            ],
            $this->listModalidades()
        );

        return response()->json($modalidades);
    }

    public function horarios(int $id): JsonResponse
    {
        foreach ($this->listModalidades() as $modalidade) {
            if ($modalidade['id'] === $id) {
                return response()->json([
                    'modalidade' => $modalidade['nome'],
                    'professor' => $modalidade['professor'],
                    'horarios' => $modalidade['horarios'],
                ]);
            }
        }

        return response()->json(['message' => 'Modalidade não encontrada.'], 404);
    }
}
