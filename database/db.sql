-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS sistematfc;
USE sistematfc;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('admin', 'professor', 'orientador', 'coorientador', 'estudante') NOT NULL,
    matricula VARCHAR(20),
    departamento VARCHAR(100),
    area_especializacao VARCHAR(100),
    titulacao VARCHAR(50),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE
);

-- Tabela de professores
CREATE TABLE IF NOT EXISTS professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    area_atuacao VARCHAR(100),
    regime_trabalho VARCHAR(50),
    data_admissao DATE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de orientadores
CREATE TABLE IF NOT EXISTS orientadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    area_orientacao VARCHAR(100),
    titulacao VARCHAR(50),
    departamento VARCHAR(100),
    quantidade_orientandos INT DEFAULT 0,
    disponivel_novos_orientandos BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de coorientadores
CREATE TABLE IF NOT EXISTS coorientadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    area_coorientacao VARCHAR(100),
    quantidade_orientandos INT DEFAULT 0,
    disponivel_novos_orientandos BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de estudantes
CREATE TABLE IF NOT EXISTS estudantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    curso VARCHAR(100) NOT NULL,
    numero_processo VARCHAR(50) NOT NULL,
    orientador_id INT,
    coorientador_id INT,
    tema_defesa VARCHAR(255),
    data_defesa DATE,
    semestre_ingresso VARCHAR(6),
    previsao_conclusao DATE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id),
    FOREIGN KEY (coorientador_id) REFERENCES usuarios(id)
);

-- Tabela de projetos
CREATE TABLE IF NOT EXISTS projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    estudante_id INT,
    orientador_id INT,
    area_pesquisa VARCHAR(100),
    linha_pesquisa VARCHAR(100),
    status ENUM('em_andamento', 'concluido', 'reprovado') DEFAULT 'em_andamento',
    data_inicio DATE,
    data_conclusao DATE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    codigo_fonte_path VARCHAR(255),
    FOREIGN KEY (estudante_id) REFERENCES usuarios(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id)
);

-- Tabela de relação entre projetos e coorientadores
CREATE TABLE IF NOT EXISTS projeto_coorientadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT,
    coorientador_id INT,
    data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id),
    FOREIGN KEY (coorientador_id) REFERENCES usuarios(id)
);

-- Tabela de permissões de usuário
CREATE TABLE IF NOT EXISTS permissoes_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    permissao VARCHAR(50) NOT NULL,
    data_concessao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    concedido_por INT,
    status BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (concedido_por) REFERENCES usuarios(id)
);

-- Tabela de documentos
CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT,
    nome VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
);

-- Tabela de orientações
CREATE TABLE IF NOT EXISTS orientacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT,
    data_orientacao DATETIME NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('agendada', 'realizada', 'cancelada') DEFAULT 'agendada',
    observacoes TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
);

-- Tabela de configurações
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_instituicao VARCHAR(255) NOT NULL DEFAULT 'Nome da Instituição',
    periodo_letivo VARCHAR(50) NOT NULL DEFAULT '2024.1',
    data_limite_defesa DATE,
    email_sistema VARCHAR(100) NOT NULL DEFAULT 'sistema@sistematfc.com',
    notificacoes_ativas BOOLEAN DEFAULT TRUE,
    max_orientandos INT NOT NULL DEFAULT 5,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de temas TFC
CREATE TABLE IF NOT EXISTS temas_tfc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    curso VARCHAR(100) NOT NULL,
    area_pesquisa VARCHAR(100) NOT NULL,
    ano_letivo VARCHAR(4) NOT NULL,
    docente_proponente_id INT NOT NULL,
    max_estudantes INT DEFAULT 1,
    data_limite_submissao DATE,
    data_limite_escolha DATE,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'publicado') DEFAULT 'pendente',
    sugerido_por_estudante_id INT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_proponente_id) REFERENCES usuarios(id),
    FOREIGN KEY (sugerido_por_estudante_id) REFERENCES usuarios(id)
);

-- Tabela de inscrições em temas
CREATE TABLE IF NOT EXISTS inscricoes_tema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tema_id INT NOT NULL,
    estudante_id INT NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tema_id) REFERENCES temas_tfc(id),
    FOREIGN KEY (estudante_id) REFERENCES usuarios(id),
    UNIQUE KEY unique_inscricao (tema_id, estudante_id)
);

-- Tabela de logs de atividades
CREATE TABLE IF NOT EXISTS user_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(255) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserção do usuário administrador padrão
INSERT INTO usuarios (nome, email, senha, tipo_usuario, matricula, departamento, titulacao)
SELECT * FROM (SELECT
    'Administrador' as nome,
    'admin@sistematfc.com' as email,
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' as senha,
    'admin' as tipo_usuario,
    'ADM001' as matricula,
    'TI' as departamento,
    'Administrador' as titulacao
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'admin@sistematfc.com');

-- Inserção das permissões para o administrador
INSERT INTO permissoes_usuario (usuario_id, permissao, concedido_por)
SELECT u.id, p.permissao, u.id
FROM usuarios u
CROSS JOIN (
    SELECT 'gerenciar_usuarios' as permissao UNION ALL
    SELECT 'gerenciar_projetos' UNION ALL
    SELECT 'gerenciar_orientacoes' UNION ALL
    SELECT 'gerenciar_avaliacoes' UNION ALL
    SELECT 'gerenciar_documentos' UNION ALL
    SELECT 'visualizar_relatorios'
) p
WHERE u.email = 'admin@sistematfc.com'
AND NOT EXISTS (
    SELECT 1
    FROM permissoes_usuario pu
    WHERE pu.usuario_id = u.id
    AND pu.permissao = p.permissao
);
-- Criação da tabela de defesas
CREATE TABLE IF NOT EXISTS defesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    data_defesa DATE NOT NULL,
    hora_defesa TIME NOT NULL,
    sala VARCHAR(50) NOT NULL,
    status ENUM('agendada', 'realizada', 'cancelada') DEFAULT 'agendada',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
);

