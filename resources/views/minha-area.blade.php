<h1>Bem-vindo(a), {{ $aluno->nome }}!</h1>

<p>E-mail: {{ $aluno->email }}</p>
<p>Telefone: {{ $aluno->telefone }}</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Sair</button>
</form>