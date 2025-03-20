# Product Service

> **Note**: Due to time constraints and personal issues, this project is not in its final stable version. However, all core requirements have been implemented and the basic structure is in place for future development.

## Project Overview
This is a PHP-based product service that provides a clean, maintainable, and scalable architecture for managing products. The project follows SOLID principles and implements a custom MVC-like pattern.

## Project Structure

```
src/
├── Core/             # Core framework components
│   ├── Configs/      # Configuration classes
|   |── Http/         # HTTP-related classes
│   ├── Schema/       # Data transfer objects and validation
│   ├── View/         # Base view classes
│   └── Database/     # Database connection and query builder
├── ControllerDtos/   # Request/Response DTOs for controllers
├── Controllers/      # Application controllers
├── Entity/          # Domain entities
├── Repository/      # Data access layer
└── View/            # View classes for rendering responses
```

## Core Components

### 1. Core Framework (`src/Core/`)

#### HTTP Component (`Core/Http/`)
- `Request.php`: Handles HTTP requests, query parameters, and body data
- `Response.php`: Manages HTTP responses with proper headers and content
- `Router.php`: Routes requests to appropriate controllers based on defined routes

#### Schema Component (`Core/Schema/`)
- `Dto.php`: Base class for Data Transfer Objects with validation
- `ValidationRule.php`: Defines validation rules for DTOs
- `ValidationRuleType.php`: Enum for different types of validation rules

#### View Component (`Core/View/`)
- `BaseView.php`: Abstract base class for all views
- Provides common functionality for rendering responses

#### Database Component (`Core/Database/`)
- `Connection.php`: Manages database connections
- `QueryBuilder.php`: Builds SQL queries with proper escaping
- `QueryResult.php`: Handles query results and data mapping

### 2. Application Components

#### Controllers (`src/Controllers/`)
- Handle HTTP requests
- Use DTOs for request/response handling
- Implement business logic
- Example: `ProductController.php`

#### Entities (`src/Entity/`)
- Represent domain models
- Define data structure and relationships
- Example: `ProductEntity.php`

#### Repositories (`src/Repository/`)
- Handle data access
- Implement CRUD operations
- Example: `ProductRepository.php`

#### Views (`src/View/`)
- Handle response rendering
- Format data for client consumption
- Example: `ProductsView.php`

## Adding New Features

### 1. Adding a New Route
1. Define the route in `public/index.php`:
```php
$router->addRoute('GET', '/api/new-endpoint', 'NewController@method');
```

2. Create a new controller in `src/Controllers/`:
```php
namespace Hertz\ProductService\Controllers;

class NewController extends BaseController
{
    public function method(Request $request)
    {
        // Implementation
    }
}
```

### 2. Creating a New Entity
1. Create a new entity class in `src/Entity/`:
```php
namespace Hertz\ProductService\Entity;

class NewEntity
{
    public int $id;
    public string $name;
    // Add other properties
}
```

2. Create corresponding repository in `src/Repository/`:
```php
namespace Hertz\ProductService\Repository;

class NewRepository
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = new Connection();
    }

    // Implement CRUD methods
}
```

### 3. Adding Request/Response DTOs
1. Create DTOs in `src/ControllerDtos/`:
```php
namespace Hertz\ProductService\ControllerDtos\NewFeature;

class Request extends Dto
{
    #[ValidationRule(ValidationRuleType::REQUIRED)]
    public string $name;
}

class Response extends Dto
{
    public int $id;
    public string $name;
}
```

## Running the Application

### Using Docker
1. Build and start containers:
```bash
docker-compose up -d --build
```

2. Run tests:
```bash
docker-compose exec app ./vendor/bin/phpunit
```

3. Access phpMyAdmin:
- URL: http://localhost:8080
- Username: root
- Password: root

### Environment Configuration
The application uses environment variables for configuration. Create a `.env` file with:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=product_service
DB_USERNAME=product_service
DB_PASSWORD=root
```

## Testing
The project includes both unit and integration tests:
- Unit tests: `tests/Unit/`
- Integration tests: `tests/Integration/`

Run tests with:
```bash
./vendor/bin/phpunit
```

## Future Improvements
1. Implement proper error handling and logging
2. Add authentication and authorization
3. Implement caching layer
4. Add API documentation
5. Implement rate limiting
6. Add more comprehensive test coverage
7. Implement proper dependency injection
8. Add request validation middleware
9. Implement proper response formatting
10. Add database migrations

## Contributing
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License
This project is licensed under the MIT License. 