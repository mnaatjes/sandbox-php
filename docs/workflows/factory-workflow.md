# Factory Pattern Workflow

This document outlines the concepts, best practices, and workflow for creating and using the Factory design pattern in PHP.

## Overview

A Factory is a design pattern used to create objects without specifying the exact class of object that will be created. You call a single "factory" method with a parameter (e.g., a string) to get a specific object back.

The primary goal of the Factory pattern is to **decouple** the client code (the code that *uses* an object) from the creation code (the code that *instantiates* the object). This makes your application more flexible, easier to maintain, and simpler to test.

## Best Practices: Class Types

When creating a factory, you can structure it in several ways. Here are the options, from most to least recommended.

### 1. Regular (Instantiable) Class (Recommended Best Practice)

This is the modern, preferred approach. The factory itself is a standard class that you instantiate (`$factory = new MyFactory();`).

*   **Why it's best:** It fully supports **Dependency Injection (DI)**. The factory instance can be "injected" (passed) into the constructors of other classes. This makes your code highly testable, as you can easily substitute the real factory with a "mock" or "fake" factory during unit tests.
*   **When to use:** In all modern object-oriented applications. It is the most flexible and scalable option.

### 2. Static Class

This approach uses a static method on a class to create the object (`$product = MyFactory::create('type');`). You do not instantiate the factory.

*   **Why it's used:** It can seem simpler because it requires one less step (no `new Factory()`).
*   **Why it's not ideal:** It creates **tight coupling**. The client code is now directly dependent on the concrete `MyFactory` class name. This makes testing difficult because you cannot easily swap it out for a test double. It behaves like a global function, which can hide dependencies.
*   **When to use:** Only for very simple scripts or in legacy codebases where DI is not used.

### 3. Abstract Class

This is for a more complex pattern called the **Abstract Factory Pattern**, which is a "factory for factories."

*   **What it is:** You define an `abstract class AbstractFactory` with multiple creation methods (e.g., `createButton()`, `createCheckbox()`). Then you create concrete factories that extend it (e.g., `WindowsFactory`, `MacFactory`).
*   **When to use:** When you need to create *families* of related objects. This is overkill for simple object creation.

## Naming Conventions

*   **Interface:** The contract for the object being created. Suffix with `Interface`.
    *   Example: `LoggerInterface`, `VehicleInterface`
*   **Concrete Class:** The specific implementations.
    *   Example: `FileLogger`, `DatabaseLogger`
*   **Factory:** The factory class itself. Suffix with `Factory`.
    *   Example: `LoggerFactory`, `VehicleFactory`
*   **Creation Method:** The method inside the factory that builds the object.
    *   Example: `create()`, `build()`

## Instantiation and Usage

The client code should not use the `new` keyword for the products. It should only know about the factory.

1.  Instantiate the factory class: `$loggerFactory = new LoggerFactory();`
2.  In your application code (e.g., a controller or service), ask for the factory as a dependency.
3.  Call the `create()` method on the factory to get the object you need.
4.  Use the object via its interface methods.

```php
// 1. Instantiate the factory.
$loggerFactory = new LoggerFactory();

// 2. Use the factory to get the object.
// The code doesn't know or care about the concrete class, only the interface.
$logger = $loggerFactory->create(\'file\');

// 3. Use the object.
$logger->log(\'This is a message.\');
```

## Workflow for Creating a Factory

1.  **Define the Interface:** Identify the shared contract for the objects (products) you want to create. This ensures they can be used interchangeably.
    ```php
    interface LoggerInterface {
        public function log(string $message): void;
    }
    ```

2.  **Create Concrete Classes:** Write the specific classes that implement the interface. Each class represents a variation of the object.
    ```php
    class FileLogger implements LoggerInterface { /* ... */ }
    class DatabaseLogger implements LoggerInterface { /* ... */ }
    ```

3.  **Create the Factory Class:** Create a regular, instantiable class for the factory itself.
    ```php
    class LoggerFactory {
        // ...
    }
    ```

4.  **Implement the Creation Method:** Inside the factory, add a method (e.g., `create()`) that accepts a parameter to determine which object to build. A `match` expression (PHP 8.0+) is a clean way to handle this.

5.  **Return the Interface Type:** The creation method's signature should return the *interface*, not a concrete class. This enforces abstraction.
    ```php
    public function create(string $type): LoggerInterface {
        return match ($type) {
            \'file\' => new FileLogger(),
            \'database\' => new DatabaseLogger(),
            // ...
        };
    }
    ```

6.  **Handle Invalid Types:** Always include a default case to handle requests for unknown object types. Throwing an exception is standard practice.
    ```php
    public function create(string $type): LoggerInterface {
        return match ($type) {
            \'file\' => new FileLogger(),
            \'database\' => new DatabaseLogger(),
            default => throw new \InvalidArgumentException("Invalid logger type."),
        };
    }
    ```