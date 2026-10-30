<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModalidadesApiTest extends TestCase
{
    public function test_modalidades_endpoint_returns_the_four_cards(): void
    {
        $this->getJson('/api/modalidades')
            ->assertOk()
            ->assertJsonCount(4)
            ->assertJsonFragment([
                'id' => 1,
                'slug' => 'musculacao',
                'nome' => 'Musculação',
            ])
            ->assertJsonFragment([
                'id' => 2,
                'slug' => 'pilates',
                'nome' => 'Pilates',
            ])
            ->assertJsonFragment([
                'id' => 3,
                'slug' => 'funcional',
                'nome' => 'Funcional',
            ])
            ->assertJsonFragment([
                'id' => 4,
                'slug' => 'spinning',
                'nome' => 'Spinning',
            ]);
    }

    public function test_schedule_endpoint_returns_the_selected_modality_schedule(): void
    {
        $horariosEsperados = [
            1 => [
                'modalidade' => 'Musculação',
                'professor' => 'Carlos Silva',
                'horarios' => ['Seg/qua/sex — 07:00', 'Ter/qui — 18:00'],
            ],
            2 => [
                'modalidade' => 'Pilates',
                'professor' => 'Ana Costa',
                'horarios' => ['Seg/qua — 08:00', 'Ter/qui — 19:00'],
            ],
            3 => [
                'modalidade' => 'Funcional',
                'professor' => 'Mariana Alves',
                'horarios' => ['Seg/qua/sex — 06:30', 'Ter/qui — 18:30'],
            ],
            4 => [
                'modalidade' => 'Spinning',
                'professor' => 'Rafael Mendes',
                'horarios' => ['Seg/qua/sex — 19:00', 'Sábado — 09:00'],
            ],
        ];

        foreach ($horariosEsperados as $id => $dados) {
            $this->getJson("/api/modalidades/{$id}/horarios")
                ->assertOk()
                ->assertExactJson($dados);
        }
    }

    public function test_schedule_endpoint_returns_not_found_for_unknown_modality(): void
    {
        $this->getJson('/api/modalidades/999/horarios')
            ->assertNotFound()
            ->assertJson([
                'message' => 'Modalidade não encontrada.',
            ]);
    }
}
