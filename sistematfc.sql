-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24-Jun-2025 às 11:26
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistematfc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes_finais`
--

CREATE TABLE `avaliacoes_finais` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `nota_final` decimal(3,1) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status_defesa` enum('aprovado','reprovado','pendente') DEFAULT 'pendente',
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes_projeto`
--

CREATE TABLE `avaliacoes_projeto` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `orientador_id` int(11) NOT NULL,
  `nota` decimal(3,1) NOT NULL,
  `comentarios` text NOT NULL,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `capitulos`
--

CREATE TABLE `capitulos` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `numero_capitulo` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo_path` varchar(255) NOT NULL,
  `status` enum('pendente','aprovado','revisao') DEFAULT 'pendente',
  `data_submissao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `capitulos`
--

INSERT INTO `capitulos` (`id`, `projeto_id`, `numero_capitulo`, `titulo`, `descricao`, `arquivo_path`, `status`, `data_submissao`, `data_atualizacao`) VALUES
(1, 2, 1, 'Capítulo 1', NULL, '681d4bdd77a43_SADTE apresentação defesa.pdf', 'pendente', '2025-05-09 00:27:09', '2025-05-09 00:27:09'),
(2, 2, 2, 'Capítulo 2', NULL, '681d4bfaa1b1c_ISO 690.docx', 'pendente', '2025-05-09 00:27:38', '2025-05-09 00:27:38'),
(3, 2, 3, 'Capítulo 3', NULL, '681d4c0975f46_CALENDÁRIO_DE_DEFESA-_INFORMÁTICA_2025.docx[1].pdf', 'pendente', '2025-05-09 00:27:53', '2025-05-09 00:27:53'),
(4, 1, 1, 'Capítulo 1', NULL, '681d4cb48fef4_SADTE apresentação defesa.pdf', 'revisao', '2025-05-09 00:30:44', '2025-05-09 00:33:50'),
(5, 1, 2, 'Capítulo 2', NULL, '681d4cbd25178_LISTA TFC-2024-PUBLICAR-INFORMÁTICA-1.pdf', 'revisao', '2025-05-09 00:30:53', '2025-05-09 00:33:40'),
(6, 1, 3, 'Capítulo 3', NULL, '681d4cc66c683_TFC_DEFESA_ENG_INF_Josemar Rafael_correção V9 - OpenUp.docx', 'aprovado', '2025-05-09 00:31:02', '2025-05-09 00:32:12');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `nome_instituicao` varchar(255) NOT NULL DEFAULT 'Nome da Instituição',
  `periodo_letivo` varchar(50) NOT NULL DEFAULT '2024.1',
  `data_limite_defesa` date DEFAULT NULL,
  `email_sistema` varchar(100) NOT NULL DEFAULT 'sistema@sistematfc.com',
  `notificacoes_ativas` tinyint(1) DEFAULT 1,
  `max_orientandos` int(11) NOT NULL DEFAULT 5,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `coorientadores`
--

CREATE TABLE `coorientadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `area_coorientacao` varchar(100) DEFAULT NULL,
  `quantidade_orientandos` int(11) DEFAULT 0,
  `disponivel_novos_orientandos` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `defesas`
--

CREATE TABLE `defesas` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `data_defesa` date NOT NULL,
  `hora_defesa` time NOT NULL,
  `sala` varchar(50) NOT NULL,
  `status` enum('agendada','realizada','cancelada') DEFAULT 'agendada',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `defesas`
--

