// Em um script temporário create-user.php
<?php
require 'config.php';

$email = 'japinhanaruto@hotmail.com';
$senha = 'admin';
$senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Método mais seguro

$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)");
$stmt->execute(['Naruto', $email, $senhaHash, 'operador']);

echo "Usuário criado com sucesso!";