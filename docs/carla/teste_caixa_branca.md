# Teste de Caixa Branca - Sistema TFC

## Função Analisada: Gerenciamento de Orientações

```php
function gerenciarOrientacao($orientacao_id, $projeto_id, $orientador_id, $estudante_id) {
    // 1. Validação inicial dos parâmetros
    if (!$orientacao_id || !$projeto_id || !$orientador_id || !$estudante_id) {
        return false;
    }

    // 2. Verificar disponibilidade do orientador
    $disponivel = verificarDisponibilidadeOrientador($orientador_id);
    if (!$disponivel) {
        return false;
    }

    // 3. Verificar status do projeto
    $status_projeto = obterStatusProjeto($projeto_id);
    if ($status_projeto === 'concluido' || $status_projeto === 'reprovado') {
        return false;
    }

    // 4. Atualizar orientação
    $resultado = atualizarOrientacao($orientacao_id, $projeto_id, $orientador_id, $estudante_id);
    if (!$resultado) {
        return false;
    }

    // 5. Registrar log da atividade
    registrarLog($orientador_id, 'orientacao_atualizada', $orientacao_id);
    return true;
}
```

## Grafo de Fluxo de Controle

```svg
<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="600" xmlns="http://www.w3.org/2000/svg">
    <!-- Nós do grafo -->
    <circle cx="200" cy="50" r="20" fill="#4CAF50" />
    <text x="195" y="55" fill="white">1</text>
    
    <circle cx="200" cy="150" r="20" fill="#2196F3" />
    <text x="195" y="155" fill="white">2</text>
    
    <circle cx="200" cy="250" r="20" fill="#2196F3" />
    <text x="195" y="255" fill="white">3</text>
    
    <circle cx="200" cy="350" r="20" fill="#2196F3" />
    <text x="195" y="355" fill="white">4</text>
    
    <circle cx="200" cy="450" r="20" fill="#2196F3" />
    <text x="195" y="455" fill="white">5</text>
    
    <circle cx="200" cy="550" r="20" fill="#F44336" />
    <text x="195" y="555" fill="white">6</text>
    
    <circle cx="350" cy="300" r="20" fill="#F44336" />
    <text x="345" y="305" fill="white">7</text>
    
    <!-- Arestas do grafo -->
    <line x1="200" y1="70" x2="200" y2="130" stroke="black" />
    <line x1="200" y1="170" x2="200" y2="230" stroke="black" />
    <line x1="200" y1="270" x2="200" y2="330" stroke="black" />
    <line x1="200" y1="370" x2="200" y2="430" stroke="black" />
    <line x1="200" y1="470" x2="200" y2="530" stroke="black" />
    
    <line x1="220" y1="150" x2="350" y2="300" stroke="black" />
    <line x1="220" y1="250" x2="350" y2="300" stroke="black" />
    <line x1="220" y1="350" x2="350" y2="300" stroke="black" />
</svg>
```

## Análise dos Caminhos

### Nós do Grafo
1. Início da função
2. Validação dos parâmetros
3. Verificação de disponibilidade do orientador
4. Verificação do status do projeto
5. Atualização da orientação
6. Sucesso - Retorno true
7. Falha - Retorno false

### Caminhos Possíveis

1. Caminho de Sucesso:
   1 → 2 → 3 → 4 → 5 → 6

2. Falha na validação de parâmetros:
   1 → 2 → 7

3. Falha na disponibilidade do orientador:
   1 → 2 → 3 → 7

4. Falha no status do projeto:
   1 → 2 → 3 → 4 → 7

5. Falha na atualização:
   1 → 2 → 3 → 4 → 5 → 7

### Casos de Teste

1. **CT01 - Caminho de Sucesso**
   - Entrada: Todos os IDs válidos, orientador disponível, projeto em andamento
   - Resultado Esperado: true

2. **CT02 - Parâmetros Inválidos**
   - Entrada: Um ou mais IDs nulos ou vazios
   - Resultado Esperado: false

3. **CT03 - Orientador Indisponível**
   - Entrada: IDs válidos, orientador com limite de orientandos
   - Resultado Esperado: false

4. **CT04 - Projeto Concluído**
   - Entrada: IDs válidos, projeto com status 'concluido'
   - Resultado Esperado: false

5. **CT05 - Erro na Atualização**
   - Entrada: IDs válidos, erro no banco de dados
   - Resultado Esperado: false

## Complexidade Ciclomática
- Número de arestas (E) = 8
- Número de nós (N) = 7
- V(G) = E - N + 2 = 8 - 7 + 2 = 3

A complexidade ciclomática é 3, indicando que existem 3 caminhos independentes no código.