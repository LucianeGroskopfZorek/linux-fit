@extends('layouts.app')

@section('title', 'Esqueci minha senha | Linux Fit')

@section('content')
  <main class="alunos-page student-area-page">
    <section class="container alunos-container student-login-container" aria-labelledby="password-forgot-title">
      <header class="alunos-heading">
        <h1 class="section-title" id="password-forgot-title">Esqueci minha senha</h1>
        <p>Recuperação da senha de {{ $tipo === 'professor' ? 'professor' : 'aluno' }}</p>
      </header>

      <div class="alunos-form student-login-card">
        @if (session('status'))
          <div class="alunos-msg-sucesso" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alunos-msg-erro" role="alert">
            <ul>
              @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form class="student-login-form" method="POST" action="{{ route('password.email') }}">
          @csrf
          <input type="hidden" name="tipo" value="{{ $tipo }}">

          <div class="form-group">
            <label for="email">E-mail</label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              autocomplete="email"
              maxlength="254"
              required
            >
          </div>

          <button class="btn btn-primary alunos-submit" type="submit">Enviar link</button>
        </form>

        <a class="student-login-forgot" href="{{ route('login', ['tipo' => $tipo]) }}">Voltar ao login</a>
      </div>
    </section>
  </main>
@endsection
