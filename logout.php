<?php
// Arquivo: logout.php
require_once 'config.php';
require_once 'RegistroAcessoModel.php';

// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

// Registra o logout apenas se o usuário estava logado
if (isset($_SESSION['usuario']['id']) && !isset($_SESSION['logout_em_andamento'])) {
    $_SESSION['logout_em_andamento'] = true; // Previne execução duplicada
    
    try {
        $registroAcessoModel = new RegistroAcessoModel($pdo);
        $registroAcessoModel->registrarAcesso($_SESSION['usuario']['id'], 'logout');
    } catch (Exception $e) {
        error_log("Erro ao registrar logout: " . $e->getMessage());
    }
}

// Limpa e destrói a sessão
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// Redirecionamento seguro
$parametro = isset($_GET['timeout']) ? 'timeout=1' : 'logout=1';
header("Location: " . BASE_URL . "/login.php?" . $parametro);
exit;
?>