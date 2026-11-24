const estado = {
    usuario: null,
    filtro: { tipo: 'todas', id: 0 }
};

const $ = (id) => document.getElementById(id);
const limpar = (...ids) => ids.forEach((id) => { $(id).textContent = ''; });

async function api(acao, params = {}, metodo = 'GET') {
    let url = 'api.php?acao=' + acao;
    const opcoes = { method: metodo, credentials: 'same-origin' };

    if (metodo === 'GET') {
        url += '&' + new URLSearchParams(params);
    } else {
        opcoes.body = params instanceof FormData ? params : new URLSearchParams(params);
    }

    const resposta = await fetch(url, opcoes);
    return resposta.json();
}

function atualizarCabecalho() {
    const u = estado.usuario;

    $('fotoUsuario').src = 'assets/imagens_usuarios/' + (u ? u.imagem : 'saepVision.jpeg');
    $('nomeUsuario').textContent = '@' + (u ? u.nome : 'SAEPVision');
    $('btnPerfil').disabled = !!u && u.tipo !== 'fotografo';
    $('btnLogin').hidden = !!u;
    $('btnLogout').hidden = !u;
    $('campoBusca').placeholder = u
        ? 'Digite o nome do fotógrafo'
        : 'Digite o nome do fotógrafo... ex: @fotografo1';

    if (!u || u.tipo !== 'fotografo') {
        fecharMenu();
    }

    atualizarAreaCadastro();
}

function atualizarAreaCadastro() {
    const u = estado.usuario;
    $('areaCadastro').hidden = !(u && u.tipo === 'fotografo' && estado.filtro.tipo === 'minhas');
}

function abrirLogin() {
    $('formLogin').reset();
    limpar('erroEmail', 'erroSenha', 'erroLogin');
    $('modalLogin').hidden = false;
    $('email').focus();
}

function fecharLogin() {
    $('modalLogin').hidden = true;
}

async function enviarLogin(evento) {
    evento.preventDefault();

    const r = await api('login', { email: $('email').value.trim(), senha: $('senha').value }, 'POST');

    $('erroEmail').textContent = r.erros?.email || '';
    $('erroSenha').textContent = r.erros?.senha || '';
    $('erroLogin').textContent = r.erro || '';

    if (!r.ok) {
        return;
    }

    estado.usuario = r.usuario;
    fecharLogin();
    atualizarCabecalho();
    carregarGaleria();
}

async function sair() {
    await api('logout', {}, 'POST');
    window.location.href = 'index.php';
}

async function carregarPerfil() {
    const r = await api('perfil');

    if (r.ok) {
        $('totalLikes').textContent = r.likes;
        $('totalPublicacoes').textContent = r.publicacoes;
    }
}

async function abrirMenu() {
    if (!estado.usuario) {
        abrirLogin();
        return;
    }

    await carregarPerfil();
    $('menuLateral').hidden = false;
}

function fecharMenu() {
    $('menuLateral').hidden = true;
}

function mostrarSuasPublicacoes() {
    estado.filtro = { tipo: 'minhas', id: 0 };
    $('campoBusca').value = '';
    limpar('erroBusca');
    fecharMenu();
    atualizarAreaCadastro();
    carregarGaleria();
}

async function buscar(evento) {
    evento.preventDefault();

    if (!estado.usuario) {
        abrirLogin();
        return;
    }

    const valor = $('campoBusca').value.trim();
    limpar('erroBusca');

    if (valor === '') {
        estado.filtro = { tipo: 'todas', id: 0 };
        atualizarAreaCadastro();
        carregarGaleria();
        return;
    }

    const r = await api('buscar', { nome: valor });

    if (!r.ok) {
        $('erroBusca').textContent = r.erro;
        estado.filtro = { tipo: 'nenhuma', id: 0 };
        atualizarAreaCadastro();
        desenharGaleria([]);
        return;
    }

    estado.filtro = { tipo: 'fotografo', id: r.id };
    atualizarAreaCadastro();
    carregarGaleria();
}

async function buscarPublicacoes() {
    const f = estado.filtro;

    if (f.tipo === 'nenhuma') {
        return [];
    }

    let params = {};
    if (f.tipo === 'fotografo') params = { fotografo: f.id };
    if (f.tipo === 'minhas') params = { minhas: 1 };

    const r = await api('publicacoes', params);
    return r.ok ? r.publicacoes : [];
}

async function carregarGaleria() {
    desenharGaleria(await buscarPublicacoes());
}

