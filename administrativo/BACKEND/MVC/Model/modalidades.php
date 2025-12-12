<?php
// Model/Modalidade.php

class Modalidade {
    private $id_modalidade;
    private $nome_modalidade;
    private $descricao_modalidade;
    private $criado_em;
    
    // Construtor
    public function __construct($id_modalidade = null, $nome_modalidade = '', $descricao_modalidade = '', $criado_em = null) {
        $this->id_modalidade = $id_modalidade;
        $this->nome_modalidade = $nome_modalidade;
        $this->descricao_modalidade = $descricao_modalidade;
        $this->criado_em = $criado_em;
    }
    
    // Getters
    public function getIdModalidade() {
        return $this->id_modalidade;
    }
    
    public function getNomeModalidade() {
        return $this->nome_modalidade;
    }
    
    public function getDescricaoModalidade() {
        return $this->descricao_modalidade;
    }
    
    public function getCriadoEm() {
        return $this->criado_em;
    }
    
    public function getCriadoEmFormatado() {
        if ($this->criado_em) {
            return date('d/m/Y H:i:s', strtotime($this->criado_em));
        }
        return null;
    }
    
    // Setters
    public function setIdModalidade($id_modalidade) {
        $this->id_modalidade = $id_modalidade;
        return $this;
    }
    
    public function setNomeModalidade($nome_modalidade) {
        $this->nome_modalidade = trim($nome_modalidade);
        return $this;
    }
    
    public function setDescricaoModalidade($descricao_modalidade) {
        $this->descricao_modalidade = trim($descricao_modalidade);
        return $this;
    }
    
    public function setCriadoEm($criado_em) {
        $this->criado_em = $criado_em;
        return $this;
    }
    
    // Validações
    public function validar() {
        $erros = [];
        
        if (empty($this->nome_modalidade)) {
            $erros[] = "Nome da modalidade é obrigatório";
        } elseif (strlen($this->nome_modalidade) > 120) {
            $erros[] = "Nome da modalidade deve ter no máximo 120 caracteres";
        }
        
        if (strlen($this->descricao_modalidade) > 1000) {
            $erros[] = "Descrição deve ter no máximo 1000 caracteres";
        }
        
        return $erros;
    }
    
    // Método para criar objeto a partir de array (útil para forms/API)
    public static function criarDeArray($dados) {
        $modalidade = new Modalidade();
        
        if (isset($dados['id_modalidade'])) {
            $modalidade->setIdModalidade($dados['id_modalidade']);
        }
        
        if (isset($dados['nome_modalidade'])) {
            $modalidade->setNomeModalidade($dados['nome_modalidade']);
        }
        
        if (isset($dados['descricao_modalidade'])) {
            $modalidade->setDescricaoModalidade($dados['descricao_modalidade']);
        }
        
        if (isset($dados['criado_em'])) {
            $modalidade->setCriadoEm($dados['criado_em']);
        }
        
        return $modalidade;
    }
    
    // Método para converter para array (útil para JSON/API)
    public function toArray() {
        return [
            'id_modalidade' => $this->id_modalidade,
            'nome_modalidade' => $this->nome_modalidade,
            'descricao_modalidade' => $this->descricao_modalidade,
            'criado_em' => $this->criado_em,
            'criado_em_formatado' => $this->getCriadoEmFormatado()
        ];
    }
    
    // Método para dados do banco (nomes em maiúsculo)
    public static function criarDeBanco($dados) {
        $modalidade = new Modalidade();
        
        if (isset($dados['ID_MODALIDADE'])) {
            $modalidade->setIdModalidade($dados['ID_MODALIDADE']);
        } elseif (isset($dados['id_modalidade'])) {
            $modalidade->setIdModalidade($dados['id_modalidade']);
        }
        
        if (isset($dados['NOME_MODALIDADE'])) {
            $modalidade->setNomeModalidade($dados['NOME_MODALIDADE']);
        } elseif (isset($dados['nome_modalidade'])) {
            $modalidade->setNomeModalidade($dados['nome_modalidade']);
        }
        
        if (isset($dados['DESCRICAO_MODALIDADE'])) {
            $modalidade->setDescricaoModalidade($dados['DESCRICAO_MODALIDADE']);
        } elseif (isset($dados['descricao_modalidade'])) {
            $modalidade->setDescricaoModalidade($dados['descricao_modalidade']);
        }
        
        if (isset($dados['CRIADO_EM'])) {
            $modalidade->setCriadoEm($dados['CRIADO_EM']);
        } elseif (isset($dados['criado_em'])) {
            $modalidade->setCriadoEm($dados['criado_em']);
        }
        
        return $modalidade;
    }
}
?>