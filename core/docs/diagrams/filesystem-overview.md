# FileSystem Component Overview

This document provides an overview of the core classes within the `MVCFrame\FileSystem` component, which is responsible for abstracting file and directory operations, managing application paths, and loading various file types.

---

## 1. `Path` Class

*   **Purpose:** The foundational class for the FileSystem component. It represents any generic filesystem path (file, directory, or non-existent path) and provides common operations applicable to all path types.
*   **Key Properties & Methods:**
    *   `pathName`: The string representation of the path.
    *   `create(string $path)`: Static factory method to create `Path`, `File`, or `Directory` objects based on the given path.
    *   `join(...$paths)`: Static method to join multiple path segments.
    *   `exists()`: Checks if the path exists on the filesystem.
    *   `isFile()`: Checks if the path points to a file.
    *   `isDir()`: Checks if the path points to a directory.
    *   `getParent()`: Returns the parent directory path.
    *   `getBasename()`: Returns the base name of the path.

*   **Class Diagram:**
    ```mermaid
    classDiagram
        class Path {
            #pathName: string
            +__construct(string $path_name)
            +__toString(): string
            +static create(string $path): self
            +static join(...$paths)
            +exists(): bool
            +isFile(): bool
            +isDir(): bool
            +isPopulated(): ?bool
            +getParent(): self
            +getBasename(): string
            +getType(): string
        }
    ```

*   **Sequence Diagram: `Path::create()`**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant Path
        participant File
        participant Directory

        Caller->>Path: create('/path/to/file.txt')
        Path->>Path: is_file('/path/to/file.txt')
        Path-->>Path: true
        Path->>File: new File('/path/to/file.txt')
        File-->>Path: FileObject
        Path-->>Caller: FileObject

        Caller->>Path: create('/path/to/dir')
        Path->>Path: is_file('/path/to/dir')
        Path-->>Path: false
        Path->>Path: is_dir('/path/to/dir')
        Path-->>Path: true
        Path->>Directory: new Directory('/path/to/dir')
        Directory-->>Path: DirectoryObject
        Path-->>Caller: DirectoryObject

        Caller->>Path: create('/non/existent')
        Path->>Path: is_file('/non/existent')
        Path-->>Path: false
        Path->>Path: is_dir('/non/existent')
        Path-->>Path: false
        Path->>Path: new Path('/non/existent')
        Path-->>Caller: PathObject
    ```

---

## 2. `File` Class

*   **Purpose:** Extends `Path` to specifically represent a file. It provides methods for reading, writing, loading, and rendering file contents based on their type.
*   **Key Properties & Methods:**
    *   `read()`: Reads the raw content of the file.
    *   `write(string $content)`: Writes content to the file.
    *   `load()`: Loads and parses file content (JSON, PHP return value, HTML raw) based on extension.
    *   `render(array $data)`: Renders the file as a template (PHP, HTML) with provided data.
    *   `execute()`: Executes a PHP file for side effects.
    *   `getExtension()`: Returns the file extension.
    *   `isPHP()`, `isJSON()`, `isHTML()`: Type checking methods.

*   **Class Diagram:**
    ```mermaid
    classDiagram
        Path <|-- File
        class File {
            -PHP_EXTENSIONS: array
            -CSV_DEFAULTS: array
            +read(): string
            +write(string $content): void
            +load(): mixed
            +execute(): void
            +render(array $data): string
            +isPHP(): bool
            +isJSON(): bool
            +isHTML(): bool
            +getExtension(): string
            -loadJSON(): array
            -loadYAML()
            -loadPHP(): mixed
            -loadCSV(): array
            -loadHTML(): string
            -executePHP()
            -renderHTML(array $data)
            -renderPHP(array $data): string
        }
    ```

*   **Sequence Diagram: `File::load()`**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant File
        participant File.loadJSON
        participant File.loadPHP
        participant File.loadHTML
        participant File.loadCSV

        Caller->>File: load()
        File->>File: getExtension()
        alt if .json
            File->>File.loadJSON: loadJSON()
            File.loadJSON->>File: read()
            File-->>File.loadJSON: content
            File.loadJSON-->>File: parsedData
            File-->>Caller: parsedData
        else if .php
            File->>File.loadPHP: loadPHP()
            File.loadPHP-->>File: returnedValue
            File-->>Caller: returnedValue
        else if .html
            File->>File.loadHTML: loadHTML()
            File.loadHTML->>File: read()
            File-->>File.loadHTML: content
            File.loadHTML-->>File: rawHTML
            File-->>Caller: rawHTML
        else if .csv
            File->>File.loadCSV: loadCSV()
            File.loadCSV-->>File: parsedCSVData
            File-->>Caller: parsedCSVData
        else
            File->>Caller: throws Exception (Unsupported)
        end
    ```

