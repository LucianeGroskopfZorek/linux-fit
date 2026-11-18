<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        'telefone' => 'nullable|string|max:20',
        'password' => 'required|string|min:6',
    ], [
        'password.required' => 'Digite a senha do aluno.',
        'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
    ]);

    DB::insert(
        'INSERT INTO alunos (nome, email, telefone, password) VALUES (?, ?, ?, ?)',
        [
            $dados['nome'],
            $dados['email'],
            $dados['telefone'] ?? null,
            Hash::make($dados['password']),
        ]
    );

    return redirect('/alunos')
        ->with('sucesso', 'Aluno cadastrado com sucesso!');
}
    
public function entrar(Request $request)
{
    $dados = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $aluno = DB::selectOne(
        'SELECT id, password FROM alunos WHERE email = ?',
        [$dados['email']]
    );

    if (!$aluno || !$aluno->password || !Hash::check($dados['password'], $aluno->password)) {
        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos.',
        ])->onlyInput('email');
    }

    $request->session()->regenerate();
    $request->session()->put('aluno_id', $aluno->id);

    return redirect()->route('minha-area');
}

public function minhaArea(Request $request)
{
    $alunoId = $request->session()->get('aluno_id');

    if ($alunoId === null) {
        return redirect()->route('login');
    }

    $aluno = DB::selectOne(
        'SELECT id, nome, email, telefone FROM alunos WHERE id = ?',
        [$alunoId]
    );

    if (!$aluno) {
        $request->session()->forget('aluno_id');

        return redirect()->route('login');
    }

    return view('minha-area', ['aluno' => $aluno]);
}

    public function sair(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    
}
