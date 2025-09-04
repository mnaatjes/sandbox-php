# Feature Overview

This document provides a high-level overview of the core architectural features of the MVCFrame framework. Each feature is designed with clean architecture, maintainability, and separation of concerns in mind.

---

### 1. Dependency Injection Container
*   **Purpose:** Manages class dependencies and promotes Inversion of Control (IoC). Instead of components creating their own dependencies, they receive them from the container.
*   **Benefit:** Decouples your components, making them easier to test, swap, and manage. It is the foundation of a flexible and maintainable architecture.

### 2. HTTP Messaging (Request & Response)
*   **Purpose:** Provides a clean, object-oriented abstraction over PHP's native HTTP functions. The `Request` object represents the incoming request, and the `Response` object represents the outgoing response.
*   **Benefit:** Creates a predictable and testable way to interact with HTTP data. It isolates your application logic from the global state of `$_GET`, `$_POST`, `$_SERVER`, etc.

### 3. Views
*   **Purpose:** Handles the presentation layer of your application, completely separate from business logic. The View class is responsible for taking data and rendering it within a template.
*   **Benefit:** Enforces Separation of Concerns. Your controllers and services don't know *how* a page is rendered, they only provide the data. This allows designers to work on templates without touching application logic.

### 4. Session Management
*   **Purpose:** Offers a clean, object-oriented API for managing user sessions. It acts as a wrapper around PHP's native `$_SESSION` global array.
*   **Benefit:** Provides a secure and testable way to handle session data, removing direct dependency on the global `$_SESSION` variable throughout the codebase.

### 5. Service Management
*   **Purpose:** Defines the process of registering all core framework services (like the session manager, view renderer, etc.) into the Dependency Injection Container.
*   **Benefit:** Centralizes the application's setup logic. It provides a single, predictable place to see how all the major components of the framework are wired together.
