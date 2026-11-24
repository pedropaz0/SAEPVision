<?php
namespace Controller;

use Model\Curtida;
use Model\Publicacao;

use finfo;

class PublicacaoController
{
    private $publicacaoModel;
    private $curtidaModel;

    public function __construct()
    {
        $this->publicacaoModel = new Publicacao();
        $this->curtidaModel = new Curtida();
    }

    public function listPublications(int $idLogado, ?int $idFotografo = null): array
    {
        $lista = [];

        foreach ($this->publicacaoModel->listPublications($idLogado, $idFotografo) as $p) {
            $lista[] = [
                'id' => (int) $p['id_publicacao'],
                'titulo' => $p['titulo_imagem'],
                'local' => $p['local_foto'],
                'url' => $p['url_fotografia'],
                'fotografo' => $p['nome_fotografo'],
                'curtidas' => (int) $p['total_curtidas'],
                'curtiu' => (int) $p['curtiu'] > 0
            ];
        }

        return $lista;
    }

    public function getProfile(int $idFotografo): array
    {
        return [
            'likes' => $this->curtidaModel->countReceivedByUser($idFotografo),
            'publicacoes' => $this->publicacaoModel->countByUser($idFotografo)
        ];
    }

    public function createPublication(array $usuario, string $titulo, string $local, ?array $imagem): array
    {
        $erros = [];

        if ($usuario['tipo'] !== 'fotografo') {
            return ['ok' => false, 'erro' => "Apenas fotógrafos podem publicar."];
        }

        $titulo = trim($titulo);
        $local = trim($local);

        if ($titulo === '' or mb_strlen($titulo) > 150) {
            $erros['titulo'] = "Campo obrigatório";
        }

        if ($local === '' or mb_strlen($local) > 150) {
            $erros['local'] = "Campo obrigatório";
        }

        $arquivo = null;

        if (!$imagem or $imagem['error'] === UPLOAD_ERR_NO_FILE) {
            $erros['imagem'] = "Campo obrigatório";
        } else {
            $arquivo = $this->saveImage($imagem);

            if ($arquivo === false) {
                $erros['imagem'] = "Imagem inválida. Use JPG ou PNG de até 5 MB.";
            }
        }

        if ($erros) {
            return ['ok' => false, 'erros' => $erros];
        }

        if (!$this->publicacaoModel->createPublication($titulo, $local, UPLOAD_URL . $arquivo, $usuario['id'])) {
            return ['ok' => false, 'erro' => "Não foi possível salvar a publicação."];
        }

        return ['ok' => true];
    }

    public function deletePublication(int $idUsuario, int $idPublicacao): array
    {
        $publicacao = $this->publicacaoModel->getPublicationById($idPublicacao);

        if (!$publicacao or (int) $publicacao['id_usuario'] !== $idUsuario) {
            return ['ok' => false, 'erro' => "Você só pode excluir as suas publicações."];
        }

        if (!$this->publicacaoModel->deletePublication($idPublicacao)) {
            return ['ok' => false, 'erro' => "Não foi possível excluir a publicação."];
        }

        $nomeArquivo = basename($publicacao['url_fotografia']);

        if (strncmp($nomeArquivo, 'foto_', 5) === 0 and is_file(UPLOAD_DIR . $nomeArquivo)) {
            unlink(UPLOAD_DIR . $nomeArquivo);
        }

        return ['ok' => true];
    }

    private function saveImage(array $imagem): string|false
    {
        if ($imagem['error'] !== UPLOAD_ERR_OK)
            return false;

        if ($imagem['size'] > 5 * 1024 * 1024)
            return false;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($imagem['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mime, $allowed))
            return false;

        $filename = uniqid('foto_', true) . '.' . $allowed[$mime];

        if (!is_dir(UPLOAD_DIR))
            mkdir(UPLOAD_DIR, 0755, true);

        if (!move_uploaded_file($imagem['tmp_name'], UPLOAD_DIR . $filename))
            return false;

        return $filename;
    }
}
