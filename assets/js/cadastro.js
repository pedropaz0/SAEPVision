const $ = (id) => document.getElementById(id);

const campos = {
    nome: 'erroNome',
    email: 'erroEmail',
    senha: 'erroSenha',
    confirmar: 'erroConfirmar',
    tipo: 'erroTipo',
    imagem: 'erroImagem'
};

$('formCadastroUsuario').addEventListener('submit', async (evento) => {
    evento.preventDefault();

    const dados = new FormData();
    ['nome', 'email', 'senha', 'confirmar', 'tipo'].forEach((c) => dados.append(c, $(c).value));

    if ($('imagem').files[0]) {
        dados.append('imagem', $('imagem').files[0]);
    }

    const resposta = await fetch('api.php?acao=cadastro', { method: 'POST', body: dados, credentials: 'same-origin' });
    const r = await resposta.json();

    if (r.ok) {
        window.location.href = 'index.php';
        return;
    }

    Object.keys(campos).forEach((c) => {
        $(campos[c]).textContent = r.erros?.[c] || '';
    });
    $('erroGeral').textContent = r.erro || '';
});
