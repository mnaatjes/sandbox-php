# Service Management

Service Management is not a single class, but rather the **process** of registering all your core application services into the Dependency Injection Container. This is typically done in a dedicated "bootstrap" or "service provider" location in your application.

### Design Philosophy

*   **Separation of Concerns:** The process of constructing services is a distinct responsibility. Your controllers and other application logic shouldn't know how to build a `Database` or `SessionManager` object; they should just be able to ask for one. Service management centralizes this construction logic.
*   **Clean Architecture:** This process is the "glue" that wires your application together. It happens at the outermost layer of your application, composing the core components that the inner layers will use.
*   **Maintainability:** Having a single, predictable place to see how all major services are constructed makes the application much easier to understand and debug. If you need to change how a core component is configured, you know exactly where to look.

---

### The Process

This is typically handled in a bootstrap file (e.g., `bootstrap/app.php`) that is called from your front controller (`public/index.php`).

The process involves:
1.  Creating a new instance of the `Container`.
2.  Binding all essential framework and application services into the container. This includes mapping interfaces to concrete implementations.
3.  Returning the fully configured container, which can then be used to run the application.

---

### Example Implementation

Let's imagine a file at `bootstrap/app.php`:

```php
<?php
// bootstrap/app.php

use MVCFrame\Container;
use MVCFrame\SessionManager;
use MVCFrame\View\ViewFactory;
use App\Services\MyEmailService;
use App\Interfaces\EmailServiceInterface;

// 1. Create the container
$container = new Container();

// 2. Bind core services

// Bind SessionManager as a singleton
$container->bind(SessionManager::class, function() {
    $session = new SessionManager();
    $session->start();
    return $session;
});

// Bind a View factory
$container->bind(ViewFactory::class, function() {
    return new ViewFactory('/path/to/your/views');
});

// Bind an interface to a concrete implementation
// This is powerful because you can easily swap out the email service later
// without changing any of the code that uses it.
$container->bind(EmailServiceInterface::class, function() {
    return new MyEmailService('your-api-key');
});


// 3. Return the configured container
return $container;
```

#### In your `public/index.php`:

```php
<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

// Get the fully configured container
$container = require_once __DIR__ . '/../bootstrap/app.php';

// Resolve the router/application from the container and run it
$app = $container->resolve(Application::class);

$response = $app->run();
$response->send();
```
