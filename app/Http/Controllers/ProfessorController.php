<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfessorController extends Controller
{
    public function index()
    {
        return view('professores');
    }

    public function create(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:100',
            'modalidades' => 'required|string|max:100',
            'especialidades' => 'required|string|max:100',
        ]);

        DB::insert(
            'INSERT INTO professores (nome, modalidades, especialidades)
             VALUES (?, ?, ?)',
            [
                $dados['nome'],
                $dados['modalidades'],
                $dados['especialidades']
            ]
        );

        return redirect('/professores')
            ->with('sucesso', 'Professor cadastrado com sucesso!');
    }
}