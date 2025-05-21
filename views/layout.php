<?php
/**
 * Layout principal del sistema
 * 
 * @var string $titulo Título de la página
 * @var string $conteudo Contenido HTML principal
 * @var array $cssFiles Archivos CSS adicionales
 * @var array $jsFiles Archivos JS adicionales
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de cadastro de empresas">
    
    <!-- Título dinámico con fallback -->
    <title><?= htmlspecialchars($titulo ?? 'Sistema de Empresas') ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?= $favicon ?? 'assets/images/favicon.ico' ?>" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <!-- Estilos principales -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
    
    <!-- CSS adicional por página -->
    <?php if (!empty($cssFiles)): ?>
        <?php foreach ($cssFiles as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>?v=<?= filemtime($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <!-- jQuery (necessário para máscaras) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <?php include 'views/partials/header.php'; ?>
    <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?> alert-dismissible fade show">
        <?= $_SESSION['mensagem'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensagem']); unset($_SESSION['tipo_mensagem']); ?>
<?php endif; ?>
    
    <!-- Contenido principal -->
    <main class="container mt-4 flex-grow-1">
        <!-- Mensajes flash -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?> alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['mensagem']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensagem']); unset($_SESSION['tipo_mensagem']); ?>
        <?php endif; ?>

        <!-- Contenido dinámico -->
        <?= $conteudo ?? '<div class="alert alert-warning">Nenhum conteúdo disponível</div>' ?>
    </main>

    <!-- Footer -->
    <?php include 'views/partials/footer.php'; ?>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" 
            crossorigin="anonymous"></script>
    
    <!-- JS principal -->
    <script src="assets/js/app.js?v=<?= filemtime('assets/js/app.js') ?>"></script>
    
    <!-- JS adicional por página -->
    <?php if (!empty($jsFiles)): ?>
        <?php foreach ($jsFiles as $js): ?>
            <script src="<?= $js ?>?v=<?= filemtime($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>