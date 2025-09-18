# The Registry Design Pattern

The Registry is a simple but powerful design pattern for managing application-wide information. This document outlines its purpose, how it differs from other patterns like a Service Container, and the best practices for implementing a robust registry system.

## 1. The Role of a Registry

### What is a Registry?

A Registry is essentially a globally accessible container for shared data, configuration, and values. Think of it as a centralized "phone book" or "bulletin board" for your application. Any part of the application can look up a piece of information (like a database host name or an API key) without having to know where that information originated. Its primary goal is to provide a single, predictable source of truth for application-level settings.

### Registry vs. Service Container: A Critical Distinction

This is one of the most important distinctions in framework architecture. While both are central containers, they have fundamentally different responsibilities.

**A Service Registry holds *data* and *configuration*.**
*   **Purpose:** To store and retrieve simple, often serializable, information.
*   **Content:** Database host names, API keys, application-wide settings (like `debug_mode`), paths, arrays of configuration, etc.
*   **Answers the question:** "What is the setting for X?" (e.g., "What is the database port?").

**A Service / DI Container holds *live service objects* and manages their *dependencies*.**
*   **Purpose:** To construct, manage, and retrieve complex objects and their entire dependency graph. It handles object lifecycles (e.g., creating a "singleton" that is shared everywhere).
*   **Content:** Live database connection objects, logger instances, request/response objects, and other complex services that perform actions.
*   **Answers the question:** "Give me a tool that can do Y." (e.g., "Give me a fully configured Logger object.").

```mermaid
graph TD
    subgraph Application
        A[Controller]
    end

    subgraph "DI / Service Container"
        SC[ServiceContainer]
        DB_Obj[(Database Object)]
        SC -- Manages & Constructs --> DB_Obj
    end
    
    subgraph "Configuration Registry"
        REG[ServiceRegistry]
        DB_Host[("db.host = 'localhost'")]
        REG -- Holds --> DB_Host
    end

    A -- Asks for the 'Database' service --> SC;
    SC -- Returns live object --> A;
    A -- Asks for the 'db.host' setting --> REG;
    REG -- Returns 'localhost' string --> A;
```

### What a Registry Accomplishes
*   **Centralization:** Provides one place to find application settings.
*   **Decoupling:** Components no longer need to hard-code configuration values or know how to read specific config files. They just ask the registry.
*   **Single Source of Truth:** Prevents configuration drift and ensures all parts of the application are working with the same settings.

---

## 2. Best Practices and Conventions

A well-designed registry follows a simple, predictable API.

### Core Properties
At its heart, a registry needs a place to store its data. A private associative array is the most common implementation.
*   `private array $data = [];`

### Core Methods
The public API should be simple and mirror basic CRUD (Create, Read, Update, Delete) operations.
*   `register(string $alias, mixed $value, ...)`: To add an item in the registry using a string-based key or "alias".
*   `lookup(string $alias): mixed`: To retrieve an item from the registry by its alias.
*   `has(string $alias): bool`: To check for an item's existence.

### Data Flow Diagram

This sequence diagram shows the basic data flow for registering and looking up a simple value.

```mermaid
sequenceDiagram
    participant Client as Application Code
    participant Registry as ServiceRegistry

    Client->>Registry: register('app.debug', true)
    activate Registry
    Registry->>Registry: Store `true` at key 'app.debug'
    deactivate Registry

    Note over Client, Registry: ... time passes ...

    Client->>Registry: lookup('app.debug')
    activate Registry
    Registry-->>Client: returns `true`
    deactivate Registry
```

---

## 3. Building a Robust System: Affiliated Classes

Storing simple key-value pairs is good, but a truly robust registry stores richer information. This is accomplished with helper classes.

### Beyond Simple Key-Value: The `RegistryItem`
Instead of storing the raw value directly, we wrap it in a `RegistryItem` object. This object acts as a container, holding both the value and its metadata. This prevents the registry from being a simple "junk drawer" and gives structure to every entry.

### Describing the Data: The `RegistryMetaData`
This is the most important affiliated class. For every `RegistryItem`, we attach a `RegistryMetaData` object. This object's sole purpose is to hold structured information *about* the value. It answers questions like:
*   What is this value's data type?
*   What category does it belong to?
*   Is it read-only?
*   What is it for? (description)

