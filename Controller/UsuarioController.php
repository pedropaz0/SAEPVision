<?php
namespace Controller;

use Model\Usuario;

use finfo;

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function verifyUserEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function passwordValidation(string $senha): bool
    {
        return (bool) preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,33}$/', $senha);
    }

    public function register(array $dados, ?array $foto): array
    {
        $erros = [];

        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $senha = $dados['senha'] ?? '';
        $confirmar = $dados['confirmar'] ?? '';
        $tipo = $dados['tipo'] ?? '';

        if (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $nome)) {
            $erros['nome'] = "Use de 3 a 50 letras, números, _ ou .";
        } elseif ($this->usuarioModel->getUserByName($nome)) {
            $erros['nome'] = "Este nome de usuário já existe.";
        }

        if (!$this->verifyUserEmail($email)) {
            $erros['email'] = "E-mail inválido ou vazio";
        } elseif ($this->usuarioModel->getUserByEmail($email)) {
            $erros['email'] = "Já existe um usuário cadastrado com esse endereço de e-mail";
        }

        if (!$this->passwordValidation($senha)) {
            $erros['senha'] = "A senha deve ter de 8 a 33 caracteres, com letra maiúscula, minúscula e número.";
        }

        if ($senha !== $confirmar) {
            $erros['confirmar'] = "As senhas não conferem.";
        }

        if (!in_array($tipo, ['usuario', 'fotografo'], true)) {
            $erros['tipo'] = "Escolha o tipo de conta.";
        }

        $arquivo = null;

        if ($foto && $foto['error'] !== UPLOAD_ERR_NO_FILE) {
            $arquivo = $this->saveProfilePhoto($foto);

            if ($arquivo === false) {
                $erros['imagem'] = "Imagem inválida. Use JPG ou PNG de até 5 MB.";
            }
        }

        if ($erros) {
            return ['ok' => false, 'erros' => $erros];
        }

        $hash = password_hash($senha, PASSWORD_ARGON2ID);

        if (!$this->usuarioModel->registerUser($nome, $email, $hash, $arquivo ?: null, $tipo)) {
            return ['ok' => false, 'erro' => "Não foi possível criar a conta."];
        }

        return $this->login($email, $senha);
    }

    private function saveProfilePhoto(array $foto): string|false
    {
        if ($foto['error'] !== UPLOAD_ERR_OK)
            return false;

        if ($foto['size'] > 5 * 1024 * 1024)
            return false;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($foto['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mime, $allowed))
            return false;

        $filename = uniqid('perfil_', true) . '.' . $allowed[$mime];

        if (!is_dir(PERFIL_DIR))
            mkdir(PERFIL_DIR, 0755, true);

        if (!move_uploaded_file($foto['tmp_name'], PERFIL_DIR . $filename))
            return false;

        return $filename;
    }

    public function login(string $email, string $senha): array
    {
        $erros = [];

        if (!$this->verifyUserEmail($email)) {
            $erros['email'] = "E-mail inválido ou vazio";
        }

        if ($senha === '') {
            $erros['senha'] = "A senha é obrigatória e não pode estar vazia.";
        }

        if ($erros) {
            return ['ok' => false, 'erros' => $erros];
        }

        $user = $this->usuarioModel->getUserByEmail($email);

        if (!$user or !password_verify($senha, $user['senha'])) {
            return ['ok' => false, 'erro' => "Usuário não encontrado ou senha incorreta"];
        }

        session_regenerate_id(true);
        $_SESSION['id'] = (int) $user['id_usuario'];

        return ['ok' => true, 'usuario' => $this->formatUser($user)];
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['id']);
    }

    public function getLoggedUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $user = $this->usuarioModel->getUserById((int) $_SESSION['id']);

        return $user ? $this->formatUser($user) : null;
    }

    public function searchPhotographer(string $busca): array
    {
        $nome = ltrim(trim($busca), '@');

        if (!preg_match('/^[A-Za-z0-9_.]+$/', $nome)) {
            return ['ok' => false, 'erro' => "Formato inválido (exemplo: fotografo1)"];
        }

        $fotografo = $this->usuarioModel->getPhotographerByName($nome);

        if (!$fotografo) {
            return ['ok' => false, 'erro' => "Fotógrafo não encontrado"];
        }

        return ['ok' => true, 'id' => (int) $fotografo['id_usuario']];
    }

    private function formatUser(array $user): array
    {
        return [
            'id' => (int) $user['id_usuario'],
            'nome' => $user['nome'],
            'tipo' => $user['tipo'],
            'imagem' => $user['imagem_usuario'] ?: 'saepVision.jpeg'
        ];
    }
}
