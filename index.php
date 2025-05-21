<?php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'controllers/EmpresaController.php';
require_once 'helpers.php'; // Adicione esta linha

error_reporting(E_ALL);
ini_set('display_errors', 1);

$auth = new Auth($pdo);
$auth->verificarAcesso();

$controller = new EmpresaController($pdo);
$acao = $_GET['acao'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Inicia o buffer principal
ob_start();

try {
    switch ($acao) {
        case 'cadastrar':
            $conteudo = $controller->cadastrar();
            break;
            
        case 'editar':
            if (!$id) {
                throw new Exception("ID não fornecido para edição");
            }
            $conteudo = $controller->editar($id);
            break;
            
        case 'excluir':
            if (!$id) {
                throw new Exception("ID não fornecido para exclusão");
            }
            $controller->excluir($id);
            exit;
            
        case 'visualizar':
            $conteudo = $controller->visualizar();
            break;
            
        default:
            $conteudo = $controller->listar();
    }
    
    // Se o método não retornou conteúdo, pega do buffer
    if (empty($conteudo)) {
        $conteudo = ob_get_clean();
    } else {
        // Limpa qualquer buffer extra
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
    
} catch (Exception $e) {
    // Limpa todos os buffers em caso de erro
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: index.php?acao=listar');
    exit;
}

$titulo = $controller->getTitulo();
require 'views/layout.php';