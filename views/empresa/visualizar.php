<?php
if (!isset($empresa) || !$empresa) {
    echo '<div class="alert alert-danger">Empresa não encontrada</div>';
    return;
}
/**
 * Vista de visualização de empresa
 * 
 * Variáveis disponíveis:
 * @var array $empresa Dados da empresa
 * @var string $titulo Título da página
 */

// Verifica se a empresa existe
if (!isset($empresa) || !$empresa) {
    echo '<div class="alert alert-danger">Empresa não encontrada</div>';
    return;
}

// Função adicional necessária que não está no config.php
if (!function_exists('formatarDataHora')) {
    function formatarDataHora($dataHora) {
        if (empty($dataHora)) return '-';
        return date('d/m/Y H:i:s', strtotime($dataHora));
    }
}
?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?= htmlspecialchars($titulo) ?></h3>
                <div>
                    <a href="index.php?acao=editar&id=<?= $empresa['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="index.php?acao=listar" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Informações Básicas</h5>
                    <p><strong>CNPJ:</strong> <?= formatarCNPJ($empresa['cnpj']) ?></p>
                    <p><strong>Razão Social:</strong> <?= htmlspecialchars($empresa['razao_social']) ?></p>
                    <p><strong>Nome Fantasia:</strong> <?= htmlspecialchars($empresa['nome_fantasia']) ?></p>
                    <p><strong>Data Abertura:</strong> <?= formatarData($empresa['data_abertura']) ?></p>
                    <p><strong>Porte:</strong> <?= htmlspecialchars($empresa['porte']) ?></p>
                    <p><strong>Situação Cadastral:</strong> 
                        <span class="badge bg-<?= $empresa['situacao_cadastral'] == 'Ativa' ? 'success' : 'danger' ?>">
                            <?= htmlspecialchars($empresa['situacao_cadastral']) ?>
                        </span>
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Contato</h5>
                    <p><strong>Email:</strong> <?= htmlspecialchars($empresa['email']) ?></p>
                    <p><strong>Telefone:</strong> <?= formatarTelefone($empresa['telefone']) ?></p>
                    
                    <h5 class="border-bottom pb-2 mt-4">Endereço</h5>
                    <p><?= htmlspecialchars($empresa['logradouro']) ?>, <?= htmlspecialchars($empresa['numero']) ?></p>
                    <p><?= htmlspecialchars($empresa['complemento']) ?></p>
                    <p><?= htmlspecialchars($empresa['bairro']) ?></p>
                    <p><?= htmlspecialchars($empresa['municipio']) ?>/<?= htmlspecialchars($empresa['uf']) ?></p>
                    <p>CEP: <?= formatarCEP($empresa['cep']) ?></p>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2">Informações Complementares</h5>
                    <p><strong>CNAE Principal:</strong> <?= htmlspecialchars($empresa['cnae_principal']) ?></p>
                    <p><strong>Natureza Jurídica:</strong> <?= htmlspecialchars($empresa['natureza_juridica']) ?></p>
                    <p><strong>Capital Social:</strong> R$ <?= number_format($empresa['capital_social'], 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <small>
                Cadastrado em: <?= !empty($empresa['data_cadastro']) ? formatarDataHora($empresa['data_cadastro']) : '-' ?> | 
                Última atualização: <?= !empty($empresa['data_atualizacao']) ? formatarDataHora($empresa['data_atualizacao']) : '-' ?>
            </small>
        </div>
    </div>
</div>