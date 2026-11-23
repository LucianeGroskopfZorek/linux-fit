@extends('layouts.app')

@section('title', 'Minha Área | Linux Fit')

@section('content')
  <main class="alunos-page student-area-page">
    <section class="container alunos-container student-account-container">
      <header class="alunos-heading">
        <h1 class="section-title">Minha Área</h1>
      </header>

      <div class="alunos-form student-account-card">
        <div class="student-account-details">
          <p><strong>Nome:</strong> {{ $aluno->nome }}</p>
          <p><strong>E-mail:</strong> {{ $aluno->email }}</p>
          <p><strong>Telefone:</strong> {{ $aluno->telefone }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-primary alunos-submit" type="submit">Sair</button>
        </form>
      </div>
    </section>
  </main>
@endsection
