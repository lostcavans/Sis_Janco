<?php
require_once 'config.php';
require_once 'auth.php';

// Redirecionar se não estiver logado
if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}
class CalculadoraImpostos {
    private $pdo;
    
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Calcula os impostos conforme as regras do Simples Nacional
     * 
     * @param array $dados Array contendo os valores para cálculo
     * @return array Resultados dos cálculos
     */
    public function calcularImpostos($dados) {
        // Validação básica dos dados
        if (!is_array($dados) || empty($dados)) {
            throw new Exception("Dados inválidos para cálculo de impostos");
        }
        
        // Extrair valores do array
        $vProd = (float)($dados['vProd'] ?? 0);
        $aliqInterna = (float)($dados['aliq_interna'] ?? 0) / 100;
        $vIPI = (float)($dados['vIPI'] ?? 0);
        $aliqInterestadual = (float)($dados['pICMS'] ?? 0) / 100;
        $vFrete = (float)($dados['vFrete'] ?? 0);
        $mvaOriginal = (float)($dados['mva_original'] ?? 0) / 100;
        $mvaCNAE = (float)($dados['mva_cnae'] ?? 0) / 100;
        $aliqReducao = (float)($dados['aliq_reducao'] ?? 0) / 100;
        $vICMSST = (float)($dados['vICMSST'] ?? 0);
        $creditoICMS = (float)($dados['credito_icms'] ?? 0) / 100;
        $difal = (float)($dados['difal'] ?? 0) / 100;
        $regime = $dados['regime'] ?? 'normal';
        $tipoCalculo = $dados['tipo_calculo'] ?? 'tributado';
        
        // Cálculos intermediários
        $mvaAjustada = ((1 - $aliqInterestadual) / (1 - $aliqInterna) * (1 + $mvaOriginal)) - 1;
        
        // Cálculo do ICMS Destacado conforme o tipo
        switch($tipoCalculo) {
            case 'tributado_com_credito':
                $icmsDestacado = $vProd * $creditoICMS;
                break;
            case 'tributado_sem_credito':
                $icmsDestacado = $vProd * $aliqInterestadual;
                break;
            case 'tributado_com_reducao':
                $icmsDestacado = ($vProd * ((100 - ($aliqReducao * 100)) / 100)) * $aliqInterestadual;
                break;
            default:
                $icmsDestacado = 0;
        }
        
        // Cálculo da Base de Cálculo
        if ($regime == 'normal') {
            if ($tipoCalculo == 'tributado') {
                $baseCalculo = ($vProd + $vIPI - $icmsDestacado) / (1 - $aliqInterna);
            } else {
                $baseCalculo = ($vProd + $vIPI) / (1 - $aliqInterna);
            }
        } else {
            // Simples Nacional
            if ($tipoCalculo == 'fornecedor_normal') {
                $baseCalculo = ($vProd + $vIPI + $vFrete) * (1 + $mvaAjustada);
            } else {
                $baseCalculo = ($vProd + $vIPI + $vFrete) * (1 + $mvaOriginal);
            }
        }
        
        // Cálculo do ICMS ST
        $icmsST = $baseCalculo * $aliqInterna;
        
        // Cálculo do ICMS a Recolher
        if ($tipoCalculo == 'tributado') {
            $icmsRecolher = ($aliqInterna - $aliqInterestadual) * $baseCalculo;
        } elseif ($tipoCalculo == 'tributado_com_difal') {
            $icmsRecolher = $difal * $baseCalculo;
        } else {
            $icmsRecolher = ($icmsST - $icmsDestacado) - $vICMSST;
        }
        
        // Cálculo para regime normal (adicional)
        if ($regime == 'normal') {
            $recolherTributado = (($vProd + $vIPI + $vFrete - $icmsDestacado) / 
                                 (1 - $aliqInterna) * $mvaCNAE * $aliqInterna) - $icmsDestacado;
        } else {
            $recolherTributado = 0;
        }
        
        // Retornar todos os resultados
        return [
            'mva_ajustada' => $mvaAjustada * 100,
            'icms_destacado' => $icmsDestacado,
            'base_calculo' => $baseCalculo,
            'icms_st' => $icmsST,
            'icms_recolher' => $icmsRecolher,
            'recolher_tributado' => $recolherTributado,
            'detalhes' => [
                'valores_entrada' => $dados,
                'formulas_utilizadas' => [
                    'mva_ajustada' => '((1 - aliq_interestadual) / (1 - aliq_interna) * (1 + mva_original)) - 1',
                    'icms_st' => 'base_calculo * aliq_interna',
                    'icms_recolher' => $tipoCalculo == 'tributado' ? 
                                      '(aliq_interna - aliq_interestadual) * base_calculo' : 
                                      '(icms_st - icms_destacado) - vICMSST'
                ]
            ]
        ];
    }
}

// Processar formulário
$mensagem_erro = '';
$resultados = [];
$dados_form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $calculadora = new CalculadoraImpostos($pdo);
        $resultados = $calculadora->calcularImpostos($_POST);
        $dados_form = $_POST;
    } catch (Exception $e) {
        $mensagem_erro = "Erro no cálculo: " . $e->getMessage();
    }
}

require VIEWS_PATH . '/empresa/calculo_impostos_view.php';

// Recuperar resultados da sessão se existirem
$resultados = $_SESSION['resultados_impostos'] ?? null;
$dados_form = $_SESSION['dados_formulario'] ?? [];
unset($_SESSION['resultados_impostos']);
unset($_SESSION['dados_formulario']);

?>
<!-- Footer -->
    <?php include 'views/partials/footer.php'; ?>