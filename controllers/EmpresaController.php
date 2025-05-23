<?php
require_once __DIR__ . '/../EmpresaModel.php';

class EmpresaController {
    private $model;
    private $pdo;
    private $dadosView = []; // Armazena dados para a view
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->model = new EmpresaModel($pdo);
        $this->dadosView['titulo'] = 'Sistema de Empresas'; // Título padrão
    }
    
    public function cadastrar() {
    $this->dadosView['titulo'] = 'Cadastrar Nova Empresa';
    
    // Inicia o buffer para esta view específica
    ob_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = $this->validarDados($_POST);
        
        if ($dados) {
            try {
                $id = $this->model->cadastrarEmpresa($dados);
                $_SESSION['mensagem'] = 'Empresa cadastrada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
                header("Location: index.php?acao=visualizar&id=" . $id);
                exit;
            } catch (Exception $e) {
                $this->dadosView['erro'] = "Erro ao cadastrar empresa: " . $e->getMessage();
                $this->dadosView['dados_form'] = $_POST;
            }
        } else {
            $this->dadosView['erro'] = "Dados inválidos! Verifique os campos do formulário.";
            $this->dadosView['dados_form'] = $_POST;
        }
    }
    
    // Inclui a view de cadastro
    include 'views/empresa/cadastrar.php';
    
    // Retorna o conteúdo capturado
    return ob_get_clean();
}
    
    public function listar() {
        $this->dadosView['titulo'] = 'Listagem de Empresas';
        $filtros = $_GET;
        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $itensPorPagina = 10;
        
        $this->dadosView['empresas'] = $this->model->listarEmpresas($filtros, $pagina, $itensPorPagina);
        $this->dadosView['total'] = $this->model->contarEmpresas($filtros);
        $this->dadosView['pagina'] = $pagina;
        $this->dadosView['itensPorPagina'] = $itensPorPagina;
        $this->dadosView['totalPaginas'] = ceil($this->dadosView['total'] / $itensPorPagina);
        $this->dadosView['filtros'] = $filtros;
        


        $conteudo = $this->carregarView('listar');
// Debug: verifique a primeira empresa retornada
    if (!empty($this->dadosView['empresas'])) {
        error_log(print_r($this->dadosView['empresas'][0], true));
    }

        return $conteudo;
    }
    
