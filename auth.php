<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($email, $senha) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'nivel' => $usuario['nivel']
            ];
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit;
    }
    
    public function verificarAcesso($nivelRequerido = null) {
        if (!isset($_SESSION['usuario'])) {
            header("Location: login.php");
            exit;
        }
        
        if ($nivelRequerido && $_SESSION['usuario']['nivel'] !== $nivelRequerido) {
            header("Location: acesso-negado.php");
            exit;
        }
    }
}