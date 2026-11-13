<?php

namespace App\Http\Controllers;

use App\Services\AulasService;
use Illuminate\Support\Facades\File;

class PlanoController extends Controller
{
    private function caminhoArquivo()
    {
        return storage_path('app/planos.json');
    }

    private function lerPlanos()
    {
        if (!File::exists($this->caminhoArquivo())) {
            return [];
        }

        $conteudo = File::get($this->caminhoArquivo());

        return json_decode($conteudo, true) ?? [];
    }

    public function index()
    {
        $planos = array_filter($this->lerPlanos(), fn ($plano) => $plano['ativo']);

        return response()->json(array_values($planos));
    }

    public function home(AulasService $aulasService)
    {
        $planos = array_filter($this->lerPlanos(), fn ($plano) => $plano['ativo']);

        return view('index', [
            'planos' => array_values($planos),
            'aulas' => $aulasService->aulasComStatus(),
        ]);
    }

    public function show($id)
    {
        $plano = collect($this->lerPlanos())->firstWhere('id', (int) $id);

        if (!$plano || !$plano['ativo']) {
            abort(404);
        }

        return view('plano', ['plano' => $plano]);
    }
}