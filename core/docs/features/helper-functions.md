# Framework Helper Functions

Helper functions provide a convenient, global way to interact with core services and components of the framework. They are designed to make your code more readable, expressive, and concise by abstracting away the underlying class calls.

---

## 1. `reg()` Helper

*   **Purpose:** The primary helper for interacting with the `ServiceRegistry`, which acts as a central store for various application data, including paths, files, and configuration.
*   **Name:** `reg()`
*   **Arguments:**
    *   `reg()`: Returns the `ServiceRegistry` instance.
    *   `reg(string $alias)`: Retrieves a value from the `ServiceRegistry` using the given alias.
    *   `reg(string $alias, mixed $value)`: Stores a value in the `ServiceRegistry` under the given alias.

*   **Code Example:**
    ```php
    // In your helpers.php
    if (!function_exists('reg')) {
        function reg(...$args) {
            // Assuming ServiceRegistry is a singleton
            $instance = \MVCFrame\Foundation\ServiceRegistry::getInstance();

            switch (func_num_args()) {
                case 2: // reg('key', 'value') - for setting
                    if (!is_string($args[0])) { throw new \TypeError("First argument for reg() must be a string key!"); }
                    return $instance->set($args[0], $args[1]);
                case 1: // reg('key') - for getting
                    if (!is_string($args[0])) { throw new \TypeError("Single argument for reg() must be a string key!"); }
                    return $instance->get($args[0]);
                case 0: // reg() - for getting the registry instance itself
                    return $instance;
                default:
                    throw new \Exception("Invalid number of arguments for reg() helper function!");
            }
        }
    }
    ```

*   **Interaction Diagram:**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant reg()
        participant ServiceRegistry

        Caller->>reg(): reg('my.key', 'my.value')
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>ServiceRegistry: set('my.key', 'my.value')
        ServiceRegistry-->>reg(): void
        reg()->>Caller: void

        Caller->>reg(): reg('my.key')
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>ServiceRegistry: get('my.key')
        ServiceRegistry-->>reg(): value
        reg()->>Caller: value

        Caller->>reg(): reg()
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>Caller: instance
    ```

---

## 2. `path()` Helper

*   **Purpose:** To retrieve a registered directory path (as a `Directory` object or string). These paths are typically stored in the `ServiceRegistry` under the `dir.` prefix.
*   **Name:** `path()`
*   **Arguments:** `path(string $alias)`
*   **What it does:** Calls `reg('dir.' . $alias)` to get the `Directory` object from the `ServiceRegistry`.

*   **Code Example:**
    ```php
    // In your helpers.php
    if (!function_exists('path')) {
        function path(string $alias) {
            return reg('dir.' . $alias); // Assuming 'dir.' prefix for directories
        }
    }
    ```

*   **Interaction Diagram:**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant path()
        participant reg()
        participant ServiceRegistry

        Caller->>path(): path('config')
        path()->>reg(): reg('dir.config')
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>ServiceRegistry: get('dir.config')
        ServiceRegistry-->>reg(): DirectoryObject
        reg()->>path(): DirectoryObject
        path()->>Caller: DirectoryObject
    ```

---

## 3. `file()` Helper

*   **Purpose:** To retrieve a registered file path (as a `File` object). These paths are typically stored in the `ServiceRegistry` under the `file.` prefix.
*   **Name:** `file()`
*   **Arguments:** `file(string $alias)`
*   **What it does:** Calls `reg('file.' . $alias)` to get the `File` object from the `ServiceRegistry`.

*   **Code Example:**
    ```php
    // In your helpers.php
    if (!function_exists('file')) {
        function file(string $alias) {
            return reg('file.' . $alias); // Assuming 'file.' prefix for files
        }
    }
    ```

*   **Interaction Diagram:**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant file()
        participant reg()
        participant ServiceRegistry

        Caller->>file(): file('config.app')
        file()->>reg(): reg('file.config.app')
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>ServiceRegistry: get('file.config.app')
        ServiceRegistry-->>reg(): FileObject
        reg()->>file(): FileObject
        file()->>Caller: FileObject
    ```

---

## 4. `config()` Helper

*   **Purpose:** To retrieve a configuration value. These values are typically stored in the `ServiceRegistry` under the `config.` prefix.
*   **Name:** `config()`
*   **Arguments:** `config(string $alias, mixed $default = null)`
*   **What it does:** Calls `reg('config.' . $alias)` to get the configuration value.

*   **Code Example:**
    ```php
    // In your helpers.php
    if (!function_exists('config')) {
        function config(string $alias, $default = null) {
            return reg('config.' . $alias) ?? $default;
        }
    }
    ```

*   **Interaction Diagram:**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant config()
        participant reg()
        participant ServiceRegistry

        Caller->>config(): config('app.name')
        config()->>reg(): reg('config.app.name')
        reg()->>ServiceRegistry: getInstance()
        ServiceRegistry-->>reg(): instance
        reg()->>ServiceRegistry: get('config.app.name')
        ServiceRegistry-->>reg(): value
        reg()->>config(): value
        config()->>Caller: value
    ```

---

## 5. `app()` Helper

*   **Purpose:** To retrieve the main `Application` instance or resolve services from the application's service container.
*   **Name:** `app()`
*   **Arguments:**
    *   `app()`: Returns the `Application` instance.
    *   `app(string $serviceAlias)`: Resolves a service from the container.
*   **Interacts with:** `MVCFrame\Foundation\Application` (and its internal `ServiceContainer`).

*   **Code Example:**
    ```php
    // In your helpers.php
    if (!function_exists('app')) {
        function app(string $serviceAlias = null) {
            $appInstance = \MVCFrame\Foundation\Application::getInstance();
            if ($serviceAlias) {
                // Assuming Application has a method to resolve services from its container
                return $appInstance->resolve($serviceAlias);
            }
            return $appInstance;
        }
    }
    ```

*   **Interaction Diagram:**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant app()
        participant Application
        participant ServiceContainer

        Caller->>app(): app()
        app()->>Application: getInstance()
        Application-->>app(): instance
        app()->>Caller: instance

        Caller->>app(): app('my.service')
        app()->>Application: getInstance()
        Application-->>app(): instance
        app()->>Application: resolve('my.service')
        Application->>ServiceContainer: resolve('my.service')
        ServiceContainer-->>Application: serviceInstance
        Application-->>app(): serviceInstance
        app()->>Caller: serviceInstance
    ```