<?php
namespace Controller;

use Model\Curtida;
use Model\Publicacao;

class CurtidaController
{
    private $curtidaModel;
    private $publicacaoModel;

    public function __construct()
    {
        $this->curtidaModel = new Curtida();
        $this->publicacaoModel = new Publicacao();
    }

    public function toggleLike(int $idUsuario, int $idPublicacao): array
    {
        if (!$this->publicacaoModel->getPublicationById($idPublicacao)) {
            return ['ok' => false, 'erro' => "Publicação não encontrada."];
        }

        if ($this->curtidaModel->exists($idUsuario, $idPublicacao)) {
            $this->curtidaModel->deleteLike($idUsuario, $idPublicacao);
            $curtiu = false;
        } else {
            $this->curtidaModel->createLike($idUsuario, $idPublicacao);
            $curtiu = true;
        }

        return [
            'ok' => true,
            'curtiu' => $curtiu,
            'total' => $this->curtidaModel->countByPublication($idPublicacao)
        ];
    }
}
