# Internal Helpdesk

> Sistema interno de controle de chamados de suporte, construído com Laravel, Inertia.js e Vue.js.

Aplicação web que permite que funcionários abram chamados de suporte e que a equipe técnica gerencie, atribua e resolva esses chamados com distribuição automática de carga de trabalho.

---

## Stack Tecnológica

| Camada | Tecnologia | Motivo |
|---|---|---|
| Backend | Laravel 10 | Framework PHP e roteamento expressivo |
| Bridge | Inertia.js | Elimina a necessidade de uma API REST separada |
| Frontend | Vue.js 3 | Componentes reativos com Composition API |
| Estilização | Tailwind CSS | CSS utilitário para interface consistente e rápida |
| Banco de dados | SQLite | Banco sem configuração de servidor, ideal para desenvolvimento local |

> **Por que Inertia.js?** Evita atrito desnecessário. O Inertia conecta o Laravel e o Vue de forma transparente — o backend retorna componentes Vue diretamente, funcionando como um SPA moderno sem a complexidade de duas aplicações separadas.

---

## Funcionalidades

- **Gestão de Chamados** — Interface de cadastro, listagem detalhada, visualização e edição dinâmica de tickets.
- **Filtro & Busca** — Busca reativa, permite filtrar chamados por título, descrição ou nome do atendente.
- **Data e Hora** — Exibição de data e hora da abertura do chamado no grid principal, no padrão (`pt-BR`).
- **Modais de Ação** — Visualização de detalhes e Edição de status e troca de responsável.
- **Níveis de Prioridade** — Classificação em baixa, média e alta prioridade
- **Controle de Status** — Aberto, em andamento, resolvido e fechado
- **Atribuição Manual** — Escolha direta do responsável pelo atendimento
- **Distribuição Automática** — Atribui ao atendente com menos chamados abertos no momento
- **Cadastro de Atendentes** — Atendentes pré-cadastrados via seeder disponíveis para atribuição

---

## Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM

---

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/GabrielRGalvao/internal-helpdesk
cd internal-helpdesk
```

### 2. Instalar dependências

```bash
composer install
npm install
```

### 3. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Abra o arquivo `.env` e configure a conexão com o banco:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database/database.sqlite
```

> **Atenção:** Crie o arquivo SQLite antes de rodar as migrations: `touch database/database.sqlite`

### 4. Rodar migrations e seeders

```bash
php artisan migrate --seed
```


### 5. Iniciar os servidores

Abra dois terminais e rode:

```bash
# Terminal 1 — Backend Laravel
php artisan serve

# Terminal 2 — Compilador Vite
npm run dev
```

Acesse a aplicação em: **http://127.0.0.1:8000**

---

## Atendentes Padrão

O seeder cria os seguintes atendentes para testes:

| Nome          | Departamento |
| Alice Freeman | Infraestrutura |
| John Doe      | Suporte |
| Sarah Jenkins | Suporte |

---

## Roteiro de Testes

**Fluxo de Busca Instantânea**
Digite uma palavra-chave (como o nome de um atendente ou termo da descrição) na barra de pesquisa localizada acima da tabela. A listagem se adaptará na mesma hora.

**Fluxo de Distribuição Automática**
Crie um ticket marcando a opção Atribuição Automática. O sistema fará a contagem em tempo real e encaminhará o chamado para o operador menos sobrecarregado no momento.

**Fluxo de Modificação (Edição)**
Clique no botão Editar de qualquer chamado na lista. O modal abrirá permitindo que você altere o progresso do chamado (ex: de "Aberto" para "Em Andamento") ou mude o operador responsável pelo ticket.

---

## Estrutura do Projeto

app/
├── Http/Controllers/
│   ├── TicketController.php     # Lógica central do CRUD, regras de negócio e distribuição
│   └── AttendantController.php  # Fornecimento de dados dos operadores via API
├── Models/
│   ├── Ticket.php               # Modelo de chamados e relacionamentos
│   └── Attendant.php            # Modelo de atendentes e escopos de contagem
database/
├── migrations/                  # Estrutura relacional do banco de dados
└── seeders/                     # População automatizada do ambiente de testes
resources/
└── js/
    └── Pages/
        └── Tickets/
            └── Index.vue        # Interface reativa unificada

---

**Autor:** Gabriel Galvão