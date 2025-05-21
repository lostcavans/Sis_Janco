<?php
/**
 * Formulário de cadastro de nova empresa
 * 
 * @var string $erro Mensagem de erro (opcional)
 * @var array $dados_form Dados do formulário submetido (para repopular)
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-building-add"></i> Cadastrar Nova Empresa
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form id="form-empresa" method="POST" novalidate>
                        <input type="hidden" name="acao" value="cadastrar">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Dados Cadastrais</h5>
                                
                                <div class="mb-3">
                                    <label for="cnpj" class="form-label required">CNPJ</label>
                                    <input type="text" class="form-control cnpj-mask" id="cnpj" name="cnpj" 
                                           required maxlength="18"
                                           value="<?= htmlspecialchars($dados_form['cnpj'] ?? '') ?>">
                                    <div class="invalid-feedback">Por favor, informe um CNPJ válido.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="razao_social" class="form-label required">Razão Social</label>
                                    <input type="text" class="form-control" id="razao_social" name="razao_social" 
                                           required
                                           value="<?= htmlspecialchars($dados_form['razao_social'] ?? '') ?>">
                                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                                    <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia"
                                           value="<?= htmlspecialchars($dados_form['nome_fantasia'] ?? '') ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_abertura" class="form-label">Data de Abertura</label>
                                        <input type="date" class="form-control" id="data_abertura" name="data_abertura"
                                               value="<?= htmlspecialchars($dados_form['data_abertura'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="porte" class="form-label required">Porte</label>
                                        <select class="form-select" id="porte" name="porte" required>
                                            <option value="">Selecione...</option>
                                            <option value="MEI" <?= isset($dados_form['porte']) && $dados_form['porte'] === 'MEI' ? 'selected' : '' ?>>MEI</option>
                                            <option value="ME" <?= isset($dados_form['porte']) && $dados_form['porte'] === 'ME' ? 'selected' : '' ?>>ME</option>
                                            <option value="EPP" <?= isset($dados_form['porte']) && $dados_form['porte'] === 'EPP' ? 'selected' : '' ?>>EPP</option>
                                            <option value="DEMAIS" <?= isset($dados_form['porte']) && ($dados_form['porte'] === 'DEMAIS' || !isset($dados_form['porte'])) ? 'selected' : '' ?>>Demais</option>
                                        </select>
                                        <div class="invalid-feedback">Selecione o porte da empresa.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Informações Complementares</h5>
                                
                                <div class="mb-3">
                                    <label for="cnae_principal" class="form-label">CNAE Principal</label>
                                    <input type="text" class="form-control cnae-mask" id="cnae_principal" name="cnae_principal"
                                           value="<?= htmlspecialchars($dados_form['cnae_principal'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="natureza_juridica" class="form-label">Natureza Jurídica</label>
                                    <input type="text" class="form-control" id="natureza_juridica" name="natureza_juridica"
                                           value="<?= htmlspecialchars($dados_form['natureza_juridica'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="situacao_cadastral" class="form-label required">Situação Cadastral</label>
                                    <select class="form-select" id="situacao_cadastral" name="situacao_cadastral" required>
                                        <option value="Ativa" <?= isset($dados_form['situacao_cadastral']) && $dados_form['situacao_cadastral'] === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
                                        <option value="Baixada" <?= isset($dados_form['situacao_cadastral']) && $dados_form['situacao_cadastral'] === 'Baixada' ? 'selected' : '' ?>>Baixada</option>
                                        <option value="Suspensa" <?= isset($dados_form['situacao_cadastral']) && $dados_form['situacao_cadastral'] === 'Suspensa' ? 'selected' : '' ?>>Suspensa</option>
                                        <option value="Inapta" <?= isset($dados_form['situacao_cadastral']) && $dados_form['situacao_cadastral'] === 'Inapta' ? 'selected' : '' ?>>Inapta</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="capital_social" class="form-label">Capital Social (R$)</label>
                                    <input type="text" class="form-control money-mask" id="capital_social" name="capital_social"
                                           value="<?= htmlspecialchars($dados_form['capital_social'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Contato</h5>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= htmlspecialchars($dados_form['email'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control phone-mask" id="telefone" name="telefone"
                                           value="<?= htmlspecialchars($dados_form['telefone'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Endereço</h5>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control cep-mask" id="cep" name="cep"
                                               value="<?= htmlspecialchars($dados_form['cep'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="logradouro" class="form-label">Logradouro</label>
                                        <input type="text" class="form-control" id="logradouro" name="logradouro"
                                               value="<?= htmlspecialchars($dados_form['logradouro'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero" name="numero"
                                               value="<?= htmlspecialchars($dados_form['numero'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento"
                                               value="<?= htmlspecialchars($dados_form['complemento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label for="bairro" class="form-label">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro"
                                               value="<?= htmlspecialchars($dados_form['bairro'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="municipio" class="form-label">Município</label>
                                        <input type="text" class="form-control" id="municipio" name="municipio"
                                               value="<?= htmlspecialchars($dados_form['municipio'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="uf" class="form-label">UF</label>
                                        <select class="form-select" id="uf" name="uf">
                                            <option value="">--</option>
                                            <?php foreach (estadosBrasil() as $sigla => $nome): ?>
                                                <option value="<?= $sigla ?>" <?= isset($dados_form['uf']) && $dados_form['uf'] === $sigla ? 'selected' : '' ?>>
                                                    <?= $sigla ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?acao=listar" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cadastrar Empresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Função para validar o formulário
document.getElementById('form-empresa').addEventListener('submit', function(event) {
    const form = event.target;
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
});

// Máscaras (requer o plugin jQuery Mask ou similar)
$(document).ready(function(){
    $('.cnpj-mask').mask('00.000.000/0000-00');
    $('.cnae-mask').mask('0000-0/00');
    $('.phone-mask').mask('(00) 00000-0000');
    $('.cep-mask').mask('00000-000');
    $('.money-mask').mask('000.000.000.000.000,00', {reverse: true});
});

// Consulta CEP
$('#cep').blur(function() {
    const cep = $(this).val().replace(/\D/g, '');
    if (cep.length === 8) {
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
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
</script>