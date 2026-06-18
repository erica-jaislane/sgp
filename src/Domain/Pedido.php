<?php

// Ativa a verificação rigorosa de tipos no PHP.
// Isso evita que o sistema aceite, por exemplo, uma string no lugar de um número.
declare(strict_types=1);

// Define o "endereço" desta classe dentro do projeto.
// É como uma pasta lógica: qualquer arquivo que precisar usar Pedido
// vai importar daqui: "use App\Domain\Pedido"
namespace App\Domain;

/**
 * Classe Pedido — a entidade central do sistema.
 *
 * Aqui vivem os dados e as regras do pedido.
 * Quem tem os dados é quem executa a ação — isso é o padrão GRASP Especialista.
 * Nenhuma classe de fora precisa calcular o total do pedido; o próprio Pedido faz isso.
 */
class Pedido
{
    // Identificador único do pedido (número inteiro)
    private int $id;

    // Valor total calculado automaticamente a partir dos itens
    private float $total = 0.0;

    // Lista de itens do pedido. Cada item tem 'preco' e 'quantidade'
    private array $itens = [];

    /**
     * Construtor: chamado toda vez que criamos um novo Pedido.
     * Recebe um array com os dados vindos do controller.
     *
     * Se não vier um ID nos dados, o sistema gera um número aleatório
     * entre 1000 e 9999 (útil para testes).
     */
    public function __construct(array $dados)
    {
        // Usa o operador "??" para pegar o ID se existir, ou gerar um aleatório
        $this->id = $dados['id'] ?? rand(1000, 9999);

        // Pega a lista de itens; se não vier nenhum, usa um array vazio
        $this->itens = $dados['itens'] ?? [];

        // Calcula e armazena o total logo na criação do pedido
        $this->total = $this->calcularTotal();
    }

    /**
     * Percorre todos os itens e soma o subtotal de cada um.
     * Subtotal = preço × quantidade de cada produto.
     *
     * Retorna o valor final como número decimal (float).
     */
    public function calcularTotal(): float
    {
        $subtotal = 0.0;

        foreach ($this->itens as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }

        return $subtotal;
    }

    // --- Métodos de leitura (Getters) ---
    // Permitem que outras classes leiam os dados sem modificá-los diretamente.
    // Isso protege os atributos privados — princípio do encapsulamento.

    /** Retorna o ID do pedido */
    public function getId(): int
    {
        return $this->id;
    }

    /** Retorna o total calculado do pedido */
    public function getTotal(): float
    {
        return $this->total;
    }
}