<?php
// Teste rápido para verificar o hash
$hash_no_banco = '$2y$12$CoGXcpZmWAA3e/XVhQl37.cnWku7HFcrJJ84xY2hKS.920EOGdAHC';
$senha_teste = '1234'; // Senha que você acha que é

echo "Hash no banco: " . $hash_no_banco . "\n";
echo "Testando senha: '" . $senha_teste . "'\n";

$resultado = password_verify($senha_teste, $hash_no_banco);
echo "Resultado: " . ($resultado ? 'VERDADEIRO' : 'FALSO') . "\n";

// Gerar novo hash para comparação
$novo_hash = password_hash($senha_teste, PASSWORD_DEFAULT);
echo "Novo hash da mesma senha: " . $novo_hash . "\n";

// Testar com o novo hash
$resultado2 = password_verify($senha_teste, $novo_hash);
echo "Resultado com novo hash: " . ($resultado2 ? 'VERDADEIRO' : 'FALSO') . "\n";
?>