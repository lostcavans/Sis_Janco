<?php
class EmpresaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function cadastrarEmpresa($dados) {
    $sql = "INSERT INTO empresas (
        cnpj, razao_social, nome_fantasia, data_abertura, cnae_principal,
        descricao_cnae, natureza_juridica, capital_social, porte, situacao_cadastral,
        data_situacao_cadastral, motivo_situacao_cadastral, email, telefone,
        cep, logradouro, numero, complemento, bairro, municipio, uf
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        $dados['cnpj'],
        $dados['razao_social'],
        $dados['nome_fantasia'],
        $dados['data_abertura'],
        $dados['cnae_principal'],
        $dados['descricao_cnae'],
        $dados['natureza_juridica'],
        $dados['capital_social'],
        $dados['porte'],
        $dados['situacao_cadastral'],
        $dados['data_situacao_cadastral'],
        $dados['motivo_situacao_cadastral'],
        $dados['email'],
        $dados['telefone'],
        $dados['cep'],
        $dados['logradouro'],
        $dados['numero'],
        $dados['complemento'],
        $dados['bairro'],
        $dados['municipio'],
        $dados['uf']
    ]);
    
    return $this->pdo->lastInsertId();
}

    public function listarEmpresas($filtros = [], $pagina = 1, $itensPorPagina = 10) {
    $sql = "SELECT id, cnpj, razao_social, nome_fantasia, municipio, uf FROM empresas WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['cnpj'])) {
        $sql .= " AND cnpj LIKE ?";
        $params[] = '%' . $filtros['cnpj'] . '%';
    }
    
    if (!empty($filtros['razao_social'])) {
        $sql .= " AND razao_social LIKE ?";
        $params[] = '%' . $filtros['razao_social'] . '%';
    }
    
    if (!empty($filtros['municipio'])) {
        $sql .= " AND municipio LIKE ?";
        $params[] = '%' . $filtros['municipio'] . '%';
    }
    
    // Correção da paginação - convertendo para inteiros
    $offset = ($pagina - 1) * $itensPorPagina;
    $sql .= " LIMIT :offset, :itensPorPagina";
    
    $stmt = $this->pdo->prepare($sql);
    
    // Bind dos parâmetros de paginação
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':itensPorPagina', (int)$itensPorPagina, PDO::PARAM_INT);
    
    // Executa a query com os parâmetros de filtro
    foreach ($params as $i => $param) {
        $stmt->bindValue($i + 1, $param);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function buscarEmpresaPorId($id) {
    try {
        error_log("Buscando empresa com ID: " . $id);
        $stmt = $this->pdo->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Resultado da busca: " . print_r($empresa, true));
        
        return $empresa;
    } catch (PDOException $e) {
        error_log("Erro ao buscar empresa: " . $e->getMessage());
        return false;
    }
}

public function buscarEmpresaPorCnpj($cnpj) {
    $stmt = $this->pdo->prepare("SELECT * FROM empresas WHERE cnpj = ?");
    $stmt->execute([$cnpj]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function atualizarEmpresa($id, $dados) {
    $sql = "UPDATE empresas SET 
            razao_social = ?, nome_fantasia = ?, data_abertura = ?, 
            cnae_principal = ?, natureza_juridica = ?, capital_social = ?, 
            porte = ?, situacao_cadastral = ?, email = ?, telefone = ?, 
            cep = ?, logradouro = ?, numero = ?, complemento = ?, 
            bairro = ?, municipio = ?, uf = ? 
            WHERE id = ?";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        $dados['razao_social'], $dados['nome_fantasia'], $dados['data_abertura'],
        $dados['cnae_principal'], $dados['natureza_juridica'], $dados['capital_social'],
        $dados['porte'], $dados['situacao_cadastral'], $dados['email'], $dados['telefone'],
        $dados['cep'], $dados['logradouro'], $dados['numero'], $dados['complemento'],
        $dados['bairro'], $dados['municipio'], $dados['uf'], $id
    ]);
}

public function excluirEmpresa($id) {
    $stmt = $this->pdo->prepare("DELETE FROM empresas WHERE id = ?");
    return $stmt->execute([$id]);
}
    public function contarEmpresas($filtros = []) {
    $sql = "SELECT COUNT(*) as total FROM empresas WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['cnpj'])) {
        $sql .= " AND cnpj LIKE ?";
        $params[] = '%' . $filtros['cnpj'] . '%';
    }
    
    if (!empty($filtros['razao_social'])) {
        $sql .= " AND razao_social LIKE ?";
        $params[] = '%' . $filtros['razao_social'] . '%';
    }
    
    if (!empty($filtros['municipio'])) {
        $sql .= " AND municipio LIKE ?";
        $params[] = '%' . $filtros['municipio'] . '%';
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $resultado['total'] ?? 0;
}
}