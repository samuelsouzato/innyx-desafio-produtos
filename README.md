# 📦 Sistema de Gestão de Produtos - Fullstack

## Visão Geral

Este projeto é uma **API RESTful** desenvolvida com **Laravel 11**, **MySQL** e **Docker (Sail)**, integrada a um frontend em **Vue 3**.  
A aplicação segue padrões de arquitetura profissional, utilizando o **Service Pattern** para isolar a lógica de negócio, autenticação **Sanctum**, gerenciamento de estado com **Pinia** e interface responsiva utilizando **Tailwind CSS 4** e **PrimeVue**.

## 📂 Estrutura do Projeto

```text
/
├── app/
│   ├── Http/Controllers/  # Controllers da API v1 (Auth, Product, Category)
│   ├── Services/          # Service Pattern (ProductService - Lógica de Negócio)
│   └── Models/            # Entidades do Banco (Product, Category, User)
├── database/
│   ├── migrations/        # Estrutura do banco (campos nullable configurados)
│   └── seeders/           # Dados de teste (Admin e 9 Categorias resumidas)
├── routes/
│   └── api.php            # Definição dos endpoints da API protegidos
├── compose.yaml           # Configuração da infra Docker (Sail)
│
└── frontend/
    ├── src/
    │   ├── services/      # Comunicação com API (Axios)
    │   ├── stores/        # Pinia (Gerenciamento de Auth e Estado)
    │   ├── router/        # Gerenciamento de rotas SPA
    │   └── views/         # Páginas (LoginView, DashboardView)
    └── package.json       # Dependências (PrimeVue, Tailwind 4, Pinia)
```

## Tecnologias Utilizadas

- **Backend:** PHP 8.5 / Laravel 13 
- **Banco de Dados:** MySQL 8.4 (Docker)  
- **Infra:** Laravel Sail & Adminer (Gestão Web do Banco)  
- **Frontend:** VueJS 3 (Composition API) / Vite  
- **TypeScript:** Tipagem estática no frontend  
- **Gerenciamento de Estado:** Pinia  
- **Estilização:** Tailwind CSS 4 & PrimeVue  

---

## Como Executar (Passo a Passo Completo):

# Pré-requesitos:
- **Docker Desktop** rodando com WSL2 habilitado.

### Ambiente  Linux (WSL2)

No Windows, abra o PowerShell como Administrador:

```bash
wsl --install
```

(Reinicie o computador após a instalação. Ao abrir o Ubuntu pela primeira vez, configure usuário e senha.)

#### Garantindo WSL 2

```bash
wsl --set-default-version 2
wsl -l -v
wsl --set-version Ubuntu 2
```

---

### Configurar o Docker Desktop

- Instale o Docker Desktop  
- Vá em **Settings > General** → ative *Use the WSL 2 based engine*  
- Vá em **Resources > WSL Integration** → ative o Ubuntu  
- Clique em *Apply & Restart*

---

### 1. Clonar o Repositório (dentro do Linux)

```bash
cd ~

git clone https://github.com/samuelsouzato/innyx-desafio-produtos.git

cd innyx-desafio-produtos
```

---

### 2. Configurar ambiente

```bash
cp .env.example .env
```

---

### 3. Instalar dependências e subir containers

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up -d
```
- Demora em torno de 15 minutos.
---

### 4. Inicializar Banco de Dados e Storage

```bash
./vendor/bin/sail artisan key:generate

./vendor/bin/sail artisan migrate:refresh --seed

./vendor/bin/sail artisan storage:link
```

---

### 5. Rodar o Frontend

```bash
./vendor/bin/sail npm --prefix frontend install
./vendor/bin/sail npm --prefix frontend run dev -- --host
```

---

### 6. Credenciais de Acesso

- **URL:** http://localhost:5173  
- **Usuário:** admin@innyx.com  
- **Senha:** admin1234  

---

## Funcionalidades e Diferenciais

- **UX Inteligente (Alimentos):**  
  Categoria "Alimentos" exige validade automaticamente.

- **Service Pattern:**  
  Lógica centralizada no `ProductService`, incluindo validações e upload de imagens.

- **Tratamento de Preço:**  
  Aceita vírgula (BR) e converte para padrão SQL automaticamente.

- **Dashboard Premium:**  
  Paginação (5 itens), busca com debounce e filtros inteligentes.

- **Flexibilidade:**  
  Campos opcionais (nullable), respeitando regras de negócio.

---

## Comandos Úteis do Sail

```bash
./vendor/bin/sail stop
./vendor/bin/sail restart
./vendor/bin/sail mysql
```

- **Adminer:** http://localhost:8080  
    - Server/Host: mysql  
    - User: sail  
    - Pass: password  

---

## Endpoints da API (v1)

- **POST** `/api/v1/login` → Autenticação e token  
- **GET** `/api/v1/products` → Listagem (paginado/busca)  
- **POST** `/api/v1/products` → Criar produto  
- **PUT** `/api/v1/products/{id}` → Atualizar produto  
- **DELETE** `/api/v1/products/{id}` → Remover produto  

---

## Observação

Se reiniciar o PC:

```bash
cd ~/innyx-desafio-produtos

./vendor/bin/sail up -d

./vendor/bin/sail npm --prefix frontend run dev -- --host
```