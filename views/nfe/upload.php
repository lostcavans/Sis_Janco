<?php
// views/nfe/upload.php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../classes/NFeProcessor.php';
require_once __DIR__ . '/../../controllers/NFeController.php';

$controller = new NFeController($pdo);
$mensagem = '';
$nfeId = null;
$nfe = null;
$itens = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_FILES['xml_file'])) {
            // Upload do XML
            $nfeId = $controller->uploadXML($_FILES['xml_file']);
            $_SESSION['mensagem'] = 'NFe processada com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
            header("Location: nfe_calculo.php?id=" . $nfeId);
            exit;
        } elseif (isset($_POST['calcular_st'])) {
            // Cálculo do ST
            $nfeId = $_POST['nfe_id'];
            $aliquotaInterna = $_POST['aliq_interna'];
            $mvaOriginal = $_POST['mva_original'];
            $mvaCNAE = $_POST['mva_cnae'] ?? null;
            
            $controller->calcularST($nfeId, $aliquotaInterna, $mvaOriginal, $mvaCNAE);
            
            $_SESSION['mensagem'] = 'Cálculo de ST realizado com sucesso!';
            $_SESSION['tipo_mensagem'] = 'success';
            header("Location: nfe_calculo.php?id=" . $nfeId);
            exit;
        }
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
    }
}

if (isset($_GET['id'])) {
    $nfeId = $_GET['id'];
    try {
        $nfe = $controller->getNFe($nfeId);
        $itens = $controller->getItensNFe($nfeId);
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
    }
}

// Exibe mensagens de sessão
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipoMensagem = $_SESSION['tipo_mensagem'];
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processar NFe - Sistema de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-invoice me-2"></i>Processar NFe</h2>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipoMensagem ?? 'danger' ?> alert-dismissible fade show">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload de NFe</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="xml_file" class="form-label">Arquivo XML da NFe</label>
                                <input type="file" class="form-control" id="xml_file" name="xml_file" accept=".xml" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-2"></i> Enviar e Processar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php if ($nfe): ?>
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Cálculo de ST</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="nfe_id" value="<?= $nfe['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="aliq_interna" class="form-label">Alíquota Interna (%)</label>
                                <input type="number" step="0.01" class="form-control" id="aliq_interna" 
                                       name="aliq_interna" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mva_original" class="form-label">MVA Original (%)</label>
                                <input type="number" step="0.01" class="form-control" id="mva_original" 
                                       name="mva_original" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mva_cnae" class="form-label">MVA CNAE (%)</label>
                                <input type="number" step="0.01" class="form-control" id="mva_cnae" 
                                       name="mva_cnae">
                            </div>
                            
                            <button type="submit" name="calcular_st" class="btn btn-success w-100">
                                <i class="fas fa-calculator me-2"></i> Calcular ST
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($nfe): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da NFe</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Número:</strong> <?= $nfe['numero'] ?></p>
                        <p><strong>Série:</strong> <?= $nfe['serie'] ?></p>
                        <p><strong>Data Emissão:</strong> <?= date('d/m/Y H:i', strtotime($nfe['data_emissao'])) ?></p>
                        <p><strong>Valor Total:</strong> R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Emitente:</strong> <?= $nfe['nome_emitente'] ?> (<?= $nfe['cnpj_emitente'] ?>)</p>
                        <p><strong>Destinatário:</strong> <?= $nfe['nome_destinatario'] ?> (<?= $nfe['cnpj_destinatario'] ?>)</p>
                        <p><strong>Total de Itens:</strong> <?= $nfe['total_itens'] ?></p>
                        <p><strong>Total Produtos:</strong> R$ <?= number_format($nfe['total_produtos'], 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Itens da NFe</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Descrição</th>
                                <th>NCM</th>
                                <th>Qtd</th>
                                <th>Valor Unit.</th>
                                <th>Valor Total</th>
                                <th>ST</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><?= $item['numero_item'] ?></td>
                                <td><?= $item['descricao'] ?></td>
                                <td><?= $item['ncm'] ?></td>
                                <td><?= number_format($item['quantidade'], 4, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['valor_unitario'], 4, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['valor_total'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($item['valor_st'] ?? 0, 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>