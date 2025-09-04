# Views

The View component is responsible for the presentation layer of your application. Its sole purpose is to render data into a template (like HTML), ensuring a strong separation between your application's logic and its presentation.

### Design Philosophy

*   **Separation of Concerns:** This is the primary principle for views. Controllers, services, and models should have no knowledge of HTML, CSS, or how data is displayed. They prepare and provide data, and the view's only job is to render it. This allows designers and developers to work independently.
*   **Maintainability:** Templates can be modified, redesigned, or even completely swapped (e.g., from HTML to JSON) without requiring any changes to your controller or business logic.

---

### `View` Class Structure

```
View
├── Properties
│   ├── protected templatePath: string
│   └── protected data: array
│
└── Methods
    ├── __construct(string $templatePath, array $data = [])
    ├── render(): string
    └── make(string $template, array $data = []): self
```

#### Method Details

*   **`make(string $template, array $data = []): self`**
    *   A static factory method for convenience. It creates a new `View` instance. The `$template` would be a dot-notation path like `users.show`, which maps to a file at `views/users/show.php`.

*   **`render(): string`**
    *   This is the core method. It takes the `data` array, extracts its contents into variables, and then includes the corresponding PHP template file. It uses output buffering (`ob_start()`, `ob_get_clean()`) to capture the output of the template file as a string, which it then returns. This string can then be set as the content of a `Response` object.

---

### Example Usage

#### Controller Logic
```php
<?php
// In a UserController

public function show()
{
    $user = $this->userService->find(1);
    $posts = $this->postService->getPostsForUser($user);

    // The controller knows nothing about HTML. It just passes data to the view.
    return View::make('users.show', [
        'user' => $user,
        'posts' => $posts
    ]);
}
```

#### View Template (`views/users/show.php`)
```php
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($user->name); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user->email); ?></p>

    <h2>Posts</h2>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li><?php echo htmlspecialchars($post->title); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
```

#### Tying it to the Response
```php
<?php
// In your router or front controller

$view = $userController->show(); // This returns a View instance

$response = new Response();
$response->setContent($view->render()); // The view is rendered into a string
$response->send();
```
