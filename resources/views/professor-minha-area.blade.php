@extends('layouts.app')

@section('title', 'Minha Área do Professor | Linux Fit')
@section('menu-page', 'professores')

@section('content')
  <main class="alunos-page student-area-page">
    <section class="container alunos-container student-account-container">
      <header class="alunos-heading">
        <h1 class="section-title">Minha Área</h1>
      </header>

      <div class="alunos-form student-account-card">
        <div class="student-account-details">
          <p><strong>Nome:</strong> {{ $professor->nome }}</p>
          <p><strong>E-mail:</strong> {{ $professor->email }}</p>
          <p><strong>Modalidades:</strong> {{ $professor->modalidades }}</p>
          <p><strong>Especialidades:</strong> {{ $professor->especialidades }}</p>
        </div>

        <form method="POST" action="{{ route('professor.sair') }}">
          @csrf
          <button class="btn btn-primary alunos-submit" type="submit">Sair</button>
        </form>
      </div>
    </section>
  </main>
@endsection
