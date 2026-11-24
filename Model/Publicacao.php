<?php
namespace Model;

use PDO;
use PDOException;

class Publicacao
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function listPublications(int $idLogado, ?int $idFotografo = null): array
    {
        try {
            $sql = "SELECT p.id_publicacao, p.titulo_imagem, p.local_foto, p.url_fotografia,
                           p.id_usuario, u.nome AS nome_fotografo,
                           (SELECT COUNT(*) FROM tb_curtida c
                             WHERE c.id_publicacao = p.id_publicacao) AS total_curtidas,
                           (SELECT COUNT(*) FROM tb_curtida c
                             WHERE c.id_publicacao = p.id_publicacao AND c.id_usuario = :logado) AS curtiu
                    FROM tb_publicacao p
                    INNER JOIN tb_usuario u ON u.id_usuario = p.id_usuario";

            if ($idFotografo !== null) {
                $sql .= " WHERE p.id_usuario = :fotografo";
            }

            $sql .= " ORDER BY p.id_publicacao ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":logado", $idLogado, PDO::PARAM_INT);

            if ($idFotografo !== null) {
                $stmt->bindValue(":fotografo", $idFotografo, PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao listar publicações: " . $error->getMessage());
            return [];
        }
    }

    public function getPublicationById(int $id): array|bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_publicacao WHERE id_publicacao = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao buscar publicação: " . $error->getMessage());
            return false;
        }
    }

    public function createPublication(string $titulo, string $local, string $url, int $idUsuario): bool
    {
        try {
            $sql = "INSERT INTO tb_publicacao (titulo_imagem, local_foto, id_usuario, url_fotografia)
                    VALUES (:titulo, :local, :id_usuario, :url)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":titulo", $titulo, PDO::PARAM_STR);
            $stmt->bindValue(":local", $local, PDO::PARAM_STR);
            $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":url", $url, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log("Erro ao criar publicação: " . $error->getMessage());
            return false;
        }
    }

    public function deletePublication(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tb_publicacao WHERE id_publicacao = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log("Erro ao excluir publicação: " . $error->getMessage());
            return false;
        }
    }

    public function countByUser(int $idUsuario): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_publicacao WHERE id_usuario = :id");
            $stmt->bindValue(":id", $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn();
        } catch (PDOException $error) {
            error_log("Erro ao contar publicações: " . $error->getMessage());
            return 0;
        }
    }
}
