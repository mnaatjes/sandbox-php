# Scaffold Descriptions and Workflow

## 1.0 Overview

**Version:** 3.0

## 2.0 Docker

## 3.0 PHP

### 3.1 Composer Definition and Framework Declaration

1. **Define Namespace**

    Use the PSR-4 autoloading standard. 
    - This means a namespace will map directly to a directory. 
    - Use a placeholder namespace for your framework

    **Composer Name:** `mnaatjes/mvc-framework`
    **Placeholder Namespace:** `MVCFrame`

2. **Create `composer.json` file in `~/`**

3. **Example Implementation:**

```json
{
    "name": "mnaatjes/mvc-framework",
    "description": "PHP Model, View, Controller Framework",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Michael Naatjes",
            "email": "michael.naatjes87@gmail.com"
        }
    ],
    "require": {
        "php": ">=8.1"
    },
    "require-dev": {
        "phpunit/phpunit":"^9.5"
    },
    "autoload": {
        "psr-4": {
            "MVCFrame\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MVCFrame\\Tests\\": "tests/"
        }
    },
    "files": [
        "src/helpers.php"
    ]
}
```

4. Run `composer install`

5. **Adding `/var/www/html` as safe directory in git:**

```bash
    git config --global --add safe.directory /var/www/html
```

6. **Inviting scripts under `files` to run:**
    - Require `vendor/autoload.php` in either:
        - `public/index.php` for production
        - `tests/main.php` for development

### 3.2 Git Clone Workflow 

1. Clone repository `git clone https://github.com/mnaatjes/sandbox-php.git`
2. `cd project directory`
3. Run in php service: `composer install`

### 3.3 Interactive Terminal 

The command you need uses the -it flags to get an interactive terminal (a shell) inside the container.

**Exec Command:** where `sandbox_php` is the php service defined in `docker-compose.yml`

```bash
    docker exec -it sandbox_php bash
```

**How it Works**
- **docker exec:** The command to execute a command inside a running container.
- **-it:** A combination of two flags:
    - **-i (interactive):** Keeps input open, allowing you to type into the container.
    - **-t (tty):** Allocates a terminal, making it look and feel like a real shell.
- **sandbox_php:** The name of the container you want to enter.
- **bash:** The program you want to run inside the container (in this case, the bash shell).

### 4.0 Composer

#### 4.1 Refreshing Skeleton via symlink

1. Make changes to `/core/composer.json` and save
2. Update auto-loader in mvc-framework: `composer dump-autoload` in core/
3. Move skeleton/ `cd ../skeleton`
4. Update skeleton/composer.json: `composer mnaatjes/mvc-framework`
5. Dump autoloader in skeleton/ `composer dump-autoload`