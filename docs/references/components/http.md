# HTTP Messaging (Request & Response)

Handling HTTP input and output cleanly is critical for a web framework. These classes provide an object-oriented abstraction layer over PHP's messy global variables (`$_GET`, `$_POST`, `$_SERVER`, etc.) and functions (`header()`, `echo`).

### Design Philosophy

*   **Separation of Concerns:** Your application logic should not be coupled to PHP's global state. The `Request` class encapsulates all incoming data, and the `Response` class encapsulates all outgoing data. Your controllers and services should only ever interact with these objects.
*   **Clean Architecture:** By depending on these abstractions instead of concrete globals, your core application becomes independent of the delivery mechanism (HTTP). You could, in theory, drive your application from a command line interface by creating a different kind of `Request`.
*   **Maintainability & Testability:** You can easily create mock `Request` objects to test your controllers under various conditions (e.g., different HTTP methods, headers, or body data) without having to actually send HTTP requests.

---

### `Request` Class Structure

The `Request` object should be **immutable**. It represents a single, specific request and should not be changed during the application lifecycle.

```
Request
├── Properties
│   ├── protected queryParams: array
│   ├── protected parsedBody: array
│   ├── protected method: string
│   └── protected path: string
│
└── Methods
    ├── __construct(array $queryParams, array $parsedBody, string $method, string $path)
    ├── createFromGlobals(): self
    ├── getPath(): string
    ├── getMethod(): string
    ├── getQueryParams(): array
    └── getParsedBody(): array
```

#### Method Details
*   **`createFromGlobals(): self`**: A static factory method that reads from `$_GET`, `$_POST`, `$_SERVER` to build a new `Request` object at the start of the application lifecycle.
*   **`getPath(): string`**: Gets the request path (e.g., `/users/1`).
*   **`getMethod(): string`**: Gets the HTTP method (e.g., `GET`, `POST`).

---

### `Response` Class Structure

The `Response` object is **mutable**, allowing headers and the body to be built up before being sent.

```
Response
├── Properties
│   ├── protected content: string
│   ├── protected statusCode: int
│   └── protected headers: array
│
└── Methods
    ├── __construct(string $content = '', int $statusCode = 200, array $headers = [])
    ├── setStatusCode(int $code): void
    ├── setHeader(string $name, string $value): void
    ├── setContent(string $content): void
    └── send(): void
```

#### Method Details
*   **`send(): void`**: This is the final action. This method uses the properties of the `Response` object to send the HTTP status code, all registered headers, and finally `echo` the content. After this is called, the script should terminate.

---

### Example Usage

```php
<?php
// In your front controller (public/index.php)
$request = MVCFrame\Http\Request::createFromGlobals();

// ... router sends request to a controller ...

// In a controller
public function show(Request $request)
{
    $user = $this->userService->find($request->getQueryParams()['id']);

    $content = "<h1>Hello, {$user->name}</h1>";
    
    $response = new MVCFrame\Http\Response($content, 200);
    $response->setHeader('Content-Type', 'text/html');
    
    return $response;
}

// ... front controller receives response from controller and calls send() ...
$response->send();
```
