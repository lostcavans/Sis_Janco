<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../controllers/EmpresaController.php';

$controller = new EmpresaController($pdo);

// Verifica se há uma mensagem na sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipoMensagem = $_SESSION['tipo_mensagem'];
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}

// Determina a ação com base nos parâmetros
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Processa a ação
switch ($acao) {
    case 'visualizar':
        $conteudo = $controller->visualizar();
        break;
    case 'editar':
        $conteudo = $controller->editar($id);
        break;
    case 'listar':
    default:
        $conteudo = $controller->listar();
        break;
}

// Inicia o buffer de saída
ob_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Empresas - <?= ucfirst($acao) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Estilos principais -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../../views/partials/header.php'; ?>
    
    <main class="container mt-4">
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?= $conteudo ?>
    </main>
    
    <?php include __DIR__ . '/../../views/partials/footer.php'; ?>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" 
            crossorigin="anonymous"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <!-- Scripts principais -->
    <script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= filemtime('assets/js/main.js') ?>"></script>
</body>
</html>
<?php
// Limpa o buffer e exibe o conteúdo
ob_end_flush();
?>