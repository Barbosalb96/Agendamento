# Sistema de Agendamento

API REST desenvolvida em Laravel para gerenciamento de agendamentos com autenticação JWT, gestão de dias fechados e QR Code para validação.

## 🚀 Funcionalidades

- **Autenticação JWT**: Sistema de login seguro com tokens
- **Gestão de Agendamentos**: CRUD completo para agendamentos
- **Gestão de Dias**: Controle de dias fechados/disponíveis
- **QR Code**: Geração e validação de códigos QR
- **Email Notifications**: Envio de emails para confirmação e cancelamento
- **Documentação Swagger**: API documentada automaticamente

## 📋 Pré-requisitos

- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Docker (opcional)

## 🛠️ Instalação

### Via Docker
```bash
docker-compose up -d
```

### Manual
```bash
# Clone o repositório
git clone <repository-url>
cd agendamento

# Instale as dependências
composer install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrações
php artisan migrate --seed

# Inicie o servidor
php artisan serve
```

## 🔧 Configuração

### Banco de Dados
Configure as credenciais do banco no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendamento
DB_USERNAME=root
DB_PASSWORD=
```

### JWT
Configure a chave JWT:
```bash
php artisan jwt:secret
```

### Email
Configure o provedor de email no `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@agendamento.com
```

## 📚 Documentação da API

A documentação completa da API está disponível em:
- **Desenvolvimento**: `http://localhost:8000/api/documentation`
- **Produção**: `https://sua-url.com/api/documentation`

### Principais Endpoints

#### Autenticação
- `POST /api/login` - Login do usuário
- `POST /api/password/request-reset` - Solicitar reset de senha

#### Agendamentos
- `GET /api/admin/agendamento` - Listar agendamentos
- `POST /api/admin/agendamento` - Criar agendamento
- `DELETE /api/admin/agendamento/{id}` - Cancelar agendamento

#### Gestão de Dias
- `GET /api/admin/gestao-dias` - Listar dias
- `POST /api/admin/gestao-dias/store` - Criar/bloquear dia

#### Validação
- `GET /api/validar-qrcode/{uuid}` - Validar QR Code

## 🧪 Testes

Execute a suíte completa de testes:
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter="LoginTest"

# Testes por categoria
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

Para mais detalhes sobre os testes, consulte [TESTS_README.md](TESTS_README.md).

## 🏗️ Arquitetura

O projeto segue os princípios de **Clean Architecture** e **Domain-Driven Design**:

```
app/
├── Application/          # Casos de uso e serviços de aplicação
├── Domains/             # Entidades e regras de negócio
├── Infrastructure/      # Implementações de repositórios
└── Http/               # Controllers e recursos da API
```

### Padrões Utilizados
- **Repository Pattern**: Abstração da camada de dados
- **Service Layer**: Lógica de negócio centralizada
- **DTO Pattern**: Transferência segura de dados
- **Factory Pattern**: Criação de objetos complexos

## 🔒 Segurança

- Autenticação JWT com expiração configurável
- Validação rigorosa de entrada de dados
- Middleware de autorização por rotas
- Hash seguro de senhas com bcrypt
- Rate limiting para endpoints sensíveis

## 📦 Dependências Principais

- **Laravel Framework**: Base do projeto
- **JWT-Auth**: Autenticação JSON Web Token  
- **L5-Swagger**: Documentação automática da API
- **PHPUnit**: Testes automatizados
- **Faker**: Geração de dados para testes

## 🚀 Deploy

### Preparação
```bash
# Otimizações de produção
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Variáveis de Ambiente
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sua-url.com
```

## 📝 Changelog

Veja [CHANGELOG.md](CHANGELOG.md) para histórico de versões.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).
