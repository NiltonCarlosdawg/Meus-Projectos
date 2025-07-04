# Teste de Caixa Branca - reunioes.php

## 1. Análise de Complexidade Ciclomática

Identificamos os seguintes nós de decisão no código:

1. Verificação de login e permissão: `if (!isLoggedIn() || !isOrientador())`
2. Verificação de projeto_id: `if ($projeto_id)`
3. Tratamento de exceção: `try-catch` para busca de reuniões
4. Verificação de erro: `if ($error)`
5. Verificação de sucesso: `if ($success)`
6. Verificação de reuniões vazias: `if (empty($reunioes))`
7. Verificação de tipo de reunião: `if ($reuniao['tipo'] === 'online' && $reuniao['link_reuniao'])`

Complexidade Ciclomática = 7 (número de decisões) + 1 = 8

## 2. Grafo de Fluxo de Controle

```
[Início] -> [Verificação de Login/Permissão]
                |
                v
[Conexão BD] -> [Obter projeto_id]
                |
                v
[Construir SQL Base] -> [Verificar projeto_id]
                |               |
                |               v
                |        [Adicionar Filtro]
                |               |
                v               v
        [Executar Query] -> [Try-Catch]
                |
                v
[Buscar Projetos] -> [Renderizar HTML]
                |
                v
[Verificar Erro/Sucesso] -> [Exibir Mensagens]
                |
                v
[Listar Reuniões] -> [Verificar Reuniões Vazias]
                |               |
                v               v
        [Iterar Reuniões]   [Exibir Mensagem Vazia]
                |
                v
[Verificar Tipo Reunião] -> [Exibir Link/Local]
                |
                v
            [Fim]
```

## 3. Caminhos de Execução

### Caminho 1: Acesso Não Autorizado
- Condição: `!isLoggedIn() || !isOrientador()`
- Resultado Esperado: Redirecionamento para login.php

### Caminho 2: Listagem Sem Filtro
- Condição: `$projeto_id === null`
- SQL: Sem cláusula AND adicional
- Resultado Esperado: Todas as reuniões do orientador

### Caminho 3: Listagem Com Filtro
- Condição: `$projeto_id !== null`
- SQL: Com cláusula AND adicional
- Resultado Esperado: Apenas reuniões do projeto específico

### Caminho 4: Erro na Consulta
- Condição: Exceção PDO
- Resultado Esperado: Mensagem de erro exibida

### Caminho 5: Sem Reuniões
- Condição: `empty($reunioes)`
- Resultado Esperado: Mensagem "Nenhuma reunião encontrada"

### Caminho 6: Reunião Online
- Condição: `$reuniao['tipo'] === 'online' && $reuniao['link_reuniao']`
- Resultado Esperado: Link clicável para a reunião

### Caminho 7: Reunião Presencial
- Condição: `$reuniao['tipo'] !== 'online'`
- Resultado Esperado: Exibição do local físico

## 4. Casos de Teste

### CT01: Verificação de Acesso
```php
// Cenário: Usuário não logado
Session::destroy();
// Resultado Esperado: Redirecionamento para login.php

// Cenário: Usuário logado mas não orientador
$_SESSION['user_type'] = 'estudante';
// Resultado Esperado: Redirecionamento para login.php
```

### CT02: Listagem de Reuniões
```php
// Cenário: Sem filtro de projeto
$_GET['projeto_id'] = null;
// Resultado Esperado: Todas as reuniões listadas

// Cenário: Com filtro de projeto
$_GET['projeto_id'] = '1';
// Resultado Esperado: Apenas reuniões do projeto 1
```

### CT03: Tratamento de Erros
```php
// Cenário: Erro na conexão com banco
$pdo = null;
// Resultado Esperado: Mensagem "Erro ao buscar reuniões"

// Cenário: Erro na consulta SQL
$sql = "SELECT * FROM tabela_inexistente";
// Resultado Esperado: Mensagem "Erro ao buscar reuniões"
```

### CT04: Exibição de Reuniões
```php
// Cenário: Nenhuma reunião encontrada
$reunioes = [];
// Resultado Esperado: Mensagem "Nenhuma reunião encontrada"

// Cenário: Reunião online com link
$reuniao = ['tipo' => 'online', 'link_reuniao' => 'https://meet.google.com/abc'];
// Resultado Esperado: Link clicável para a reunião

// Cenário: Reunião presencial
$reuniao = ['tipo' => 'presencial', 'local' => 'Sala 101'];
// Resultado Esperado: Exibição do local
```

## 5. Pontos de Atenção

1. **Segurança**:
   - Verificação de autenticação e autorização
   - Sanitização de entrada (projeto_id)
   - Escape de saída HTML

2. **Tratamento de Erros**:
   - Captura de exceções PDO
   - Mensagens de erro amigáveis

3. **Validação de Dados**:
   - Formato de data/hora
   - URLs de reunião online
   - Campos obrigatórios

4. **Performance**:
   - Índices nas colunas de JOIN
   - Limitação de resultados
   - Paginação (recomendação futura)

## 6. Recomendações de Melhoria

1. Implementar paginação para grandes conjuntos de dados
2. Adicionar filtros adicionais (data, status)
3. Implementar cache para consultas frequentes
4. Adicionar logs de erro detalhados
5. Implementar validação de URL para reuniões online
6. Adicionar confirmação antes de acessar links externos