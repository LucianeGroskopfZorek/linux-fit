<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    public function index()
    {
        return view('alunos');
    }
    
    public function create(Request $request)
    {
    $dados = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:30',
            'telefone' => 'required|string|max:20',
        ]);
     
        DB::insert('INSERT INTO alunos (nome, email, telefone) VALUES (?, ?, ?)', [$dados['nome'], $dados['email'], $dados['telefone']]);

       return redirect('/alunos')->with('sucesso', 'Aluno cadastrado com sucesso!');
    }
//
}
