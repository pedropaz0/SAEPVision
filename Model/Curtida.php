<?php
namespace Model;

use PDO;
use PDOException;

class Curtida
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function exists(int $idUsuario, int $idPublicacao): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM tb_curtida WHERE id_usuario = :usuario AND id_publicacao = :publicacao"
            );
            $stmt->bindValue(":usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":publicacao", $idPublicacao, PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $error) {
            error_log("Erro ao verificar curtida: " . $error->getMessage());
            return false;
        }
    }

    public function createLike(int $idUsuario, int $idPublicacao): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tb_curtida (id_usuario, id_publicacao) VALUES (:usuario, :publicacao)"
            );
            $stmt->bindValue(":usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":publicacao", $idPublicacao, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log("Erro ao criar curtida: " . $error->getMessage());
            return false;
        }
    }

    public function deleteLike(int $idUsuario, int $idPublicacao): bool
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM tb_curtida WHERE id_usuario = :usuario AND id_publicacao = :publicacao"
            );
            $stmt->bindValue(":usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":publicacao", $idPublicacao, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log("Erro ao remover curtida: " . $error->getMessage());
            return false;
        }
    }

    public function countByPublication(int $idPublicacao): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_curtida WHERE id_publicacao = :publicacao");
            $stmt->bindValue(":publicacao", $idPublicacao, PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn();
        } catch (PDOException $error) {
            error_log("Erro ao contar curtidas: " . $error->getMessage());
            return 0;
        }
    }

    public function countReceivedByUser(int $idFotografo): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM tb_curtida c
                    INNER JOIN tb_publicacao p ON p.id_publicacao = c.id_publicacao
                    WHERE p.id_usuario = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $idFotografo, PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn();
        } catch (PDOException $error) {
            error_log("Erro ao contar curtidas do fotógrafo: " . $error->getMessage());
            return 0;
        }
    }
}
