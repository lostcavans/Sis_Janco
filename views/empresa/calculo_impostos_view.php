<?php
// views/empresa/calculo_impostos_view.php
// Verificar se BASE_URL está definida, se não, definir
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

// Verifique se está usando o caminho correto para estes arquivos
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth.php';

// Verificar autenticação
if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

// Incluir o header usando caminho absoluto
require PARTIALS_PATH . '/header.php';

$calculadora = new CalculadoraImpostos($pdo);
$mensagem_erro = '';
$resultados = [];
$dados_form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $resultados = $calculadora->calcularImpostos($_POST);
        $dados_form = $_POST;
    } catch (Exception $e) {
        $mensagem_erro = "Erro no cálculo: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cálculo de Impostos - Sistema de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calculator me-2"></i>Cálculo de Impostos</h2>
            <a href="?acao=listar" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        
        <?php if ($mensagem_erro): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $mensagem_erro ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Dados para Cálculo</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formCalculo">
                            <div class="row g-3">
                                <!-- Seção de Valores Básicos -->
                                <div class="col-md-6">
                                    <label for="vProd" class="form-label">Valor do Produto (R$)</label>
                                    <input type="number" step="0.01" class="form-control" id="vProd" name="vProd" 
                                           value="<?= htmlspecialchars($dados_form['vProd'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="vFrete" class="form-label">Valor do Frete (R$)</label>
                                    <input type="number" step="0.01" class="form-control" id="vFrete" name="vFrete" 
                                           value="<?= htmlspecialchars($dados_form['vFrete'] ?? '0') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="vIPI" class="form-label">Valor do IPI (R$)</label>
                                    <input type="number" step="0.01" class="form-control" id="vIPI" name="vIPI" 
                                           value="<?= htmlspecialchars($dados_form['vIPI'] ?? '0') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="vICMSST" class="form-label">Valor da GNRE (R$)</label>
                                    <input type="number" step="0.01" class="form-control" id="vICMSST" name="vICMSST" 
                                           value="<?= htmlspecialchars($dados_form['vICMSST'] ?? '0') ?>">
                                </div>
                                
                                <!-- Seção de Alíquotas -->
                                <div class="col-md-6">
                                    <label for="aliq_interna" class="form-label">Alíquota Interna (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="aliq_interna" 
                                           name="aliq_interna" value="<?= htmlspecialchars($dados_form['aliq_interna'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="pICMS" class="form-label">Alíquota Interestadual (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="pICMS" name="pICMS" 
                                           value="<?= htmlspecialchars($dados_form['pICMS'] ?? '') ?>" required>
                                </div>
                                
                                <!-- Seção de MVAs -->
                                <div class="col-md-6">
                                    <label for="mva_original" class="form-label">MVA Original (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="mva_original" 
                                           name="mva_original" value="<?= htmlspecialchars($dados_form['mva_original'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="mva_cnae" class="form-label">MVA CNAE (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="mva_cnae" name="mva_cnae" 
                                           value="<?= htmlspecialchars($dados_form['mva_cnae'] ?? '0') ?>">
                                </div>
                                
                                <!-- Seção de Configurações Adicionais -->
                                <div class="col-md-6">
                                    <label for="aliq_reducao" class="form-label">Alíquota de Redução (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="aliq_reducao" 
                                           name="aliq_reducao" value="<?= htmlspecialchars($dados_form['aliq_reducao'] ?? '0') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="credito_icms" class="form-label">Crédito ICMS (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="credito_icms" 
                                           name="credito_icms" value="<?= htmlspecialchars($dados_form['credito_icms'] ?? '0') ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="difal" class="form-label">DIFAL (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="difal" name="difal" 
                                           value="<?= htmlspecialchars($dados_form['difal'] ?? '0') ?>">
                                </div>
                                
                                <!-- Seleção de Regime e Tipo -->
                                <div class="col-md-6">
                                    <label for="regime" class="form-label">Regime</label>
                                    <select class="form-select" id="regime" name="regime" required>
                                        <option value="normal" <?= ($dados_form['regime'] ?? '') == 'normal' ? 'selected' : '' ?>>Normal</option>
                                        <option value="simples" <?= ($dados_form['regime'] ?? '') == 'simples' ? 'selected' : '' ?>>Simples Nacional</option>
                                    </select>
                                </div>
                                
                                <div class="col-12">
                                    <label for="tipo_calculo" class="form-label">Tipo de Cálculo</label>
                                    <select class="form-select" id="tipo_calculo" name="tipo_calculo" required>
                                        <option value="tributado" <?= ($dados_form['tipo_calculo'] ?? '') == 'tributado' ? 'selected' : '' ?>>Tributado</option>
                                        <option value="tributado_com_credito" <?= ($dados_form['tipo_calculo'] ?? '') == 'tributado_com_credito' ? 'selected' : '' ?>>Tributado com Crédito ICMS</option>
                                        <option value="tributado_sem_credito" <?= ($dados_form['tipo_calculo'] ?? '') == 'tributado_sem_credito' ? 'selected' : '' ?>>Tributado sem Crédito ICMS</option>
                                        <option value="tributado_com_reducao" <?= ($dados_form['tipo_calculo'] ?? '') == 'tributado_com_reducao' ? 'selected' : '' ?>>Tributado com Redução na B.C.</option>
                                        <option value="tributado_com_difal" <?= ($dados_form['tipo_calculo'] ?? '') == 'tributado_com_difal' ? 'selected' : '' ?>>Tributado com DIFAL</option>
                                        <option value="fornecedor_normal" <?= ($dados_form['tipo_calculo'] ?? '') == 'fornecedor_normal' ? 'selected' : '' ?>>ST (Fornecedor do Regime Normal)</option>
                                        <option value="fornecedor_simples" <?= ($dados_form['tipo_calculo'] ?? '') == 'fornecedor_simples' ? 'selected' : '' ?>>ST (Fornecedor do Simples Nacional)</option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                        <i class="fas fa-calculator me-2"></i> Calcular Impostos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($resultados)): ?>
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Resultados do Cálculo</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>MVA Ajustada</strong></td>
                                        <td class="text-end"><?= number_format($resultados['mva_ajustada'], 2, ',', '.') ?>%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ICMS Destacado</strong></td>
                                        <td class="text-end">R$ <?= number_format($resultados['icms_destacado'], 2, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Base de Cálculo</strong></td>
                                        <td class="text-end">R$ <?= number_format($resultados['base_calculo'], 2, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>ICMS ST</strong></td>
                                        <td class="text-end">R$ <?= number_format($resultados['icms_st'], 2, ',', '.') ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>ICMS a Recolher</strong></td>
                                        <td class="text-end fw-bold">R$ <?= number_format($resultados['icms_recolher'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php if ($resultados['recolher_tributado'] > 0): ?>
                                    <tr>
                                        <td><strong>Recolher Tributado (Regime Normal)</strong></td>
                                        <td class="text-end">R$ <?= number_format($resultados['recolher_tributado'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#detalhesCalculo" aria-expanded="false">
                                <i class="fas fa-info-circle me-1"></i> Ver Detalhes do Cálculo
                            </button>
                            
                            <div class="collapse mt-2" id="detalhesCalculo">
                                <div class="card card-body bg-light">
                                    <h6 class="mb-3"><i class="fas fa-calculator me-2"></i>Fórmulas Utilizadas</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <strong>MVA Ajustada:</strong><br>
                                            <code>((1 - aliq_interestadual) / (1 - aliq_interna) * (1 + mva_original)) - 1</code>
                                        </li>
                                        <li class="mb-2">
                                            <strong>ICMS ST:</strong><br>
                                            <code>base_calculo * aliq_interna</code>
                                        </li>
                                        <li>
                                            <strong>ICMS a Recolher:</strong><br>
                                            <code><?= ($dados_form['tipo_calculo'] ?? 'tributado') == 'tributado' ? 
                                                  '(aliq_interna - aliq_interestadual) * base_calculo' : 
                                                  '(icms_st - icms_destacado) - vICMSST' ?></code>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Incluir Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação básica do formulário
        document.getElementById('formCalculo').addEventListener('submit', function(e) {
            const vProd = parseFloat(document.getElementById('vProd').value);
            if (isNaN(vProd) || vProd <= 0) {
                alert('O valor do produto deve ser maior que zero');
                e.preventDefault();
                return false;
            }
            return true;
        });
    </script>
</body>
</html>
<?php
// Incluir o footer se necessário
require PARTIALS_PATH . '/footer.php';
?>