-- Criação da tabela de membros da banca
CREATE TABLE IF NOT EXISTS membros_banca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    defesa_id INT NOT NULL,
    professor_id INT NOT NULL,
    tipo ENUM('presidente', 'membro') DEFAULT 'membro',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (defesa_id) REFERENCES defesas(id),
    FOREIGN KEY (professor_id) REFERENCES usuarios(id)
);
-- Criação da tabela de importação de usuários
CREATE TABLE IF NOT EXISTS importacao_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arquivo_nome VARCHAR(255) NOT NULL,
    status ENUM('pendente', 'concluido', 'erro') DEFAULT 'pendente',
    total_registros INT DEFAULT 0,
    registros_processados INT DEFAULT 0,
    registros_com_erro INT DEFAULT 0,
    data_importacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Criação da tabela de defesas
CREATE TABLE IF NOT EXISTS defesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    data_defesa DATE NOT NULL,
    hora_defesa TIME NOT NULL,
    sala VARCHAR(50) NOT NULL,
    status ENUM('agendada', 'realizada', 'cancelada') DEFAULT 'agendada',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
);

-- Criação da tabela de membros da banca
CREATE TABLE IF NOT EXISTS membros_banca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    defesa_id INT NOT NULL,
    professor_id INT NOT NULL,
    tipo ENUM('presidente', 'membro') DEFAULT 'membro',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (defesa_id) REFERENCES defesas(id),
    FOREIGN KEY (professor_id) REFERENCES usuarios(id)
);
-- Criar tabela de reuniões
CREATE TABLE IF NOT EXISTS reunioes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    orientador_id INT NOT NULL,
    estudante_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('agendada', 'confirmada', 'cancelada', 'concluida') DEFAULT 'agendada',
    descricao TEXT,
    local VARCHAR(255),
    link_reuniao VARCHAR(255),
    tipo ENUM('presencial', 'online') NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id),
    FOREIGN KEY (estudante_id) REFERENCES usuarios(id)
);

-- Criar tabela de notificações de reunião
CREATE TABLE IF NOT EXISTS notificacoes_reuniao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reuniao_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo ENUM('agendamento', 'confirmacao', 'cancelamento', 'lembrete') NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reuniao_id) REFERENCES reunioes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
-- Criação da tabela de avaliações de projetos
CREATE TABLE IF NOT EXISTS avaliacoes_projeto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    orientador_id INT NOT NULL,
    nota DECIMAL(3,1) NOT NULL,
    comentarios TEXT NOT NULL,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Criação da tabela de propostas
CREATE TABLE IF NOT EXISTS propostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudante_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    area_pesquisa VARCHAR(100) NOT NULL,
    resumo TEXT NOT NULL,
    arquivo_caminho VARCHAR(255) NOT NULL,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    data_submissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_avaliacao TIMESTAMP NULL,
    comentarios_avaliacao TEXT NULL,
    avaliado_por INT NULL,
    FOREIGN KEY (estudante_id) REFERENCES usuarios(id),
    FOREIGN KEY (avaliado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização de consultas
CREATE INDEX idx_estudante_id ON propostas(estudante_id);
CREATE INDEX idx_status ON propostas(status);
-- Tabela de mensagens
CREATE TABLE IF NOT EXISTS mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    assunto VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    arquivo_anexo VARCHAR(255),
    lida BOOLEAN DEFAULT FALSE,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id),
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização de consultas
CREATE INDEX idx_remetente ON mensagens(remetente_id);
CREATE INDEX idx_destinatario ON mensagens(destinatario_id);
CREATE INDEX idx_data_envio ON mensagens(data_envio);
-- Tabela de capítulos
CREATE TABLE IF NOT EXISTS capitulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    numero_capitulo INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    arquivo_path VARCHAR(255) NOT NULL,
    status ENUM('pendente', 'aprovado', 'revisao') DEFAULT 'pendente',
    data_submissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
);

-- Tabela de feedbacks para capítulos
CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capitulo_id INT NOT NULL,
    orientador_id INT NOT NULL,
    comentario TEXT NOT NULL,
    tipo ENUM('positivo', 'revisao') NOT NULL,
    data_feedback TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (capitulo_id) REFERENCES capitulos(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id)
);
-- Criação da tabela de avaliações de projetos
CREATE TABLE IF NOT EXISTS avaliacoes_projeto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    orientador_id INT NOT NULL,
    nota DECIMAL(3,1) NOT NULL,
    comentarios TEXT NOT NULL,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id),
    FOREIGN KEY (orientador_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Criação da tabela de avaliações finais
CREATE TABLE IF NOT EXISTS avaliacoes_finais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    nota_final DECIMAL(3,1) NOT NULL,
    observacoes TEXT,
    status_defesa ENUM('aprovado', 'reprovado', 'pendente') DEFAULT 'pendente',
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice para otimização de consultas
CREATE INDEX idx_projeto_id ON avaliacoes_finais(projeto_id);