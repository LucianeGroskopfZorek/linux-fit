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
                'imagem' => '/imagens/musculação.jpg',
            ])
            ->assertJsonFragment([
                'id' => 2,
                'slug' => 'pilates',
                'nome' => 'Pilates',
                'imagem' => '/imagens/pilates.jpg',
            ])
            ->assertJsonFragment([
                'id' => 3,
                'slug' => 'funcional',
                'nome' => 'Funcional',
                'imagem' => '/imagens/treino funcional.jpg',
            ])
            ->assertJsonFragment([
                'id' => 4,
                'slug' => 'spinning',
                'nome' => 'Spinning',
                'imagem' => '/imagens/spinning.jpg',
            ]);
    }

    public function test_schedule_endpoint_returns_the_selected_modality_schedule(): void
    {
        $horariosEsperados = [
            1 => [
                'modalidade' => 'Musculação',
                'imagem_professores' => '/imagens/professores_musculacao.jpg',
                'professores' => ['Carlos Silva', 'Fernanda Rocha'],
                'biografias' => [
                    'Carlos Silva' => 'Especialista em biomecânica do movimento e treinamento de força, Carlos possui mais de 8 anos de experiência em hipertrofia e reabilitação funcional. Sua metodologia é focada em execução técnica perfeita e alta performance.',
                    'Fernanda Rocha' => 'Graduada em Educação Física com pós-graduação em Fisiologia do Exercício, Fernanda é referência em treino de força feminino e condicionamento físico geral, combinando intensidade com acompanhamento personalizado.',
                ],
                'horarios' => ['Seg/qua/sex — 07:00', 'Ter/qui — 18:00'],
            ],
            2 => [
                'modalidade' => 'Pilates',
                'imagem_professores' => '/imagens/professores_pilates.jpg',
                'professores' => ['Ana Costa', 'Juliana Lima'],
                'biografias' => [
                    'Ana Costa' => 'Fisioterapeuta e instrutora certificada no método Pilates Clássico e Contemporâneo. Com foco em correção postural, prevenção de lesões e fortalecimento do core, ajuda alunos de todos os níveis a conquistarem mais mobilidade.',
                    'Juliana Lima' => 'Especialista em Pilates em Equipamentos e mat Pilates, Juliana dedica sua carreira à promoção da consciência corporal, alívio de dores crônicas e flexibilidade integrada ao bem-estar.',
                ],
                'horarios' => ['Seg/qua — 08:00', 'Ter/qui — 19:00'],
            ],
            3 => [
                'modalidade' => 'Funcional',
                'imagem_professores' => '/imagens/professores_funcional.jpg',
                'professores' => ['Mariana Alves', 'Bruno Santos'],
                'biografias' => [
                    'Mariana Alves' => 'Pós-graduada em Treinamento Desportivo, Mariana elabora dinâmicas de alta energia com foco em agilidade, resistência cardiovascular e perda de gordura, sempre adaptando os exercícios ao ritmo de cada aluno.',
                    'Bruno Santos' => 'Com bagagem no atletismo e na preparação física de atletas, Bruno traz para o funcional um treino dinâmico focado em força explosiva, coordenação e ganho de capacidade física global.',
                ],
                'horarios' => ['Seg/qua/sex — 06:30', 'Ter/qui — 18:30'],
            ],
            4 => [
                'modalidade' => 'Spinning',
                'imagem_professores' => '/imagens/professores_spinning.jpg',
                'professores' => ['Rafael Mendes', 'Lucas Pereira'],
                'biografias' => [
                    'Rafael Mendes' => 'Entusiasta de ciclismo de alta intensidade e ritmos envolventes, Rafael planeja aulas de Spinning focadas em queima calórica extrema, controle de frequência cardíaca e superação de limites.',
                    'Lucas Pereira' => 'Especialista em cardiorrespiratório e simulação de terrenos em bike indoor, Lucas transforma as aulas em uma experiência motivacional imersiva para elevar a resistência física de forma segura e divertida.',
                ],
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
