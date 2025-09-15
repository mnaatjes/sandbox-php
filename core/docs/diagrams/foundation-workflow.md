# Foundation Workflow

This document outlines the architecture and interaction of the core foundation classes: `Application`, `ServiceContainer`, and `ServiceRegistry`.

## 1. Class Diagrams

This diagram shows the properties, methods, and relationships between the core classes.

### 1.1 Application Structure
```mermaid
classDiagram
    class Application {
        -static Application $instance
        -Path $basepath
        -ServiceContainer $container
        -ServiceRegistry $registry
        +__construct(string $base_path)
        +static getInstance()
        +set(string $key, $value)
        +share(string $key, $value)
        +has(string $key) bool
        +get(string $key)
        +all() array
    }

    class ServiceContainer {
        -static ServiceContainer $instance
        -array $bindings
        -array $instances
        - __construct()
        +static getInstance()
        +bind(string $key, Closure $resolver)
        +share(string $key, Closure $resolver)
        +resolve(string $key)
        -reflect(string $key)
    }

    class ServiceRegistry {
        -static ServiceRegistry $instance
        -array $data
        - __construct()
        +static getInstance()
        +register(string $alias, $value)
        +has(string $alias) bool
        +lookup(string $alias)
    }

    Application "1" *-- "1" ServiceContainer : manages
    Application "1" *-- "1" ServiceRegistry : manages
```

### 1.2 DotEnv

```mermaid
classDiagram
    class DotEnv {
        <<Service>>
        
    }
```

## 2. Organizational Charts

This chart illustrates the ownership and instantiation hierarchy. The `Application` class is the entry point and is responsible for creating and managing the container and registry instances.

```mermaid
graph TD
    A[Application] --> B[ServiceContainer];
    A --> C[ServiceRegistry];
```

## 3. Sequence Diagrams

### 3.1 Instantiation Phase

This diagram shows the typical sequence of events during application boot-up and when resolving a service from the container.

```mermaid
sequenceDiagram
    participant Client as (index.php)
    participant App as Application
    participant Container as ServiceContainer
    participant Registry as ServiceRegistry

    Client->>App: new Application(base_path)
    activate App
    App->>Registry: getInstance()
    activate Registry
    Registry-->>App: registryInstance
    deactivate Registry
    App->>Container: getInstance()
    activate Container
    Container-->>App: containerInstance
    deactivate Container
    deactivate App

    Client->>App: get('MyService')
    activate App
    App->>Container: resolve('MyService')
    activate Container
    Container->>Container: reflect('MyService')
    Container-->>App: myServiceInstance
    deactivate Container
    App-->>Client: myServiceInstance
    deactivate App
```

### 3.2 Configuration Phase

```mermaid
sequenceDiagram
    participant App as Application
    participant Container as ServiceContainer
    participant Registry as ServiceRegistry
    participant env as DotEnv

```