// ===== MENU RESPONSIVO =====
const menuToggle = document.getElementById('menuToggle');
const nav = document.getElementById('nav');
const navLinks = document.querySelectorAll('.nav-link');

menuToggle.addEventListener('click', () => {
    nav.classList.toggle('active');
    menuToggle.classList.toggle('active');
});

// Fecha o menu ao clicar em um link (útil no mobile)
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        nav.classList.remove('active');
        menuToggle.classList.remove('active');
    });
});

// ===== MODALIDADES E HORÁRIOS CARREGADOS DA API =====
const listaModalidades = document.getElementById('lista-modalidades');
const painelModalidade = document.getElementById('detalhes-modalidade');
let consultaAtual = 0;

function criarCardModalidade(modalidade) {
    const card = document.createElement('article');
    card.className = 'card modality-card';
    card.dataset.modalidadeId = modalidade.id;

    const icone = document.createElement('span');
    icone.className = 'card-icon';
    icone.setAttribute('aria-hidden', 'true');
    icone.textContent = modalidade.icone;

    const titulo = document.createElement('h3');
    titulo.textContent = modalidade.nome;

    const descricao = document.createElement('p');
    descricao.textContent = modalidade.descricao;

    const botao = document.createElement('button');
    botao.type = 'button';
    botao.className = 'btn btn-primary btn-schedule';
    botao.setAttribute('aria-controls', 'detalhes-modalidade');
    botao.setAttribute('aria-pressed', 'false');
    botao.textContent = 'Ver horários';

    card.append(icone, titulo, descricao, botao);
    return card;
}

async function carregarModalidades() {
    try {
        const resposta = await fetch('/api/modalidades', {
            headers: { Accept: 'application/json' }
        });
        if (!resposta.ok) {
            throw new Error(`Falha ao consultar modalidades (HTTP ${resposta.status}).`);
        }

        const modalidades = await resposta.json();
        if (!Array.isArray(modalidades)) {
            throw new Error('A API retornou uma lista de modalidades inválida.');
        }

        const cards = modalidades.map(modalidade => {
            if (
                !modalidade ||
                !Number.isInteger(modalidade.id) ||
                typeof modalidade.nome !== 'string' ||
                typeof modalidade.descricao !== 'string' ||
                typeof modalidade.icone !== 'string'
            ) {
                throw new Error('A API retornou uma modalidade com dados inválidos.');
            }

            return criarCardModalidade(modalidade);
        });

        if (cards.length === 0) {
            const mensagem = document.createElement('p');
            mensagem.className = 'modalities-message';
            mensagem.textContent = 'Nenhuma modalidade disponível no momento.';
            listaModalidades.replaceChildren(mensagem);
            return;
        }

        listaModalidades.replaceChildren(...cards);
    } catch (erro) {
        console.error('Não foi possível carregar as modalidades:', erro);
        const mensagem = document.createElement('p');
        mensagem.className = 'modalities-message';
        mensagem.textContent =
            'Não foi possível carregar as modalidades. Tente novamente mais tarde.';
        listaModalidades.replaceChildren(mensagem);
    } finally {
        listaModalidades.setAttribute('aria-busy', 'false');
    }
}

async function carregarHorarios(id, cardSelecionado) {
    const consulta = ++consultaAtual;
    painelModalidade.replaceChildren();
    painelModalidade.hidden = false;
    painelModalidade.textContent = 'Carregando horários...';

    listaModalidades.querySelectorAll('.modality-card').forEach(card => {
        const selecionado = card === cardSelecionado;
        card.classList.toggle('selected', selecionado);
        card.querySelector('.btn-schedule')
            .setAttribute('aria-pressed', String(selecionado));
    });

    painelModalidade.scrollIntoView({ behavior: 'smooth', block: 'start' });

    try {
        const resposta = await fetch(`/api/modalidades/${id}/horarios`, {
            headers: { Accept: 'application/json' }
        });
        if (!resposta.ok) {
            throw new Error(`Falha ao consultar horários (HTTP ${resposta.status}).`);
        }

        const dados = await resposta.json();
        if (
            consulta !== consultaAtual ||
            typeof dados.modalidade !== 'string' ||
            typeof dados.professor !== 'string' ||
            !Array.isArray(dados.horarios) ||
            !dados.horarios.every(horario => typeof horario === 'string')
        ) {
            if (consulta === consultaAtual) {
                throw new Error('A API retornou horários em formato inválido.');
            }
            return;
        }

        const titulo = document.createElement('h3');
        titulo.textContent = dados.modalidade;

        const professor = document.createElement('p');
        const rotuloProfessor = document.createElement('strong');
        rotuloProfessor.textContent = 'Professor(a): ';
        professor.append(rotuloProfessor, dados.professor);

        const subtitulo = document.createElement('h4');
        subtitulo.textContent = 'Horários disponíveis:';

        const horarios = document.createElement('ul');
        dados.horarios.forEach(horario => {
            const item = document.createElement('li');
            item.textContent = horario;
            horarios.append(item);
        });

        painelModalidade.replaceChildren(titulo, professor, subtitulo, horarios);
    } catch (erro) {
        if (consulta !== consultaAtual) {
            return;
        }

        console.error('Não foi possível carregar os horários:', erro);
        painelModalidade.textContent =
            'Não foi possível carregar os horários. Tente novamente mais tarde.';
    }
}

