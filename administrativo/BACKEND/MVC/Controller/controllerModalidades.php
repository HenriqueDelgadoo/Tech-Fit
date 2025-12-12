<?php
// Controller/ControllerModalidades.php

require_once '../Model/Modalidade.php';
require_once '../Model/ModalidadeDAO.php';

class ControllerModalidades {
    private $modalidadeDAO;
    
    public function __construct($conn) {
        $this->modalidadeDAO = new ModalidadeDAO($conn);
    }
    
    // 1. Criar modalidade
    public function criarModalidade($dados) {
        try {
            // Validação
            $erros = $this->validarDadosModalidade($dados, 'criar');
            if (!empty($erros)) {
                return ['success' => false, 'message' => 'Erros de validação', 'errors' => $erros];
            }
            
            // Verificar se nome já existe
            if ($this->modalidadeDAO->nomeExiste($dados['nome_modalidade'])) {
                return ['success' => false, 'message' => 'Nome da modalidade já existe'];
            }
            
            // Criar objeto
            $modalidade = Modalidade::criarDeArray($dados);
            
            // Salvar no banco
            $id = $this->modalidadeDAO->criar($modalidade);
            
            if (!$id) {
                return ['success' => false, 'message' => 'Erro ao criar modalidade'];
            }
            
            // Buscar modalidade criada
            $modalidadeCriada = $this->modalidadeDAO->buscarPorId($id);
            
            return [
                'success' => true,
                'message' => 'Modalidade criada com sucesso',
                'data' => $modalidadeCriada->toArray()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao criar modalidade: ' . $e->getMessage()
            ];
        }
    }
    
    // 2. Listar modalidades
    public function listarModalidades($filtros = []) {
        try {
            $modalidades = $this->modalidadeDAO->listarTodas($filtros);
            
            $modalidadesFormatadas = [];
            foreach ($modalidades as $modalidade) {
                $modalidadesFormatadas[] = $modalidade->toArray();
            }
            
            return [
                'success' => true,
                'data' => $modalidadesFormatadas,
                'total' => count($modalidadesFormatadas)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao listar modalidades: ' . $e->getMessage()
            ];
        }
    }
    
    // 3. Buscar modalidade por ID
    public function buscarModalidade($id_modalidade) {
        try {
            if (empty($id_modalidade) || !is_numeric($id_modalidade)) {
                return ['success' => false, 'message' => 'ID inválido'];
            }
            
            $modalidade = $this->modalidadeDAO->buscarPorId($id_modalidade);
            
            if (!$modalidade) {
                return ['success' => false, 'message' => 'Modalidade não encontrada'];
            }
            
            return [
                'success' => true,
                'data' => $modalidade->toArray()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar modalidade: ' . $e->getMessage()
            ];
        }
    }
    
    // 4. Atualizar modalidade
    public function atualizarModalidade($id_modalidade, $dados) {
        try {
            if (empty($id_modalidade) || !is_numeric($id_modalidade)) {
                return ['success' => false, 'message' => 'ID inválido'];
            }
            
            // Verificar se modalidade existe
            $modalidadeExistente = $this->modalidadeDAO->buscarPorId($id_modalidade);
            if (!$modalidadeExistente) {
                return ['success' => false, 'message' => 'Modalidade não encontrada'];
            }
            
            // Validação
            $erros = $this->validarDadosModalidade($dados, 'atualizar');
            if (!empty($erros)) {
                return ['success' => false, 'message' => 'Erros de validação', 'errors' => $erros];
            }
            
            // Verificar se nome já existe (outra modalidade)
            if (isset($dados['nome_modalidade']) && 
                $this->modalidadeDAO->nomeExiste($dados['nome_modalidade'], $id_modalidade)) {
                return ['success' => false, 'message' => 'Nome da modalidade já existe'];
            }
            
            // Atualizar dados
            if (isset($dados['nome_modalidade'])) {
                $modalidadeExistente->setNomeModalidade($dados['nome_modalidade']);
            }
            
            if (isset($dados['descricao_modalidade'])) {
                $modalidadeExistente->setDescricaoModalidade($dados['descricao_modalidade']);
            }
            
            // Salvar alterações
            $resultado = $this->modalidadeDAO->atualizar($modalidadeExistente);
            
            if (!$resultado) {
                return ['success' => false, 'message' => 'Nenhuma alteração realizada'];
            }
            
            // Buscar modalidade atualizada
            $modalidadeAtualizada = $this->modalidadeDAO->buscarPorId($id_modalidade);
            
            return [
                'success' => true,
                'message' => 'Modalidade atualizada com sucesso',
                'data' => $modalidadeAtualizada->toArray()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao atualizar modalidade: ' . $e->getMessage()
            ];
        }
    }
    