### Class Diagram

This diagram shows how the classes relate to each other. The `ServiceRegistry` holds many `RegistryItem` objects, and each `RegistryItem` has exactly one `RegistryMetaData` object.

```mermaid
classDiagram
    class ServiceRegistry {
        -data: array
        +register(alias, value, metadata)
        +lookup(alias)
    }
    class RegistryItem {
        -value: mixed
        -metadata: RegistryMetaData
        +resolve()
        +getValue()
    }
    class RegistryMetaData {
        +type: string
        +category: string
        +description: string
        +tags: array
    }

    ServiceRegistry "1" *-- "many" RegistryItem : contains
    RegistryItem "1" *-- "1" RegistryMetaData : has
```

### Deeper Dive: Approaches to Item and Metadata Storage

#### Approach 1: Simple Arrays (The Convention-over-Configuration approach)

Many successful frameworks (like Laravel and Symfony) use simple nested associative arrays for their configuration registries. The "item" and its "metadata" are not distinct objects, but rather conventions within the array structure.

*   **Pros:** Extremely fast, low memory overhead, simple to debug with `print_r`.
*   **Cons:** Relies entirely on developer convention. There is no type safety, no defined structure, and no way to enforce what keys are valid. It is prone to typos.

#### Approach 2: Value Objects (The API-over-Convention approach)

The design we have explored with `RegistryItem` and `RegistryMetaData` follows the **Value Object** pattern. This approach provides a formal, structured API for every entry in the registry.

*   **Pros:** Provides structure and type safety. The class definition becomes self-documenting. The API is explicit and less prone to user error. Allows for methods on the items themselves (like `resolve()`).
*   **Cons:** More boilerplate code to write up front. Slightly more memory usage (often negligible).

```mermaid
classDiagram
    direction LR

    note "Approach 1: Simple Arrays"
    class RegistryForArrays {
      -data: array
    }
    class PlainArray {
      <<data type>>
    }
    RegistryForArrays -- "stores" PlainArray

    note "Approach 2: Value Objects"
    class RegistryForObjects {
      -data: array
    }
    class RegistryItem {
      -value: mixed
    }
    class RegistryMetaData {
      +type: string
    }
    RegistryForObjects -- "stores" RegistryItem
    RegistryItem -- "has a" RegistryMetaData
```

### Best Practices and Underlying Concepts

When using the Value Object approach, the goal is to create a more robust and predictable system. The underlying computer science concepts are:

*   **Data Abstraction & Encapsulation:** The `RegistryItem` object hides the complexity of its internal structure. You interact with it through a clean API (`getValue()`, `resolve()`), not by accessing raw array keys. This prevents you from depending on a fragile internal structure.
*   **Self-Documentation:** The class definitions for `RegistryItem` and `RegistryMetaData` form a contract. A developer can read the class to understand exactly what a registry entry consists of. This is impossible with a simple array.
*   **Immutability:** For maximum safety, Value Objects should be **immutable**. Once a `RegistryItem` is created, it should not be changed. Any modification should result in a new `RegistryItem` instance. This prevents bugs from "action at a distance" where one part of the code unintentionally modifies a configuration object used by another.

---

## 4. Design Philosophies

The architecture of a registry revolves around a few key philosophies.

