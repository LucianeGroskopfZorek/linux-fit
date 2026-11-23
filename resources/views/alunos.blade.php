@php
  $cadastroSolicitado = old('tipo_cadastro', request('tipo'));
  $tipoCadastro = $cadastroSolicitado === 'professor' ? 'professor' : 'aluno';
@endphp
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos | Linux Fit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
  </head>
  <body>
    @include('partials.menu', ['menuPage' => 'alunos'])

    <main class="alunos-page">
      <section class="container alunos-container">
        <nav class="cadastro-opcoes" aria-label="Tipo de cadastro">
          <a
            class="cadastro-opcao {{ $tipoCadastro === 'aluno' ? 'ativo' : '' }}"
            href="{{ route('alunos.index') }}"
            @if ($tipoCadastro === 'aluno') aria-current="page" @endif
          >
            Sou aluno
          </a>
          <a
            class="cadastro-opcao {{ $tipoCadastro === 'professor' ? 'ativo' : '' }}"
            href="{{ route('alunos.index', ['tipo' => 'professor']) }}"
            @if ($tipoCadastro === 'professor') aria-current="page" @endif
          >
            Sou professor
          </a>
        </nav>

        <header class="alunos-heading">
          <p class="alunos-eyebrow">Faça parte da nossa comunidade</p>
          <h1 class="section-title">Cadastro de <span class="text-neon-green">{{ $tipoCadastro === 'professor' ? 'Professores' : 'Alunos' }}</span></h1>
          <p>Preencha seus dados para começar sua jornada na Linux Fit.</p>
        </header>

        @if (session('sucesso'))
          <div class="alunos-msg-sucesso" role="status">
            {{ session('sucesso') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="alunos-msg-erro" role="alert">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if ($tipoCadastro === 'aluno')
          <form action="/alunos" method="POST" class="alunos-form">
            @csrf

            <div class="form-group">
              <label for="nome">Nome</label>
              <input
                type="text"
                id="nome"
                name="nome"
                autocomplete="name"
                maxlength="100"
                placeholder="Seu nome completo"
                required
              >
            </div>

            <div class="form-group">
              <label for="email">E-mail</label>
              <input
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                maxlength="254"
                placeholder="voce@exemplo.com"
                required
              >
            </div>

            <div class="form-group">
              <label for="telefone">Telefone</label>
              <input
                type="tel"
                id="telefone"
                name="telefone"
                autocomplete="tel"
                inputmode="tel"
                maxlength="20"
                placeholder="(00) 00000-0000"
                required
              >
            </div>

            <div class="form-group">
              <label for="password">Senha</label>
              <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="6"
                placeholder="Mínimo de 6 caracteres"
                required
              >
            </div>

            <button type="submit" class="btn btn-primary alunos-submit">Cadastrar</button>
          </form>
        @else
          <form action="{{ route('professores.cadastro') }}" method="POST" class="alunos-form">
            @csrf
            <input type="hidden" name="tipo_cadastro" value="professor">

            <div class="form-group">
              <label for="nome">Nome</label>
              <input
                type="text"
                id="nome"
                name="nome"
                value="{{ old('nome') }}"
                autocomplete="name"
                maxlength="100"
                placeholder="Nome completo"
                required
              >
            </div>

            <div class="form-group">
              <label for="email">E-mail</label>
              <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                maxlength="254"
                placeholder="voce@exemplo.com"
                required
              >
            </div>

            <div class="form-group">
              <label for="modalidades">Modalidades</label>
              <input
                type="text"
                id="modalidades"
                name="modalidades"
                value="{{ old('modalidades') }}"
                maxlength="100"
                placeholder="Musculação, Funcional..."
                required
              >
            </div>

            <div class="form-group">
              <label for="especialidades">Especialidades</label>
              <input
                type="text"
                id="especialidades"
                name="especialidades"
                value="{{ old('especialidades') }}"
                maxlength="100"
                placeholder="Hipertrofia, Emagrecimento..."
                required
              >
            </div>

            <div class="form-group">
              <label for="password">Senha</label>
              <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="6"
                placeholder="Mínimo de 6 caracteres"
                required
              >
            </div>

            <button type="submit" class="btn btn-primary alunos-submit">Cadastrar Professor</button>
          </form>
        @endif

      </section>
    </main>
  </body>
</html>