<?php
// API/modalidades.php

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json');

// Se for uma requisição OPTIONS (preflight), encerre aqui
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir as classes necessárias
require_once __DIR__ . '/../Model/Modalidade.php';
require_once __DIR__ . '/../Model/ModalidadeDAO.php';

// Conexão com o banco
$conn = new mysqli("localhost", "root", "senaisp", "tech_fit");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Erro de conexão com o banco de dados",
        "error" => $conn->connect_error
    ]);
    exit();
}

// Criar instância do DAO
$modalidadeDAO = new ModalidadeDAO($conn);

// Obter o método da requisição e parâmetros
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$action = isset($segments[2]) ? $segments[2] : '';

// Obter dados do corpo da requisição
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = [];
}

// Obter parâmetros da query string
$queryParams = $_GET;

// Função para enviar resposta padrão
function sendResponse($success, $message = '', $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = ['success' => $success];
    
    if ($message) {
        $response['message'] = $message;
    }
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($modalidadeDAO, $action, $queryParams);
            break;
            
        case 'POST':
            handlePostRequest($modalidadeDAO, $action, $input);
            break;
            
        case 'PUT':
            handlePutRequest($modalidadeDAO, $action, $input, $queryParams);
            break;
            
        case 'DELETE':
            handleDeleteRequest($modalidadeDAO, $action, $queryParams);
            break;
            
        default:
            sendResponse(false, 'Método não permitido', null, 405);
    }
    
} catch (Exception $e) {
    error_log("Erro na API de modalidades: " . $e->getMessage());
    sendResponse(false, 'Erro interno do servidor: ' . $e->getMessage(), null, 500);
} finally {
    $conn->close();
}

// ============================================
// FUNÇÕES PARA TRATAR REQUISIÇÕES
// ============================================

function handleGetRequest($modalidadeDAO, $action, $queryParams) {
    switch ($action) {
        case '':
        case 'listar':
            // GET /api/modalidades ou /api/modalidades/listar
            listarModalidades($modalidadeDAO, $queryParams);
            break;
            
        case 'buscar':
            // GET /api/modalidades/buscar?id=XXX
            buscarModalidadePorId($modalidadeDAO, $queryParams);
            break;
            
        case 'nome':
            // GET /api/modalidades/nome?nome=XXX
            buscarPorNome($modalidadeDAO, $queryParams);
            break;
            
        case 'select':
            // GET /api/modalidades/select
            buscarParaSelect($modalidadeDAO);
            break;
            
        case 'contar':
            // GET /api/modalidades/contar
            contarModalidades($modalidadeDAO, $queryParams);
            break;
            
        case 'paginacao':
            // GET /api/modalidades/paginacao?pagina=1&itens=10
            buscarComPaginacao($modalidadeDAO, $queryParams);
            break;
            
        default:
            // GET /api/modalidades/{id}
            if (is_numeric($action)) {
                buscarModalidadePorId($modalidadeDAO, ['id' => $action]);
            } else {
                sendResponse(false, 'Ação não reconhecida', null, 404);
            }
    }
}

function handlePostRequest($modalidadeDAO, $action, $input) {
    switch ($action) {
        case '':
        case 'criar':
            // POST /api/modalidades ou /api/modalidades/criar
            criarModalidade($modalidadeDAO, $input);
            break;
            
        case 'verificar-nome':
            // POST /api/modalidades/verificar-nome
            verificarNome($modalidadeDAO, $input);
            break;
            
        default:
            sendResponse(false, 'Ação não reconhecida', null, 404);
    }
}

function handlePutRequest($modalidadeDAO, $action, $input, $queryParams) {
    switch ($action) {
        case '':
        case 'atualizar':
            // PUT /api/modalidades ou /api/modalidades/atualizar
            atualizarModalidade($modalidadeDAO, $input, $queryParams);
            break;
            
        default:
            // PUT /api/modalidades/{id}
            if (is_numeric($action)) {
                $input['id_modalidade'] = $action;
                atualizarModalidade($modalidadeDAO, $input, $queryParams);
            } else {
                sendResponse(false, 'Ação não reconhecida', null, 404);
            }
    }
}

function handleDeleteRequest($modalidadeDAO, $action, $queryParams) {
    switch ($action) {
        case '':
        case 'excluir':
            // DELETE /api/modalidades ou /api/modalidades/excluir
            excluirModalidade($modalidadeDAO, $queryParams);
            break;
            
        default:
            // DELETE /api/modalidades/{id}
            if (is_numeric($action)) {
                excluirModalidade($modalidadeDAO, ['id' => $action]);
            } else {
                sendResponse(false, 'Ação não reconhecida', null, 404);
            }
    }
}

