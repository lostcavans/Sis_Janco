public function autenticar($email, $senha) {
    $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        return $usuario;
    }
    
    return false;
}