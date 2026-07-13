# WhatsApp Suite

Solução completa para gerenciamento WhatsApp com API Go + Manager Laravel + PostgreSQL.

![License](https://img.shields.io/badge/license-MIT-green)
![Docker](https://img.shields.io/badge/docker-ready-blue)
![PHP](https://img.shields.io/badge/php-8.2-purple)
![Go](https://img.shields.io/badge/go-1.22-cyan)

## Demo

| Componente | URL | Descrição |
|------------|-----|-----------|
| **Manager** | `http://localhost:8080` | Painel web Laravel 12 + Tailwind |
| **API Go** | `http://localhost:8084` | Backend Go com whatsmeow |
| **PostgreSQL** | `localhost:5432` | Database compartilhado |

## Funcionalidades

- Gerenciamento de múltiplas instâncias WhatsApp
- Conexão via QR Code ou código de pareamento
- Envio de mensagens (texto, imagem, vídeo, documento, áudio)
- Envio de contatos e localização
- Webhooks para eventos
- Configurações por instância (rejeitar chamadas, ignorar grupos, etc.)
- Tema Dark/Light
- Autenticação por API Key

## Estrutura do Projeto

```
whatsapp-suite/
├── api/                          # WhatsApp API Go
│   ├── Dockerfile
│   ├── cmd/api/                  # Entry point
│   ├── internal/                 # Código Go
│   │   ├── http/handlers/        # Handlers HTTP
│   │   ├── message/              # Serviço de mensagens
│   │   ├── whatsapp/             # Cliente WhatsApp
│   │   └── config/               # Configurações
│   └── go.mod
├── manager/                      # WhatsApp Manager (Laravel)
│   ├── Dockerfile
│   ├── nginx.conf
│   ├── app/Http/Controllers/     # Controllers
│   ├── app/Models/               # Models
│   ├── app/Services/             # Services
│   ├── resources/views/          # Templates Blade
│   └── routes/web.php
├── docker-compose.yml            # Compose completo
├── .env.docker                   # Template de variáveis
└── README.md
```

---

## Instalação

### Opção 1: Docker Compose (Recomendado)

#### Pré-requisitos
- Docker Desktop ou Docker Engine
- Docker Compose v2

#### Passo a passo

```bash
# 1. Clonar o repositório
git clone https://github.com/jadersistemas/whatsapp-suite.git
cd whatsapp-suite

# 2. Criar arquivo .env
cp .env.docker .env

# 3. Editar variáveis de ambiente
# Windows:
notepad .env
# Linux/Mac:
nano .env
```

#### Variáveis de ambiente (.env)

```env
# App
APP_NAME=WhatsAppManager
APP_URL=http://localhost:8080
APP_DEBUG=false
APP_PORT=8080

# API Go
API_PORT=8084
TZ=America/Sao_Paulo
LOG_LEVEL=info
JWT_EXPIRES_IN=0
JWT_SECRET=sua-chave-segura-aqui

# Database
DB_DATABASE=whatsapp_manager
DB_USERNAME=whatsapp
DB_PASSWORD=sua-senha-segura-aqui

# WhatsApp API
WHATSAPP_API_KEY=sua-api-key-segura-aqui
```

```bash
# 4. Subir todos os serviços
docker compose up -d --build

# 5. Aguardar serviços iniciarem (~30 segundos)
docker compose ps

# 6. Gerar APP_KEY do Laravel
docker compose exec manager php artisan key:generate

# 7. Rodar migrations
docker compose exec manager php artisan migrate --force
```

#### Acessar o sistema

- **Manager:** http://localhost:8080
- **API Health:** http://localhost:8084/health

#### Comandos úteis Docker

```bash
# Ver logs
docker compose logs -f

# Logs apenas da API
docker compose logs -f api

# Logs apenas do Manager
docker compose logs -f manager

# Reiniciar um serviço
docker compose restart api

# Parar tudo
docker compose down

# Parar e remover volumes (ATENÇÃO: apaga dados)
docker compose down -v
```

---

### Opção 2: EasyPanel

#### Pré-requisitos
- Servidor VPS com Ubuntu 22.04+
- EasyPanel instalado
- Docker instalado

#### Passo a passo

**1. Acessar o EasyPanel**
```
https://seu-servidor:8096
```

**2. Criar um novo projeto**
- Clique em "New Project"
- Nome: `whatsapp-suite`

**3. Adicionar o serviço API**
- Clique em "New Service" → "Docker Compose"
- Cole o conteúdo do `docker-compose.yml` ou configure manualmente:

**Serviço API:**
```yaml
api:
  image: golang:1.22-alpine
  build:
    context: ./api
    dockerfile: Dockerfile
  container_name: whatsapp-api
  restart: unless-stopped
  environment:
    - DOCKER_ENV=true
    - SERVER_PORT=8084
    - DATABASE_URL=postgres://whatsapp:SENHA@postgres:5432/whatsapp_api?sslmode=disable
    - AUTHENTICATION_GLOBAL_AUTH_TOKEN=SUA_API_KEY
  ports:
    - "8084:8084"
```

**4. Adicionar o serviço Manager**
```yaml
manager:
  build:
    context: ./manager
    dockerfile: Dockerfile
  container_name: whatsapp-manager
  restart: unless-stopped
  environment:
    - APP_URL=https://manager.seudominio.com
    - DB_CONNECTION=pgsql
    - DB_HOST=postgres
    - DB_DATABASE=whatsapp_manager
    - DB_USERNAME=whatsapp
    - DB_PASSWORD=SENHA
    - WHATSAPP_API_KEY=SUA_API_KEY
  ports:
    - "8080:80"
```

**5. Adicionar PostgreSQL**
```yaml
postgres:
  image: postgres:17-alpine
  container_name: whatsapp-postgres
  restart: unless-stopped
  environment:
    POSTGRES_USER: whatsapp
    POSTGRES_PASSWORD: SENHA
    POSTGRES_DB: whatsapp_manager
  volumes:
    - pgdata:/var/lib/postgresql/data
```

**6. Configurar domínio (opcional)**
- No EasyPanel, vá em "Networks"
- Adicione proxy reverso para `manager.seudominio.com` → `manager:80`

**7. Deploy**
- Clique em "Deploy" no EasyPanel
- Aguarde o build concluir

**8. Pós-deploy**
```bash
# Acessar o container do Manager
docker exec -it whatsapp-manager bash

# Rodar migrations
php artisan migrate --force

# Gerar APP_KEY
php artisan key:generate
```

---

### Opção 3: Portainer

#### Pré-requisitos
- Portainer CE ou EE instalado
- Docker Swarm ou Standalone

#### Passo a passo

**1. Acessar o Portainer**
```
https://seu-servidor:9443
```

**2. Criar Stack**
- Vai em "Stacks" → "Add Stack"
- Nome: `whatsapp-suite`
- Método: "Web Editor"

**3. Colar docker-compose.yml**

Cole o conteúdo completo do `docker-compose.yml`:

```yaml
version: '3.8'

services:
  api:
    build:
      context: https://github.com/jadersistemas/whatsapp-suite.git#main:api
      dockerfile: Dockerfile
    container_name: whatsapp-api
    restart: unless-stopped
    environment:
      - DOCKER_ENV=true
      - SERVER_PORT=8084
      - DATABASE_URL=postgres://whatsapp:${DB_PASSWORD}@postgres:5432/whatsapp_api?sslmode=disable
      - AUTHENTICATION_GLOBAL_AUTH_TOKEN=${WHATSAPP_API_KEY}
      - AUTHENTICATION_JWT_SECRET=${JWT_SECRET}
      - WHATSAPP_AUTO_RECONNECT=true
    ports:
      - "8084:8084"
    depends_on:
      - postgres
    networks:
      - internal

  manager:
    build:
      context: https://github.com/jadersistemas/whatsapp-suite.git#main:manager
      dockerfile: Dockerfile
    container_name: whatsapp-manager
    restart: unless-stopped
    environment:
      - APP_URL=${APP_URL}
      - DB_CONNECTION=pgsql
      - DB_HOST=postgres
      - DB_DATABASE=whatsapp_manager
      - DB_USERNAME=whatsapp
      - DB_PASSWORD=${DB_PASSWORD}
      - WHATSAPP_API_KEY=${WHATSAPP_API_KEY}
    ports:
      - "8080:80"
    depends_on:
      - postgres
    networks:
      - internal

  postgres:
    image: postgres:17-alpine
    container_name: whatsapp-postgres
    restart: unless-stopped
    environment:
      POSTGRES_USER: whatsapp
      POSTGRES_PASSWORD: ${DB_PASSWORD}
      POSTGRES_DB: whatsapp_manager
    volumes:
      - pgdata:/var/lib/postgresql/data
    networks:
      - internal

volumes:
  pgdata:

networks:
  internal:
    driver: bridge
```

**4. Configurar variáveis de ambiente**

Na seção "Environment variables", adicione:

| Variable | Value |
|----------|-------|
| `DB_PASSWORD` | `sua-senha-segura` |
| `WHATSAPP_API_KEY` | `sua-api-key` |
| `JWT_SECRET` | `seu-jwt-secret` |
| `APP_URL` | `http://seu-ip:8080` |

**5. Deploy**
- Clique em "Deploy the stack"

**6. Pós-deploy**
```bash
# Acessar terminal do container Manager
docker exec -it whatsapp-manager bash

php artisan key:generate
php artisan migrate --force
```

---

### Opção 4: Localhost (XAMPP/WAMP)

#### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/) ou [WAMP](https://www.wampserver.com/) com PHP 8.2+
- [Go 1.22+](https://go.dev/dl/)
- [PostgreSQL](https://www.postgresql.org/download/) 15+
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) 18+

#### Passo a passo

**1. Clonar o repositório**
```bash
git clone https://github.com/jadersistemas/whatsapp-suite.git
cd whatsapp-suite
```

**2. Configurar PostgreSQL**
```sql
-- Criar banco e usuário
CREATE USER whatsapp WITH PASSWORD 'sua-senha';
CREATE DATABASE whatsapp_api OWNER whatsapp;
CREATE DATABASE whatsapp_manager OWNER whatsapp;
GRANT ALL PRIVILEGES ON DATABASE whatsapp_api TO whatsapp;
GRANT ALL PRIVILEGES ON DATABASE whatsapp_manager TO whatsapp;
```

**3. Configurar a API Go**
```bash
cd api

# Criar arquivo .env
cp .env .env.local

# Editar .env com suas configurações
# Windows:
notepad .env
# Linux/Mac:
nano .env
```

**Arquivo .env da API:**
```env
SERVER_PORT=8084
DATABASE_URL=postgres://whatsapp:sua-senha@localhost:5432/whatsapp_api?sslmode=disable
AUTHENTICATION_GLOBAL_AUTH_TOKEN=sua-api-key
AUTHENTICATION_JWT_SECRET=seu-jwt-secret
WHATSAPP_AUTO_RECONNECT=true
```

```bash
# Compilar e rodar
go build -o whatsapp-api.exe ./cmd/api
./whatsapp-api.exe
```

A API deve estar rodando em http://localhost:8084

**4. Configurar o Manager (Laravel)**
```bash
cd manager

# Instalar dependências PHP
composer install

# Instalar dependências JS
npm install
npm run build

# Criar arquivo .env
cp .env.example .env

# Editar .env
# Windows:
notepad .env
# Linux/Mac:
nano .env
```

**Arquivo .env do Manager:**
```env
APP_NAME="WhatsApp Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=whatsapp_manager
DB_USERNAME=whatsapp
DB_PASSWORD=sua-senha

WHATSAPP_API_URL=http://localhost:8084
WHATSAPP_API_KEY=sua-api-key
```

```bash
# Gerar APP_KEY
php artisan key:generate

# Rodar migrations
php artisan migrate

# Iniciar servidor de desenvolvimento
php artisan serve
```

O Manager deve estar rodando em http://localhost:8000

**5. Verificar funcionamento**
- Acesse http://localhost:8000
- Faça login com a API Key configurada
- Crie uma instância e teste a conexão

---

### Opção 5: Docker (Individual)

#### Pré-requisitos
- Docker Desktop ou Docker Engine

#### Passo a passo

**1. Clonar e entrar na pasta**
```bash
git clone https://github.com/jadersistemas/whatsapp-suite.git
cd whatsapp-suite
```

**2. Criar rede Docker**
```bash
docker network create whatsapp-net
```

**3. Criar container PostgreSQL**
```bash
docker run -d \
  --name whatsapp-postgres \
  --network whatsapp-net \
  -e POSTGRES_USER=whatsapp \
  -e POSTGRES_PASSWORD=sua-senha \
  -e POSTGRES_DB=whatsapp_manager \
  -v pgdata:/var/lib/postgresql/data \
  -p 5432:5432 \
  postgres:17-alpine
```

**4. Criar container API**
```bash
docker build -t whatsapp-api ./api

docker run -d \
  --name whatsapp-api \
  --network whatsapp-net \
  -e DOCKER_ENV=true \
  -e DATABASE_URL=postgres://whatsapp:sua-senha@whatsapp-postgres:5432/whatsapp_manager?sslmode=disable \
  -e AUTHENTICATION_GLOBAL_AUTH_TOKEN=sua-api-key \
  -e AUTHENTICATION_JWT_SECRET=seu-jwt-secret \
  -p 8084:8084 \
  whatsapp-api
```

**5. Criar container Manager**
```bash
docker build -t whatsapp-manager ./manager

docker run -d \
  --name whatsapp-manager \
  --network whatsapp-net \
  -e APP_URL=http://localhost:8080 \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=whatsapp-postgres \
  -e DB_DATABASE=whatsapp_manager \
  -e DB_USERNAME=whatsapp \
  -e DB_PASSWORD=sua-senha \
  -e WHATSAPP_API_KEY=sua-api-key \
  -p 8080:80 \
  whatsapp-manager
```

**6. Rodar migrations**
```bash
docker exec -it whatsapp-manager php artisan key:generate
docker exec -it whatsapp-manager php artisan migrate --force
```

---

## Variáveis de Ambiente

### API Go

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `SERVER_PORT` | Porta do servidor | `8084` |
| `DATABASE_URL` | URL do PostgreSQL | - |
| `AUTHENTICATION_GLOBAL_AUTH_TOKEN` | API Key global | - |
| `AUTHENTICATION_JWT_SECRET` | Segredo JWT | - |
| `WHATSAPP_AUTO_RECONNECT` | Auto reconectar | `true` |
| `LOG_LEVEL` | Nível de log | `info` |

### Manager (Laravel)

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_URL` | URL do Manager | `http://localhost:8080` |
| `APP_KEY` | Chave de criptografia Laravel | - |
| `DB_HOST` | Host do PostgreSQL | `localhost` |
| `DB_DATABASE` | Nome do banco Manager | `whatsapp_manager` |
| `DB_USERNAME` | Usuário do PostgreSQL | `whatsapp` |
| `DB_PASSWORD` | Senha do PostgreSQL | - |
| `WHATSAPP_API_URL` | URL da API Go | `http://localhost:8084` |
| `WHATSAPP_API_KEY` | API Key da API Go | - |

---

## Endpoints da API

### Instâncias

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/instance/create` | Criar instância |
| `GET` | `/instance/fetchInstances` | Listar instâncias |
| `GET` | `/instance/connect/:name` | Conectar via QR |
| `GET` | `/instance/connectionState/:name` | Status da conexão |
| `DELETE` | `/instance/logout/:name` | Desconectar |
| `DELETE` | `/instance/delete/:name` | Remover instância |
| `PUT` | `/instance/settings/:name` | Atualizar configurações |

### Mensagens

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/message/sendText/:name` | Enviar texto |
| `POST` | `/message/sendLink/:name` | Enviar link |
| `POST` | `/message/sendMedia/:name` | Enviar mídia |
| `POST` | `/message/sendContact/:name` | Enviar contato |
| `POST` | `/message/sendLocation/:name` | Enviar localização |
| `POST` | `/message/sendReaction/:name` | Enviar reação |

### Webhooks

| Método | Rota | Descrição |
|--------|------|-----------|
| `PUT` | `/webhook/set/:name` | Configurar webhook |
| `GET` | `/webhook/find/:name` | Buscar webhook |

### Autenticação

Todas as rotas requerem:
- Header `apikey: sua-api-key` (rotas globais)
- Header `Authorization: Bearer <token>` (rotas de instância)

---

## Configurações da Instância

| Config | Descrição |
|--------|-----------|
| `rejectCalls` | Rejeitar chamadas recebidas |
| `ignoreGroups` | Ignorar mensagens de grupos |
| `alwaysOnline` | Manter presença disponível |
| `readMessages` | Marcar mensagens como lidas |
| `syncFullHistory` | Sincronizar histórico ao conectar |
| `viewStatus` | Marcar status como visualizados |

### Exemplo de atualização

```bash
curl -X PUT http://localhost:8084/instance/settings/minha-instancia \
  -H "apikey: sua-api-key" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "rejectCalls": true,
    "ignoreGroups": true,
    "alwaysOnline": true,
    "readMessages": true
  }'
```

---

## Solução de Problemas

### API não conecta

```bash
# Verificar logs
docker compose logs api

# Verificar se PostgreSQL está rodando
docker compose ps postgres

# Testar conexão com banco
docker exec -it whatsapp-postgres psql -U whatsapp -d whatsapp_manager
```

### Manager não inicia

```bash
# Verificar logs
docker compose logs manager

# Rodar migrations manualmente
docker compose exec manager php artisan migrate --force

# Limpar cache
docker compose exec manager php artisan cache:clear
docker compose exec manager php artisan config:clear
```

### Erro de autenticação

1. Verifique se `WHATSAPP_API_KEY` está igual no `.env` do Manager e da API
2. Verifique se o token da instância está correto
3. Teste a API diretamente:
```bash
curl http://localhost:8084/health
```

---

## Licença

MIT License - veja [LICENSE](LICENSE) para mais detalhes.

## Autor

**Jáder Oliveira** - 88988420622

## Contribuindo

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request
