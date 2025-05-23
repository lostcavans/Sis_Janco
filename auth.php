<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 dia
        'cookie_secure' => true,    // Apenas em HTTPS
        'cookie_httponly' => true,  // Acessível apenas via HTTP (não JavaScript)
        'cookie_samesite' => 'Strict' // Proteção contra CSRF
    ]);
}

require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
public function login($email, $senha) {
    if (empty($email) || empty($senha) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Dados de login inválidos'];
    }

    try {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, nivel FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuário não encontrado'];
        }

        if (!password_verify($senha, $usuario['senha'])) {
            return ['success' => false, 'message' => 'Senha incorreta'];
        }

        session_regenerate_id(true);
        
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'nivel' => $usuario['nivel'],
            'ultimo_login' => time()
        ];

        return ['success' => true, 'usuario' => $_SESSION['usuario']];
        
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro no servidor'];
    }
}
    public function logout() {
        // Limpa todos os dados da sessão
        $_SESSION = array();
        
        // Remove o cookie de sessão
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
        
        // Destrói a sessão
        session_destroy();
        
        // Redireciona para login sem usar BASE_URL para evitar problemas
        header("Location: login.php");
        exit;
    }
    
    public function verificarAcesso($niveisPermitidos = []) {
        // Se não está logado, redireciona
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['url_redirect'] = $_SERVER['REQUEST_URI'];
            header("Location: login.php");
            exit;
        }
        
        // Verifica permissões se necessário
        if (!empty($niveisPermitidos)) {
            $nivelUsuario = $_SESSION['usuario']['nivel'] ?? '';
            if (!in_array($nivelUsuario, $niveisPermitidos)) {
                header("Location: acesso-negado.php");
                exit;
            }
        }
        
        return true;
    }
    
    public function usuarioLogado() {
        return $_SESSION['usuario'] ?? null;
    }
    
    public function isAdmin() {
        return ($_SESSION['usuario']['nivel'] ?? '') === 'admin';
    }
}