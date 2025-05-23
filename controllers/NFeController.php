<?php
// controllers/NFeController.php

require_once __DIR__ . '/../classes/NFeProcessor.php';

class NFeController {
    private $pdo;
    private $processor;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->processor = new NFeProcessor($pdo);
    }

    public function uploadXML($file) {
        // Verifica se é um arquivo XML
        if ($file['type'] != 'text/xml' && $file['type'] != 'application/xml') {
            throw new Exception("Por favor, envie um arquivo XML válido.");
        }

        // Lê o conteúdo do arquivo
        $xmlString = file_get_contents($file['tmp_name']);
        if ($xmlString === false) {
            throw new Exception("Erro ao ler o arquivo XML.");
        }

        // Processa o XML
        $nfeData = $this->processor->processXML($xmlString);

        // Salva no banco de dados
        $nfeId = $this->processor->saveToDatabase();

        return $nfeId;
    }

    public function getEmpresaByCnpj($cnpj) {
    $stmt = $this->pdo->prepare("
        SELECT * FROM empresas WHERE cnpj = ?
    ");
    $stmt->execute([$cnpj]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// controllers/NFeController.php

public function getDadosEmpresa($cnpj) {
    $stmt = $this->pdo->prepare("
        SELECT id, razao_social, nome_fantasia, ie, logradouro, numero, bairro,
               municipio, uf, cep, telefone, email
        FROM empresas 
        WHERE cnpj = ?
    ");
    $stmt->execute([$cnpj]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function calcularST($nfeId, $aliquotaInterna, $mvaOriginal, $mvaCNAE = null) {
        return $this->processor->calcularST($nfeId, $aliquotaInterna, $mvaOriginal, $mvaCNAE);
    }

    public function getNFe($nfeId) {
        $stmt = $this->pdo->prepare("
            SELECT n.*, 
                   (SELECT COUNT(*) FROM nfe_itens WHERE nfe_id = n.id) as total_itens,
                   (SELECT SUM(valor_total) FROM nfe_itens WHERE nfe_id = n.id) as total_produtos
            FROM nfe n
            WHERE n.id = ?
        ");
        $stmt->execute([$nfeId]);
        $nfe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nfe) {
            throw new Exception("NFe não encontrada.");
        }

        return $nfe;
    }

    public function getItensNFe($nfeId) {
        $stmt = $this->pdo->prepare("
            SELECT ni.*, 
                   (SELECT valor_icms_st FROM nfe_calculo_st WHERE nfe_item_id = ni.id LIMIT 1) as valor_st
            FROM nfe_itens ni
            WHERE ni.nfe_id = ?
        ");
        $stmt->execute([$nfeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}