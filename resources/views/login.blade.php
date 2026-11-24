@extends('layouts.app')

@php
  $tipoLogin = request('tipo') === 'professor' ? 'professor' : 'aluno';
@endphp

@section('title', 'Entrar | Linux Fit')

@section('content')
  <style>
    .cadastro-opcoes {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 28px;
    }

    .cadastro-opcao {
      padding: 12px 24px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: #fff;
      color: var(--ink-700);
      font-family: var(--font-body);
      font-weight: 600;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .cadastro-opcao:hover,
    .cadastro-opcao:focus-visible {
      border-color: var(--primary-red);
      box-shadow: 0 0 0 3px rgba(179, 13, 38, 0.12);
    }

    .cadastro-opcao.ativo {
      border-color: transparent;
      background: linear-gradient(135deg, #c71232 0%, #8d0f24 100%);
      color: #fff;
      box-shadow: 0 12px 22px rgba(179, 13, 38, 0.2);
    }

    @media (max-width: 500px) {
      .cadastro-opcoes {
        gap: 8px;
      }

      .cadastro-opcao {
        flex: 1;
        padding: 12px 16px;
        text-align: center;
      }
    }
  </style>

  <main class="alunos-page student-area-page">
    <section class="container alunos-container student-login-container" aria-labelledby="student-login-title">
      <nav class="cadastro-opcoes" aria-label="Tipo de login">
        <a
          class="cadastro-opcao {{ $tipoLogin === 'aluno' ? 'ativo' : '' }}"
          href="{{ route('login') }}"
          @if ($tipoLogin === 'aluno') aria-current="page" @endif
        >
          Sou aluno
        </a>
        <a
          class="cadastro-opcao {{ $tipoLogin === 'professor' ? 'ativo' : '' }}"
          href="{{ route('login', ['tipo' => 'professor']) }}"
          @if ($tipoLogin === 'professor') aria-current="page" @endif
        >
          Sou professor
        </a>
      </nav>

      <header class="alunos-heading">
        <h1 class="section-title" id="student-login-title">
          {{ $tipoLogin === 'professor' ? 'Área do Professor' : 'Área do Aluno' }}
        </h1>
      </header>

      <div class="alunos-form student-login-card">
        @if (session('status'))
          <div class="alunos-msg-sucesso" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alunos-msg-erro" role="alert">
            <p>Não foi possível entrar. Confira seus dados e tente novamente.</p>
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
          action="{{ $tipoLogin === 'professor' ? route('professor.entrar') : route('login.entrar') }}"
        >
          @csrf

          <div class="form-group">
            <label for="email">E-mail</label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              autocomplete="username"
              required
            >
          </div>

          <div class="form-group">
            <label for="password">Senha</label>
            <input
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
            >
          </div>

          <button class="btn btn-primary alunos-submit" type="submit">Entrar</button>
          <a class="student-login-forgot" href="{{ route('password.request', ['tipo' => $tipoLogin]) }}">Esqueci minha senha</a>
        </form>
      </div>
    </section>
  </main>
@endsection
