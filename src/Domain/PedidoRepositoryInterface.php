<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Interface PedidoRepositoryInterface — o "contrato" de persistência.
 *
 * Aplica o princípio SOLID DIP (Inversão de Dependência):
 * as regras de negócio dependem deste contrato genérico,
 * nunca do MySQL ou qualquer banco diretamente.
 */
interface PedidoRepositoryInterface
{
    /**
     * Qualquer repositório concreto DEVE implementar este método.
     * Recebe um Pedido pronto e retorna true se salvou, false se falhou.
     */
    public function salvar(Pedido $pedido): bool;
}