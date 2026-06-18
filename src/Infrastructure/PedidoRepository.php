<?php

// Tipagem estrita ativada — garante que os tipos declarados sejam respeitados
declare(strict_types=1);

// Esta classe mora na camada de Infraestrutura.
// É aqui que os "detalhes técnicos" vivem — conexão, SQL, banco de dados.
// As regras de negócio nunca chegam até aqui diretamente.
namespace App\Infrastructure;

// Importa a entidade Pedido e o contrato que esta classe deve cumprir
use App\Domain\Pedido;
use App\Domain\PedidoRepositoryInterface;

// Importa o PDO do PHP — o driver padrão para conexão com bancos de dados
use \PDO;

/**
 * Classe PedidoRepository — a implementação concreta do repositório.
 *
 * Esta classe "assina o contrato" da interface PedidoRepositoryInterface,
 * ou seja, ela é OBRIGADA a ter o método salvar().
 *
 * Aqui o SOLID DIP se completa na prática:
 * - O domínio (Pedido, Interface) não sabe nada sobre MySQL.
 * - Esta classe sabe, e é a única responsável por isso.
 *
 * Se amanhã precisar trocar MySQL por PostgreSQL, só este arquivo muda.
 */
class PedidoRepository implements PedidoRepositoryInterface
{
    // Guarda a conexão ativa com o banco de dados
    private PDO $conexao;

    /**
     * Construtor: recebe a conexão PDO pronta de fora.
     *
     * Isso é injeção de dependência — a classe não cria a própria conexão,
     * ela recebe de quem a instancia. Facilita testes e troca de ambiente.
     */
    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
    }

    /**
     * Implementa o método exigido pelo contrato: salvar um pedido no banco.
     *
     * Usa prepared statements (prepare + execute) para evitar SQL Injection.
     * Os valores reais só entram na query na hora do execute(), de forma segura.
     *
     * Retorna true se o INSERT funcionou, false se algo deu errado.
     */
    public function salvar(Pedido $pedido): bool
    {
        // Monta a query com parâmetros nomeados (:id e :total)
        // em vez de valores diretos — isso é a proteção contra SQL Injection
        $sql = "INSERT INTO pedidos (id, total) VALUES (:id, :total)";

        // Prepara a query no banco antes de executar
        $stmt = $this->conexao->prepare($sql);

        // Executa passando os valores reais vinculados aos parâmetros
        return $stmt->execute([
            ':id'    => $pedido->getId(),
            ':total' => $pedido->getTotal()
        ]);
    }
}