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
                'imagem' => '/imagens/musculação.jpg',
                'imagem_professores' => '/imagens/professores_musculacao.jpg',
                'professores' => ['Carlos Silva', 'Fernanda Rocha'],
                'biografias' => [
                    'Carlos Silva' => 'Especialista em biomecânica do movimento e treinamento de força, Carlos possui mais de 8 anos de experiência em hipertrofia e reabilitação funcional. Sua metodologia é focada em execução técnica perfeita e alta performance.',
                    'Fernanda Rocha' => 'Graduada em Educação Física com pós-graduação em Fisiologia do Exercício, Fernanda é referência em treino de força feminino e condicionamento físico geral, combinando intensidade com acompanhamento personalizado.',
                ],
                'horarios' => ['Seg/qua/sex — 07:00', 'Ter/qui — 18:00'],
            ],
            [
                'id' => 2,
                'slug' => 'pilates',
                'nome' => 'Pilates',
                'descricao' => 'Fortalecimento, flexibilidade e consciência corporal.',
                'icone' => '🧘',
                'imagem' => '/imagens/pilates.jpg',
                'imagem_professores' => '/imagens/professores_pilates.jpg',
                'professores' => ['Ana Costa', 'Juliana Lima'],
                'biografias' => [
                    'Ana Costa' => 'Fisioterapeuta e instrutora certificada no método Pilates Clássico e Contemporâneo. Com foco em correção postural, prevenção de lesões e fortalecimento do core, ajuda alunos de todos os níveis a conquistarem mais mobilidade.',
                    'Juliana Lima' => 'Especialista em Pilates em Equipamentos e mat Pilates, Juliana dedica sua carreira à promoção da consciência corporal, alívio de dores crônicas e flexibilidade integrada ao bem-estar.',
                ],
                'horarios' => ['Seg/qua — 08:00', 'Ter/qui — 19:00'],
            ],
            [
                'id' => 3,
                'slug' => 'funcional',
                'nome' => 'Funcional',
                'descricao' => 'Exercícios dinâmicos para o corpo todo e o condicionamento.',
                'icone' => '🔥',
                'imagem' => '/imagens/treino funcional.jpg',
                'imagem_professores' => '/imagens/professores_funcional.jpg',
                'professores' => ['Mariana Alves', 'Bruno Santos'],
                'biografias' => [
                    'Mariana Alves' => 'Pós-graduada em Treinamento Desportivo, Mariana elabora dinâmicas de alta energia com foco em agilidade, resistência cardiovascular e perda de gordura, sempre adaptando os exercícios ao ritmo de cada aluno.',
                    'Bruno Santos' => 'Com bagagem no atletismo e na preparação física de atletas, Bruno traz para o funcional um treino dinâmico focado em força explosiva, coordenação e ganho de capacidade física global.',
                ],
                'horarios' => ['Seg/qua/sex — 06:30', 'Ter/qui — 18:30'],
            ],
            [
                'id' => 4,
                'slug' => 'spinning',
                'nome' => 'Spinning',
                'descricao' => 'Aulas de ciclismo indoor com energia e ritmo intensos.',
                'icone' => '🚴',
                'imagem' => '/imagens/spinning.jpg',
                'imagem_professores' => '/imagens/professores_spinning.jpg',
                'professores' => ['Rafael Mendes', 'Lucas Pereira'],
                'biografias' => [
                    'Rafael Mendes' => 'Entusiasta de ciclismo de alta intensidade e ritmos envolventes, Rafael planeja aulas de Spinning focadas em queima calórica extrema, controle de frequência cardíaca e superação de limites.',
                    'Lucas Pereira' => 'Especialista em cardiorrespiratório e simulação de terrenos em bike indoor, Lucas transforma as aulas em uma experiência motivacional imersiva para elevar a resistência física de forma segura e divertida.',
                ],
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
                'imagem' => $modalidade['imagem'],
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
                    'imagem_professores' => $modalidade['imagem_professores'],
                    'professores' => $modalidade['professores'],
                    'biografias' => $modalidade['biografias'],
                    'horarios' => $modalidade['horarios'],
                ]);
            }
        }

        return response()->json(['message' => 'Modalidade não encontrada.'], 404);
    }
}
