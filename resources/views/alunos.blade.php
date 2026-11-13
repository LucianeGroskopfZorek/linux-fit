<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos | Linux Fit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
  </head>
  <body>
    @include('partials.menu', ['menuPage' => 'alunos'])

    <main class="alunos-page">
      <section class="container alunos-container">
        <header class="alunos-heading">
          <p class="alunos-eyebrow">Faça parte da nossa comunidade</p>
          <h1 class="section-title">Cadastro de <span class="text-neon-green">Alunos</span></h1>
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

          <button type="submit" class="btn btn-primary alunos-submit">Cadastrar</button>
        </form>
      </section>
    </main>
  </body>
</html>