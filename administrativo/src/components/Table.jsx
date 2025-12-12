import { useEffect, useState } from "react";
import Button from "../components/Button";
import PopUpAlunos from "../components/popUpAlunos";
// import SearchIcon from '@mui/icons-material/Search';

function Table({ onDataLoaded }) {
  const [alunos, setAlunos] = useState([]);
  const [PopUPOpen, setPopUp] = useState(false);
  const [PopUpMode, setPopUpMode] = useState("create");
  const [selectedAluno, setSelectedAluno] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [isSearching, setIsSearching] = useState(false);

  const openPopUp = (mode, alunoID) => {
    setPopUpMode(mode);
    setSelectedAluno(alunoID);
    setPopUp(true);
  };

  const closePopUp = () => {
    setPopUp(false);
    setSelectedAluno(null);
  };

  // Função para buscar alunos
  const fetchAlunos = async (query = "") => {
    try {
      setIsSearching(true);
      const url = query.trim() === "" 
        ? "http://localhost:8000/alunosAPI.php"
        : `http://localhost:8000/alunosAPI.php?search=${encodeURIComponent(query)}`;

      const response = await fetch(url);
      const data = await response.json();
      
      let alunosData = [];
      
      // Trata diferentes formatos de resposta
      if (Array.isArray(data)) {
        alunosData = data;
      } else if (data && typeof data === 'object') {
        // Se for um único aluno, coloca em array
        alunosData = [data];
      }
      
      setAlunos(alunosData);
      
      // Chama o callback para enviar dados para o componente pai
      if (onDataLoaded) {
        onDataLoaded(alunosData);
      }
    } catch (err) {
      console.error("Erro ao buscar alunos:", err);
      // Ainda chama o callback mesmo com erro (com array vazio)
      if (onDataLoaded) {
        onDataLoaded([]);
      }
    } finally {
      setIsSearching(false);
    }
  };

  useEffect(() => {
    fetchAlunos();
  }, []);

  const handleDelete = async (id) => {
    if (window.confirm("Tem certeza que deseja excluir este aluno?")) {
      try {
        await fetch("http://localhost:8000/alunosAPI.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ idAlunos: id }),
        });
        
        const novosAlunos = alunos.filter((aluno) => aluno.ID_ALUNO !== id);
        setAlunos(novosAlunos);
        
        // Atualiza o pai com os novos dados
        if (onDataLoaded) {
          onDataLoaded(novosAlunos);
        }
      } catch (err) {
        console.error("Erro ao excluir aluno:", err);
      }
    }
  };

  const handleSearch = (e) => {
    // Previne o comportamento padrão do formulário
    e.preventDefault();
    
    const query = e.target.value;
    setSearchQuery(query);
    
    // Usa debounce para não fazer requisição a cada tecla pressionada
    const timeoutId = setTimeout(() => {
      fetchAlunos(query);
    }, 300); // Aguarda 300ms após a última digitação
    
    return () => clearTimeout(timeoutId);
  };

  // Função para limpar a busca
  const handleClearSearch = () => {
    setSearchQuery("");
    fetchAlunos();
  };

  return (
    <>
      {/* WRAPPER COM SCROLL */}
      <div className="w-full max-h-[700px] overflow-auto border border-gray-300 rounded-lg">
        
        {/* Container do campo de busca */}
        <div className="flex justify-end p-4 bg-transparent border-b">
          <div className="relative w-full max-w-60">
            <input 
              className="
                w-full
                px-3 py-2
                rounded-xl
                border border-gray-300
                shadow-sm
                focus:outline-none 
                focus:ring-2 
                focus:ring-red-600 
                focus:border-red-600
                placeholder-gray-400
                bg-white
                text-black
                pr-10
              "
              value={searchQuery}
              onChange={handleSearch}
              placeholder="Buscar aluno..."
            />
            
            {/* Botão para limpar busca */}
            {searchQuery && (
              <button
                type="button"
                onClick={handleClearSearch}
                className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600"
              >
                ✕
              </button>
            )}
            
            {/* Indicador de carregamento */}
            {isSearching && (
              <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-red-600"></div>
              </div>
            )}
          </div>
          
        </div>

        {/* Tabela */}
        <table className="table-auto w-full border-none min-w-max">
          <thead className="bg-red-950 text-white sticky top-0">
            <tr>
              <th className="px-4 py-2 border-2">ID</th>
              <th className="px-4 py-2 border-2">Nome</th>
              <th className="px-4 py-2 border-2">Email</th>
              <th className="px-4 py-2 border-2">Telefone</th>
              <th className="px-4 py-2 border-2">Sexo</th>
              <th className="px-4 py-2 border-2">Status</th>
              <th className="px-4 py-2 border-2">Ações</th>
            </tr>
          </thead>

          <tbody>
            {alunos.length === 0 ? (
              <tr>
                <td colSpan="7" className="px-4 py-8 text-center text-gray-500">
                  {isSearching ? "Buscando alunos..." : "Nenhum aluno encontrado"}
                </td>
              </tr>
            ) : (
              alunos.map((aluno) => (
                <tr key={aluno.ID_ALUNO} className="bg-red-950 text-white hover:border-2 hover:border-white hover:bg-red-600 hover:font-bold hover:text-base">
                  <td className="px-4 py-2 border border-transparent">{aluno.ID_ALUNO}</td>
                  <td className="px-4 py-2 border-none text-center">{aluno.NOME}</td>
                  <td className="px-4 py-2 border-none text-center">{aluno.EMAIL}</td>
                  <td className="px-4 py-2 border-none text-center">{aluno.TELEFONE}</td>
                  <td className="px-4 py-2 border-none text-center">{aluno.SEXO}</td>
                  <td className="px-4 py-2 border-none text-center">{aluno.STATUS_ALUNO}</td>
                  <td className="px-4 py-2 border-none text-center space-x-2">
                    <Button variant="update" onClick={() => openPopUp("edit", aluno.ID_ALUNO)}>
                      Editar
                    </Button>
                    <Button variant="delete" onClick={() => handleDelete(aluno.ID_ALUNO)}>
                      Excluir
                    </Button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {/* Pop-up */}
      {PopUPOpen && (
        <PopUpAlunos
          isOpen={PopUPOpen}
          onClose={closePopUp}
          mode={PopUpMode}
          alunoId={selectedAluno}
          onSuccess={() => {
            closePopUp();
            fetchAlunos(searchQuery); // Recarrega com a busca atual
          }}
        />
      )}
    </>
  );
}

export default Table;