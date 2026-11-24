<?php
session_start();
require_once __DIR__ . '/autoload.php';

use Controller\UsuarioController;
use Controller\PublicacaoController;
use Controller\CurtidaController;

header('Content-Type: application/json; charset=utf-8');

function responder(array $dados, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    $acao = $_GET['acao'] ?? '';
    $metodo = $_SERVER['REQUEST_METHOD'];

    $usuarioController = new UsuarioController();
    $publicacaoController = new PublicacaoController();
    $usuario = $usuarioController->getLoggedUser();
    $idLogado = $usuario['id'] ?? 0;

    switch ($acao) {
        case 'sessao':
            responder(['ok' => true, 'usuario' => $usuario]);

        case 'login':
            if ($metodo !== 'POST') {
                responder(['ok' => false, 'erro' => 'Método inválido.'], 405);
            }
            $resultado = $usuarioController->login(trim($_POST['email'] ?? ''), $_POST['senha'] ?? '');
            responder($resultado);

        case 'cadastro':
            if ($metodo !== 'POST') {
                responder(['ok' => false, 'erro' => 'Método inválido.'], 405);
            }
            responder($usuarioController->register($_POST, $_FILES['imagem'] ?? null));

        case 'logout':
            $usuarioController->logout();
            responder(['ok' => true]);

        case 'publicacoes':
            $idFotografo = null;

            if (isset($_GET['minhas'])) {
                if (!$usuario) {
                    responder(['ok' => false, 'erro' => 'Faça login para continuar.'], 401);
                }
                $idFotografo = $idLogado;
            } elseif (isset($_GET['fotografo'])) {
                $idFotografo = (int) $_GET['fotografo'];
            }

            responder(['ok' => true, 'publicacoes' => $publicacaoController->listPublications($idLogado, $idFotografo)]);

        case 'buscar':
            if (!$usuario) {
                responder(['ok' => false, 'erro' => 'Faça login para continuar.'], 401);
            }
            responder($usuarioController->searchPhotographer($_GET['nome'] ?? ''));

        case 'perfil':
            if (!$usuario or $usuario['tipo'] !== 'fotografo') {
                responder(['ok' => false, 'erro' => 'Acesso negado.'], 403);
            }
            responder(['ok' => true] + $publicacaoController->getProfile($idLogado));

        case 'curtir':
            if ($metodo !== 'POST') {
                responder(['ok' => false, 'erro' => 'Método inválido.'], 405);
            }
            if (!$usuario) {
                responder(['ok' => false, 'erro' => 'Faça login para continuar.'], 401);
            }
            $curtidaController = new CurtidaController();
            responder($curtidaController->toggleLike($idLogado, (int) ($_POST['id_publicacao'] ?? 0)));

        case 'cadastrar':
            if ($metodo !== 'POST') {
                responder(['ok' => false, 'erro' => 'Método inválido.'], 405);
            }
            if (!$usuario) {
                responder(['ok' => false, 'erro' => 'Faça login para continuar.'], 401);
            }
            responder($publicacaoController->createPublication(
                $usuario,
                $_POST['titulo'] ?? '',
                $_POST['local'] ?? '',
                $_FILES['imagem'] ?? null
            ));

        case 'excluir':
            if ($metodo !== 'POST') {
                responder(['ok' => false, 'erro' => 'Método inválido.'], 405);
            }
            if (!$usuario) {
                responder(['ok' => false, 'erro' => 'Faça login para continuar.'], 401);
            }
            responder($publicacaoController->deletePublication($idLogado, (int) ($_POST['id_publicacao'] ?? 0)));

        default:
            responder(['ok' => false, 'erro' => 'Ação inválida.'], 404);
    }
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    responder(['ok' => false, 'erro' => 'Erro interno do servidor.'], 500);
}
