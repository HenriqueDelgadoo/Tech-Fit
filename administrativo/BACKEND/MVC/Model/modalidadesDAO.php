<?php
// Model/ModalidadeDAO.php

require_once 'Modalidade.php';
require_once 'Connection.php';

class ModalidadeDAO {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // 1. Criar modalidade
    public function criar(Modalidade $modalidade) {
        try {
            $sql = "INSERT INTO MODALIDADES (NOME_MODALIDADE, DESCRICAO_MODALIDADE) 
                    VALUES (?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $nome = $modalidade->getNomeModalidade();
            $descricao = $modalidade->getDescricaoModalidade();
            
            $stmt->bind_param("ss", $nome, $descricao);
            
            if ($stmt->execute()) {
                $id = $this->conn->insert_id;
                $modalidade->setIdModalidade($id);
                return $id;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Erro ao criar modalidade: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 2. Buscar por ID
    public function buscarPorId($id_modalidade) {
        try {
            $sql = "SELECT ID_MODALIDADE, NOME_MODALIDADE, DESCRICAO_MODALIDADE, CRIADO_EM 
                    FROM MODALIDADES 
                    WHERE ID_MODALIDADE = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_modalidade);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            $row = $result->fetch_assoc();
            return Modalidade::criarDeBanco($row);
            
        } catch (Exception $e) {
            error_log("Erro ao buscar modalidade por ID: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 3. Buscar por nome
    public function buscarPorNome($nome_modalidade) {
        try {
            $sql = "SELECT ID_MODALIDADE, NOME_MODALIDADE, DESCRICAO_MODALIDADE, CRIADO_EM 
                    FROM MODALIDADES 
                    WHERE NOME_MODALIDADE LIKE ? 
                    ORDER BY NOME_MODALIDADE";
            
            $stmt = $this->conn->prepare($sql);
            $nome = "%" . $nome_modalidade . "%";
            $stmt->bind_param("s", $nome);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modalidades = [];
            while ($row = $result->fetch_assoc()) {
                $modalidades[] = Modalidade::criarDeBanco($row);
            }
            
            return $modalidades;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar modalidade por nome: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 4. Listar todas as modalidades
    public function listarTodas($filtros = []) {
        try {
            $sql = "SELECT ID_MODALIDADE, NOME_MODALIDADE, DESCRICAO_MODALIDADE, CRIADO_EM 
                    FROM MODALIDADES 
                    WHERE 1=1";
            
            $params = [];
            $types = "";
            
            // Filtro por nome
            if (isset($filtros['nome']) && !empty($filtros['nome'])) {
                $sql .= " AND NOME_MODALIDADE LIKE ?";
                $params[] = "%" . $filtros['nome'] . "%";
                $types .= "s";
            }
            
            // Filtro por descrição
            if (isset($filtros['descricao']) && !empty($filtros['descricao'])) {
                $sql .= " AND DESCRICAO_MODALIDADE LIKE ?";
                $params[] = "%" . $filtros['descricao'] . "%";
                $types .= "s";
            }
            
            $sql .= " ORDER BY NOME_MODALIDADE";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modalidades = [];
            while ($row = $result->fetch_assoc()) {
                $modalidades[] = Modalidade::criarDeBanco($row);
            }
            
            return $modalidades;
            
        } catch (Exception $e) {
            error_log("Erro ao listar modalidades: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 5. Atualizar modalidade
    public function atualizar(Modalidade $modalidade) {
        try {
            $sql = "UPDATE MODALIDADES 
                    SET NOME_MODALIDADE = ?, DESCRICAO_MODALIDADE = ? 
                    WHERE ID_MODALIDADE = ?";
            
            $stmt = $this->conn->prepare($sql);
            $nome = $modalidade->getNomeModalidade();
            $descricao = $modalidade->getDescricaoModalidade();
            $id = $modalidade->getIdModalidade();
            
            $stmt->bind_param("ssi", $nome, $descricao, $id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Erro ao atualizar modalidade: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 6. Excluir modalidade
    public function excluir($id_modalidade) {
        try {
            // Primeiro verificar se há funcionários associados
            $sql_check = "SELECT COUNT(*) as total FROM FUNCIONARIOS WHERE ID_MODALIDADE = ?";
            $stmt_check = $this->conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id_modalidade);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['total'] > 0) {
                throw new Exception("Não é possível excluir a modalidade pois existem funcionários associados.");
            }
            
            $sql = "DELETE FROM MODALIDADES WHERE ID_MODALIDADE = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_modalidade);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Erro ao excluir modalidade: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 7. Verificar se nome já existe
    public function nomeExiste($nome_modalidade, $id_excluir = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM MODALIDADES 
                    WHERE NOME_MODALIDADE = ?";
            
            if ($id_excluir) {
                $sql .= " AND ID_MODALIDADE != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            
            if ($id_excluir) {
                $stmt->bind_param("si", $nome_modalidade, $id_excluir);
            } else {
                $stmt->bind_param("s", $nome_modalidade);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['total'] > 0;
            
        } catch (Exception $e) {
            error_log("Erro ao verificar nome da modalidade: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 8. Contar total de modalidades
    public function contarTotal($filtros = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM MODALIDADES WHERE 1=1";
            
            $params = [];
            $types = "";
            
            if (isset($filtros['nome']) && !empty($filtros['nome'])) {
                $sql .= " AND NOME_MODALIDADE LIKE ?";
                $params[] = "%" . $filtros['nome'] . "%";
                $types .= "s";
            }
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['total'];
            
        } catch (Exception $e) {
            error_log("Erro ao contar modalidades: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 9. Buscar modalidades com paginação
    public function buscarComPaginacao($pagina = 1, $itensPorPagina = 10, $filtros = []) {
        try {
            $offset = ($pagina - 1) * $itensPorPagina;
            
            $sql = "SELECT ID_MODALIDADE, NOME_MODALIDADE, DESCRICAO_MODALIDADE, CRIADO_EM 
                    FROM MODALIDADES 
                    WHERE 1=1";
            
            $params = [];
            $types = "";
            
            if (isset($filtros['nome']) && !empty($filtros['nome'])) {
                $sql .= " AND NOME_MODALIDADE LIKE ?";
                $params[] = "%" . $filtros['nome'] . "%";
                $types .= "s";
            }
            
            $sql .= " ORDER BY NOME_MODALIDADE 
                     LIMIT ? OFFSET ?";
            
            $params[] = $itensPorPagina;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modalidades = [];
            while ($row = $result->fetch_assoc()) {
                $modalidades[] = Modalidade::criarDeBanco($row);
            }
            
            return $modalidades;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar modalidades com paginação: " . $e->getMessage());
            throw $e;
        }
    }
    
    // 10. Buscar modalidades para select (apenas id e nome)
    public function buscarParaSelect() {
        try {
            $sql = "SELECT ID_MODALIDADE, NOME_MODALIDADE 
                    FROM MODALIDADES 
                    ORDER BY NOME_MODALIDADE";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modalidades = [];
            while ($row = $result->fetch_assoc()) {
                $modalidades[] = [
                    'id' => $row['ID_MODALIDADE'],
                    'nome' => $row['NOME_MODALIDADE']
                ];
            }
            
            return $modalidades;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar modalidades para select: " . $e->getMessage());
            throw $e;
        }
    }
}
?>