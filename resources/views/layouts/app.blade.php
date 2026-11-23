<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Linux Fit | Treine com energia')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('style.css') }}" />
  </head>
  <body class="student-site">
    @include('partials.menu', ['menuPage' => trim($__env->yieldContent('menu-page', 'login'))])

    @yield('content')

    <footer class="footer">
      <div class="container footer-content">
        <a href="{{ url('/') }}" class="logo">LINUX<span>FIT</span></a>
        <p>&copy; 2026 Linux Fit. Todos os direitos reservados.</p>
        <div class="footer-social">
          <a href="#">Instagram</a>
          <a href="#">Facebook</a>
          <a href="#">WhatsApp</a>
        </div>
      </div>
    </footer>
  </body>
</html>
