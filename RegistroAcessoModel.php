<?php
class RegistroAcessoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarAcesso($usuario_id, $tipo_acao) {
        // Verifica se já existe um registro idêntico recente (últimos 5 segundos)
        $stmt = $this->pdo->prepare("SELECT id FROM registros_acesso 
                                    WHERE usuario_id = ? AND tipo_acao = ? 
                                    AND data_hora > DATE_SUB(NOW(), INTERVAL 5 SECOND)
                                    LIMIT 1");
        $stmt->execute([$usuario_id, $tipo_acao]);
        
        if ($stmt->fetch()) {
            return false; // Já existe um registro recente
        }

        // Procede com o novo registro
        $sql = "INSERT INTO registros_acesso 
                (usuario_id, tipo_acao, data_hora, ip_address, user_agent) 
                VALUES (?, ?, NOW(), ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $usuario_id,
            $tipo_acao,
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido'
        ]);
        
        return $this->pdo->lastInsertId();
    }

    private function getClientIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}