*   **Sequence Diagram: `File::render()`**
    ```mermaid
    sequenceDiagram
        participant Caller
        participant File
        participant File.renderPHP
        participant File.renderHTML

        Caller->>File: render(data)
        File->>File: getExtension()
        alt if .php
            File->>File.renderPHP: renderPHP(data)
            File.renderPHP-->>File: renderedOutput
            File-->>Caller: renderedOutput
        else if .html
            File->>File.renderHTML: renderHTML(data)
            File.renderHTML-->>File: renderedOutput
            File-->>Caller: renderedOutput
        else
            File->>Caller: throws Exception (Unsupported)
        end
    ```

---

## 3. `Directory` Class

*   **Purpose:** Extends `Path` to specifically represent a directory. It provides methods for listing contents and creating directories.
*   **Key Properties & Methods:**
    *   `listContents()`: Returns an array of items within the directory.
    *   `makeDir()`: Creates the directory on the filesystem.

*   **Class Diagram:**
    ```mermaid
    classDiagram
        Path <|-- Directory
        class Directory {
            +listContents(): array
            +makeDir()
        }
    ```

---

## 4. `FileSystem` Class

*   **Purpose:** The central manager for the FileSystem component. It's responsible for initializing the base path, discovering and registering application/framework paths (as `Path`, `File`, or `Directory` objects) into the `ServiceRegistry`.
*   **Key Properties & Methods:**
    *   `basePath`: The base `Path` object for the application.
    *   `registry`: The `ServiceRegistry` instance where paths are stored.
    *   `__construct(string|Path|Directory $base_path, ServiceRegistry $service_registry)`: Initializes the FileSystem manager.
    *   `getInstance()`: Static method to retrieve the singleton instance.
    *   `registerFrameworkPaths()`: Discovers and registers framework-specific paths.
    *   `registerApplicationPaths()`: Discovers and registers application-specific paths.
    *   `registerPath(string $alias, string|Path|File $value)`: Registers a path/file into the `ServiceRegistry` with appropriate prefixes (`dir.`, `file.`, `path.`).
    *   `determinePrefix($value)`: Determines the correct prefix based on the type of path/file.
    *   `normalize(string $alias, string $prefix)`: Normalizes aliases with prefixes.
    *   `getPath(string $alias)`: Retrieves a path/file object from the `ServiceRegistry`.
    *   `all()`: Returns a structured array of all registered paths/files.
    *   `list()`: Returns a flattened array of all registered paths/files with dot-notation aliases.

*   **Class Diagram:**
    ```mermaid
    classDiagram
        class FileSystem {
            -registry: ServiceRegistry
            -instance: FileSystem
            -basePath: Path
            -APP_DIR: array
            -FRAME_DIR: array
            +__construct(string|Path|Directory $base_path, ServiceRegistry $service_registry)
            +static getInstance()
            -registerFrameworkPaths(): void
            -registerApplicationPaths(): void
            +registerPath(string $alias, string|Path|File $value): void
            -determinePrefix($value): string
            -normalize(string $alias, string $prefix): string
            +getPath(string $alias)
            +all(): array
            +list(): array
            -flatten(?array $arr, string $prefix): array
        }
        FileSystem --> ServiceRegistry : uses
        FileSystem --> Path : creates
        FileSystem --> File : creates
        FileSystem --> Directory : creates
    ```

*   **Sequence Diagram: `FileSystem` Initialization & Path Registration**
    ```mermaid
    sequenceDiagram
        participant Application
        participant FileSystem
        participant Path
        participant Directory
        participant File
        participant ServiceRegistry

        Application->>FileSystem: new FileSystem(basePath, registry)
        FileSystem->>Path: create(basePath)
        Path-->>FileSystem: PathObject (basePath)
        FileSystem->>FileSystem: registerPath('base', basePath)
        FileSystem->>FileSystem: registerFrameworkPaths()
        loop for each FRAME_DIR entry
            FileSystem->>Path: create(relative_path)
            Path-->>FileSystem: PathObject
            FileSystem->>Path: join(basePath, PathObject)
            Path-->>FileSystem: AbsolutePathObject
            FileSystem->>FileSystem: determinePrefix(AbsolutePathObject)
            FileSystem->>FileSystem: normalize(key, prefix)
            FileSystem->>ServiceRegistry: register(normalizedAlias, AbsolutePathObject)
        end
        FileSystem->>FileSystem: registerApplicationPaths()
        loop for each APP_DIR entry
            FileSystem->>Path: create(relative_path)
            Path-->>FileSystem: PathObject
            FileSystem->>Path: join(basePath, PathObject)
            Path-->>FileSystem: AbsolutePathObject
            FileSystem->>FileSystem: determinePrefix(AbsolutePathObject)
            FileSystem->>FileSystem: normalize(key, prefix)
            FileSystem->>ServiceRegistry: register(normalizedAlias, AbsolutePathObject)
        end
        FileSystem-->>Application: FileSystemInstance
    ```