// ============================================
// FUNÇÕES ESPECÍFICAS DO DAO
// ============================================

function listarModalidades($modalidadeDAO, $params) {
    try {
        $filtros = [];
        
        if (isset($params['nome']) && !empty($params['nome'])) {
            $filtros['nome'] = $params['nome'];
        }
        
        if (isset($params['descricao']) && !empty($params['descricao'])) {
            $filtros['descricao'] = $params['descricao'];
        }
        
        $modalidades = $modalidadeDAO->listarTodas($filtros);
        
        $resultado = [];
        foreach ($modalidades as $modalidade) {
            $resultado[] = $modalidade->toArray();
        }
        
        sendResponse(true, 'Modalidades listadas com sucesso', [
            'modalidades' => $resultado,
            'total' => count($resultado)
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao listar modalidades: ' . $e->getMessage());
    }
}

function buscarModalidadePorId($modalidadeDAO, $params) {
    try {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            sendResponse(false, 'ID da modalidade não informado ou inválido');
        }
        
        $modalidade = $modalidadeDAO->buscarPorId($params['id']);
        
        if (!$modalidade) {
            sendResponse(false, 'Modalidade não encontrada', null, 404);
        }
        
        sendResponse(true, 'Modalidade encontrada', $modalidade->toArray());
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao buscar modalidade: ' . $e->getMessage());
    }
}

function buscarPorNome($modalidadeDAO, $params) {
    try {
        if (!isset($params['nome']) || empty($params['nome'])) {
            sendResponse(false, 'Nome não informado');
        }
        
        $modalidades = $modalidadeDAO->buscarPorNome($params['nome']);
        
        $resultado = [];
        foreach ($modalidades as $modalidade) {
            $resultado[] = $modalidade->toArray();
        }
        
        sendResponse(true, 'Modalidades encontradas', [
            'modalidades' => $resultado,
            'total' => count($resultado)
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao buscar modalidades por nome: ' . $e->getMessage());
    }
}

function criarModalidade($modalidadeDAO, $dados) {
    try {
        // Validação básica
        if (!isset($dados['nome_modalidade']) || empty(trim($dados['nome_modalidade']))) {
            sendResponse(false, 'Nome da modalidade é obrigatório');
        }
        
        // Verificar se nome já existe
        if ($modalidadeDAO->nomeExiste($dados['nome_modalidade'])) {
            sendResponse(false, 'Nome da modalidade já existe');
        }
        
        // Criar objeto Modalidade
        $modalidade = new Modalidade(
            null,
            trim($dados['nome_modalidade']),
            isset($dados['descricao_modalidade']) ? trim($dados['descricao_modalidade']) : '',
            null
        );
        
        // Validar
        $erros = $modalidade->validar();
        if (!empty($erros)) {
            sendResponse(false, 'Erros de validação', ['errors' => $erros], 400);
        }
        
        // Salvar no banco
        $id = $modalidadeDAO->criar($modalidade);
        
        if (!$id) {
            sendResponse(false, 'Erro ao criar modalidade no banco de dados');
        }
        
        // Buscar modalidade criada
        $modalidadeCriada = $modalidadeDAO->buscarPorId($id);
        
        sendResponse(true, 'Modalidade criada com sucesso', $modalidadeCriada->toArray(), 201);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao criar modalidade: ' . $e->getMessage());
    }
}

function atualizarModalidade($modalidadeDAO, $dados, $params) {
    try {
        // Obter ID da modalidade
        $id_modalidade = null;
        
        if (isset($dados['id_modalidade'])) {
            $id_modalidade = $dados['id_modalidade'];
        } elseif (isset($params['id'])) {
            $id_modalidade = $params['id'];
        }
        
        if (!$id_modalidade || !is_numeric($id_modalidade)) {
            sendResponse(false, 'ID da modalidade não informado ou inválido');
        }
        
        // Verificar se modalidade existe
        $modalidadeExistente = $modalidadeDAO->buscarPorId($id_modalidade);
        if (!$modalidadeExistente) {
            sendResponse(false, 'Modalidade não encontrada', null, 404);
        }
        
        // Atualizar dados
        if (isset($dados['nome_modalidade'])) {
            // Verificar se novo nome já existe (outra modalidade)
            if ($modalidadeDAO->nomeExiste($dados['nome_modalidade'], $id_modalidade)) {
                sendResponse(false, 'Nome da modalidade já existe para outra modalidade');
            }
            $modalidadeExistente->setNomeModalidade($dados['nome_modalidade']);
        }
        
        if (isset($dados['descricao_modalidade'])) {
            $modalidadeExistente->setDescricaoModalidade($dados['descricao_modalidade']);
        }
        
        // Validar
        $erros = $modalidadeExistente->validar();
        if (!empty($erros)) {
            sendResponse(false, 'Erros de validação', ['errors' => $erros], 400);
        }
        
        // Atualizar no banco
        $resultado = $modalidadeDAO->atualizar($modalidadeExistente);
        
        if (!$resultado) {
            sendResponse(false, 'Nenhuma alteração realizada ou erro ao atualizar');
        }
        
        // Buscar modalidade atualizada
        $modalidadeAtualizada = $modalidadeDAO->buscarPorId($id_modalidade);
        
        sendResponse(true, 'Modalidade atualizada com sucesso', $modalidadeAtualizada->toArray());
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao atualizar modalidade: ' . $e->getMessage());
    }
}

function excluirModalidade($modalidadeDAO, $params) {
    try {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            sendResponse(false, 'ID da modalidade não informado ou inválido');
        }
        
        $id_modalidade = $params['id'];
        
        // Verificar se modalidade existe
        $modalidade = $modalidadeDAO->buscarPorId($id_modalidade);
        if (!$modalidade) {
            sendResponse(false, 'Modalidade não encontrada', null, 404);
        }
        
        // Excluir
        $resultado = $modalidadeDAO->excluir($id_modalidade);
        
        if (!$resultado) {
            sendResponse(false, 'Erro ao excluir modalidade');
        }
        
        sendResponse(true, 'Modalidade excluída com sucesso');
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao excluir modalidade: ' . $e->getMessage());
    }
}

function buscarParaSelect($modalidadeDAO) {
    try {
        $modalidades = $modalidadeDAO->buscarParaSelect();
        
        sendResponse(true, 'Modalidades para select', $modalidades);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao buscar modalidades para select: ' . $e->getMessage());
    }
}

function contarModalidades($modalidadeDAO, $params) {
    try {
        $filtros = [];
        
        if (isset($params['nome']) && !empty($params['nome'])) {
            $filtros['nome'] = $params['nome'];
        }
        
        $total = $modalidadeDAO->contarTotal($filtros);
        
        sendResponse(true, 'Total de modalidades', ['total' => $total]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao contar modalidades: ' . $e->getMessage());
    }
}

function buscarComPaginacao($modalidadeDAO, $params) {
    try {
        $pagina = isset($params['pagina']) ? (int)$params['pagina'] : 1;
        $itensPorPagina = isset($params['itens']) ? (int)$params['itens'] : 10;
        
        $filtros = [];
        if (isset($params['nome']) && !empty($params['nome'])) {
            $filtros['nome'] = $params['nome'];
        }
        
        $modalidades = $modalidadeDAO->buscarComPaginacao($pagina, $itensPorPagina, $filtros);
        $total = $modalidadeDAO->contarTotal($filtros);
        
        $resultado = [];
        foreach ($modalidades as $modalidade) {
            $resultado[] = $modalidade->toArray();
        }
        
        sendResponse(true, 'Modalidades com paginação', [
            'modalidades' => $resultado,
            'paginacao' => [
                'pagina_atual' => $pagina,
                'itens_por_pagina' => $itensPorPagina,
                'total_itens' => $total,
                'total_paginas' => ceil($total / $itensPorPagina)
            ]
        ]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao buscar modalidades com paginação: ' . $e->getMessage());
    }
}

function verificarNome($modalidadeDAO, $dados) {
    try {
        if (!isset($dados['nome_modalidade']) || empty($dados['nome_modalidade'])) {
            sendResponse(false, 'Nome da modalidade não informado');
        }
        
        $id_excluir = isset($dados['id_modalidade']) ? $dados['id_modalidade'] : null;
        $existe = $modalidadeDAO->nomeExiste($dados['nome_modalidade'], $id_excluir);
        
        sendResponse(true, 'Verificação concluída', ['existe' => $existe]);
        
    } catch (Exception $e) {
        sendResponse(false, 'Erro ao verificar nome: ' . $e->getMessage());
    }
}
?>