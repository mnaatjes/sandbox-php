# Session Management

This component provides a clean, object-oriented API for interacting with PHP's session data. It serves as a wrapper around the `$_SESSION` superglobal, abstracting away the direct use of global state.

### Design Philosophy

*   **Separation of Concerns:** Your application logic should not be directly coupled to PHP's `$_SESSION` global. By using a `SessionManager` class, you create a clear, intentional API for session interactions.
*   **Testability:** Because you are no longer directly using a global variable, you can easily mock the `SessionManager` in your tests. You can simulate a user being logged in, having flash data, etc., without needing to manipulate the `$_SESSION` global.
*   **Maintainability:** If you ever need to change how sessions are stored (e.g., move to a database or Redis-backed session handler), you only need to change the implementation inside the `SessionManager`. The rest of your application code remains untouched because it only ever interacts with your defined `SessionManager` interface.

---

### `SessionManager` Class Structure

```
SessionManager
├── Properties
│   └── (none, interacts with $_SESSION)
│
└── Methods
    ├── start(): void
    ├── get(string $key, $default = null): mixed
    ├── set(string $key, $value): void
    ├── has(string $key): bool
    ├── remove(string $key): void
    ├── flash(string $key, $value): void
    ├── getFlash(string $key): mixed
    ├── regenerateId(): void
```

#### Method Details

*   **`start(): void`**: A wrapper for `session_start()`. Should be called before any other session methods.
*   **`get(string $key, $default = null)`**: Retrieves a value from the session by its key. Returns a default value if the key does not exist.
*   **`set(string $key, $value)`**: Stores a value in the session.
*   **`has(string $key)`**: Checks if a key exists in the session.
*   **`remove(string $key)`**: Deletes a key-value pair from the session.
*   **`flash(string $key, $value)`**: "Flashes" data to the session. This is data that will only be available for the very next request and is then automatically removed. Useful for success/error messages after a form submission.
*   **`regenerateId(): void`**: A wrapper for `session_regenerate_id(true)`. This is a crucial security practice to prevent session fixation attacks, and should be called whenever a user's privilege level changes (e.g., on login).

---

### Example Usage

```php
<?php

// Register the session as a service in your container
$container->bind(SessionManager::class, function() {
    $session = new MVCFrame\SessionManager();
    $session->start();
    return $session;
});


// In a LoginController
public function login(Request $request)
{
    // ... validate user credentials ...

    if ($isValid) {
        $session = $this->container->resolve(SessionManager::class);

        // Regenerate ID on login for security
        $session->regenerateId();

        // Store user ID in session
        $session->set('user_id', $user->id);

        // Flash a success message for the next request
        $session->flash('success_message', 'You have been successfully logged in.');

        // Redirect to dashboard...
    }
}

// In a view helper or middleware
function getLoggedInUser(SessionManager $session)
{
    if ($session->has('user_id')) {
        return $this->userRepository->find($session->get('user_id'));
    }
    return null;
}
```
