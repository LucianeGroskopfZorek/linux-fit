<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class AulasService
{
    private function caminhoAulas(): string
    {
        return storage_path('app/aulas.json');
    }

    private function caminhoAgendamentos(): string
    {
        return storage_path('app/agendamentos.json');
    }

    public function lerJson(string $caminho): array
    {
        if (! File::exists($caminho)) {
            return [];
        }

        $conteudo = trim(File::get($caminho));
        if ($conteudo === '') {
            return [];
        }

        $dados = json_decode($conteudo, true);

        return is_array($dados) ? $dados : [];
    }

    public function lerAulas(): array
    {
        return $this->lerJson($this->caminhoAulas());
    }

    public function lerAgendamentos(): array
    {
        return $this->lerJson($this->caminhoAgendamentos());
    }

    public function gravarJson(string $caminho, array $dados): void
    {
        File::ensureDirectoryExists(dirname($caminho));
        file_put_contents($caminho, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function aulasComStatus(): array
    {
        return array_map(function (array $aula): array {
            $aula['_vagas_livres'] = $this->vagasLivres($aula);
            $aula['_realizada'] = $this->aulaFoiRealizada($aula);

            if (($aula['status'] ?? '') === 'cancelada') {
                $aula['_status'] = 'Cancelada';
            } elseif ($aula['_realizada']) {
                $aula['_status'] = 'Realizada';
            } elseif ($aula['_vagas_livres'] === 0) {
                $aula['_status'] = 'Lotada';
            } else {
                $aula['_status'] = 'Disponível';
            }

            $aula['_pode_agendar'] = $aula['_status'] === 'Disponível';

            return $aula;
        }, $this->lerAulas());
    }

    public function caminhoArquivoAulas(): string
    {
        return $this->caminhoAulas();
    }

    public function caminhoArquivoAgendamentos(): string
    {
        return $this->caminhoAgendamentos();
    }

    public function vagasLivres(array $aula): int
    {
        return max(0, (int) $aula['vagas_total'] - (int) $aula['vagas_ocupadas']);
    }

    public function aulaFoiRealizada(array $aula): bool
    {
        $dataHoraAula = Carbon::createFromFormat(
            'Y-m-d H:i',
            $aula['data'].' '.$aula['horario'],
            config('app.timezone')
        );

        return $dataHoraAula->lessThanOrEqualTo(now());
    }
}