### Generic vs. Specialized
*   A **Generic Registry** (like the one we've designed) is a general-purpose tool that can store anything. Because it has no specific knowledge of the data it holds, it requires explicit instructions from the developer.
*   A **Specialized Registry** (e.g., a `FileSystemRegistry`) is an expert on a narrow domain. It can have "smart" methods with built-in logic (e.g., a `registerPath()` method that knows how to tell a file from a directory). Powerful systems often use specialized facades that use a generic registry internally.

### Instructive vs. Descriptive Metadata
This is the most critical philosophy for a generic registry.
*   **Descriptive:** Stating what a value *is* right now (e.g., `gettype('8080')` returns `string`). This is not very useful, as the registry can't guess your intent.
*   **Instructive:** Telling the registry what you *want the value to become*. This is the correct approach for a generic registry. By providing `['type' => 'int']`, you are giving the registry the **instruction** it needs to transform the raw value `'8080'` into the usable integer `8080`.

### The `resolve()` Method: Hydration and Transformation

The `resolve()` method is the engine that acts on the instructive metadata. Its job is to transform the raw, stored value into its final, usable form. This process is often called **Hydration**—taking "dry" data (like a string) and adding the "water" (class logic, type casting) to bring it to life.

```mermaid
flowchart TD
    A[Start resolve] --> B{Explicit type exists};
    B -- No --> C[Return Raw Value];
    B -- Yes --> D{Is type a scalar};
    D -- Yes --> E[Cast value to scalar type];
    D -- No --> F[Assume type is Class Name];
    F --> G[Instantiate new Class];
    C --> Z[End];
    E --> Z;
    G --> Z;
```

---

## 5. Related Design Patterns

The concepts we've discussed touch upon several other formal design patterns.

### The Singleton Pattern
A Registry is almost always implemented as a **Singleton**.
*   **Purpose:** To ensure that a class has only one instance and to provide a single, global point of access to it.
*   **How it relates:** The `getInstance()` method is the classic implementation of the Singleton pattern. This guarantees that all parts of your application are accessing the exact same registry.

### Service Locator
This is a very closely related pattern.
*   **Purpose:** A Service Locator is a registry that is specifically used to find and return **service objects**.
*   **How it relates:** If you were to use your `ServiceRegistry` to store and retrieve live service objects, you would be using it as a **Service Locator**.
*   **Key Difference:** With a Service Locator, the client code actively asks for its dependencies (`$db = Registry::lookup('database');`). With Dependency Injection, the dependencies are passively given to the client, usually in its constructor.

### Factory Method Pattern
The `resolve()` method we designed is a perfect example of a **Factory Method**.
*   **Purpose:** To define an interface (or method) for creating an object, but letting the metadata alter the type of object that will be created.
*   **How it relates:** The `resolve()` method's job is to "manufacture" the final value. It reads the `type` instruction and decides whether to manufacture an `int`, a `string`, or a `new File()` object.

### Value Object
The `RegistryItem` and `RegistryMetaData` classes are examples of **Value Objects**.
*   **Purpose:** A simple object that represents a descriptive aspect of the domain with no conceptual identity. Their equality is based on their value, not their identity.
*   **How it relates:** A `RegistryMetaData` object holding `type: 'int'` is equal to any other `RegistryMetaData` object holding `type: 'int'`. They are simple, structured containers for data.

### Summary Table: Registry vs. Related Concepts

| Pattern | Purpose | Typical Content | How Dependencies are Handled |
| :--- | :--- | :--- | :--- |
| **Registry** | Store & retrieve configuration/data. | Strings, numbers, arrays, config. | Does not manage dependencies. |
| **Service Locator** | Find & retrieve service objects. | Live service objects. | Client actively requests services. |
| **DI Container** | Create & manage service objects. | Service definitions & live objects. | Dependencies are injected into clients. |

---

## 6. Hierarchical Structure and Dot-Notation

While a registry can be a simple, flat key-value store, its power is greatly enhanced by organizing entries into a hierarchy. This is analogous to how a filesystem uses folders to organize files. The most common convention for this is **dot-notation**.

### The Dot-Notation Convention

The convention is to structure keys like a path, with each level separated by a dot.

`level1.level2.level3.key`

This creates logical groups and makes the configuration self-documenting.

*   `database.mysql.host`
*   `database.mysql.port`
*   `cache.redis.host`
*   `app.debug_mode`

This practice avoids naming collisions and makes it easy to find related settings.

### What is this Pattern Called?

Using dot-notation to access a nested data structure is not a single, formal design pattern but rather a practical application of several computer science concepts:

*   **Tree Data Structure:** The hierarchy itself is a tree, where each dot represents descending one level.
*   **Associative Arrays (Hash Maps):** The underlying implementation is typically a tree of nested associative arrays.
*   **Namespacing:** Most importantly, dot-notation provides **namespaces** for your configuration. The `database.mysql` part acts as a namespace for all MySQL-related settings, preventing a conflict with `cache.redis.host`.

### Naming Conventions and Conflict Avoidance

*   **Conflict Avoidance:** The best way to avoid conflicts is to use well-defined, unique top-level keys (e.g., `app`, `database`, `cache`, `services`). For third-party modules, a common practice is to use a vendor namespace, similar to package managers: `vendor.package.setting`.
*   **Syntax Conventions:** The segments of the key should be consistent. Common conventions are `snake_case` (e.g., `app.debug_mode`, common in PHP) and `kebab-case` (e.g., `app.debug-mode`, common in YAML). The key is to pick one and stick to it.

### Querying and Searching Hierarchies

A powerful registry allows you to retrieve not just single values, but entire branches of the hierarchy.

*   **Strategy 1: Exact Match:** Retrieving a single leaf node value. This is the standard `lookup()`.
*   **Strategy 2: Section Retrieval:** Retrieving an entire "directory" of settings. A call like `lookup('database.mysql')` should return an array of all settings under that namespace: `['host' => ..., 'port' => ...]`. This allows a component to grab all the configuration it needs in one call.
*   **Strategy 3: Wildcard Search (Advanced):** Some systems allow for wildcard searches, like `lookup('database.*.host')`. This is powerful but complex to implement efficiently, often requiring a full traversal of the configuration tree.

```mermaid
flowchart TD
    A[Start lookup] --> B{Traverse to first key};
    B --> C{Traverse to next key};
    C --> D{Is final node a sub-array};
    D -- Yes --> E[Return sub-array];
    D -- No --> F[Return value or null];
    E --> Z[End];
    F --> Z;
```

### Implementation Data Flow

To implement this, the `register()` method must parse the alias and recursively build the nested array structure.

```php
// A simplified look inside register('database.mysql.host', '127.0.0.1')

$keys = explode('.', 'database.mysql.host');
// $keys is ['database', 'mysql', 'host']

$temp = &$this->data; // Start with a reference to the main data array

// Loop through keys, stopping before the last one
while (count($keys) > 1) {
    $key = array_shift($keys);
    if (!isset($temp[$key]) || !is_array($temp[$key])) {
        $temp[$key] = []; // Create the nested array if it doesn't exist
    }
    // Move the reference down one level
    $temp = &$temp[$key];
}

// Assign the value to the final key
$temp[array_shift($keys)] = $value;
```

```mermaid
flowchart TD
    A[Start register call] --> B[Explode alias string];
    B --> C{Process first key};
    C --> D{Is sub-array defined};
    D -- No --> E[Create sub-array];
    E --> F[Descend into sub-array];
    D -- Yes --> F;
    F --> G{Process next key};
    G --> H{Is sub-array defined};
    H -- No --> I[Create sub-array];
    I --> J[Descend into sub-array];
    H -- Yes --> J;
    J --> K[Assign value to final key];
    K --> Z[End];
```

### Enforcing the Hierarchy

Strictly enforcing a schema is complex. The best approach is usually a combination of:

1.  **Convention and Documentation:** Clearly document the expected configuration structure. This is the most common method.
2.  **Schema Validation:** For highly critical applications, you can validate a new entry against a predefined schema array before storing it.
3.  **Specialized Methods:** Create methods like `registerDbSetting($driver, $key, $value)` which build the alias `database.$driver.$key` internally. This provides a structured API that prevents incorrect aliases.

---

## 7. Origins and Computer Science Concepts

### Conceptual Origins

The idea of a central place for configuration is as old as complex software itself.

*   **The Windows Registry:** As you mentioned, this is a famous (and famously complex) example. It provides a hierarchical database for the entire operating system and its applications to store settings, from hardware configurations to user preferences.
*   **UNIX/Linux Configuration Files:** The concept is also embodied by files in `/etc`, and by application-specific `.ini` or `.conf` files. These often use sections `[section]` and key-value pairs, which is a form of hierarchy that a dot-notation registry emulates in memory.
*   **macOS Property Lists (`.plist`):** These are XML or binary files that store nested key-value data, serving a similar purpose for macOS and iOS applications.

### Core Computer Science Concepts

*   **Hash Table (Associative Array):** This is the fundamental data structure that makes a registry efficient. A hash table provides, on average, constant-time O(1) complexity for lookups, insertions, and deletions. Our registry is essentially a tree of nested hash tables.
*   **Global State:** A Singleton Registry is a form of **managed global state**. This is a significant architectural decision. 
    *   **Pros:** Unmatched convenience and a single, predictable point of access for universal constants and settings.
    *   **Cons:** Can make unit testing harder, as tests can't easily swap in their own configuration. It can hide the dependencies of a class, as the class can just reach out to the global registry at any time.
    *   Using a Registry is a conscious trade-off, accepting the risks of global state for the convenience of accessing ubiquitous configuration.

### Implementations in PHP Frameworks

Most major PHP frameworks use this pattern for configuration.

*   **Laravel:** Laravel's `config()` helper function and `Config` facade are a classic Registry implementation. Configuration is loaded from `.php` files in the `config/` directory into a central repository.
    ```php
    // Get the application name
    $appName = config('app.name');

    // Get the MySQL database host
    $dbHost = config('database.connections.mysql.host');
    ```

*   **Symfony:** Symfony uses a `ParameterBag` within its main Dependency Injection Container. Parameters are defined in configuration files (e.g., `services.yaml`) and are accessed via the container.
    ```php
    # In services.yaml
    parameters:
        app.debug_mode: true

    # In a service or controller
    $isDebug = $container->getParameter('app.debug_mode');
    ```

```mermaid
sequenceDiagram
    participant User
    participant Controller
    participant ConfigFacade as config()
    participant ConfigRepository

    User->>Controller: GET /home
    activate Controller
    Controller->>ConfigFacade: config('app.name')
    activate ConfigFacade
    ConfigFacade->>ConfigRepository: get('app.name')
    activate ConfigRepository
    ConfigRepository-->>ConfigFacade: returns 'My Awesome App'
    deactivate ConfigRepository
    ConfigFacade-->>Controller: returns 'My Awesome App'
    deactivate ConfigFacade
    Controller-->>User: Responds with view containing app name
    deactivate Controller
```

---

## 8. Appendix: Questions

### How do you enforce immutability of a RegistryItem object once it is registered?

Based on the document, the key principle is that any modification should result in a **new** `RegistryItem` instance, rather than changing the original one.

You enforce this in PHP through a combination of three techniques:

1.  **Make properties private:** This prevents direct modification from outside the class.
2.  **Initialize all properties in the constructor:** The object is created in its complete and final state.
3.  **Omit setters, use "withers":** Instead of methods that change properties (like `setValue()`), you create methods that return a *new* instance of the object with the updated value. These are often called "wither" methods (e.g., `withValue()`).

Here is a code example of how the `RegistryItem` class could be implemented to be immutable:

```php
final class RegistryItem
{
    public function __construct(
        private readonly mixed $value,
        private readonly RegistryMetaData $metadata
    ) {
        // Properties are initialized here and cannot be changed.
        // Using 'readonly' provides an extra layer of enforcement.
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getMetadata(): RegistryMetaData
    {
        return $this->metadata;
    }

    /**
     * Returns a NEW RegistryItem with a different value.
     * The original object is not modified.
     */
    public function withValue(mixed $newValue): self
    {
        // Create and return a new instance with the new value
        return new self($newValue, $this->metadata);
    }
}
```

With this implementation, once a `RegistryItem` is created and registered, it cannot be altered. Any attempt to "change" it just gives you back a completely new object, preserving the integrity of the original.

```mermaid
classDiagram
    note "An immutable implementation of RegistryItem"
    class RegistryItem {
        -value: mixed
        -metadata: RegistryMetaData
        +__construct(value, metadata)
        +getValue(): mixed
        +getMetadata(): RegistryMetaData
        +withValue(newValue): RegistryItem
    }
```

### What if that object - when pulled by lookup() is unserialized / hydrated (or an array has an element added to it)? Does the serialized form of that object in the registry change also? What is best practice concerning this?

That's an excellent question. This touches on the lifecycle of a configuration value and the difference between the stored data and the in-memory representation.

The short answer is: **No, the original serialized form in the registry does not change.**

#### The Hydration/Unserialization Process is One-Way

Think of the process as a one-way factory assembly line:

1.  **Storage:** The registry stores the "raw material"—a serialized string, a simple array from a config file, or some other "dry" data.
2.  **Lookup & Hydration:** When you call `lookup('some.value')`, the registry's `resolve()` method acts as a factory. It takes the raw material and *builds a brand new object* from it. This is hydration/unserialization.
3.  **Return:** The registry gives you the newly created object.

The crucial point is that the finished product (the hydrated object) has **no link back to the raw material** (the serialized string). Modifying the object you received is like drawing a mustache on a car that just came off the assembly line; it doesn't change the factory's blueprint or the next car that gets made.

The same is true for arrays. When `lookup()` returns an array, PHP gives you a **copy** of that array. Adding an element to your copy does not affect the original array stored in the registry.

#### What is the Best Practice?

This behavior leads to a critical best practice: **Treat configuration as read-only and stateless.**

1.  **Configuration is for Reading, Not Writing:** The registry's purpose is to provide a consistent source of truth for configuration that is established when the application boots. It is not designed to be a place to store and manage application *state* that changes during a request.

2.  **Avoid In-Memory State Drift:** If you hydrate an object, modify it, and then another part of the application looks up and hydrates the *same* configuration value, it will get a *new, clean* object based on the original raw data. The two parts of your application would now have different versions of the "same" configuration, which is a recipe for subtle and hard-to-find bugs.

3.  **`resolve()` Should Always Return a Fresh Instance:** To enforce this read-only nature, the `resolve()` method should ideally perform the hydration every time it's called for a non-scalar value. This ensures that every part of the code gets a fresh, identical object, preventing one component from modifying a shared object and affecting another (the "action at a distance" problem in a new form).

**In summary:** If you need to manage data that changes, you should use a dedicated state management service, a database, or a cache—not the configuration registry. The registry should remain a pristine, read-only source of the application's initial setup.

---

## 9. Designing a Formal, Structured Registry API

As a framework matures, moving from a purely convention-based system (where users must remember to type `database.mysql.host` correctly) to a formal, structured API becomes essential. A formal API makes the registry safer, more explicit, and easier to use.

This is achieved by creating **Component-Specific Registry Facades** (or Helpers). These are classes that provide a dedicated, structured API for a specific category of the registry.

### The Core Pattern: Registry Facades

Instead of having every part of your framework call the generic `ServiceRegistry::register()` method, they interact with a facade designed for their specific domain.

*   For each major component (`Database`, `Cache`, `FileSystem`), you create a corresponding facade (`DatabaseRegistry`, `CacheRegistry`, `FileSystemRegistry`).
*   Each facade holds a reference to the single, main `ServiceRegistry` instance.
*   Each facade provides domain-specific methods (e.g., `DatabaseRegistry::addConnection(...)`).
*   These methods contain the logic to build the correct dot-notation alias and then call the underlying generic `ServiceRegistry`.

This pattern provides a layer of abstraction that enforces structure and correctness.

```mermaid
classDiagram
    class ServiceRegistry {
        +register(alias, value, metadata)
    }

    class DatabaseRegistry {
        -registry: ServiceRegistry
        +addConnection(name, host, port)
    }

    class CacheRegistry {
        -registry: ServiceRegistry
        +addStore(name, driver)
    }

    DatabaseRegistry --|> ServiceRegistry : uses
    CacheRegistry --|> ServiceRegistry : uses
```

### Enforcing Dot-Notation and Structure

This pattern directly enforces the dot-notation structure because the user **never writes the alias string themselves.** The method signature is the enforcement.

*   **Before (Error-prone):** `$registry->register('database.mysql.host', 'localhost');`
*   **After (Structured & Safe):** `$dbRegistry->addConnection('mysql', 'host', 'localhost');`

A typo in the `addConnection` method call will result in an immediate and obvious error from PHP, whereas a typo in the string might fail silently or lead to hard-to-find bugs.

#### Sample PHP Implementation

```php
// 1. The generic ServiceRegistry remains as designed.

// 2. Create a specialized facade for database configuration.
class DatabaseRegistry
{
    // The top-level category is a constant, ensuring consistency.
    private const CATEGORY = 'database';

    public function __construct(private ServiceRegistry $registry)
    {}

    /**
     * Adds a full database connection configuration.
     */
    public function addConnection(string $name, string $driver, string $host, int $port, string $user, string $pass): self
    {
        $baseAlias = self::CATEGORY . ".connections." . $name;

        $this->registry->register("{$baseAlias}.driver", $driver);
        $this->registry->register("{$baseAlias}.host", $host);
        $this->registry->register("{$baseAlias}.port", $port, ['type' => 'int']);
        $this->registry->register("{$baseAlias}.user", $user);
        $this->registry->register("{$baseAlias}.pass", $pass);

        return $this;
    }
}

// 3. How client code uses the formal API.
$mainRegistry = ServiceRegistry::getInstance();
$dbRegistry = new DatabaseRegistry($mainRegistry);

$dbRegistry->addConnection('mysql_primary', 'mysql', 'localhost', 3306, 'user', 'pass');
```

### Systematizing the Interaction

To make this pattern consistent across your entire framework, you can use interfaces and have the main `ServiceContainer` manage the facades.

1.  **Create an Interface:** Define a `RegistryFacadeInterface` to ensure all facades share a common structure.
2.  **Dependency Injection:** Instead of `new DatabaseRegistry(...)`, you would ask the `ServiceContainer` for the `DatabaseRegistry`. The container would be responsible for creating it and injecting the main `ServiceRegistry` instance.

#### Sequence Diagram: Formal API Flow

This diagram shows how the facade acts as an intermediary, creating multiple specific registry entries from a single, structured method call.

```mermaid
sequenceDiagram
    participant Client
    participant DB_Registry as DatabaseRegistry
    participant Main_Registry as ServiceRegistry

    Client->>DB_Registry: addConnection('mysql', 'host', 'localhost', ...)
    activate DB_Registry

    DB_Registry->>DB_Registry: Build alias: "database.connections.mysql.host"
    DB_Registry->>Main_Registry: register("database.connections.mysql.host", "localhost")

    DB_Registry->>DB_Registry: Build alias: "database.connections.mysql.port"
    DB_Registry->>Main_Registry: register("database.connections.mysql.port", 3306)
    
    Note over DB_Registry, Main_Registry: ...and so on for user, pass, etc.

    deactivate DB_Registry
```

### Should the User Be Barred from Direct Registry Access?

This is a key architectural decision with a trade-off between strictness and flexibility.

*   **The Strict Approach (Bar Direct Access):** In this model, you would make the generic `ServiceRegistry::register()` method `protected` or `internal`. The only way to add settings would be through a dedicated, trusted facade. This guarantees that 100% of your registry entries are structured and validated. This is suitable for high-security or mission-critical applications where consistency is paramount.

*   **The Pragmatic Approach (Allow Direct Access):** This is the model used by most frameworks. The facades provide a convenient and safe API for 95% of use cases, but the generic `register()` method remains `public` as an "escape hatch." This allows developers to register one-off, unique, or module-specific settings without needing to create a dedicated facade method for a rare edge case.

**Best Practice:** For most frameworks, the **pragmatic approach** is best. Enforce the use of facades through team convention and documentation, but leave the generic method available for flexibility.

### Enforcing the Facade Pattern

How can you ensure all facades are built correctly? Beyond convention, you can use PHP's OOP features.

*   **Approach 1: Abstract Base Class (Recommended):** Create an `abstract class BaseRegistryFacade`. This class can hold the protected `$registry` property and the constructor. You can also force child classes to define their category.

    ```php
    abstract class BaseRegistryFacade
    {
        public function __construct(protected ServiceRegistry $registry) {}

        abstract protected function getCategory(): string;
    }

    class DatabaseRegistry extends BaseRegistryFacade
    {
        protected function getCategory(): string
        {
            return 'database';
        }
        // ... specific methods like addConnection ...
    }
    ```

*   **Approach 2: PHP 8 Attributes:** A more modern, decoupled approach. You create an attribute that declares a class as a facade for a specific category.

    ```php
    #[\Attribute(\Attribute::TARGET_CLASS)]
    final class RegistryFacade
    {
        public function __construct(public string $category) {}
    }

    // The ServiceContainer could read this attribute via reflection
    // to automatically know how to categorize this facade.
    #[RegistryFacade('database')]
    class DatabaseRegistry
    {
        // ...
    }
    ```

```mermaid
flowchart TD
    subgraph "Facade Creation via Service Container"
        A[Client requests DatabaseRegistry] --> B(ServiceContainer);
        B --> C{Instantiate DatabaseRegistry};
        C --> D[Get ServiceRegistry instance];
        D --> E[Inject ServiceRegistry into DatabaseRegistry constructor];
        E --> F[Return completed facade];
    end
    F --> G[Client uses facade];
```