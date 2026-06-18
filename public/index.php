<?php
declare(strict_types=1);

/**
 * index.php — Ponto de entrada do Sistema de Gestão de Pedidos (SGP)
 * Conecta todas as camadas: Controller → Domain → Infrastructure → Banco
 */

// Carrega cada arquivo uma única vez com require_once
require_once __DIR__ . '/../src/Domain/Pedido.php';
require_once __DIR__ . '/../src/Domain/PedidoRepositoryInterface.php';
require_once __DIR__ . '/../src/Infrastructure/PedidoRepository.php';
require_once __DIR__ . '/../src/Controller/PedidoController.php';

use App\Controller\PedidoController;

// Informa ao navegador que a resposta será em JSON
header('Content-Type: application/json');

// Configurações do banco de dados
$host    = 'localhost';
$banco   = 'sgp_db';
$usuario = 'root';
$senha   = '';

try {
    // Cria a conexão com o MySQL via PDO
    $conexao = new PDO(
        "mysql:host={$host};dbname={$banco};charset=utf8",
        $usuario,
        $senha
    );
    // Configura o PDO para lançar exceções em caso de erro
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    http_response_code(503);
    echo json_encode([
        "status"   => "Erro",
        "mensagem" => "Não foi possível conectar ao banco de dados.",
        "detalhe"  => $e->getMessage()
    ]);
    exit;
}

// Dados de teste simulando um pedido com 2 itens
// Total esperado: R$ 130,00 (50x2 + 30x1)
$dadosDoPedido = [
    'id'    => 1001,
    'itens' => [
        ['preco' => 50.00, 'quantidade' => 2],
        ['preco' => 30.00, 'quantidade' => 1],
    ]
];

// Instancia o controller e cria o pedido
$controller = new PedidoController($conexao);
$controller->criarPedido($dadosDoPedido);