<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAEPVision | Cadastro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <header class="cabecalho">
        <div>
            <a class="btn-preto btn-link" href="index.php">Página principal</a>
        </div>
        <div class="secao-empresa">SAEPVision</div>
        <div></div>
    </header>

    <main class="conteudo">
        <section class="pagina-cadastro">
            <h1>Cadastro</h1>

            <form id="formCadastroUsuario" novalidate>
                <label for="nome">Nome de usuário</label>
                <input id="nome" type="text" placeholder="Digite seu nome de usuário">
                <p id="erroNome" class="erro"></p>

                <label for="email">E-mail</label>
                <input id="email" type="email" placeholder="Digite seu e-mail">
                <p id="erroEmail" class="erro"></p>

                <label for="senha">Senha</label>
                <input id="senha" type="password" placeholder="Digite sua senha">
                <p id="erroSenha" class="erro"></p>

                <label for="confirmar">Confirmar senha</label>
                <input id="confirmar" type="password" placeholder="Digite a senha novamente">
                <p id="erroConfirmar" class="erro"></p>

                <label for="tipo">Tipo de conta</label>
                <select id="tipo">
                    <option value="">Selecione</option>
                    <option value="usuario">Usuário comum</option>
                    <option value="fotografo">Fotógrafo</option>
                </select>
                <p id="erroTipo" class="erro"></p>

                <label for="imagem">Foto de perfil (opcional)</label>
                <input id="imagem" type="file" accept="image/jpeg,image/png">
                <p id="erroImagem" class="erro"></p>

                <p id="erroGeral" class="erro"></p>

                <div class="botoes">
                    <a class="btn-contorno" href="index.php">Cancelar</a>
                    <button class="btn-preto" type="submit">Cadastrar</button>
                </div>
            </form>
        </section>
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

    <script src="assets/js/cadastro.js"></script>
</body>

</html>
