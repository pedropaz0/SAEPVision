<?php
namespace Model;

use PDO;
use PDOException;

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function getUserByEmail(string $email): array|bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_usuario WHERE email = :email");
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao buscar usuário por e-mail: " . $error->getMessage());
            return false;
        }
    }

    public function getUserById(int $id): array|bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_usuario WHERE id_usuario = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao buscar usuário: " . $error->getMessage());
            return false;
        }
    }

    public function getUserByName(string $nome): array|bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_usuario WHERE nome = :nome");
            $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao buscar usuário por nome: " . $error->getMessage());
            return false;
        }
    }

    public function registerUser(string $nome, string $email, string $senhaHash, ?string $imagem, string $tipo): bool
    {
        try {
            $sql = "INSERT INTO tb_usuario (nome, nome_usuario, email, senha, imagem_usuario, tipo)
                    VALUES (:nome, :nome_usuario, :email, :senha, :imagem, :tipo)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindValue(":nome_usuario", $nome, PDO::PARAM_STR);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":senha", $senhaHash, PDO::PARAM_STR);
            $stmt->bindValue(":imagem", $imagem, $imagem === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(":tipo", $tipo, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log("Erro ao cadastrar usuário: " . $error->getMessage());
            return false;
        }
    }

    public function getPhotographerByName(string $nome): array|bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_usuario WHERE nome = :nome AND tipo = 'fotografo'");
            $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Erro ao buscar fotógrafo: " . $error->getMessage());
            return false;
        }
    }
}
