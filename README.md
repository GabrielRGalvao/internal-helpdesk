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

- **Gestão de Chamados** — Criação, listagem, visualização e edição de tickets
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

**Atribuição Manual**
Crie um ticket, selecione *Atribuição Manual* e escolha um atendente específico. O ticket aparecerá na listagem vinculado diretamente a ele.

**Distribuição Automática**
Selecione *Atribuição Automática* ao criar um ticket. O backend contará os chamados abertos de cada atendente e atribuirá ao que tiver menos, equilibrando a carga de trabalho de forma inteligente.

---

## Estrutura do Projeto

app/

├── Http/Controllers/

│   ├── TicketController.php     # CRUD e lógica de atribuição automática

│   └── AttendantController.php

├── Models/

│   ├── Ticket.php

│   └── Attendant.php

database/

├── migrations/                  # Versionamento do esquema do banco

└── seeders/                     # População inicial de dados

resources/

└── js/

└── Pages/                   # Componentes de página Vue.js

---

**Autor:** Gabriel Galvão
**Desafio:** Codificar Sistemas Tecnológicos — Avaliação Técnica Full Stack