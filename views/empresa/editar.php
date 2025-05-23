<?php
if (!isset($empresa)) {
    echo '<div class="alert alert-danger">Empresa não encontrada</div>';
    return;
}
/**
 * Formulário de edição de empresa
 * 
 * Variáveis disponíveis:
 * @var array $empresa Dados da empresa
 * @var string $erro Mensagem de erro (opcional)
 * @var array $dados_form Dados do formulário (opcional)
 */
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Empresa: <?= htmlspecialchars($empresa['razao_social'] ?? '') ?></h3>
                <a href="index.php?acao=visualizar&id=<?= $empresa['id'] ?? '' ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" value="<?= $empresa['id'] ?? '' ?>">
                
                <div class="row g-3">
                    <!-- Dados Básicos -->
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Dados Básicos</h5>
                        
                        <div class="mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" class="form-control cnpj-mask" id="cnpj" name="cnpj" 
                                   value="<?= htmlspecialchars($empresa['cnpj'] ?? '') ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="razao_social" class="form-label">Razão Social *</label>
                            <input type="text" class="form-control" id="razao_social" name="razao_social" 
                                   value="<?= htmlspecialchars($empresa['razao_social'] ?? ($dados_form['razao_social'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                            <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia" 
                                   value="<?= htmlspecialchars($empresa['nome_fantasia'] ?? ($dados_form['nome_fantasia'] ?? '')) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="data_abertura" class="form-label">Data de Abertura</label>
                            <input type="date" class="form-control" id="data_abertura" name="data_abertura" 
                                   value="<?= htmlspecialchars($empresa['data_abertura'] ?? ($dados_form['data_abertura'] ?? '')) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="porte" class="form-label">Porte</label>
                            <select class="form-select" id="porte" name="porte">
                                <option value="MEI" <?= ($empresa['porte'] ?? '') == 'MEI' ? 'selected' : '' ?>>MEI</option>
                                <option value="EPP" <?= ($empresa['porte'] ?? '') == 'EPP' ? 'selected' : '' ?>>EPP</option>
                                <option value="DEMAIS" <?= ($empresa['porte'] ?? '') == 'DEMAIS' ? 'selected' : '' ?>>Demais</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Contato e Endereço -->
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Contato e Endereço</h5>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($empresa['email'] ?? ($dados_form['email'] ?? '')) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control telefone-mask" id="telefone" name="telefone" 
                                   value="<?= htmlspecialchars($empresa['telefone'] ?? ($dados_form['telefone'] ?? '')) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control cep-mask" id="cep" name="cep" 
                                   value="<?= htmlspecialchars($empresa['cep'] ?? ($dados_form['cep'] ?? '')) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="logradouro" class="form-label">Logradouro</label>
                            <input type="text" class="form-control" id="logradouro" name="logradouro" 
                                   value="<?= htmlspecialchars($empresa['logradouro'] ?? ($dados_form['logradouro'] ?? '')) ?>">
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="numero" name="numero" 
                                           value="<?= htmlspecialchars($empresa['numero'] ?? ($dados_form['numero'] ?? '')) ?>">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento" 
                                           value="<?= htmlspecialchars($empresa['complemento'] ?? ($dados_form['complemento'] ?? '')) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" 
                                   value="<?= htmlspecialchars($empresa['bairro'] ?? ($dados_form['bairro'] ?? '')) ?>">
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="municipio" class="form-label">Município</label>
                                    <input type="text" class="form-control" id="municipio" name="municipio" 
                                           value="<?= htmlspecialchars($empresa['municipio'] ?? ($dados_form['municipio'] ?? '')) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="uf" class="form-label">UF</label>
                                    <select class="form-select" id="uf" name="uf">
                                        <?php foreach (estadosBrasil() as $sigla => $nome): ?>
                                            <option value="<?= $sigla ?>" <?= ($empresa['uf'] ?? '') == $sigla ? 'selected' : '' ?>>
                                                <?= $sigla ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações Complementares -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Informações Complementares</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cnae_principal" class="form-label">CNAE Principal</label>
                                    <input type="text" class="form-control" id="cnae_principal" name="cnae_principal" 
                                           value="<?= htmlspecialchars($empresa['cnae_principal'] ?? ($dados_form['cnae_principal'] ?? '')) ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="natureza_juridica" class="form-label">Natureza Jurídica</label>
                                    <input type="text" class="form-control" id="natureza_juridica" name="natureza_juridica" 
                                           value="<?= htmlspecialchars($empresa['natureza_juridica'] ?? ($dados_form['natureza_juridica'] ?? '')) ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="capital_social" class="form-label">Capital Social (R$)</label>
                                    <input type="text" class="form-control money-mask" id="capital_social" name="capital_social" 
                                           value="<?= htmlspecialchars($empresa['capital_social'] ?? ($dados_form['capital_social'] ?? '')) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between pt-3">
                            <a href="index.php?acao=visualizar&id=<?= $empresa['id'] ?? '' ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function(){
    // Máscaras
    $('.cnpj-mask').mask('00.000.000/0000-00');
    $('.telefone-mask').mask('(00) 00000-0000');
    $('.cep-mask').mask('00000-000');
    $('.money-mask').mask('000.000.000.000.000,00', {reverse: true});
    
    // Busca automática de CEP
    $('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/'+cep+'/json/', function(data) {
                if (!data.erro) {
                    $('#logradouro').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#municipio').val(data.localidade);
                    $('#uf').val(data.uf);
                    $('#numero').focus();
                }
            });
        }
    });
});
</script>