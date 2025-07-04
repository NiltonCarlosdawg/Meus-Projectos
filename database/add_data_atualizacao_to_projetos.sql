-- Adicionar coluna data_atualizacao na tabela projetos
ALTER TABLE projetos
ADD COLUMN data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;