function desenharGaleria(lista) {
    const galeria = $('galeria');
    galeria.replaceChildren();

    if (lista.length === 0) {
        const vazio = document.createElement('p');
        vazio.className = 'vazio';
        vazio.textContent = 'Nenhuma foto encontrada.';
        galeria.appendChild(vazio);
        return;
    }

    lista.forEach((p) => galeria.appendChild(montarCard(p)));
}

function montarCard(p) {
    const card = document.createElement('article');
    card.className = 'card';
    card.dataset.id = p.id;

    const titulo = document.createElement('h3');
    titulo.textContent = p.titulo;

    const caixa = document.createElement('div');
    caixa.className = 'imagem';

    const img = document.createElement('img');
    img.src = p.url;
    img.alt = p.titulo;

    const dica = document.createElement('div');
    dica.className = 'dica';
    const tirada = document.createElement('span');
    tirada.textContent = 'Foto tirada por: @' + p.fotografo;
    const local = document.createElement('span');
    local.textContent = 'Local: ' + (p.local || '');
    dica.append(tirada, local);
    caixa.append(img, dica);

    const linha = document.createElement('div');
    linha.className = 'linha';

    const botao = document.createElement('button');
    botao.type = 'button';
    botao.className = 'curtir';
    botao.innerHTML = '<span class="coracao"></span><span class="qtd"></span>';
    atualizarCurtida(botao, p.curtiu, p.curtidas);
    botao.addEventListener('click', () => curtir(p.id, botao));
    linha.appendChild(botao);

    if (estado.filtro.tipo === 'minhas') {
        const lixeira = document.createElement('button');
        lixeira.type = 'button';
        lixeira.className = 'excluir';
        lixeira.title = 'Excluir publicação';
        lixeira.innerHTML = '<img src="assets/icones/lixeira.svg" alt="Excluir">';
        lixeira.addEventListener('click', () => excluir(p.id));
        linha.appendChild(lixeira);
    }

    card.append(titulo, caixa, linha);
    return card;
}

function atualizarCurtida(botao, curtiu, total) {
    botao.classList.toggle('curtido', curtiu);
    botao.querySelector('.qtd').textContent = total;
}

async function curtir(id, botao) {
    if (!estado.usuario) {
        abrirLogin();
        return;
    }

    const r = await api('curtir', { id_publicacao: id }, 'POST');

    if (r.ok) {
        atualizarCurtida(botao, r.curtiu, r.total);

        if (!$('menuLateral').hidden) {
            carregarPerfil();
        }
    }
}

async function excluir(id) {
    if (!confirm('Deseja excluir esta publicação?')) {
        return;
    }

    const r = await api('excluir', { id_publicacao: id }, 'POST');

    if (r.ok) {
        carregarGaleria();
        carregarPerfil();
    }
}

async function enviarCadastro(evento) {
    evento.preventDefault();

    const dados = new FormData();
    dados.append('titulo', $('titulo').value);
    dados.append('local', $('local').value);

    if ($('imagem').files[0]) {
        dados.append('imagem', $('imagem').files[0]);
    }

    const r = await api('cadastrar', dados, 'POST');

    $('erroTitulo').textContent = r.erros?.titulo || '';
    $('erroLocal').textContent = r.erros?.local || '';
    $('erroImagem').textContent = r.erros?.imagem || r.erro || '';

    if (!r.ok) {
        return;
    }

    $('formCadastro').reset();
    carregarGaleria();
    carregarPerfil();
}

async function iniciar() {
    $('btnPerfil').addEventListener('click', abrirMenu);
    $('fecharMenu').addEventListener('click', fecharMenu);
    $('btnSuasPublicacoes').addEventListener('click', mostrarSuasPublicacoes);
    $('btnLogin').addEventListener('click', abrirLogin);
    $('btnLogout').addEventListener('click', sair);
    $('fecharLogin').addEventListener('click', fecharLogin);
    $('btnCancelar').addEventListener('click', fecharLogin);
    $('formLogin').addEventListener('submit', enviarLogin);
    $('formBusca').addEventListener('submit', buscar);
    $('formCadastro').addEventListener('submit', enviarCadastro);

    $('campoBusca').addEventListener('click', () => {
        if (!estado.usuario) {
            abrirLogin();
        }
    });

    const sessao = await api('sessao');
    estado.usuario = sessao.usuario;
    atualizarCabecalho();
    carregarGaleria();
}

iniciar();
