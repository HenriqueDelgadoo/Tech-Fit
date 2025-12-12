import { useState } from 'react'
import { useNavigate } from 'react-router-dom';
import '../style/Login.css'
import Inputs from '../components/Inputs'

function Login() {
  const [usuario, setUsuario] = useState('');
  const [senha, setSenha] = useState('');
  const [mensagem, setMensagem] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const navigate = useNavigate();

  const onLoginClick = async (e) => {
    e.preventDefault();
    setMensagem(""); // limpa mensagens anteriores
    
    // Validação dos campos
    if (usuario.trim() === '' || senha.trim() === '') {
      setMensagem("Insira todos os campos!");
      return;
    }

    setIsLoading(true);

    try {
      const res = await fetch('http://localhost:8000/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ usuario, senha })
      });

      const data = await res.json();
      console.log('Resposta completa da API:', data);

      // CORREÇÃO: Verificar corretamente o status
      if (data.status === 'sucesso') {
        setMensagem(`Bem-vindo, ${data.usuario}! Redirecionando...`);
        
        // Armazenar dados do usuário
        localStorage.setItem('usuarioLogado', JSON.stringify({
          nome: data.usuario,
          // Se a API retornar mais dados, adicione aqui
          ...(data.dados && { dados: data.dados })
        }));
        
        // Aguarda 1.5 segundos para mostrar a mensagem antes de redirecionar
        setTimeout(() => {
          navigate('/index');
        }, 1500);
        
      } else {
        // Se status for 'erro', mostrar a mensagem
        setMensagem(data.mensagem || "Usuário ou senha inválidos. Tente novamente.");
      }

    } catch (erro) {
      console.error("Erro completo:", erro);
      setMensagem("Erro ao conectar ao servidor. Verifique se o backend está rodando.");
    } finally {
      setIsLoading(false);
    }
  }

  return (
    <main className='loginPai'> 
      <form className="container" onSubmit={onLoginClick}>
        <img className="logo" src="/justgorila.png" alt="Logo-TechFit" />
        <h1 className="title">Tech Fit</h1>

        <label className='label' htmlFor="usuario">Username:</label>
        <Inputs
          type="text"
          placeholder="Digite seu usuário"
          value={usuario}
          onChange={(e) => setUsuario(e.target.value)}
          required
          disabled={isLoading}
        />

        <label className='label' htmlFor="senha">Senha:</label>
        <Inputs
          type="password"
          placeholder="Digite sua senha"
          value={senha}
          onChange={(e) => setSenha(e.target.value)}
          required
          disabled={isLoading}
        />

        <p id="mensagem-login" className={mensagem.includes('Bem-vindo') ? 'mensagem-sucesso' : 'mensagem-erro'}>
          {mensagem}
        </p>

        <button 
          className="btn-login" 
          type='submit'
          disabled={isLoading}
        >
          {isLoading ? 'Carregando...' : 'Entrar'}
        </button>
      </form>
    </main>
  );
}

export default Login;