<?php

// Tipagem estrita ativada em todo o projeto
declare(strict_types=1);

// Esta classe mora na camada de Aplicação (Controller).
// Ela é o "porteiro" do sistema: recebe a requisição e distribui o trabalho.
namespace App\Controller;

// Importa as classes que este controller vai precisar usar
use App\Domain\Pedido;
use App\Infrastructure\PedidoRepository;
use \PDO;

/**
 * Classe PedidoController — o orquestrador da criação de pedidos.
 *
 * Aplica o princípio SOLID SRP (Responsabilidade Única):
 * esta classe tem UMA única função — receber a requisição HTTP,
 * montar os objetos necessários e devolver uma resposta.
 *
 * Ela NÃO calcula totais (isso é trabalho do Pedido).
 * Ela NÃO executa SQL (isso é trabalho do Repository).
 * Ela apenas coordena quem faz o quê.
 */
class PedidoController
{
    // Guarda o repositório que vai cuidar da persistência
    private PedidoRepository $repository;

    /**
     * Construtor: recebe a conexão PDO e já monta o repositório interno.
     *
     * Assim o controller não precisa saber como a conexão foi criada —
     * ele recebe pronta e delega ao repositório o trabalho com o banco.
     */
    public function __construct(PDO $conexao)
    {
        $this->repository = new PedidoRepository($conexao);
    }

    /**
     * Método principal: cria um pedido a partir dos dados recebidos.
     *
     * O fluxo acontece em três passos simples:
     * 1. Instancia a entidade Pedido com os dados (ela mesma calcula o total)
     * 2. Pede ao repositório que salve o pedido no banco
     * 3. Devolve a resposta HTTP em formato JSON para quem fez a requisição
     *
     * @param array $dados — dados vindos da requisição (id, itens, preços)
     */
    public function criarPedido(array $dados): void
    {
        // Cria o objeto Pedido — neste momento o total já é calculado
        // internamente pela própria entidade (GRASP Especialista)
        $pedido = new Pedido($dados);

        // Delega a persistência ao repositório e guarda o resultado
        $sucesso = $this->repository->salvar($pedido);

        // Verifica se o salvamento funcionou e responde de acordo
        if ($sucesso) {
            // Código 201 = "Created" — pedido criado com sucesso
            http_response_code(201);

            echo json_encode([
                "status"    => "Sucesso",
                "mensagem"  => "Pedido criado com sucesso!",
                "pedido_id" => $pedido->getId(),
                "total"     => $pedido->getTotal()
            ]);
        } else {
            // Código 500 = erro interno no servidor
            http_response_code(500);

            echo json_encode([
                "status"   => "Erro",
                "mensagem" => "Falha ao salvar no banco de dados."
            ]);
        }
    }
}