listaModalidades.addEventListener('click', evento => {
    const card = evento.target.closest('.modality-card');
    if (!card || !listaModalidades.contains(card)) {
        return;
    }

    carregarHorarios(card.dataset.modalidadeId, card);
});

carregarModalidades();

// ===== DESTAQUE DE PLANO SELECIONADO =====
const planCards = document.querySelectorAll('.plan-card');
const planButtons = document.querySelectorAll('.btn-plan');

planButtons.forEach(button => {
    button.addEventListener('click', () => {
        planCards.forEach(card => card.classList.remove('selected'));
        const card = button.closest('.plan-card');
        card.classList.add('selected');

        const planName = card.querySelector('h3').textContent;
        button.textContent = 'Plano Selecionado ✓';

        setTimeout(() => {
            button.textContent = 'Escolher Plano';
        }, 2000);

        // Rola até o formulário de contato para o usuário prosseguir
        document.getElementById('contato').scrollIntoView({ behavior: 'smooth' });
    });
});

// ===== VALIDAÇÃO DO FORMULÁRIO DE CONTATO =====
const contactForm = document.getElementById('contactForm');
const formSuccess = document.getElementById('formSuccess');

function mostrarErro(campo, mensagem) {
    campo.classList.add('invalid');
    const erroSpan = document.getElementById('erro' + campo.id.charAt(0).toUpperCase() + campo.id.slice(1));
    if (erroSpan) erroSpan.textContent = mensagem;
}

function limparErro(campo) {
    campo.classList.remove('invalid');
    const erroSpan = document.getElementById('erro' + campo.id.charAt(0).toUpperCase() + campo.id.slice(1));
    if (erroSpan) erroSpan.textContent = '';
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    formSuccess.classList.remove('show');

    const nome = document.getElementById('nome');
    const email = document.getElementById('email');
    const assunto = document.getElementById('assunto');
    const mensagem = document.getElementById('mensagem');

    let valido = true;

    if (nome.value.trim().length < 3) {
        mostrarErro(nome, 'Digite seu nome completo.');
        valido = false;
    } else {
        limparErro(nome);
    }

    if (!validarEmail(email.value.trim())) {
        mostrarErro(email, 'Digite um e-mail válido.');
        valido = false;
    } else {
        limparErro(email);
    }

    if (assunto.value.trim().length < 3) {
        mostrarErro(assunto, 'Digite o assunto da mensagem.');
        valido = false;
    } else {
        limparErro(assunto);
    }

    if (mensagem.value.trim().length < 10) {
        mostrarErro(mensagem, 'Sua mensagem deve ter pelo menos 10 caracteres.');
        valido = false;
    } else {
        limparErro(mensagem);
    }

    if (valido) {
        // Front-end apenas: não há envio real ao servidor (conforme RF07)
        formSuccess.classList.add('show');
        contactForm.reset();

        setTimeout(() => {
            formSuccess.classList.remove('show');
        }, 5000);
    }
});

// ===== HEADER COM SOMBRA AO ROLAR + BOTÃO VOLTAR AO TOPO =====
const header = document.getElementById('header');
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.scrollY > 60) {
        header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.4)';
    } else {
        header.style.boxShadow = 'none';
    }

    if (window.scrollY > 400) {
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});

backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ===== DESTAQUE DO LINK ATIVO NO MENU CONFORME A ROLAGEM =====
const sections = document.querySelectorAll('section[id]');

window.addEventListener('scroll', () => {
    let atual = '';

    sections.forEach(section => {
        const topo = section.offsetTop - 100;
        if (window.scrollY >= topo) {
            atual = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active-link');
        if (link.getAttribute('href') === '#' + atual) {
            link.classList.add('active-link');
        }
    });
});