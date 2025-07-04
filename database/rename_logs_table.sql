-- Renomear a tabela logs_atividades para user_logs
RENAME TABLE logs_atividades TO user_logs;

-- Adicionar campos faltantes na tabela user_logs
ALTER TABLE user_logs
ADD COLUMN ip_address VARCHAR(45) AFTER descricao,
ADD COLUMN user_agent VARCHAR(255) AFTER ip_address;
ADD COLUMN data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,