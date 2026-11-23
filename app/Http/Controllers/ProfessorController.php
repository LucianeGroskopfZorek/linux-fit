<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    public function login(): RedirectResponse
    {
        return redirect()->route('login', ['tipo' => 'professor']);
    }

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

    public function cadastrar(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:254', Rule::unique('professores', 'email')],
            'modalidades' => ['required', 'string', 'max:100'],
            'especialidades' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nome.required' => 'Informe o nome do professor.',
            'email.required' => 'Informe o e-mail do professor.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado para outro professor.',
            'modalidades.required' => 'Informe as modalidades do professor.',
            'especialidades.required' => 'Informe as especialidades do professor.',
            'password.required' => 'Digite a senha do professor.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        Professor::query()->create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'modalidades' => $dados['modalidades'],
            'especialidades' => $dados['especialidades'],
            'password' => Hash::make($dados['password']),
        ]);

        return redirect()->route('professor.login')
            ->with('sucesso', 'Professor cadastrado com sucesso. Faça login para continuar.');
    }

    public function entrar(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Informe o e-mail do professor.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'password.required' => 'Digite a senha do professor.',
        ]);

        $professor = Professor::query()
            ->where('email', $dados['email'])
            ->first();

        if (
            $professor === null
            || ! is_string($professor->password)
            || $professor->password === ''
            || ! Hash::check($dados['password'], $professor->password)
        ) {
            return redirect()->route('login', ['tipo' => 'professor'])->withErrors([
                'email' => 'E-mail ou senha incorretos.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('professor_id', $professor->getKey());

        return redirect()->route('professor.minha-area');
    }

    public function minhaArea(Request $request): View|RedirectResponse
    {
        $professorId = $request->session()->get('professor_id');

        if ($professorId === null) {
            return redirect()->route('professor.login');
        }

        $professor = Professor::query()
            ->select(['id', 'nome', 'email', 'modalidades', 'especialidades'])
            ->find($professorId);

        if ($professor === null) {
            $request->session()->forget('professor_id');

            return redirect()->route('professor.login');
        }

        return view('professor-minha-area', ['professor' => $professor]);
    }

    public function sair(Request $request): RedirectResponse
    {
        $request->session()->forget('professor_id');
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('professor.login');
    }
}