# SGP — Sistema de Gestão de Pedidos

Projeto desenvolvido para a disciplina de **Arquitetura de Software** da Universidade Católica de Brasília (UCB), sob orientação do professor Oscar Galdino.

---

## Sobre o projeto

O SGP é um sistema backend desenvolvido em **PHP 8**, criado com o objetivo de colocar em prática três pilares do design de software profissional: os princípios **SOLID**, as boas práticas de **Clean Code** e os padrões de atribuição de responsabilidade **GRASP**.

A ideia central não é apenas fazer o sistema funcionar — é fazê-lo funcionar de forma organizada, legível e preparada para crescer sem quebrar o que já existe.

O sistema simula o fluxo de criação de pedidos em um ambiente de vendas, onde cada camada do código tem uma responsabilidade clara e isolada.

---

## O que eu aprendi construindo isso

Antes de estudar esses conceitos, eu escrevia código que funcionava, mas que era difícil de entender depois de alguns dias. Aplicar SOLID me fez perceber que o problema não era a lógica — era a organização.

Separar o que **calcula** do que **salva** do que **controla** tornou o código muito mais fácil de ler e de modificar. Se precisar trocar o banco de dados amanhã, por exemplo, só um arquivo muda — e o resto do sistema nem percebe.

---

## Tecnologias utilizadas

- PHP 8.x com tipagem estrita (`declare(strict_types=1)`)
- MySQL 8.0 via PDO (PHP Data Objects)
- Arquitetura em camadas (Controller, Domain, Infrastructure)
- Padrão Repository para isolamento da persistência

---

## Estrutura de pastas

```
sgp/
├── public/
│   └── index.php               # Ponto de entrada do sistema
└── src/
    ├── Controller/
    │   └── PedidoController.php  # Recebe a requisição e coordena o fluxo
    ├── Domain/
    │   ├── Pedido.php            # Entidade central com regras de negócio
    │   └── PedidoRepositoryInterface.php  # Contrato de persistência
    └── Infrastructure/
        └── PedidoRepository.php  # Implementação concreta com MySQL
```

---

## Como cada princípio foi aplicado

### SOLID

**S — Responsabilidade Única (SRP)**
Cada classe tem um único propósito. O `PedidoController` só coordena o fluxo. O `Pedido` só guarda dados e calcula totais. O `PedidoRepository` só acessa o banco. Nenhuma delas faz o trabalho da outra.

**O — Aberto/Fechado (OCP)**
O sistema foi pensado para receber novas regras de negócio (como diferentes tipos de desconto) sem precisar modificar o código que já funciona — apenas adicionando novos módulos.

**L — Substituição de Liskov (LSP)**
Qualquer implementação futura da `PedidoRepositoryInterface` pode substituir o `PedidoRepository` atual sem quebrar o sistema.

**I — Segregação de Interfaces (ISP)**
A interface `PedidoRepositoryInterface` é pequena e específica — define apenas o que é necessário, sem forçar implementações desnecessárias.

**D — Inversão de Dependência (DIP)**
As regras de negócio dependem de contratos (interfaces), não de implementações concretas. O domínio nunca conhece o MySQL diretamente — ele conhece apenas a interface.

---

### Clean Code

- **Nomes descritivos:** variáveis e métodos dizem exatamente o que fazem. `calcularTotal()` é mais claro que `calc()`, `dadosDoPedido` é mais claro que `$d`.
- **Funções curtas:** cada método faz uma coisa só e cabe em poucas linhas.
- **Organização lógica:** o código segue uma narrativa — quem lê sabe exatamente onde cada responsabilidade começa e termina.

---

### GRASP

**Especialista na Informação**
A classe `Pedido` calcula o próprio total porque ela é quem tem os dados dos itens. Não faz sentido pedir para outra classe fazer isso de fora.

**Alta Coesão**
Cada classe é focada. Nenhuma acumula responsabilidades que não são dela.

**Baixo Acoplamento**
As camadas se comunicam por contratos, não por dependências diretas. Isso significa que uma mudança em uma camada não força mudanças nas outras.

---

## Como rodar o projeto

### Pré-requisitos

- PHP 8.x instalado
- MySQL rodando localmente
- Servidor local (XAMPP, Laragon ou similar)

### Passos

**1.** Clone o repositório:
```bash
git clone https://github.com/seu-usuario/sgp.git
```

**2.** Crie o banco de dados no MySQL:
```sql
CREATE DATABASE sgp_db;

USE sgp_db;

CREATE TABLE pedidos (
    id INT PRIMARY KEY,
    total DECIMAL(10,2) NOT NULL
);
```

**3.** Ajuste as configurações de conexão no arquivo `public/index.php`:
```php
$host    = 'localhost';
$banco   = 'sgp_db';
$usuario = 'root';
$senha   = '';
```

**4.** Acesse pelo navegador:
```
http://localhost/sgp/public/index.php
```

**5.** Se tudo estiver certo, a resposta será:
```json
{
    "status": "Sucesso",
    "mensagem": "Pedido criado com sucesso!",
    "pedido_id": 1001,
    "total": 130
}
```

---

## Autora

**Erica Jaislane Campos Fernandes**
Disciplina: Arquitetura de Software — GPE17N50290
Universidade Católica de Brasília — UCB, 2026
