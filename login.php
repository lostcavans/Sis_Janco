<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'RegistroAcessoModel.php';

// Inicia a sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se já estiver logado, redireciona para a página inicial
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$auth = new Auth($pdo);
$registroAcessoModel = new RegistroAcessoModel($pdo);

$mensagem = '';
$email = '';
$senha = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    
    $resultado = $auth->login($email, $senha);
    
    if ($resultado['success']) {
        // Registra o acesso no banco de dados
        $registroAcessoModel->registrarAcesso($_SESSION['usuario']['id'], 'login');
        
        $_SESSION['mensagem'] = 'Login realizado com sucesso!';
        $_SESSION['tipo_mensagem'] = 'success';
        header("Location: index.php");
        exit;
    } else {
        sleep(1); // Atraso para prevenir brute force
        $mensagem = $resultado['message'];
        $email = htmlspecialchars($email);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Cadastro de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="fas fa-sign-in-alt"></i> Acessar o Sistema</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensagem): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?= $mensagem ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['logout'])): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Logout realizado com sucesso!
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="fas fa-envelope"></i> E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= $email ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label"><i class="fas fa-lock"></i> Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small class="text-muted">
                            Sistema de Cadastro de Empresas &copy; <?= date('Y') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome (para ícones) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>