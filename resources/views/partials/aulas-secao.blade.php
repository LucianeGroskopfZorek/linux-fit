<div class="container">
  <h2 class="section-title">Agendamento de <span class="text-neon-orange">Aulas</span></h2>

  @if (session('success'))
    <div class="aulas-alert aulas-alert-success" role="alert">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="aulas-alert aulas-alert-error" role="alert">
      <ul>
        @foreach ($errors->all() as $erro)
          <li>{{ $erro }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section class="aulas-panel">
    <h2>Aulas disponíveis</h2>
    <div class="aulas-table-wrap">
      <table class="aulas-table">
        <thead>
          <tr>
            <th>Professor(a)</th>
            <th>Modalidade</th>
            <th>Data</th>
            <th>Horário</th>
            <th>Vagas</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($aulas as $aula)
            <tr>
              <td>{{ $aula['professor'] }}</td>
              <td>{{ $aula['modalidade'] }}</td>
              <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $aula['data'])->format('d/m/Y') }}</td>
              <td>{{ $aula['horario'] }}</td>
              <td>{{ $aula['_vagas_livres'] }} / {{ $aula['vagas_total'] }}</td>
              <td><span class="aulas-status aulas-status-{{ $aula['_pode_agendar'] ? 'available' : strtolower($aula['_status']) }}">{{ $aula['_status'] }}</span></td>
            </tr>
          @empty
            <tr><td colspan="6">Nenhuma aula cadastrada.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="aulas-grid">
    <section class="aulas-panel">
      <h2>Reservar aula</h2>
      <form method="POST" action="{{ route('aulas.agendar') }}" class="aulas-form">
        @csrf
        <label for="nome">Nome completo</label>
        <input id="nome" name="nome" type="text" value="{{ old('nome') }}" minlength="3" required>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="aula_id">Aula</label>
        <select id="aula_id" name="aula_id" required>
          <option value="">Selecione uma aula disponível</option>
          @foreach ($aulas as $aula)
            @if ($aula['_pode_agendar'])
              <option value="{{ $aula['id'] }}" @selected(old('aula_id') == $aula['id'])>
                {{ $aula['modalidade'] }} — {{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $aula['data'])->format('d/m/Y') }} às {{ $aula['horario'] }} — {{ $aula['professor'] }}
              </option>
            @endif
          @endforeach
        </select>

        <button type="submit" class="btn btn-primary aulas-submit">Confirmar agendamento</button>
      </form>
    </section>

    <section class="aulas-panel">
      <h2>Cancelar reserva</h2>
      <form method="POST" action="{{ route('aulas.cancelar') }}" class="aulas-form">
        @csrf
        <label for="cancelar_email">E-mail</label>
        <input id="cancelar_email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="reserva_id">Número da reserva</label>
        <input id="reserva_id" name="reserva_id" type="number" min="1" value="{{ old('reserva_id') }}" required>

        <button type="submit" class="btn btn-outline aulas-submit">Cancelar reserva</button>
      </form>
    </section>
  </div>
</div>
