
This analysis provides a comprehensive view of the function's behavior, possible execution paths, and test cases. The control flow graph helps visualize the decision points and execution flow, while the test cases cover the main scenarios that should be validated.# Teste de Caixa Branca - selecionar-tema.php

## 1. Análise de Complexidade Ciclomática

Identificamos os seguintes nós de decisão no código:

1. Verificação de login e permissão: `if (!isLoggedIn() || !isAluno())`
2. Verificação de tema selecionado: `if (!isset($_POST['tema_id']) || empty($_POST['tema_id']))`
3. Bloco try-catch para tratamento de exceções
4. Verificação de tema disponível: `if (!$tema)`
5. Verificação de projeto existente: `if (!$projeto_existente)`
6. Verificação de inscrição existente: `if (!$inscricao_existente)`
7. Verificação de transação ativa: `if ($conn->inTransaction())`

Complexidade Ciclomática = 7 (número de decisões) + 1 = 8

## 2. Grafo de Fluxo de Controle

```
[Início] -> [Verificação de Login/Permissão]
                |
                v
[Verificar Tema ID] -> [Iniciar Transação]
                |
                v
[Try Block] -> [Verificar Disponibilidade]
                |               |
                |               v
                |        [Tema Indisponível]
                |               |
                v               v
[Verificar Projeto] -> [Criar/Usar Projeto]
                |               |
                v               v
[Verificar Inscrição] -> [Registrar Inscrição]
                |               |
                v               v
[Atualizar Status] -> [Commit]
                |               |
                v               v
[Catch Block] -> [Rollback se Necessário]
                |               |
                v               v
            [Finally] -> [Fim]
```

## 3. Caminhos de Execução

### Caminho 1: Acesso Não Autorizado
- Condição: `!isLoggedIn() || !isAluno()`
- Resultado Esperado: Redirecionamento para página inicial

### Caminho 2: Tema Não Selecionado
- Condição: `!isset($_POST['tema_id']) || empty($_POST['tema_id'])`
- Resultado Esperado: Redirecionamento para temas-disponiveis.php com mensagem de erro

### Caminho 3: Tema Indisponível
- Condição: `!$tema`
- Resultado Esperado: Exceção e rollback da transação

### Caminho 4: Novo Projeto
- Condição: `!$projeto_existente`
- Resultado Esperado: Criação de novo projeto

### Caminho 5: Projeto Existente
- Condição: Projeto em andamento encontrado
- Resultado Esperado: Uso do projeto existente

### Caminho 6: Nova Inscrição
- Condição: `!$inscricao_existente`
- Resultado Esperado: Registro de nova inscrição

### Caminho 7: Sucesso
- Condição: Todas as operações bem-sucedidas
- Resultado Esperado: Commit da transação e redirecionamento

## 4. Casos de Teste

### CT01: Verificação de Acesso
```php
// Cenário: Usuário não logado
Session::destroy();
// Resultado Esperado: Redirecionamento para '/'

// Cenário: Usuário logado mas não aluno
$_SESSION['user_type'] = 'professor';
// Resultado Esperado: Redirecionamento para '/'
```

### CT02: Validação de Tema
```php
// Cenário: Tema não informado
$_POST['tema_id'] = null;
// Resultado Esperado: Mensagem "É necessário selecionar um tema"

// Cenário: Tema indisponível
$tema_id = 999; // ID inexistente
// Resultado Esperado: Mensagem "O tema selecionado não está mais disponível"
```

### CT03: Gestão de Projeto
```php
// Cenário: Primeiro projeto do estudante
$estudante_id = 1;
// Resultado Esperado: Novo projeto criado

// Cenário: Estudante com projeto existente
$projeto_id = 1;
// Resultado Esperado: Uso do projeto existente
```

### CT04: Inscrição em Tema
```php
// Cenário: Primeira inscrição no tema
$tema_id = 1;
// Resultado Esperado: Nova inscrição registrada

// Cenário: Tema já em andamento
$status = 'em_andamento';
// Resultado Esperado: Tema atualizado com sucesso
```

## 5. Pontos de Atenção

1. **Segurança**:
   - Verificação de autenticação e autorização
   - Validação de entrada (tema_id)
   - Proteção contra race conditions

2. **Integridade dos Dados**:
   - Uso de transações para operações múltiplas
   - Verificação de disponibilidade do tema
   - Validação de datas limite

3. **Tratamento de Erros**:
   - Captura de exceções
   - Rollback em caso de falha
   - Mensagens de erro amigáveis

4. **Performance**:
   - Índices nas colunas de JOIN
   - Otimização das consultas
   - Gerenciamento de conexões

## 6. Recomendações de Melhoria

1. Implementar validação adicional de datas limite
2. Adicionar log detalhado de operações
3. Implementar notificação ao orientador
4. Adicionar confirmação antes da seleção
5. Implementar limite de tentativas de inscrição
6. Melhorar feedback visual do processo
7. Adicionar histórico de seleções anteriores