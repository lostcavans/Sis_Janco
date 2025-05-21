<?php
// Remova session_start() daqui e deixe apenas no auth.php
require_once 'config.php';
require_once 'auth.php';

$auth = new Auth($pdo);

$mensagem = '';
$email = '';
$senha = '';

// Se já estiver logado, redireciona para a página inicial
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    
    if ($auth->login($email, $senha)) {
        $_SESSION['mensagem'] = 'Login realizado com sucesso!';
        $_SESSION['tipo_mensagem'] = 'success';
        header("Location: index.php");
        exit;
    } else {
        $mensagem = 'E-mail ou senha incorretos!';
        $email = htmlspecialchars($email);
    }

    // APENAS PARA DESENVOLVIMENTO - REMOVA EM PRODUÇÃO
    if ($_POST['email'] === 'japinhanaruto@hotmail.com' && $_POST['senha'] === 'admin') {
        $_SESSION['usuario'] = [
            'id' => 2,
            'nome' => 'Janco',
            'email' => 'japinhanaruto@hotmail.com',
            'nivel' => 'operador'
        ];
        header("Location: index.php");
        exit;
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <!-- Restante do seu HTML permanece igual -->
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Acessar o Sistema</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensagem): ?>
                            <div class="alert alert-danger"><?= $mensagem ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= $email ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>Sistema de Cadastro de Empresas &copy; <?= date('Y') ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>