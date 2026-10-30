<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Aula | LinuxFit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f0e6;
            display: flex;
            justify-content: center;
            padding: 40px 16px;
        }
        .card {
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        h1 { margin-top: 0; }
        label { display: block; margin: 14px 0 6px; font-weight: bold; }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            margin-top: 24px;
            padding: 12px;
            background: #c0392b;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #a93226; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Agendar aula</h1>

        <form id="form-agendamento">
            <label for="aluno">Nome</label>
            <input type="text" id="aluno" placeholder="Seu nome" required>

            <label for="modalidade">Modalidade</label>
            <select id="modalidade" required>
                <option value="">Escolha...</option>
                <option>Musculação</option>
                <option>Pilates</option>
                <option>Funcional</option>
                <option>Spinning</option>
            </select>

            <label for="data">Data</label>
            <input type="date" id="data" required>

            <label for="horario">Horário</label>
            <input type="time" id="horario" required>

            <button type="submit">Agendar</button>
        </form>
    </div>

    <script>
        document.getElementById('form-agendamento').addEventListener('submit', function (e) {
            e.preventDefault();

            // WhatsApp da academia: 55 + DDD + número, só dígitos
            const numero = '5547992929550';

            const aluno = document.getElementById('aluno').value.trim();
            const modalidade = document.getElementById('modalidade').value;
            const data = document.getElementById('data').value.split('-').reverse().join('/');
            const horario = document.getElementById('horario').value;

            const texto = `Olá! Quero agendar uma aula.\n\nNome: ${aluno}\nModalidade: ${modalidade}\nData: ${data}\nHorário: ${horario}`;

            window.open(`https://wa.me/${numero}?text=${encodeURIComponent(texto)}`, '_blank');
        });
    </script>
</body>
</html>