<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de IMC | Linux Fit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
    @include('partials.menu', ['menuPage' => 'imc'])

    <main class="imc-page">
        <section class="section">
            <div class="container">
                @if(session()->has('bmi'))
                    <div class="imc-result-only" role="status">
                        <strong>{{ number_format(session('bmi'), 1, ',', '.') }}</strong>
                        <p>{{ session('category') }}</p>
                        <a href="{{ route('imc.index') }}" class="btn btn-primary">Calcular novamente</a>
                    </div>
                @else
                <div class="imc-heading">
                    <p class="imc-eyebrow">Acompanhe sua saúde</p>
                    <h1 class="section-title">Calculadora de <span class="text-neon-green">IMC</span></h1>
                    <p>Informe seu peso e altura para descobrir seu Índice de Massa Corporal.</p>
                </div>

                <div class="imc-grid">
                    <form action="{{ route('imc.calculate') }}" method="POST" class="imc-form">
                        @csrf

                        <div class="form-group">
                            <label for="weight">Peso</label>
                            <div class="imc-input-wrap">
                                <input
                                    type="number"
                                    id="weight"
                                    name="weight"
                                    value="{{ old('weight') }}"
                                    min="1"
                                    max="500"
                                    step="0.01"
                                    placeholder="70"
                                    required
                                >
                                <span>kg</span>
                            </div>
                            @error('weight')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="height">Altura</label>
                            <div class="imc-input-wrap">
                                <input
                                    type="number"
                                    id="height"
                                    name="height"
                                    value="{{ old('height') }}"
                                    min="0.50"
                                    max="3"
                                    step="0.01"
                                    placeholder="1,75"
                                    required
                                >
                                <span>m</span>
                            </div>
                            @error('height')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary imc-submit">Calcular meu IMC</button>
                    </form>

                    <aside class="imc-info">
                        <div class="imc-result imc-result-empty">
                            <span>Seu resultado</span>
                            <strong>--</strong>
                            <p>Preencha os campos ao lado</p>
                        </div>
                    </aside>
                </div>

                <div class="imc-scale" aria-label="Faixas de classificação do IMC">
                    <div><span class="imc-dot imc-dot-low"></span><p><strong>Abaixo de 18,5</strong><br>Abaixo do peso</p></div>
                    <div><span class="imc-dot imc-dot-normal"></span><p><strong>18,5 – 24,9</strong><br>Peso normal</p></div>
                    <div><span class="imc-dot imc-dot-high"></span><p><strong>25 – 29,9</strong><br>Sobrepeso</p></div>
                    <div><span class="imc-dot imc-dot-obesity"></span><p><strong>A partir de 30</strong><br>Obesidade</p></div>
                </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
