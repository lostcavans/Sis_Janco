footer<?php
// classes/NFeProcessor.php

class NFeProcessor {
    private $pdo;
    private $xml;
    private $nfeData = [];
    private $itens = [];
    private $impostos = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function processXML($xmlString) {
        // Carrega o XML
        $this->xml = simplexml_load_string($xmlString);
        if ($this->xml === false) {
            throw new Exception("Erro ao processar XML da NFe");
        }

        // Registra os namespaces
        $this->xml->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');

        // Extrai dados básicos da NFe
        $this->extractNFeData();
        
        // Extrai itens e impostos
        $this->extractItens();

        // Extrai e registra empresas
        $this->processarEmpresas();

        return $this->nfeData;
    }

    private function extractEmpresas() {
    $infNFe = $this->xml->NFe->infNFe;
    
    // Processa emitente
    $emitente = [
        'cnpj' => (string)$infNFe->emit->CNPJ,
        'razao_social' => (string)$infNFe->emit->xNome,
        'nome_fantasia' => isset($infNFe->emit->xFant) ? (string)$infNFe->emit->xFant : null,
        'ie' => (string)$infNFe->emit->IE,
        'logradouro' => (string)$infNFe->emit->enderEmit->xLgr,
        'numero' => (string)$infNFe->emit->enderEmit->nro,
        'bairro' => (string)$infNFe->emit->enderEmit->xBairro,
        'municipio' => (string)$infNFe->emit->enderEmit->xMun,
        'uf' => (string)$infNFe->emit->enderEmit->UF,
        'cep' => (string)$infNFe->emit->enderEmit->CEP,
        'telefone' => isset($infNFe->emit->fone) ? (string)$infNFe->emit->fone : null
    ];

    // Processa destinatário
    $destinatario = [
        'cnpj' => (string)$infNFe->dest->CNPJ,
        'razao_social' => (string)$infNFe->dest->xNome,
        'ie' => (string)$infNFe->dest->IE,
        'logradouro' => (string)$infNFe->dest->enderDest->xLgr,
        'numero' => (string)$infNFe->dest->enderDest->nro,
        'bairro' => (string)$infNFe->dest->enderDest->xBairro,
        'municipio' => (string)$infNFe->dest->enderDest->xMun,
        'uf' => (string)$infNFe->dest->enderDest->UF,
        'cep' => (string)$infNFe->dest->enderDest->CEP,
        'telefone' => isset($infNFe->dest->fone) ? (string)$infNFe->dest->fone : null
    ];

    // Verifica e cadastra empresas
    $this->cadastrarEmpresa($emitente);
    $this->cadastrarEmpresa($destinatario);
}

private function cadastrarEmpresa($dados) {
    // Verifica se já existe
    $stmt = $this->pdo->prepare("SELECT id FROM empresas WHERE cnpj = ?");
    $stmt->execute([$dados['cnpj']]);
    $empresa = $stmt->fetch();

    if ($empresa) {
        $this->nfeData['empresas_existentes'][] = $dados['cnpj'];
        return $empresa['id'];
    }

    // Cadastra nova empresa
    $stmt = $this->pdo->prepare("INSERT INTO empresas 
        (cnpj, razao_social, nome_fantasia, ie, logradouro, numero, bairro, 
         municipio, uf, cep, telefone, situacao_cadastral, data_cadastro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Ativa', NOW())");
    
    $stmt->execute([
        $dados['cnpj'],
        $dados['razao_social'],
        $dados['nome_fantasia'],
        $dados['ie'],
        $dados['logradouro'],
        $dados['numero'],
        $dados['bairro'],
        $dados['municipio'],
        $dados['uf'],
        $dados['cep'],
        $dados['telefone']
    ]);

    $this->nfeData['empresas_novas'][] = $dados['cnpj'];
    return $this->pdo->lastInsertId();
}

    private function extractNFeData() {
        $infNFe = $this->xml->NFe->infNFe;
        
        $this->nfeData = [
            'chave_acesso' => (string)$infNFe['Id'],
            'numero' => (string)$infNFe->ide->nNF,
            'serie' => (string)$infNFe->ide->serie,
            'data_emissao' => (string)$infNFe->ide->dhEmi,
            'cnpj_emitente' => (string)$infNFe->emit->CNPJ,
            'nome_emitente' => (string)$infNFe->emit->xNome,
            'cnpj_destinatario' => (string)$infNFe->dest->CNPJ,
            'nome_destinatario' => (string)$infNFe->dest->xNome,
            'valor_total' => (float)$infNFe->total->ICMSTot->vNF,
            'xml' => $this->xml->asXML()
        ];
    }

    private function extractItens() {
        foreach ($this->xml->NFe->infNFe->det as $det) {
            $item = [
                'numero_item' => (int)$det['nItem'],
                'codigo_produto' => (string)$det->prod->cProd,
                'descricao' => (string)$det->prod->xProd,
                'ncm' => (string)$det->prod->NCM,
                'cfop' => (string)$det->prod->CFOP,
                'unidade' => (string)$det->prod->uCom,
                'quantidade' => (float)$det->prod->qCom,
                'valor_unitario' => (float)$det->prod->vUnCom,
                'valor_total' => (float)$det->prod->vProd
            ];

            // Extrai impostos do item
            $impostos = $this->extractImpostos($det);

            $this->itens[] = $item;
            $this->impostos = array_merge($this->impostos, $impostos);
        }
    }

    private function extractImpostos($det) {
        $impostos = [];
        $nItem = (int)$det['nItem'];

        // ICMS
        if (isset($det->imposto->ICMS->ICMS00)) {
            $icms = $det->imposto->ICMS->ICMS00;
            $impostos[] = [
                'nItem' => $nItem,
                'tipo_imposto' => 'ICMS',
                'cst' => (string)$icms->CST,
                'base_calculo' => (float)$icms->vBC,
                'aliquota' => (float)$icms->pICMS,
                'valor' => (float)$icms->vICMS
            ];
        }

        // IPI
        if (isset($det->imposto->IPI->IPITrib)) {
            $ipi = $det->imposto->IPI->IPITrib;
            $impostos[] = [
                'nItem' => $nItem,
                'tipo_imposto' => 'IPI',
                'cst' => (string)$ipi->CST,
                'base_calculo' => (float)$ipi->vBC,
                'aliquota' => (float)$ipi->pIPI,
                'valor' => (float)$ipi->vIPI
            ];
        }

        // PIS
        if (isset($det->imposto->PIS->PISAliq)) {
            $pis = $det->imposto->PIS->PISAliq;
            $impostos[] = [
                'nItem' => $nItem,
                'tipo_imposto' => 'PIS',
                'cst' => (string)$pis->CST,
                'base_calculo' => (float)$pis->vBC,
                'aliquota' => (float)$pis->pPIS,
                'valor' => (float)$pis->vPIS
            ];
        }

        // COFINS
        if (isset($det->imposto->COFINS->COFINSAliq)) {
            $cofins = $det->imposto->COFINS->COFINSAliq;
            $impostos[] = [
                'nItem' => $nItem,
                'tipo_imposto' => 'COFINS',
                'cst' => (string)$cofins->CST,
                'base_calculo' => (float)$cofins->vBC,
                'aliquota' => (float)$cofins->pCOFINS,
                'valor' => (float)$cofins->vCOFINS
            ];
        }

        return $impostos;
    }

    private function processarEmpresas() {
    $infNFe = $this->xml->NFe->infNFe;
    
    // Processa emitente
    $emitente = $this->extrairDadosEmpresa($infNFe->emit, 'emitente');
    $this->registrarEmpresa($emitente);
    
    // Processa destinatário
    $destinatario = $this->extrairDadosEmpresa($infNFe->dest, 'destinatario');
    $this->registrarEmpresa($destinatario);
}

private function extrairDadosEmpresa($dadosXml, $tipo) {
    return [
        'tipo' => $tipo,
        'cnpj' => (string)$dadosXml->CNPJ,
        'razao_social' => (string)$dadosXml->xNome,
        'nome_fantasia' => isset($dadosXml->xFant) ? (string)$dadosXml->xFant : null,
        'ie' => (string)$dadosXml->IE,
        'logradouro' => (string)$dadosXml->enderEmit->xLgr ?? (string)$dadosXml->enderDest->xLgr,
        'numero' => (string)$dadosXml->enderEmit->nro ?? (string)$dadosXml->enderDest->nro,
        'bairro' => (string)$dadosXml->enderEmit->xBairro ?? (string)$dadosXml->enderDest->xBairro,
        'municipio' => (string)$dadosXml->enderEmit->xMun ?? (string)$dadosXml->enderDest->xMun,
        'uf' => (string)$dadosXml->enderEmit->UF ?? (string)$dadosXml->enderDest->UF,
        'cep' => (string)$dadosXml->enderEmit->CEP ?? (string)$dadosXml->enderDest->CEP,
        'telefone' => isset($dadosXml->fone) ? (string)$dadosXml->fone : null,
        'email' => isset($dadosXml->email) ? (string)$dadosXml->email : null
    ];
}


private function registrarEmpresa($dados) {
    // Verifica se já existe
    $stmt = $this->pdo->prepare("SELECT id FROM empresas WHERE cnpj = ?");
    $stmt->execute([$dados['cnpj']]);
    
    if ($stmt->fetch()) {
        $this->nfeData['mensagens'][] = [
            'tipo' => 'info',
            'texto' => "Empresa {$dados['tipo']} ({$dados['cnpj']}) já cadastrada"
        ];
        return false;
    }
    
    // Cadastra nova empresa
    try {
        $stmt = $this->pdo->prepare("INSERT INTO empresas 
            (cnpj, razao_social, nome_fantasia, ie, logradouro, numero, bairro,
             municipio, uf, cep, telefone, email, situacao_cadastral, porte, data_cadastro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Ativa', 'DEMAIS', NOW())");
        
        $stmt->execute([
            $dados['cnpj'],
            $dados['razao_social'],
            $dados['nome_fantasia'],
            $dados['ie'],
            $dados['logradouro'],
            $dados['numero'],
            $dados['bairro'],
            $dados['municipio'],
            $dados['uf'],
            $dados['cep'],
            $dados['telefone'],
            $dados['email']
        ]);
        
        $this->nfeData['mensagens'][] = [
            'tipo' => 'success',
            'texto' => "Nova empresa {$dados['tipo']} cadastrada: {$dados['razao_social']}"
        ];
        return true;
        
    } catch (PDOException $e) {
        $this->nfeData['mensagens'][] = [
            'tipo' => 'danger',
            'texto' => "Erro ao cadastrar empresa {$dados['tipo']}: " . $e->getMessage()
        ];
        return false;
    }
}

    public function saveToDatabase() {
        try {
            $this->pdo->beginTransaction();

            // Salva NFe
            $stmt = $this->pdo->prepare("INSERT INTO nfe 
                (chave_acesso, numero, serie, data_emissao, cnpj_emitente, nome_emitente, 
                 cnpj_destinatario, nome_destinatario, valor_total, xml)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $this->nfeData['chave_acesso'],
                $this->nfeData['numero'],
                $this->nfeData['serie'],
                $this->nfeData['data_emissao'],
                $this->nfeData['cnpj_emitente'],
                $this->nfeData['nome_emitente'],
                $this->nfeData['cnpj_destinatario'],
                $this->nfeData['nome_destinatario'],
                $this->nfeData['valor_total'],
                $this->nfeData['xml']
            ]);

            $nfeId = $this->pdo->lastInsertId();

            // Salva itens
            foreach ($this->itens as $item) {
                $stmt = $this->pdo->prepare("INSERT INTO nfe_itens 
                    (nfe_id, numero_item, codigo_produto, descricao, ncm, cfop, unidade, 
                     quantidade, valor_unitario, valor_total)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $nfeId,
                    $item['numero_item'],
                    $item['codigo_produto'],
                    $item['descricao'],
                    $item['ncm'],
                    $item['cfop'],
                    $item['unidade'],
                    $item['quantidade'],
                    $item['valor_unitario'],
                    $item['valor_total']
                ]);

                $itemId = $this->pdo->lastInsertId();

                // Salva impostos do item
                foreach ($this->impostos as $imposto) {
                    if ($imposto['nItem'] == $item['numero_item']) {
                        $stmt = $this->pdo->prepare("INSERT INTO nfe_impostos 
                            (nfe_item_id, tipo_imposto, cst, base_calculo, aliquota, valor)
                            VALUES (?, ?, ?, ?, ?, ?)");
                        
                        $stmt->execute([
                            $itemId,
                            $imposto['tipo_imposto'],
                            $imposto['cst'],
                            $imposto['base_calculo'],
                            $imposto['aliquota'],
                            $imposto['valor']
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return $nfeId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function calcularST($nfeId, $aliquotaInterna, $mvaOriginal, $mvaCNAE = null) {
        try {
            $this->pdo->beginTransaction();

            // Obtém itens da NFe
            $stmt = $this->pdo->prepare("
                SELECT ni.id, ni.numero_item, ni.valor_total, 
                       (SELECT valor FROM nfe_impostos WHERE nfe_item_id = ni.id AND tipo_imposto = 'IPI' LIMIT 1) as valor_ipi,
                       (SELECT valor FROM nfe_impostos WHERE nfe_item_id = ni.id AND tipo_imposto = 'ICMS' LIMIT 1) as valor_icms,
                       (SELECT aliquota FROM nfe_impostos WHERE nfe_item_id = ni.id AND tipo_imposto = 'ICMS' LIMIT 1) as aliquota_interestadual
                FROM nfe_itens ni
                WHERE ni.nfe_id = ?
            ");
            $stmt->execute([$nfeId]);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($itens as $item) {
                $vProd = $item['valor_total'];
                $vIPI = $item['valor_ipi'] ?? 0;
                $vICMS = $item['valor_icms'] ?? 0;
                $aliqInterestadual = $item['aliquota_interestadual'] ?? 0;

                // Calcula MVA ajustada
                $mvaAjustada = ((1 - ($aliqInterestadual/100)) / (1 - ($aliquotaInterna/100)) * (1 + ($mvaOriginal/100))) - 1;
                $mvaAjustadaPercent = $mvaAjustada * 100;

                // Base de cálculo ST
                $baseCalculoST = ($vProd + $vIPI) * (1 + $mvaAjustada);

                // Valor ICMS ST
                $valorICMSST = $baseCalculoST * ($aliquotaInterna/100);

                // Valor ICMS a recolher (ST - ICMS destacado)
                $valorICMSRecolher = $valorICMSST - $vICMS;

                // Insere cálculo na tabela
                $stmt = $this->pdo->prepare("INSERT INTO nfe_calculo_st 
                    (nfe_item_id, mva_original, mva_ajustada, aliquota_interna, aliquota_interestadual, 
                     base_calculo_st, valor_icms_st, valor_icms_destacado, valor_icms_recolher)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $item['id'],
                    $mvaOriginal,
                    $mvaAjustadaPercent,
                    $aliquotaInterna,
                    $aliqInterestadual,
                    $baseCalculoST,
                    $valorICMSST,
                    $vICMS,
                    $valorICMSRecolher
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}