INSERT INTO `defesas` (`id`, `projeto_id`, `data_defesa`, `hora_defesa`, `sala`, `status`, `data_cadastro`) VALUES
(1, 1, '3332-04-04', '03:22:00', '206', '', '2025-06-08 14:06:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `documentos`
--

INSERT INTO `documentos` (`id`, `projeto_id`, `nome`, `tipo`, `caminho`, `data_upload`) VALUES
(1, 2, 'SADTE apresentação defesa.pdf', 'capitulo1', '681d4bdd77a43_SADTE apresentação defesa.pdf', '2025-05-09 00:27:09'),
(2, 2, 'ISO 690.docx', 'capitulo2', '681d4bfaa1b1c_ISO 690.docx', '2025-05-09 00:27:38'),
(3, 2, 'CALENDÁRIO_DE_DEFESA-_INFORMÁTICA_2025.docx[1].pdf', 'capitulo3', '681d4c0975f46_CALENDÁRIO_DE_DEFESA-_INFORMÁTICA_2025.docx[1].pdf', '2025-05-09 00:27:53'),
(4, 2, 'SADTE apresentação defesa.pdf', 'versao_parcial', '681d4c1064e65_SADTE apresentação defesa.pdf', '2025-05-09 00:28:00'),
(5, 1, 'SADTE apresentação defesa.pdf', 'capitulo1', '681d4cb48fef4_SADTE apresentação defesa.pdf', '2025-05-09 00:30:44'),
(6, 1, 'LISTA TFC-2024-PUBLICAR-INFORMÁTICA-1.pdf', 'capitulo2', '681d4cbd25178_LISTA TFC-2024-PUBLICAR-INFORMÁTICA-1.pdf', '2025-05-09 00:30:53'),
(7, 1, 'TFC_DEFESA_ENG_INF_Josemar Rafael_correção V9 - OpenUp.docx', 'capitulo3', '681d4cc66c683_TFC_DEFESA_ENG_INF_Josemar Rafael_correção V9 - OpenUp.docx', '2025-05-09 00:31:02'),
(8, 1, 'CALENDÁRIO_DE_DEFESA-_INFORMÁTICA_2025.docx[1].pdf', 'versao_parcial', '681d4ccd00a41_CALENDÁRIO_DE_DEFESA-_INFORMÁTICA_2025.docx[1].pdf', '2025-05-09 00:31:09');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estudantes`
--

CREATE TABLE `estudantes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso` varchar(100) NOT NULL,
  `numero_processo` varchar(50) NOT NULL,
  `orientador_id` int(11) DEFAULT NULL,
  `coorientador_id` int(11) DEFAULT NULL,
  `tema_defesa` varchar(255) DEFAULT NULL,
  `data_defesa` date DEFAULT NULL,
  `semestre_ingresso` varchar(6) DEFAULT NULL,
  `previsao_conclusao` date DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `estudantes`
--

INSERT INTO `estudantes` (`id`, `usuario_id`, `curso`, `numero_processo`, `orientador_id`, `coorientador_id`, `tema_defesa`, `data_defesa`, `semestre_ingresso`, `previsao_conclusao`, `data_cadastro`, `status`) VALUES
(1, 2, 'Engenharia Informática', '923507455', 3, 4, NULL, NULL, NULL, NULL, '2025-05-06 10:39:47', 1),
(2, 5, 'Engenharia Informática', '18544', NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-08 09:49:43', 1),
(3, 35, 'Telecomunicações', '1212', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-12 01:21:38', 1),
(4, 36, 'Telecomunicações', '2323', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-12 01:23:11', 1),
(5, 37, 'Telecomunicações', '3434', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-12 01:24:37', 1),
(6, 38, 'Telecomunicações', '123412', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-12 01:43:01', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `capitulo_id` int(11) NOT NULL,
  `orientador_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `tipo` enum('positivo','revisao') NOT NULL,
  `data_feedback` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `capitulo_id`, `orientador_id`, `comentario`, `tipo`, `data_feedback`) VALUES
(1, 6, 3, '8oi', 'positivo', '2025-05-09 00:32:12'),
(2, 5, 3, 'iu', 'revisao', '2025-05-09 00:33:40'),
(3, 4, 3, '98i', 'revisao', '2025-05-09 00:33:50');

-- --------------------------------------------------------

--
-- Estrutura da tabela `importacao_usuarios`
--

