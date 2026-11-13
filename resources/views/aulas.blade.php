<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Aulas | Linux Fit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
  </head>
  <body>
    @include('partials.menu', ['menuPage' => 'aulas'])

    <main class="aulas-page">
      @include('partials.aulas-secao', ['aulas' => $aulas])
    </main>
  </body>
</html>
