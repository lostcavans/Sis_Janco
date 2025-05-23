<?php
require 'config.php';

$senha_digitada = 'admin'; // Coloque a senha que você acha que é
$hash_do_banco = '$2y$10$7IXHV0BoXUI3ykIJOAbHxefUVa8FrSBEpNpGPtF6W8tXEFASDhR0a'; // Copie o hash completo

if (password_verify($senha_digitada, $hash_do_banco)) {
    echo "Senha CORRETA!";
} else {
    echo "Senha INCORRETA!";
}