CREATE TABLE `importacao_usuarios` (
  `id` int(11) NOT NULL,
  `arquivo_nome` varchar(255) NOT NULL,
  `status` enum('pendente','concluido','erro') DEFAULT 'pendente',
  `total_registros` int(11) DEFAULT 0,
  `registros_processados` int(11) DEFAULT 0,
  `registros_com_erro` int(11) DEFAULT 0,
  `data_importacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `inscricoes_tema`
--

CREATE TABLE `inscricoes_tema` (
  `id` int(11) NOT NULL,
  `tema_id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `status` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `inscricoes_tema`
--

INSERT INTO `inscricoes_tema` (`id`, `tema_id`, `estudante_id`, `status`, `data_inscricao`) VALUES
(1, 1, 2, 'pendente', '2025-05-06 14:06:48'),
(2, 2, 5, 'pendente', '2025-05-08 09:50:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `membros_banca`
--

CREATE TABLE `membros_banca` (
  `id` int(11) NOT NULL,
  `defesa_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `tipo` enum('presidente','membro') DEFAULT 'membro',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `membros_banca`
--

INSERT INTO `membros_banca` (`id`, `defesa_id`, `professor_id`, `tipo`, `data_cadastro`) VALUES
(1, 1, 34, 'membro', '2025-06-08 14:06:37'),
(2, 1, 3, 'membro', '2025-06-08 14:06:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `arquivo_anexo` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente_id`, `destinatario_id`, `assunto`, `mensagem`, `arquivo_anexo`, `lida`, `data_envio`) VALUES
(1, 2, 3, 'rre', 'erreere', NULL, 1, '2025-05-09 00:31:19'),
(2, 3, 2, 'uoi', '898u', NULL, 0, '2025-05-09 00:33:28'),
(3, 2, 3, 'dgdfg', 'dfgdfgdf', NULL, 0, '2025-05-09 14:56:28');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes_reuniao`
--

CREATE TABLE `notificacoes_reuniao` (
  `id` int(11) NOT NULL,
  `reuniao_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('agendamento','confirmacao','cancelamento','lembrete') NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `orientacoes`
--

CREATE TABLE `orientacoes` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `data_orientacao` datetime NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('agendada','realizada','cancelada') DEFAULT 'agendada',
  `observacoes` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `orientadores`
--

CREATE TABLE `orientadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `area_orientacao` varchar(100) DEFAULT NULL,
  `titulacao` varchar(50) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `quantidade_orientandos` int(11) DEFAULT 0,
  `disponivel_novos_orientandos` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissoes_usuario`
--

CREATE TABLE `permissoes_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `permissao` varchar(50) NOT NULL,
  `data_concessao` timestamp NOT NULL DEFAULT current_timestamp(),
  `concedido_por` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `permissoes_usuario`
--

INSERT INTO `permissoes_usuario` (`id`, `usuario_id`, `permissao`, `data_concessao`, `concedido_por`, `status`) VALUES
(1, 1, 'gerenciar_usuarios', '2025-05-05 20:16:59', 1, 1),
(2, 1, 'gerenciar_projetos', '2025-05-05 20:16:59', 1, 1),
(3, 1, 'gerenciar_orientacoes', '2025-05-05 20:16:59', 1, 1),
(4, 1, 'gerenciar_avaliacoes', '2025-05-05 20:16:59', 1, 1),
(5, 1, 'gerenciar_documentos', '2025-05-05 20:16:59', 1, 1),
(6, 1, 'visualizar_relatorios', '2025-05-05 20:16:59', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `area_atuacao` varchar(100) DEFAULT NULL,
  `regime_trabalho` varchar(50) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `projetos`
--

CREATE TABLE `projetos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `estudante_id` int(11) DEFAULT NULL,
  `orientador_id` int(11) DEFAULT NULL,
  `area_pesquisa` varchar(100) DEFAULT NULL,
  `linha_pesquisa` varchar(100) DEFAULT NULL,
  `status` enum('em_andamento','concluido','reprovado') DEFAULT 'em_andamento',
  `data_inicio` date DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `codigo_fonte_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `projetos`
--

INSERT INTO `projetos` (`id`, `titulo`, `descricao`, `estudante_id`, `orientador_id`, `area_pesquisa`, `linha_pesquisa`, `status`, `data_inicio`, `data_conclusao`, `data_cadastro`, `data_atualizacao`, `codigo_fonte_path`) VALUES
(1, '', NULL, 2, 3, NULL, NULL, '', NULL, NULL, '2025-05-06 10:39:42', '2025-06-08 14:06:37', NULL),
(2, '', NULL, 5, NULL, NULL, NULL, 'em_andamento', NULL, NULL, '2025-05-08 09:50:15', '2025-05-08 09:50:15', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projeto_coorientadores`
--

CREATE TABLE `projeto_coorientadores` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `coorientador_id` int(11) DEFAULT NULL,
  `data_vinculo` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `propostas`
--

CREATE TABLE `propostas` (
  `id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `area_pesquisa` varchar(100) NOT NULL,
  `resumo` text NOT NULL,
  `arquivo_caminho` varchar(255) NOT NULL,
  `status` enum('pendente','aprovada','rejeitada') DEFAULT 'pendente',
  `data_submissao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_avaliacao` timestamp NULL DEFAULT NULL,
  `comentarios_avaliacao` text DEFAULT NULL,
  `avaliado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `reunioes`
--

CREATE TABLE `reunioes` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) NOT NULL,
  `orientador_id` int(11) NOT NULL,
  `estudante_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `status` enum('agendada','confirmada','cancelada','concluida') DEFAULT 'agendada',
  `descricao` text DEFAULT NULL,
  `local` varchar(255) DEFAULT NULL,
  `link_reuniao` varchar(255) DEFAULT NULL,
  `tipo` enum('presencial','online') NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `temas_tfc`
--

CREATE TABLE `temas_tfc` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `curso` varchar(100) NOT NULL,
  `area_pesquisa` varchar(100) NOT NULL,
  `ano_letivo` varchar(4) NOT NULL,
  `docente_proponente_id` int(11) NOT NULL,
  `max_estudantes` int(11) DEFAULT 1,
  `data_limite_submissao` date DEFAULT NULL,
  `data_limite_escolha` date DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitado','publicado') DEFAULT 'pendente',
  `sugerido_por_estudante_id` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `temas_tfc`
--

INSERT INTO `temas_tfc` (`id`, `titulo`, `descricao`, `curso`, `area_pesquisa`, `ano_letivo`, `docente_proponente_id`, `max_estudantes`, `data_limite_submissao`, `data_limite_escolha`, `status`, `sugerido_por_estudante_id`, `data_cadastro`, `data_atualizacao`) VALUES
(1, 'sistema web com recursos de ia', 'ia na educação', 'Engenharia Informática', 'TECNOLOGIA(IA)', '2025', 3, 1, '2015-05-31', '2025-06-01', '', NULL, '2025-05-06 10:38:44', '2025-05-06 14:06:48'),
(2, 'sistema de cadastro de pessoal ', 'sistema de cadastro pessoal para cadastrar pessoa e io mundo ficar melhor todos cadastrados kkkkkkkkkkkk', 'Engenharia Informática', 'TECNOLOGIA', '2025', 3, 1, '2025-12-12', '2025-12-20', '', NULL, '2025-05-08 09:48:25', '2025-05-08 09:50:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `user_logs`
--

INSERT INTO `user_logs` (`id`, `usuario_id`, `acao`, `descricao`, `ip_address`, `user_agent`, `data_registro`) VALUES
(1, 3, 'Avaliação do capítulo ID: 6', '8oi', '127.0.0.1', NULL, '2025-05-09 00:32:12'),
(2, 3, 'Avaliação do capítulo ID: 5', 'iu', '127.0.0.1', NULL, '2025-05-09 00:33:40'),
(3, 3, 'Avaliação do capítulo ID: 4', '98i', '127.0.0.1', NULL, '2025-05-09 00:33:50'),
(4, 1, 'importacao_usuario', 'Usuário aldairmatias@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(5, 1, 'importacao_usuario', 'Usuário anapereira@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(6, 1, 'importacao_usuario', 'Usuário carloseduardo@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(7, 1, 'importacao_usuario', 'Usuário coorientador@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(8, 1, 'importacao_usuario', 'Usuário estudante@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(9, 1, 'importacao_usuario', 'Usuário joaosilva@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(10, 1, 'importacao_usuario', 'Usuário mariasantos@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:29'),
(11, 1, 'importacao_usuario', 'Usuário marianalima@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(12, 1, 'importacao_usuario', 'Usuário nzuzirodolfo@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(13, 1, 'importacao_usuario', 'Usuário orientador@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(14, 1, 'importacao_usuario', 'Usuário pedrocosta@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(15, 1, 'importacao_usuario', 'Usuário pintotunga@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(16, 1, 'importacao_usuario', 'Usuário professor@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:36:30'),
(17, 1, 'importacao_usuario', 'Usuário aldairmatias@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(18, 1, 'importacao_usuario', 'Usuário anapereira@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(19, 1, 'importacao_usuario', 'Usuário carloseduardo@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(20, 1, 'importacao_usuario', 'Usuário coorientador@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(21, 1, 'importacao_usuario', 'Usuário estudante@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(22, 1, 'importacao_usuario', 'Usuário joaosilva@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(23, 1, 'importacao_usuario', 'Usuário mariasantos@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(24, 1, 'importacao_usuario', 'Usuário marianalima@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:18'),
(25, 1, 'importacao_usuario', 'Usuário nzuzirodolfo@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:19'),
(26, 1, 'importacao_usuario', 'Usuário orientador@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:19'),
(27, 1, 'importacao_usuario', 'Usuário pedrocosta@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:19'),
(28, 1, 'importacao_usuario', 'Usuário pintotunga@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:19'),
(29, 1, 'importacao_usuario', 'Usuário professor@sistematfc.com importado com sucesso', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-09 00:38:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','professor','orientador','coorientador','estudante') NOT NULL,
  `matricula` varchar(20) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `area_especializacao` varchar(100) DEFAULT NULL,
  `titulacao` varchar(50) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo_usuario`, `matricula`, `departamento`, `area_especializacao`, `titulacao`, `data_cadastro`, `status`) VALUES
(1, 'Administrador', 'admin@sistematfc.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ADM001', 'TI', NULL, 'Administrador', '2025-05-05 20:16:59', 1),
(2, 'ALDAIR CARLOS MATIAS', 'aldaircmatias@gmail.com', '$2y$10$VKBzDTvhgXIzPgYMA4tn9ePzoMxtQ04nGQ6wvdeZxi0MEJpf3D6Wq', 'estudante', NULL, NULL, NULL, NULL, '2025-05-06 10:33:47', 1),
(3, 'PINTO TUNGA', 'pintungq@gmail.com', '$2y$10$7/DUKlWi9gMMgfJaeEKlAO11u1ATWstoCx3OPKtLcdPMCTLOMXt9m', 'orientador', NULL, 'ENG.informática', 'T.I', NULL, '2025-05-06 10:35:33', 1),
(4, 'Nzunzi Rodolfo', 'rodol@gmail.com', '$2y$10$s8FI.654aapIcsqbN6KSfeUCNMuXAZkvE2ddDCwZ..INZ1bK39D7O', 'coorientador', NULL, 'ENG.informática', 'T.I', NULL, '2025-05-06 10:36:44', 1),
(5, 'Dulce Maria', 'dulce@gmail.com', '$2y$10$Au4X1l6PjGhNB/Pirjiij.e0ior8qeMA/YRoJDkYBVZROa59rSg4u', 'estudante', NULL, NULL, NULL, NULL, '2025-05-08 09:49:43', 1),
(34, 'ALDAIR CARLOS MATIAS', 'admimmingiloa@mail.com', '$2y$10$5wcw2JeE8gNlwRPuT9AMK.EzsayjVFjzl8GAcyr652oKgsMMHs6SS', 'orientador', NULL, 'ainformatica', 'INFORMATICA', NULL, '2025-06-08 14:05:50', 1),
(35, 'DANIEL PEDRO MATIAS', 'daniel12@gmail.com', '$2y$10$KOpQmD72o0zVAawMqERqXema5yfQa9NGt0nledmsdwHjfBzqFpkxC', 'estudante', NULL, NULL, NULL, NULL, '2025-06-12 01:21:38', 1),
(36, 'borkina', 'borkina@gmail.com', '$2y$10$F8zghxn.8z8C1Wrs16Gf1OnhQcyK6Hqdoq5arhMgTcxgTcr2CDMJ6', 'estudante', NULL, NULL, NULL, NULL, '2025-06-12 01:23:11', 1),
(37, 'jose figueredo', 'jo@gmail.com', '$2y$10$S.t7c3Z9gVrUVw1u3mrnC.ZDjhLjgEjs6hDM7FZt5vq47kEFdgIyC', 'estudante', NULL, NULL, NULL, NULL, '2025-06-12 01:24:37', 1),
(38, 'matias carlos', 'carlos@gmail.com', '$2y$10$aVAgLhTGT1Y1a38IR3gwve48UeeeYeGZq0n1dnCPO.L2jgjl8R2KS', 'estudante', NULL, NULL, NULL, NULL, '2025-06-12 01:43:01', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `avaliacoes_finais`
--
ALTER TABLE `avaliacoes_finais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projeto_id` (`projeto_id`);

--
-- Índices para tabela `avaliacoes_projeto`
--
ALTER TABLE `avaliacoes_projeto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`),
  ADD KEY `orientador_id` (`orientador_id`);

--
-- Índices para tabela `capitulos`
--
ALTER TABLE `capitulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `coorientadores`
--
ALTER TABLE `coorientadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `defesas`
--
ALTER TABLE `defesas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices para tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices para tabela `estudantes`
--
ALTER TABLE `estudantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `orientador_id` (`orientador_id`),
  ADD KEY `coorientador_id` (`coorientador_id`);

--
-- Índices para tabela `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `capitulo_id` (`capitulo_id`),
  ADD KEY `orientador_id` (`orientador_id`);

--
-- Índices para tabela `importacao_usuarios`
--
ALTER TABLE `importacao_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `inscricoes_tema`
--
ALTER TABLE `inscricoes_tema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscricao` (`tema_id`,`estudante_id`),
  ADD KEY `estudante_id` (`estudante_id`);

--
-- Índices para tabela `membros_banca`
--
ALTER TABLE `membros_banca`
  ADD PRIMARY KEY (`id`),
  ADD KEY `defesa_id` (`defesa_id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Índices para tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_remetente` (`remetente_id`),
  ADD KEY `idx_destinatario` (`destinatario_id`),
  ADD KEY `idx_data_envio` (`data_envio`);

--
-- Índices para tabela `notificacoes_reuniao`
--
ALTER TABLE `notificacoes_reuniao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reuniao_id` (`reuniao_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `orientacoes`
--
ALTER TABLE `orientacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices para tabela `orientadores`
--
ALTER TABLE `orientadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `permissoes_usuario`
--
ALTER TABLE `permissoes_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `concedido_por` (`concedido_por`);

--
-- Índices para tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudante_id` (`estudante_id`),
  ADD KEY `orientador_id` (`orientador_id`);

--
-- Índices para tabela `projeto_coorientadores`
--
ALTER TABLE `projeto_coorientadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`),
  ADD KEY `coorientador_id` (`coorientador_id`);

--
-- Índices para tabela `propostas`
--
ALTER TABLE `propostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `avaliado_por` (`avaliado_por`),
  ADD KEY `idx_estudante_id` (`estudante_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices para tabela `reunioes`
--
ALTER TABLE `reunioes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`),
  ADD KEY `orientador_id` (`orientador_id`),
  ADD KEY `estudante_id` (`estudante_id`);

--
-- Índices para tabela `temas_tfc`
--
ALTER TABLE `temas_tfc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `docente_proponente_id` (`docente_proponente_id`),
  ADD KEY `sugerido_por_estudante_id` (`sugerido_por_estudante_id`);

--
-- Índices para tabela `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes_finais`
--
ALTER TABLE `avaliacoes_finais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avaliacoes_projeto`
--
ALTER TABLE `avaliacoes_projeto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `capitulos`
--
ALTER TABLE `capitulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coorientadores`
--
ALTER TABLE `coorientadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `defesas`
--
ALTER TABLE `defesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `estudantes`
--
ALTER TABLE `estudantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `importacao_usuarios`
--
ALTER TABLE `importacao_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `inscricoes_tema`
--
ALTER TABLE `inscricoes_tema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `membros_banca`
--
ALTER TABLE `membros_banca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `notificacoes_reuniao`
--
ALTER TABLE `notificacoes_reuniao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orientacoes`
--
ALTER TABLE `orientacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orientadores`
--
ALTER TABLE `orientadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissoes_usuario`
--
ALTER TABLE `permissoes_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `projeto_coorientadores`
--
ALTER TABLE `projeto_coorientadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `propostas`
--
ALTER TABLE `propostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reunioes`
--
ALTER TABLE `reunioes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `temas_tfc`
--
ALTER TABLE `temas_tfc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `avaliacoes_finais`
--
ALTER TABLE `avaliacoes_finais`
  ADD CONSTRAINT `avaliacoes_finais_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Limitadores para a tabela `avaliacoes_projeto`
--
ALTER TABLE `avaliacoes_projeto`
  ADD CONSTRAINT `avaliacoes_projeto_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`),
  ADD CONSTRAINT `avaliacoes_projeto_ibfk_2` FOREIGN KEY (`orientador_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `capitulos`
--
ALTER TABLE `capitulos`
  ADD CONSTRAINT `capitulos_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Limitadores para a tabela `coorientadores`
--
ALTER TABLE `coorientadores`
  ADD CONSTRAINT `coorientadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `defesas`
--
ALTER TABLE `defesas`
  ADD CONSTRAINT `defesas_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Limitadores para a tabela `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Limitadores para a tabela `estudantes`
--
ALTER TABLE `estudantes`
  ADD CONSTRAINT `estudantes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `estudantes_ibfk_2` FOREIGN KEY (`orientador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `estudantes_ibfk_3` FOREIGN KEY (`coorientador_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`capitulo_id`) REFERENCES `capitulos` (`id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`orientador_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `importacao_usuarios`
--
ALTER TABLE `importacao_usuarios`
  ADD CONSTRAINT `importacao_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `inscricoes_tema`
--
ALTER TABLE `inscricoes_tema`
  ADD CONSTRAINT `inscricoes_tema_ibfk_1` FOREIGN KEY (`tema_id`) REFERENCES `temas_tfc` (`id`),
  ADD CONSTRAINT `inscricoes_tema_ibfk_2` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `membros_banca`
--
ALTER TABLE `membros_banca`
  ADD CONSTRAINT `membros_banca_ibfk_1` FOREIGN KEY (`defesa_id`) REFERENCES `defesas` (`id`),
  ADD CONSTRAINT `membros_banca_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `notificacoes_reuniao`
--
ALTER TABLE `notificacoes_reuniao`
  ADD CONSTRAINT `notificacoes_reuniao_ibfk_1` FOREIGN KEY (`reuniao_id`) REFERENCES `reunioes` (`id`),
  ADD CONSTRAINT `notificacoes_reuniao_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `orientacoes`
--
ALTER TABLE `orientacoes`
  ADD CONSTRAINT `orientacoes_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Limitadores para a tabela `orientadores`
--
ALTER TABLE `orientadores`
  ADD CONSTRAINT `orientadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `permissoes_usuario`
--
ALTER TABLE `permissoes_usuario`
  ADD CONSTRAINT `permissoes_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `permissoes_usuario_ibfk_2` FOREIGN KEY (`concedido_por`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `professores`
--
ALTER TABLE `professores`
  ADD CONSTRAINT `professores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `projetos`
--
ALTER TABLE `projetos`
  ADD CONSTRAINT `projetos_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `projetos_ibfk_2` FOREIGN KEY (`orientador_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `projeto_coorientadores`
--
ALTER TABLE `projeto_coorientadores`
  ADD CONSTRAINT `projeto_coorientadores_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`),
  ADD CONSTRAINT `projeto_coorientadores_ibfk_2` FOREIGN KEY (`coorientador_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `propostas`
--
ALTER TABLE `propostas`
  ADD CONSTRAINT `propostas_ibfk_1` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `propostas_ibfk_2` FOREIGN KEY (`avaliado_por`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `reunioes`
--
ALTER TABLE `reunioes`
  ADD CONSTRAINT `reunioes_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`),
  ADD CONSTRAINT `reunioes_ibfk_2` FOREIGN KEY (`orientador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `reunioes_ibfk_3` FOREIGN KEY (`estudante_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `temas_tfc`
--
ALTER TABLE `temas_tfc`
  ADD CONSTRAINT `temas_tfc_ibfk_1` FOREIGN KEY (`docente_proponente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `temas_tfc_ibfk_2` FOREIGN KEY (`sugerido_por_estudante_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
