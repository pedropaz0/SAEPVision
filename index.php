<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAEPVision</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <header class="cabecalho">
        <div class="secao-usuario">
            <img id="fotoUsuario" class="foto-usuario" src="assets/imagens_usuarios/saepVision.jpeg" alt="Foto de perfil">
            <span id="nomeUsuario" class="nome-usuario">@SAEPVision</span>
            <button id="btnPerfil" class="btn-preto" type="button">Ver Perfil</button>
        </div>

        <div class="secao-empresa">SAEPVision</div>

        <div class="secao-login">
            <button id="btnLogin" class="btn-preto" type="button">Login</button>
            <button id="btnLogout" class="btn-preto" type="button" hidden>Logout</button>
        </div>
    </header>

    <aside id="menuLateral" class="menu-lateral" hidden>
        <button id="fecharMenu" class="btn-fechar" type="button" aria-label="Fechar"></button>
        <div class="estatistica"><strong id="totalLikes">0</strong> Likes</div>
        <div class="estatistica"><strong id="totalPublicacoes">0</strong> Publicações</div>
        <button id="btnSuasPublicacoes" class="btn-suas" type="button">Suas publicações</button>
    </aside>

    <main class="conteudo">
        <form id="formBusca" class="busca" novalidate>
            <input id="campoBusca" type="text" autocomplete="off"
                placeholder="Digite o nome do fotógrafo... ex: @fotografo1">
            <button id="btnBusca" type="submit" aria-label="Buscar"><img src="assets/icones/lupa.svg" alt=""></button>
        </form>
        <p id="erroBusca" class="erro erro-busca"></p>

        <section id="areaCadastro" class="cadastro" hidden>
            <form id="formCadastro" novalidate>
                <label for="titulo">Título da Imagem</label>
                <input id="titulo" type="text" placeholder="Dedo de Deus">
                <p id="erroTitulo" class="erro"></p>

                <label for="local">Fonte de origem</label>
                <input id="local" type="text" placeholder="Rio de Janeiro">
                <p id="erroLocal" class="erro"></p>

                <label for="imagem">Imagem</label>
                <input id="imagem" type="file" accept="image/jpeg,image/png">
                <p id="erroImagem" class="erro"></p>

                <button class="btn-preto" type="submit">Cadastrar</button>
            </form>
        </section>

        <section id="galeria" class="galeria"></section>
    </main>

    <footer class="rodape">
        <span>SAEPVision</span>
        <nav class="redes">
            <img src="assets/icones/instagram.svg" alt="Instagram">
            <img src="assets/icones/tiktok.svg" alt="TikTok">
            <img src="assets/icones/linkedIn.svg" alt="LinkedIn">
            <img src="assets/icones/youtube.svg" alt="YouTube">
        </nav>
        <span class="direita">Copyright 2026-2027</span>
    </footer>

    <div id="modalLogin" class="modal" hidden>
        <div class="modal-caixa">
            <button id="fecharLogin" class="btn-fechar" type="button" aria-label="Fechar"></button>
            <h2>Login</h2>
            <form id="formLogin" novalidate>
                <input id="email" type="email" placeholder="Digite seu e-mail">
                <p id="erroEmail" class="erro"></p>

                <input id="senha" type="password" placeholder="Digite sua senha">
                <p id="erroSenha" class="erro"></p>

                <p id="erroLogin" class="erro"></p>

                <div class="botoes">
                    <button id="btnCancelar" class="btn-contorno" type="button">Cancelar</button>
                    <button class="btn-preto" type="submit">Login</button>
                </div>
                <p class="link-cadastro">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
            </form>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>

</html>
