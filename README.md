# Product Service

A modern PHP-based product service that provides a clean, maintainable, and scalable architecture for managing products. The project follows SOLID principles and implements a custom MVC-like pattern.

## Project Structure

```
.
├── configs/              # Configuration files
│   ├── dev.yaml         # Development environment config
│   ├── local.yaml       # Local environment config
│   └── production.yaml  # Production environment config
├── database/            # Database related files
│   ├── migrations/      # Database migrations
│   └── seeds/          # Database seeders
├── public/             # Public directory
│   └── index.php       # Application entry point
├── src/                # Source code
│   ├── Core/           # Core framework components
│   │   ├── App.php     # Application bootstrap
│   │   ├── Config/     # Configuration management
│   │   ├── Controller/ # Base controller classes
│   │   ├── DB/         # Database components
│   │   ├── Exception/  # Custom exceptions
│   │   ├── Http/       # HTTP components
│   │   ├── Logger/     # Logging components
│   │   ├── Model/      # Base model classes
│   │   ├── Routing/    # Routing components
│   │   ├── Schema/     # Data validation
│   │   └── View/       # View components
│   ├── Controller/     # Application controllers
│   ├── ControllerDtos/ # Request/Response DTOs
│   ├── Entity/         # Domain entities
│   ├── Repository/     # Data access layer
│   ├── Routes.php      # Route definitions
│   └── View/           # View classes
├── tests/              # Test files
│   ├── Integration/    # Integration tests
│   └── Unit/          # Unit tests
├── vendor/             # Composer dependencies
├── .dockerignore      # Docker ignore rules
├── composer.json      # Composer configuration
├── composer.lock      # Composer lock file
├── docker-compose.yml # Docker compose configuration
├── Dockerfile         # Docker build configuration
├── Makefile          # Make commands for common tasks
├── phinx.php         # Database migration config
└── phpunit.xml       # PHPUnit configuration
```

## Core Components

### 1. Core Framework (`src/Core/`)

#### Application (`App.php`)
- Main application bootstrap
- Service container management
- Application lifecycle handling

#### Configuration (`Config/`)
- Environment-specific configuration
- YAML-based configuration files
- Configuration management system

#### Database (`DB/`)
- Database connection management
- Query builder
- Transaction handling

#### HTTP (`Http/`)
- Request handling
- Response management
- HTTP utilities

#### Routing (`Routing/`)
- Route definitions
- Route matching
- Route parameters handling

#### Schema (`Schema/`)
- Data validation
- Schema definitions
- Validation rules

#### View (`View/`)
- Template rendering
- View helpers
- Response formatting

### 2. Application Components

#### Controllers (`src/Controller/`)
- Request handling
- Business logic implementation
- Response preparation

#### DTOs (`src/ControllerDtos/`)
- Request/Response data transfer objects
- Input validation
- Data transformation

#### Entities (`src/Entity/`)
- Domain models
- Business rules
- Data relationships

#### Repositories (`src/Repository/`)
- Data access layer
- CRUD operations
- Query optimization

## Development Setup

### Prerequisites
- PHP 8.1 or higher
- Docker and Docker Compose
- Composer
- Make

### Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd product_service
```

2. Install dependencies:
```bash
make composer-install
```

3. Start the development environment:
```bash
make up
```

### Quick Run

1. Start the application:
```bash
make up
```

2. Run migrations and seed the database:
```bash
make migrate
make seed
```

3. Access the API:
- Get a product by ID: http://localhost:8000/products/{id}
  Example: http://localhost:8000/products/1

### Configuration
The application uses YAML-based configuration files in the `configs/` directory:
- `local.yaml`: Local development settings
- `dev.yaml`: Development environment settings
- `production.yaml`: Production environment settings

### Database Management

1. Run migrations:
```bash
make migrate
```

2. Seed the database:
```bash
make seed
```

### Accessing Services

- Application: http://localhost:8000
- PgAdmin: http://localhost:8080
  - Email: admin@admin.com
  - Password: admin

## Available Make Commands

The project includes a Makefile with common commands for development tasks:

```bash
make help              # Show all available commands
make build            # Build Docker containers
make up               # Start containers
make down             # Stop containers
make test             # Run all tests
make test-unit        # Run unit tests
make test-integration # Run integration tests
make migrate          # Run migrations
make migrate-status   # Check migration status
make seed             # Seed the database
make clean            # Clean up Docker resources
make logs             # View application logs
make shell            # Open shell in container
```

For a complete list of available commands, run:
```bash
make help
```

## Testing

### Running Tests

1. Unit Tests:
```bash
make test-unit
```

2. Integration Tests:
```bash
make test-integration
```

3. All Tests:
```bash
make test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License. 