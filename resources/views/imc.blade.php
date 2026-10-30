<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de IMC | Linux Fit</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
    <main class="container" style="max-width: 600px; padding-block: 80px;">
        <h1 class="section-title">Calculadora de <span class="text-neon-green">IMC</span></h1>

        <form action="{{ route('imc.calculate') }}" method="POST" class="contact-form">
            @csrf

            <div class="form-group">
                <label for="weight">Peso (kg)</label>
                <input
                    type="number"
                    id="weight"
                    name="weight"
                    value="{{ old('weight') }}"
                    min="1"
                    max="500"
                    step="0.01"
                    placeholder="Ex: 70"
                    required
                >
                @error('weight')
                    <span style="color: #dc3545;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="height">Altura (m)</label>
                <input
                    type="number"
                    id="height"
                    name="height"
                    value="{{ old('height') }}"
                    min="0.50"
                    max="3"
                    step="0.01"
                    placeholder="Ex: 1.75"
                    required
                >
                @error('height')
                    <span style="color: #dc3545;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Calcular IMC</button>
        </form>

        @if(session()->has('bmi'))
            <div class="form-success show" style="margin-top: 20px;">
                <strong>Seu IMC: {{ number_format(session('bmi'), 2, ',', '.') }}</strong>
                <br>
                Classificação: {{ session('category') }}
            </div>
        @endif
    </main>
</body>
</html>
