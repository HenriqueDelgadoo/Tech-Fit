<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json');

// Se for uma requisição OPTIONS (preflight), encerre aqui
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "erro", "mensagem" => "Método não permitido"]);
    exit();
}

// Conexão com o banco
$conn = new mysqli("localhost", "root", "senaisp", "tech_fit");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "erro", "mensagem" => "Erro interno do servidor"]);
    exit();
}

// Ler e validar dados
$json = file_get_contents("php://input");
$dados = json_decode($json, true);

if (!$dados || !isset($dados['usuario']) || !isset($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["status" => "erro", "mensagem" => "Dados incompletos"]);
    exit();
}

$usuario = trim($dados['usuario']);
$senha = $dados['senha'];

// Validações básicas
if (empty($usuario) || empty($senha)) {
    echo json_encode(["status" => "erro", "mensagem" => "Preencha todos os campos"]);
    $conn->close();
    exit();
}

// Buscar usuário no banco
try {
    // CORREÇÃO AQUI: Use o nome correto da coluna
    $stmt = $conn->prepare("SELECT SENHA_FUNCIONARIO FROM FUNCIONARIOS WHERE LOGIN_REDE = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Mensagem genérica por segurança
        echo json_encode(["status" => "erro", "mensagem" => "Credenciais inválidas"]);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $row = $result->fetch_assoc();
    
    // CORREÇÃO AQUI: Use o nome correto da coluna
    if (password_verify($senha, $row['SENHA_FUNCIONARIO'])) {
        // Sucesso - considere retornar mais dados se necessário
        echo json_encode([
            "status" => "sucesso", 
            "usuario" => $usuario,
            "mensagem" => "Login realizado com sucesso"
        ]);
    } else {
        echo json_encode(["status" => "erro", "mensagem" => "Credenciais inválidas"]);
    }
    
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "erro", "mensagem" => "Erro interno do servidor"]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}

?>