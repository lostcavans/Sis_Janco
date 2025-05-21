// Máscaras para campos
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CNPJ
    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.substring(0, 14);
            e.target.value = value;
        });
    }

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length > 0) {
                value = value.replace(/^(\d{0,2})(\d{0,5})(\d{0,4})/, function(_, ddd, parte1, parte2) {
                    let result = '';
                    if (ddd) result += `(${ddd}`;
                    if (parte1) result += `) ${parte1}`;
                    if (parte2) result += `-${parte2}`;
                    return result;
                });
            }
            
            e.target.value = value;
        });
    }

    // Consulta CEP via API ViaCEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('logradouro').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('municipio').value = data.localidade || '';
                            document.getElementById('uf').value = data.uf || '';
                            document.getElementById('numero').focus();
                        }
                    })
                    .catch(error => console.error('Erro ao consultar CEP:', error));
            }
        });
    }
});

// Validação de formulário
function validarFormularioEmpresa() {
    let isValid = true;
    const form = document.getElementById('form-empresa');
    
    // Validação de CNPJ
    const cnpj = form.querySelector('#cnpj').value;
    if (cnpj.length !== 14 || !validarCNPJ(cnpj)) {
        form.querySelector('#cnpj').classList.add('is-invalid');
        isValid = false;
    } else {
        form.querySelector('#cnpj').classList.remove('is-invalid');
    }
    
    // Outras validações...
    
    return isValid;
}

// Função para validar CNPJ no frontend
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g,'');
    
    if(cnpj === '') return false;
    if (cnpj.length !== 14) return false;
    
    // Elimina CNPJs invalidos conhecidos
    if (cnpj === "00000000000000" || 
        cnpj === "11111111111111" || 
        cnpj === "22222222222222" || 
        cnpj === "33333333333333" || 
        cnpj === "44444444444444" || 
        cnpj === "55555555555555" || 
        cnpj === "66666666666666" || 
        cnpj === "77777777777777" || 
        cnpj === "88888888888888" || 
        cnpj === "99999999999999")
        return false;
        
    // Valida DVs
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0,tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(0))) return false;
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(1))) return false;
    
    return true;
}