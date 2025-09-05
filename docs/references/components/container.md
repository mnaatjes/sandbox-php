# Container (Dependency Injection)

The Container is the heart of the framework's architecture, enabling powerful Inversion of Control (IoC). It is a central registry for your application's services and components.

### Design Philosophy

*   **Separation of Concerns:** The container's only job is to manage class instantiation. It knows *how* to create objects, but not what those objects do.
*   **Inversion of Control (IoC):** Instead of a class creating its own dependencies (e.g., `new Database()`), the dependency is "injected" into it by the container. This decouples the class from the concrete implementation of its dependencies.
*   **Maintainability:** When you need to change a service (e.g., swap a file-based cache for a Redis cache), you only have to change it in the one place where it's registered with the container.

---

### `Container` Class Structure

```
Container
├── Properties
│   └── protected bindings: array
│
└── Methods
    ├── bind(string $key, callable $resolver): void
    └── resolve(string $key): mixed
```

#### Method Details

*   **`bind(string $key, callable $resolver): void`**
    *   **Purpose:** Registers a service or "binding" in the container. It takes a unique key (often a class name) and a callable function (a "resolver") that knows how to create the object.
    *   **Why a callable?** This allows for lazy instantiation. The object is not created until it's actually needed, which improves performance.

*   **`resolve(string $key): mixed`**
    *   **Purpose:** Retrieves a service from the container. If the service has not been created yet, it invokes the resolver function, stores the result, and returns it. On subsequent calls, it will return the already-created instance (acting as a singleton).

---

### Example Usage

```php
<?php

$container = new MVCFrame\Container();

// Register a service. The closure will only be called when it's first resolved.
$container->bind(Database::class, function() {
    return new MySqlDatabase('dsn', 'user', 'pass');
});

// A controller or another service can now resolve the dependency without knowing how it was built.
class UserController
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}

// In your bootstrap/routing logic:
$database = $container->resolve(Database::class);
$userController = new UserController($database);

```
