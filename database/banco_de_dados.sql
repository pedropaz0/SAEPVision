CREATE DATABASE IF NOT EXISTS rede_fotografia
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE rede_fotografia;

DROP TABLE IF EXISTS tb_curtida;
DROP TABLE IF EXISTS tb_publicacao;
DROP TABLE IF EXISTS tb_usuario;

CREATE TABLE tb_usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    nome_usuario VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    imagem_usuario VARCHAR(255),
    tipo ENUM('usuario', 'fotografo') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tb_publicacao (
    id_publicacao INT AUTO_INCREMENT PRIMARY KEY,
    titulo_imagem VARCHAR(150) NOT NULL,
    local_foto VARCHAR(150),
    id_usuario INT NOT NULL,
    url_fotografia VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES tb_usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE tb_curtida (
    id_curtida INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_publicacao INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES tb_usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_publicacao) REFERENCES tb_publicacao(id_publicacao) ON DELETE CASCADE,
    UNIQUE (id_usuario, id_publicacao)
);

INSERT INTO tb_usuario (id_usuario, nome, nome_usuario, email, senha, imagem_usuario, tipo, created_at, updated_at) VALUES
(1, 'fotografo1', 'fotografo_1', 'fotografo1@email.com', '$argon2id$v=19$m=65536,t=4,p=1$npsxYkNnDeU9AQfV+tWSMw$EQMxcotiNrWcR32Ag1TWHG/znThtMz2/YBSoccMTsrE', 'fotografo1.jpg', 'fotografo', '2025-03-25 12:40:08', '2025-03-25 12:40:08'),
(2, 'fotografo2', 'fotografo_2', 'fotografo2@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$NL5ItKuE+x7y1MNpcN4kSg$5YqlImo+2Icq6RrgfSlLt9jbX0QeCtqhpixQOH/r/Nc', 'fotografo2.jpg', 'fotografo', '2025-03-25 12:42:56', '2025-03-25 12:42:56'),
(3, 'fotografo3', 'fotografo_3', 'fotografo3@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VX6Ub6KD5g3u1xlObCg5Tw$woG1DwaAZADGBjEvcjzBy8W6gqQv4tyCrIdcM7Ki2vE', 'fotografo3.jpg', 'fotografo', '2025-03-25 12:45:44', '2025-03-25 12:45:44'),
(4, 'usuario1', 'usuario_1', 'usuario1@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$Sagob7cLj8tanze1frSayg$onCU0E7CIRJFN1MsqQW5OJFsszLVJcM53b2J1tgnFa0', 'usuario1.jpg', 'usuario', '2025-03-25 12:48:32', '2025-03-25 12:48:32'),
(5, 'usuario2', 'usuario_2', 'usuario2@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$E/4i/yYtFZEx+OhfywP9uw$364DN1imDhKmKia7UQztQYfOHwJZCovte4c47TkY/04', 'usuario2.jpg', 'usuario', '2025-03-25 12:51:20', '2025-03-25 12:51:20'),
(6, 'usuario3', 'usuario_3', 'usuario3@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$vuGpEaIkj7jgrIt+U5NGEA$HV9welBUzBPxZIFku/rW61MUuUREkV88rttaWgeJ5Tc', 'usuario3.jpg', 'usuario', '2025-03-25 12:54:08', '2025-03-25 12:54:08');

INSERT INTO tb_publicacao (id_publicacao, titulo_imagem, local_foto, id_usuario, url_fotografia, created_at, updated_at) VALUES
(1, 'Foto 1', 'Rio de Janeiro', 1, 'assets/fotografias/foto1.jpg', '2025-04-02 18:10:00', '2025-04-02 18:10:00'),
(2, 'Foto 2', 'Chapada Diamantina', 1, 'assets/fotografias/foto2.jpg', '2025-04-03 09:20:00', '2025-04-03 09:20:00'),
(3, 'Foto 3', 'Campos do Jordão', 1, 'assets/fotografias/foto3.jpg', '2025-04-04 07:45:00', '2025-04-04 07:45:00'),
(4, 'Foto 4', 'Islândia', 2, 'assets/fotografias/foto4.jpg', '2025-04-05 16:30:00', '2025-04-05 16:30:00'),
(5, 'Foto 5', 'Serra Gaúcha', 2, 'assets/fotografias/foto5.jpg', '2025-04-06 11:05:00', '2025-04-06 11:05:00'),
(6, 'Foto 6', 'Gramado', 2, 'assets/fotografias/foto6.jpg', '2025-04-07 14:50:00', '2025-04-07 14:50:00'),
(7, 'Foto 7', 'Noruega', 3, 'assets/fotografias/foto7.jpg', '2025-04-08 19:15:00', '2025-04-08 19:15:00'),
(8, 'Foto 8', 'São Paulo', 3, 'assets/fotografias/foto8.jpg', '2025-04-09 12:40:00', '2025-04-09 12:40:00'),
(9, 'Foto 9', 'Canela', 3, 'assets/fotografias/foto9.jpg', '2025-04-10 08:00:00', '2025-04-10 08:00:00');

INSERT INTO tb_curtida (id_usuario, id_publicacao) VALUES
(1, 1), (2, 1), (4, 1), (3, 2), (5, 3), (4, 4), (5, 4),
(6, 5), (4, 6), (1, 8), (4, 9), (5, 9);
