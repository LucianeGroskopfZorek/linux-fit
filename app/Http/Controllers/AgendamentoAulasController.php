<?php

namespace App\Http\Controllers;

use App\Services\AulasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AgendamentoAulasController extends Controller
{
    public function __construct(private AulasService $aulasService)
    {
    }

    private function buscarAula(int $id): ?array
    {
        foreach ($this->aulasService->lerAulas() as $indice => $aula) {
            if ((int) $aula['id'] === $id) {
                $aula['_indice'] = $indice;

                return $aula;
            }
        }

        return null;
    }

    public function index(): View
    {
        return view('aulas', ['aulas' => $this->aulasService->aulasComStatus()]);
    }

    public function agendar(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email'],
            'aula_id' => ['required', 'integer'],
        ], [
            'nome.required' => 'Informe o nome completo.',
            'nome.min' => 'O nome completo deve ter pelo menos 3 caracteres.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'aula_id.required' => 'Selecione uma aula.',
            'aula_id.integer' => 'A aula selecionada é inválida.',
        ]);

        if ($validator->fails()) {
            return redirect('/#aulas')->withErrors($validator)->withInput();
        }

        $dados = $validator->validated();

        $aula = $this->buscarAula((int) $dados['aula_id']);

        if ($aula === null) {
            return redirect('/#aulas')->withInput()->withErrors(['aula_id' => 'A aula selecionada não existe.']);
        }

        if (($aula['status'] ?? '') === 'cancelada') {
            return redirect('/#aulas')->withInput()->withErrors(['aula_id' => 'Esta aula foi cancelada e não aceita reservas.']);
        }

        if ($this->aulasService->aulaFoiRealizada($aula)) {
            return redirect('/#aulas')->withInput()->withErrors(['aula_id' => 'Esta aula já foi realizada.']);
        }

        if ($this->aulasService->vagasLivres($aula) === 0) {
            return redirect('/#aulas')->withInput()->withErrors(['aula_id' => 'Esta aula está lotada e não possui vagas disponíveis.']);
        }

        $email = mb_strtolower(trim($dados['email']));
        $agendamentos = $this->aulasService->lerAgendamentos();

        foreach ($agendamentos as $agendamento) {
            if ((int) $agendamento['aula_id'] === (int) $aula['id']
                && mb_strtolower(trim($agendamento['email'])) === $email
                && ($agendamento['status'] ?? '') === 'confirmado') {
                return redirect('/#aulas')->withInput()->withErrors(['email' => 'Este e-mail já possui uma reserva confirmada nesta aula.']);
            }
        }

        $novoId = collect($agendamentos)->max('id');
        $novoId = is_numeric($novoId) ? ((int) $novoId + 1) : 1;

        $agendamentos[] = [
            'id' => $novoId,
            'aula_id' => (int) $aula['id'],
            'nome' => trim($dados['nome']),
            'email' => $email,
            'status' => 'confirmado',
            'criado_em' => now()->toIso8601String(),
        ];

        $aulas = $this->aulasService->lerAulas();
        $indice = (int) $aula['_indice'];
        $aulas[$indice]['vagas_ocupadas'] = (int) $aulas[$indice]['vagas_ocupadas'] + 1;

        $this->aulasService->gravarJson($this->aulasService->caminhoArquivoAgendamentos(), $agendamentos);
        $this->aulasService->gravarJson($this->aulasService->caminhoArquivoAulas(), $aulas);

        return redirect('/#aulas')->with('success', sprintf(
            'Reserva confirmada para %s na aula de %s, em %s às %s.',
            trim($dados['nome']),
            $aula['modalidade'],
            Carbon::createFromFormat('Y-m-d', $aula['data'])->format('d/m/Y'),
            $aula['horario']
        ));
    }

    public function cancelar(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'reserva_id' => ['required', 'integer'],
        ], [
            'email.required' => 'Informe o e-mail usado na reserva.',
            'email.email' => 'Informe um e-mail válido.',
            'reserva_id.required' => 'Informe o número da reserva.',
            'reserva_id.integer' => 'O número da reserva é inválido.',
        ]);

        if ($validator->fails()) {
            return redirect('/#aulas')->withErrors($validator)->withInput();
        }

        $dados = $validator->validated();

        $email = mb_strtolower(trim($dados['email']));
        $agendamentos = $this->aulasService->lerAgendamentos();
        $indiceReserva = null;

        foreach ($agendamentos as $indice => $agendamento) {
            if ((int) $agendamento['id'] === (int) $dados['reserva_id']
                && mb_strtolower(trim($agendamento['email'])) === $email) {
                $indiceReserva = $indice;
                break;
            }
        }

        if ($indiceReserva === null) {
            return redirect('/#aulas')->withInput()->withErrors(['cancelamento' => 'Reserva não encontrada para este e-mail.']);
        }

        if (($agendamentos[$indiceReserva]['status'] ?? '') === 'cancelado') {
            return redirect('/#aulas')->withInput()->withErrors(['cancelamento' => 'Esta reserva já está cancelada.']);
        }

        $aula = $this->buscarAula((int) $agendamentos[$indiceReserva]['aula_id']);

        if ($aula === null) {
            return redirect('/#aulas')->withInput()->withErrors(['cancelamento' => 'A aula vinculada à reserva não foi encontrada.']);
        }

        if ($this->aulasService->aulaFoiRealizada($aula)) {
            return redirect('/#aulas')->withInput()->withErrors(['cancelamento' => 'Não é possível cancelar uma reserva de aula já realizada.']);
        }

        $agendamentos[$indiceReserva]['status'] = 'cancelado';
        $aulas = $this->aulasService->lerAulas();
        $indiceAula = (int) $aula['_indice'];
        $aulas[$indiceAula]['vagas_ocupadas'] = max(0, (int) $aulas[$indiceAula]['vagas_ocupadas'] - 1);

        $this->aulasService->gravarJson($this->aulasService->caminhoArquivoAgendamentos(), $agendamentos);
        $this->aulasService->gravarJson($this->aulasService->caminhoArquivoAulas(), $aulas);

        return redirect('/#aulas')->with('success', 'Reserva cancelada com sucesso.');
    }
}
