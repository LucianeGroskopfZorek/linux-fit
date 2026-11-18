<h1>Login do aluno</h1>

<form method="POST" action="{{ route('login.entrar') }}">
    @csrf

    <label for="email">E-mail</label>
    <input id="email" name="email" type="email"
           value="{{ old('email') }}" autocomplete="username" required>

    <label for="password">Senha</label>
    <input id="password" name="password" type="password"
           autocomplete="current-password" required>

    <button type="submit">Entrar</button>

    @foreach ($errors->all() as $erro)
        <p>{{ $erro }}</p>
    @endforeach
</for