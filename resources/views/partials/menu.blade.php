@php
  $menuPage = $menuPage ?? 'index';
  $isIndex = $menuPage === 'index';
@endphp
<header class="header" id="header">
  <div class="container header-content">
    <a href="{{ $isIndex ? '#inicio' : '/' }}" class="logo">LINUX<span>FIT</span></a>

    <nav class="nav" id="nav">
      <ul class="nav-list" id="navList">
        <li><a href="{{ $isIndex ? '#inicio' : '/' }}" class="nav-link">Início</a></li>
        <li><a href="{{ $isIndex ? '#sobre' : '/#sobre' }}" class="nav-link">Sobre</a></li>
        <li><a href="{{ $isIndex ? '#modalidades' : '/#modalidades' }}" class="nav-link">Modalidades</a></li>
        <li><a href="{{ route('professores.index') }}" class="nav-link {{ $menuPage === 'professores' ? 'active' : '' }}">Professores</a></li>
        <li><a href="{{ $isIndex ? '#planos' : '/#planos' }}" class="nav-link">Planos</a></li>
        <li><a href="{{ $isIndex ? '#horarios' : '/#horarios' }}" class="nav-link">Horários</a></li>
        <li><a href="{{ $isIndex ? '#aulas' : '/#aulas' }}" class="nav-link {{ $menuPage === 'aulas' ? 'active' : '' }}">Aulas</a></li>
        <li><a href="{{ route('imc.index') }}" class="nav-link {{ $menuPage === 'imc' ? 'active' : '' }}">IMC</a></li>
        <li><a href="{{ $isIndex ? '#contato' : '/#contato' }}" class="nav-link">Contato</a></li>
      </ul>
    </nav>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</header>

@if (! $isIndex)
  <script>
    const menuToggle = document.getElementById('menuToggle');
    const nav = document.getElementById('nav');
    const navLinks = document.querySelectorAll('.nav-link');

    menuToggle?.addEventListener('click', () => {
      nav.classList.toggle('active');
      menuToggle.classList.toggle('active');
    });

    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        nav.classList.remove('active');
        menuToggle.classList.remove('active');
      });
    });
  </script>
@endif
