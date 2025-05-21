<?php
// Ativar exibição de erros (apenas para desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações do banco de dados
define('DB_HOST', 'mysql.jancoassessoriacontabil.com.br');
define('DB_USER', 'jancoassessori');
define('DB_PASS', 'g283116');
define('DB_NAME', 'jancoassessori');

// Definir constantes de caminho
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');
define('PARTIALS_PATH', VIEWS_PATH . '/partials');


// Configurações do sistema
define('SITE_URL', 'http://localhost/sistema-empresas');
define('SITE_NAME', 'Sistema de Cadastro de Empresas');

// Definir BASE_URL dinamicamente
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    define('BASE_URL', $protocol . "://" . $_SERVER['HTTP_HOST'] . '/Cadastro_de_empresas');
}

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERRO: Não foi possível conectar. " . $e->getMessage());
}

// Funções úteis
function formatarCNPJ($cnpj) {
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
}

function formatarCPF($cpf) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
}

function estadosBrasil() {
    return [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins'
    ];
}

function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14) {
        return false;
    }
    
    // Validação dos dígitos verificadores
    // ... (implementação completa da validação)
    
    return true;
}

function carregarView($caminho, $dados = []) {
    extract($dados);
    ob_start();
    include "views/{$caminho}.php";
    return ob_get_clean();
}



/**
 * Formata telefone
 */
function formatarTelefone($telefone) {
    if (empty($telefone)) return '-';
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) === 11) {
        return preg_replace("/(\d{2})(\d{5})(\d{4})/", "(\$1) \$2-\$3", $telefone);
    }
    return $telefone;
}

/**
 * Formata CEP
 */
function formatarCEP($cep) {
    if (empty($cep)) return '-';
    return preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $cep);
}

/**
 * Formata data
 */
function formatarData($data) {
    if (empty($data)) return '-';
    return date('d/m/Y', strtotime($data));
}

function includeHeader() {
    include BASE_PATH . '/header.php';
}

function includeView($viewPath) {
    include VIEWS_PATH . '/' . $viewPath;
}

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}