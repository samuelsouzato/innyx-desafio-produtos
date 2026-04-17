# 📦 Sistema de Gestão de Produtos - Fullstack

## Visão Geral

Este projeto é uma **API RESTful** desenvolvida com **Laravel 11**, **MySQL** e **Docker (Sail)**, integrada a um frontend em **Vue 3**.  
A aplicação segue padrões de arquitetura profissional, com autenticação **Sanctum**, gerenciamento de estado com **Pinia** e interface responsiva utilizando **Tailwind CSS 4** e **PrimeVue**.

## 📂 Estrutura do Projeto

```
/ 
├── app/
│   ├── Http/Controllers/  # Controllers da API (Produtos e Auth)
│   └── Models/            # Entidades do Banco (Product, Category)
├── database/
│   ├── migrations/        # Estrutura do banco de dados
│   └── seeders/           # Dados de teste (Admin e Categorias)
├── routes/
│   └── api.php            # Definição dos endpoints da API
├── docker-compose.yml     # Configuração da infra (Sail)
│
└── frontend/              # Application VueJS
    ├── src/
    │   ├── services/      # Comunicação com API (Axios)
    │   ├── stores/        # Pinia - Auth
    │   └── views/         # Páginas (Login, Dashboard, Listagem)
    └── package.json       # Dependências do Front
```

## Tecnologias Utilizadas

- PHP 8.5 / Laravel 11
- MySQL (Docker)
- Laravel Sail & Sanctum
- Vue 3 (Composition API)
- TypeScript & Vite
- Tailwind CSS 4
- PrimeVue

# Como Executar

# 1. Pré-requisitos:
- **Docker Desktop** rodando com WSL2 habilitado.
- **Node.js 22** instalado no ambiente Linux/WSL.

# 2. Clonar o repositório:
```bash
git clone [https://github.com/seu-usuario/seu-repositorio.git](https://github.com/seu-usuario/seu-repositorio.git)
cd seu-repositorio
```

# 3. Configurar ambiente (.env):
```bash
cp .env.example .env
```

# 4. Instalar dependências e subir containers:
```bash
# Instala o Composer sem precisar de PHP local
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# Sobe os containers do Docker
./vendor/bin/sail up -d
```

# 5. Inicializar Banco de Dados:
```bash
# Gera chave da aplicação
./vendor/bin/sail artisan key:generate

# Limpa o banco, cria tabelas e insere dados de teste
./vendor/bin/sail artisan migrate:fresh --seed
```

# 6. Rodar o Frontend:
```bash
cd frontend
npm install
npm run dev
```

# 7. Credenciais de Acesso:

- **URL do Sistema:** http://localhost:5173
- **Usuário Admin:** admin@innyx.com
- **Senha:** admin1234

# 8. Comandos Úteis do Sail:

- Parar tudo: `./vendor/bin/sail stop`
- Reiniciar containers: `./vendor/bin/sail restart`
- Abrir terminal do banco: `./vendor/bin/sail mysql`

# 9. Endpoints da API (Testar no Postman/Insomnia)

- **POST** `/api/v1/login` -> Autenticação
- **GET** `/api/v1/products` -> Listar produtos
- **POST** `/api/v1/products` -> Criar produto
- **PUT** `/api/v1/products/{id}` -> Editar produto
- **DELETE** `/api/v1/products/{id}` -> Deletar produto

# 10. Rodar Testes:
```bash
# Backend
./vendor/bin/sail artisan test
```

## OBS: Se reiniciar o PC, basta rodar `./vendor/bin/sail up -d` na raiz e `npm run dev` na pasta frontend!