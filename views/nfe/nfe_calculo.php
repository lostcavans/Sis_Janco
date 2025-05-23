<?php
// views/nfe/calculo.php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../classes/NFeProcessor.php';
require_once __DIR__ . '/../../controllers/NFeController.php';

$controller = new NFeController($pdo);
$mensagem = '';
$nfeId = null;
$nfe = null;
$itens = [];
$emitente = null;
$destinatario = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['calcular_st'])) {
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
        
        // Obtém dados das empresas
        $emitente = $controller->getDadosEmpresa($nfe['cnpj_emitente']);
        $destinatario = $controller->getDadosEmpresa($nfe['cnpj_destinatario']);
        
    } catch (Exception $e) {
        $mensagem = [
            'tipo' => 'danger',
            'texto' => $e->getMessage()
        ];
    }
}


// Exibe mensagens específicas do processamento da NFe
if (isset($nfe['mensagens'])) {
    foreach ($nfe['mensagens'] as $msg) {
        echo '<div class="alert alert-'.$msg['tipo'].'">'.$msg['texto'].'</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cálculo de ST - Sistema de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calculator me-2"></i>Cálculo de Substituição Tributária</h2>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        
        <?php if ($mensagem): ?>
    <div class="alert alert-<?= is_array($mensagem) ? $mensagem['tipo'] : ($tipoMensagem ?? 'danger') ?> alert-dismissible fade show">
        <?= is_array($mensagem) ? $mensagem['texto'] : $mensagem ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
        
        <?php if ($nfe): ?>
        <!-- Seção de Empresas -->
        <div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building"></i> Emitente
                    <?php if ($emitente): ?>
                        <span class="badge bg-success float-end">Cadastrado</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark float-end">Não encontrado</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Razão Social:</strong> <?= htmlspecialchars($nfe['nome_emitente']) ?></p>
                <p><strong>CNPJ:</strong> <?= htmlspecialchars($nfe['cnpj_emitente']) ?></p>
                
                <?php if ($emitente): ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-check-circle"></i> Esta empresa já está cadastrada em nosso sistema
                    </div>
                    <a href="empresas.php?acao=editar&id=<?= $emitente['id'] ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Ver cadastro completo
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i> Empresa não encontrada no cadastro
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building"></i> Destinatário
                    <?php if ($destinatario): ?>
                        <span class="badge bg-success float-end">Cadastrado</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark float-end">Não encontrado</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Razão Social:</strong> <?= htmlspecialchars($nfe['nome_destinatario']) ?></p>
                <p><strong>CNPJ:</strong> <?= htmlspecialchars($nfe['cnpj_destinatario']) ?></p>
                
                <?php if ($destinatario): ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-check-circle"></i> Esta empresa já está cadastrada em nosso sistema
                    </div>
                    <a href="empresas.php?acao=editar&id=<?= $destinatario['id'] ?>" 
                       class="btn btn-sm btn-outline-info">
                        <i class="fas fa-eye"></i> Ver cadastro completo
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i> Empresa não encontrada no cadastro
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
        
        <!-- Seção de Cálculo -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Parâmetros para Cálculo</h5>
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
            
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da NFe</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Número:</strong> <?= htmlspecialchars($nfe['numero']) ?></p>
                        <p><strong>Data Emissão:</strong> <?= date('d/m/Y H:i', strtotime($nfe['data_emissao'])) ?></p>
                        <p><strong>Valor Total:</strong> R$ <?= number_format($nfe['valor_total'], 2, ',', '.') ?></p>
                        <p><strong>Chave de Acesso:</strong> <?= htmlspecialchars($nfe['chave_acesso']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabela de Itens -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
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
                                <th>ST Calculado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><?= $item['numero_item'] ?></td>
                                <td><?= htmlspecialchars($item['descricao']) ?></td>
                                <td><?= htmlspecialchars($item['ncm']) ?></td>
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
        <?php else: ?>
        <div class="alert alert-warning">
            Nenhuma NFe encontrada. Por favor, <a href="<?= BASE_URL ?>/views/nfe/upload.php">faça upload de uma NFe</a> primeiro.
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>