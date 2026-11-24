@extends('layouts.app')

@section('title', 'Redefinir senha | Linux Fit')

@section('content')
  <main class="alunos-page student-area-page">
    <section class="container alunos-container student-login-container" aria-labelledby="password-reset-title">
      <header class="alunos-heading">
        <h1 class="section-title" id="password-reset-title">Redefinir senha</h1>
        <p>Nova senha da conta de {{ $tipo === 'professor' ? 'professor' : 'aluno' }}</p>
      </header>

      <div class="alunos-form student-login-card">
        @if (! $tokenIsValid)
          <div class="alunos-msg-erro" role="alert">
            {{ $errors->first('token') ?: 'Este link de redefinição é inválido ou expirou. Solicite um novo link.' }}
          </div>

          <a
            class="student-login-forgot"
            href="{{ route('password.request', ['tipo' => $tipo]) }}"
          >
            Solicitar novo link
          </a>
        @else
          @if ($errors->any())
            <div class="alunos-msg-erro" role="alert">
              <ul>
                @foreach ($errors->all() as $erro)
                  <li>{{ $erro }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form
            class="student-login-form"
            method="POST"
            action="{{ route('password.update', ['token' => $token, 'email' => $email, 'tipo' => $tipo]) }}"
          >
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="tipo" value="{{ $tipo }}">

            <div class="form-group">
              <label for="password">Nova senha</label>
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                minlength="6"
                required
              >
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirme a nova senha</label>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                minlength="6"
                required
              >
            </div>

            <button class="btn btn-primary alunos-submit" type="submit">Salvar nova senha</button>
          </form>
        @endif
      </div>
    </section>
  </main>
@endsection