    // 5. Excluir modalidade
    public function excluirModalidade($id_modalidade) {
        try {
            if (empty($id_modalidade) || !is_numeric($id_modalidade)) {
                return ['success' => false, 'message' => 'ID inválido'];
            }
            
            // Verificar se modalidade existe
            $modalidade = $this->modalidadeDAO->buscarPorId($id_modalidade);
            if (!$modalidade) {
                return ['success' => false, 'message' => 'Modalidade não encontrada'];
            }
            
            $resultado = $this->modalidadeDAO->excluir($id_modalidade);
            
            if (!$resultado) {
                return ['success' => false, 'message' => 'Erro ao excluir modalidade'];
            }
            
            return [
                'success' => true,
                'message' => 'Modalidade excluída com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao excluir modalidade: ' . $e->getMessage()
            ];
        }
    }
    
    // 6. Buscar modalidades por nome
    public function buscarModalidadesPorNome($nome) {
        try {
            if (empty($nome)) {
                return ['success' => false, 'message' => 'Nome não fornecido'];
            }
            
            $modalidades = $this->modalidadeDAO->buscarPorNome($nome);
            
            $modalidadesFormatadas = [];
            foreach ($modalidades as $modalidade) {
                $modalidadesFormatadas[] = $modalidade->toArray();
            }
            
            return [
                'success' => true,
                'data' => $modalidadesFormatadas,
                'total' => count($modalidadesFormatadas)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar modalidades por nome: ' . $e->getMessage()
            ];
        }
    }
    
    // 7. Buscar modalidades para select
    public function buscarParaSelect() {
        try {
            $modalidades = $this->modalidadeDAO->buscarParaSelect();
            
            return [
                'success' => true,
                'data' => $modalidades
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar modalidades: ' . $e->getMessage()
            ];
        }
    }
    
    // 8. Contar total de modalidades
    public function contarModalidades($filtros = []) {
        try {
            $total = $this->modalidadeDAO->contarTotal($filtros);
            
            return [
                'success' => true,
                'data' => [
                    'total' => $total
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao contar modalidades: ' . $e->getMessage()
            ];
        }
    }
    
    // 9. Buscar modalidades com paginação
    public function buscarComPaginacao($pagina = 1, $itensPorPagina = 10, $filtros = []) {
        try {
            if (!is_numeric($pagina) || $pagina < 1) {
                $pagina = 1;
            }
            
            if (!is_numeric($itensPorPagina) || $itensPorPagina < 1) {
                $itensPorPagina = 10;
            }
            
            $modalidades = $this->modalidadeDAO->buscarComPaginacao($pagina, $itensPorPagina, $filtros);
            $total = $this->modalidadeDAO->contarTotal($filtros);
            
            $modalidadesFormatadas = [];
            foreach ($modalidades as $modalidade) {
                $modalidadesFormatadas[] = $modalidade->toArray();
            }
            
            return [
                'success' => true,
                'data' => $modalidadesFormatadas,
                'paginacao' => [
                    'pagina_atual' => (int)$pagina,
                    'itens_por_pagina' => (int)$itensPorPagina,
                    'total_itens' => (int)$total,
                    'total_paginas' => ceil($total / $itensPorPagina)
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar modalidades com paginação: ' . $e->getMessage()
            ];
        }
    }
    
    // 10. Validação de dados
    private function validarDadosModalidade($dados, $operacao = 'criar') {
        $erros = [];
        
        // Campos obrigatórios para criação
        if ($operacao === 'criar') {
            if (empty($dados['nome_modalidade'])) {
                $erros[] = "Nome da modalidade é obrigatório";
            }
        }
        
        // Validação de nome
        if (isset($dados['nome_modalidade']) && !empty($dados['nome_modalidade'])) {
            if (strlen($dados['nome_modalidade']) > 120) {
                $erros[] = "Nome da modalidade deve ter no máximo 120 caracteres";
            }
        }
        
        // Validação de descrição
        if (isset($dados['descricao_modalidade']) && !empty($dados['descricao_modalidade'])) {
            if (strlen($dados['descricao_modalidade']) > 1000) {
                $erros[] = "Descrição deve ter no máximo 1000 caracteres";
            }
        }
        
        return $erros;
    }
}
?>