public function visualizar() {
    error_log("Tentando visualizar empresa. ID: " . $_GET['id'] . " | CNPJ: " . ($_GET['cnpj'] ?? 'N/A'));
    // Limpa qualquer buffer anterior
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    
    
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cnpj = filter_input(INPUT_GET, 'cnpj', FILTER_DEFAULT);
    
    if ($id) {
        $empresa = $this->model->buscarEmpresaPorId($id);
    } elseif ($cnpj) {
        $empresa = $this->model->buscarEmpresaPorCnpj($cnpj);
    } else {
        $_SESSION['mensagem'] = 'Nenhum identificador fornecido!';
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: index.php?acao=listar");
        exit;
    }
    
    if (!$empresa) {
        $_SESSION['mensagem'] = 'Empresa não encontrada!';
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: index.php?acao=listar");
        exit;
    }

    $this->dadosView['titulo'] = 'Detalhes: ' . $empresa['razao_social'];
    $this->dadosView['empresa'] = $empresa;
    
    // Retorna o conteúdo diretamente sem buffer
    extract($this->dadosView);
    ob_start();
    include "views/empresa/visualizar.php";
    return ob_get_clean();
}
    
    public function editar($id) {
    $empresa = $this->model->buscarEmpresaPorId($id);
    
    if (!$empresa) {
        $_SESSION['mensagem'] = 'Empresa não encontrada!';
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: " . BASE_URL . "/index.php?acao=listar");
        exit;
    }

    $this->dadosView['titulo'] = 'Editar Empresa: ' . $empresa['razao_social'];
    $this->dadosView['empresa'] = $empresa;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = $this->validarDados($_POST);
        
        if ($dados && $this->model->atualizarEmpresa($id, $dados)) {
            $_SESSION['mensagem'] = 'Empresa atualizada com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
            header("Location: " . BASE_URL . "/index.php?acao=visualizar&id=" . $id);
            exit;
        } else {
            $this->dadosView['erro'] = "Erro ao atualizar empresa!";
            $this->dadosView['dados_form'] = $_POST;
        }
    }
    
    return $this->carregarView('empresa/editar');
}

    
    
    // Añade métodos getter y setter
    public function getTitulo() {
        return $this->dadosView['titulo'] ?? 'Sistema de Empresas';
    }
    
    public function setTitulo($titulo) {
        $this->dadosView['titulo'] = $titulo;
    }
    
    public function getDadosView() {
        return $this->dadosView;
    }

    
    
    public function excluir($id) {
        if ($this->model->excluirEmpresa($id)) {
            $_SESSION['mensagem'] = 'Empresa excluída com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
        } else {
            $_SESSION['mensagem'] = 'Erro ao excluir empresa!';
            $_SESSION['tipo_mensagem'] = 'danger';
        }
        header("Location: index.php?acao=listar");
        exit;
    }
    
    private function validarDados($dados) {
    $cnpj = preg_replace('/[^0-9]/', '', $dados['cnpj']);
    
    if (!validarCNPJ($cnpj)) {
        $this->dadosView['erro'] = 'CNPJ inválido!';
        return false;
    }

    return [
        'cnpj' => $cnpj,
        'razao_social' => trim($dados['razao_social']),
        'nome_fantasia' => trim($dados['nome_fantasia'] ?? ''),
        'data_abertura' => $dados['data_abertura'] ?? null,
        'cnae_principal' => $dados['cnae_principal'] ?? '',
        'descricao_cnae' => '', // Definindo como string vazia
        'natureza_juridica' => $dados['natureza_juridica'] ?? '',
        'capital_social' => isset($dados['capital_social']) ? 
                          str_replace(['.', ','], ['', '.'], $dados['capital_social']) : 0,
        'porte' => $dados['porte'] ?? 'DEMAIS',
        'situacao_cadastral' => $dados['situacao_cadastral'] ?? 'Ativa',
        'data_situacao_cadastral' => null, // Ou date('Y-m-d') se quiser data atual
        'motivo_situacao_cadastral' => '',
        'email' => filter_var($dados['email'] ?? '', FILTER_SANITIZE_EMAIL),
        'telefone' => preg_replace('/[^0-9]/', '', $dados['telefone'] ?? ''),
        'cep' => preg_replace('/[^0-9]/', '', $dados['cep'] ?? ''),
        'logradouro' => $dados['logradouro'] ?? '',
        'numero' => $dados['numero'] ?? '',
        'complemento' => $dados['complemento'] ?? '', // String vazia ao invés de NULL
        'bairro' => $dados['bairro'] ?? '',
        'municipio' => $dados['municipio'] ?? '',
        'uf' => $dados['uf'] ?? ''
    ];
}

    private function carregarView($view) {
    $possiveisCaminhos = [
        dirname(__DIR__, 2) . '/views/empresa/' . basename($view) . '.php',
        __DIR__ . '/../views/empresa/' . basename($view) . '.php',
        'C:/xampp/htdocs/Cadastro_de_empresas/views/empresa/' . basename($view) . '.php'
    ];
    
    foreach ($possiveisCaminhos as $caminho) {
        if (file_exists($caminho)) {
            ob_start();
            extract($this->dadosView);
            include $caminho;
            return ob_get_clean();
        }
    }
    
    throw new Exception("View não encontrada em nenhum destes locais: " . implode(', ', $possiveisCaminhos));
}

public function getDadosEmpresa($cnpj) {
    try {
        return $this->model->buscarEmpresaPorCnpj($cnpj);
    } catch (Exception $e) {
        error_log("Erro ao buscar empresa por CNPJ: " . $e->getMessage());
        return null;
    }
}


}
