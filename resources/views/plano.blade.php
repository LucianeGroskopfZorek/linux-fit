<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $plano['nome'] }} | Linux Fit</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
  @include('partials.menu', ['menuPage' => 'plano'])

  <main class="plano-page">
  <section class="section">
    <div class="container">
      <h2 class="section-title">
        Plano <span class="text-neon-green">{{ $plano['nome'] }}</span>
      </h2>

      <div class="plan-card {{ $plano['destaque'] ? 'plan-featured' : '' }}" style="max-width: 500px; margin: 0 auto;">
        @if ($plano['destaque'])
          <span class="plan-badge">Mais Popular</span>
        @endif
        <p>{{ $plano['descricao'] }}</p>
        <p class="plan-price">
          R$ {{ $plano['preco'] }}<span>/{{ $plano['periodicidade'] }}</span>
        </p>
        <ul class="plan-benefits">
          @foreach ($plano['beneficios'] as $beneficio)
            <li>{{ $beneficio }}</li>
          @endforeach
        </ul>
        <a href="{{ route('aulas.index') }}" class="btn btn-primary">Agendar uma aula</a>
        <a href="/#planos" class="btn btn-outline plano-voltar">Voltar aos planos</a>
      </div>
    </div>
  </section>
  </main>
</body>
</html>