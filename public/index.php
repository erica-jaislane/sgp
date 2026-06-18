<?php

// Tipagem estrita ativada — padrão do projeto
declare(strict_types=1);

/**
 * index.php — Ponto de entrada do Sistema de Gestão de Pedidos (SGP)
 *
 * Este arquivo fica na pasta public/ e é o único que o navegador acessa.
 * Ele conecta todas as camadas do sistema:
 * Controller → Domain → Infrastructure → Banco de Dados
 *
 * Nada de regra de negócio aqui — só configuração e inicialização.
 */

// --- Carregamento das Classes ---
// Como não usamos um gerenciador de pacotes (Composer) aqui,
// importamos cada arquivo manualmente com require_once.
// O ".." sobe um nível (sai de public/ e entra em src/)
require_once __DIR__ . '/../src/Domain/Pedido.php';
require_once __DIR__ . '/../src/Domain/PedidoRepositoryInterface.php';
require_once __DIR__ . '/../src/Infrastructure/PedidoRepository.php';
require_once __DIR__ . '/../src/Controller/PedidoController.php';

// Importa as classes que vamos usar neste arquivo
use App\Controller\PedidoController;
use \PDO;

// --- Configuração do Cabeçalho HTTP ---
// Informa ao navegador que a resposta será em formato JSON
header('Content-Type: application/json');

// --- Conexão com o Banco de Dados ---
// Aqui ficam as configurações do MySQL.
// Em produção, esses valores viriam de variáveis de ambiente (.env),
// nunca expostos diretamente no código.
$host   = 'localhost';
$banco  = 'sgp_db';
$usuario = 'root';
$senha  = '';

try {
    // Cria a conexão PDO com o MySQL
    // O PDO é o driver padrão do PHP para bancos de dados relacionais
    $conexao = new PDO(
        "mysql:host={$host};dbname={$banco};charset=utf8",
        $usuario,
        $senha
    );

    // Configura o PDO para lançar exceções em caso de erro no banco
    // Sem isso, erros SQL passariam silenciosamente sem aviso
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (\PDOException $e) {
    // Se a conexão falhar, retorna erro 503 (serviço indisponível)
    // e encerra o script imediatamente
    http_response_code(503);
    echo json_encode([
        "status"   => "Erro",
        "mensagem" => "Não foi possível conectar ao banco de dados.",
        "detalhe"  => $e->getMessage()
    ]);
    exit;
}

// --- Dados de Teste do Pedido ---
// Simula um pedido com 2 itens chegando via requisição.
// Em um sistema real, esses dados viriam do corpo da requisição HTTP (POST),
// por exemplo: $dados = json_decode(file_get_contents('php://input'), true);
$dadosDoPedido = [
    'id'    => 1001,
    'itens' => [
        ['preco' => 50.00, 'quantidade' => 2],  // R$ 100,00
        ['preco' => 30.00, 'quantidade' => 1],  // R$  30,00
    ]
    // Total esperado: R$ 130,00
];

// --- Execução ---
// Instancia o controller passando a conexão já criada
// e dispara o método de criação do pedido
$controller = new PedidoController($conexao);
$controller->criarPedido($dadosDoPedido);