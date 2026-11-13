<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professores | Linux Fit</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

@include('partials.menu', ['menuPage' => 'professores'])

<main class="alunos-page">
    <section class="container alunos-container">

        <header class="alunos-heading">
            <p class="alunos-eyebrow">Conheça nossa equipe</p>

            <h1 class="section-title">
                Cadastro de
                <span class="text-neon-green">Professores</span>
            </h1>

            <p>Cadastre os professores da Linux Fit.</p>
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

        <form action="/professores" method="POST" class="alunos-form">
            @csrf

            <div class="form-group">
                <label for="nome">Nome</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="100"
                    placeholder="Nome do professor"
                    required>
            </div>

            <div class="form-group">
                <label for="modalidades">Modalidades</label>
                <input
                    type="text"
                    id="modalidades"
                    name="modalidades"
                    maxlength="100"
                    placeholder="Musculação, Funcional..."
                    required>
            </div>

            <div class="form-group">
                <label for="especialidades">Especialidades</label>
                <input
                    type="text"
                    id="especialidades"
                    name="especialidades"
                    maxlength="100"
                    placeholder="Hipertrofia, Emagrecimento..."
                    required>
            </div>

            <button type="submit"
                    class="btn btn-primary alunos-submit">
                Cadastrar Professor
            </button>

        </form>

    </section>
</main>

</body>
</html>