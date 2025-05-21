<?php
header('Content-Type: application/json');
require_once '../config.php';

$cnpj = preg_replace('/[^0-9]/', '', $_GET['cnpj'] ?? '');

if (strlen($cnpj) !== 14 || !validarCNPJ($cnpj)) {
    echo json_encode(['erro' => 'CNPJ inválido']);
    exit;
}

// Simulação de consulta à API da Receita
// Na prática, você usaria uma biblioteca como Guzzle para fazer a requisição
$dadosFicticios = [
    'cnpj' => $cnpj,
    'razao_social' => 'EMPRESA EXEMPLO LTDA',
    'nome_fantasia' => 'EXEMPLO COMERCIO',
    'data_abertura' => '2010-05-15',
    'cnae_principal' => '4711302',
    'descricao_cnae' => 'Comércio varejista de mercadorias em geral',
    'natureza_juridica' => '206-2 - Sociedade Empresária Limitada',
    'porte' => 'ME',
    'situacao_cadastral' => 'Ativa',
    'data_situacao_cadastral' => '2010-05-15',
    'email' => 'contato@empresaexemplo.com.br',
    'telefone' => '1133334444',
    'cep' => '01001000',
    'logradouro' => 'Rua Exemplo',
    'numero' => '123',
    'complemento' => 'Sala 45',
    'bairro' => 'Centro',
    'municipio' => 'São Paulo',
    'uf' => 'SP'
];

echo json_encode($dadosFicticios);