# Laravel Environment Loading Order

This document outlines the order of operations Laravel follows when loading environment variables and resolving configuration values.

## Overview of Loading Precedence

Laravel has a clearly defined hierarchy for loading environment variables. The first value it finds for a given variable is the one it uses. It will not override a loaded value with one from a lower-precedence source.

The order is as follows:

1.  **System-Level Environment Variables**: Any variables set directly on the server (e.g., in your Nginx or Apache config) or in your shell session are checked first and will always take highest priority.
2.  **Environment-Specific `.env` File**: If an `APP_ENV` variable is set (e.g., to `testing` or `production`), Laravel will look for a corresponding file like `.env.testing`. If this file exists, its values are loaded.
3.  **Default `.env` File**: If no `APP_ENV` is set or the environment-specific file doesn't exist, Laravel loads the standard `.env` file from the project root.

## The Role of Configuration Files

If a variable is not found in any of the sources above, Laravel does not have a core fallback. Instead, the fallback mechanism is handled inside the `config/*.php` files, where a default value is provided as the second argument to the `env()` helper function.

```php
// Example from config/database.php
'host' => env('DB_HOST', '127.0.0.1'), // '127.0.0.1' is the fallback
```

---

## Sequence Diagram

This diagram illustrates the decision-making process during the `LoadEnvironmentVariables` bootstrap stage.

```mermaid
sequenceDiagram
    participant Bootstrapper as LoadEnvironmentVariables
    participant System
    participant .env.{APP_ENV}
    participant .env
    participant Config Files

    Bootstrapper->>System: Check for `VAR_NAME`?
    alt Variable Exists in System
        System-->>Bootstrapper: Return value for `VAR_NAME`
        Note right of Bootstrapper: Value is set. Process for this variable stops.
    else Variable Not in System
        System-->>Bootstrapper: Not found
        Bootstrapper->>.env.{APP_ENV}: Check for `VAR_NAME`?
        alt .env.{APP_ENV} Exists and Contains VAR_NAME
            .env.{APP_ENV}-->>Bootstrapper: Return value for `VAR_NAME`
            Note right of Bootstrapper: Value is set. Process for this variable stops.
        else Not Found in .env.{APP_ENV}
            .env.{APP_ENV}-->>Bootstrapper: Not found
            Bootstrapper->>.env: Check for `VAR_NAME`?
            alt .env Exists and Contains VAR_NAME
                .env-->>Bootstrapper: Return value for `VAR_NAME`
                Note right of Bootstrapper: Value is set.
            else Not Found in .env
                .env-->>Bootstrapper: Not found
                Note right of Bootstrapper: `env('VAR_NAME')` will return null.
            end
        end
    end

    Note over Bootstrapper, Config Files: Later, when config is parsed...
    Config Files->>Bootstrapper: Use `env('VAR_NAME', 'default')`
    alt `env()` returns null
        Bootstrapper-->>Config Files: Use 'default' value
    else `env()` returns a value
        Bootstrapper-->>Config Files: Use the value found earlier
    end

```

---

### The `.env.example` File

The `.env.example` file is **never read** by Laravel. It serves only as a template for developers, showing which variables are required for the application to run.
