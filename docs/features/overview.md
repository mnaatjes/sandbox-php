# Feature Overview

This document provides a high-level overview of the core architectural features of the MVCFrame framework. Each feature is designed with clean architecture, maintainability, and separation of concerns in mind.

1. **Dependency Injection Container**
*   **Purpose:** Manages class dependencies and promotes Inversion of Control (IoC). Instead of components creating their own dependencies, they receive them from the container.
*   **Benefit:** Decouples your components, making them easier to test, swap, and manage. It is the foundation of a flexible and maintainable architecture.

2. **HTTP Messaging (Request & Response)**
*   **Purpose:** Provides a clean, object-oriented abstraction over PHP's native HTTP functions. The `Request` object represents the incoming request, and the `Response` object represents the outgoing response.
*   **Benefit:** Creates a predictable and testable way to interact with HTTP data. It isolates your application logic from the global state of `$_GET`, `$_POST`, `$_SERVER`, etc.

3. **Views**
*   **Purpose:** Handles the presentation layer of your application, completely separate from business logic. The View class is responsible for taking data and rendering it within a template.
*   **Benefit:** Enforces Separation of Concerns. Your controllers and services don't know *how* a page is rendered, they only provide the data. This allows designers to work on templates without touching application logic.

4. **Session Management**
*   **Purpose:** Offers a clean, object-oriented API for managing user sessions. It acts as a wrapper around PHP's native `$_SESSION` global array.
*   **Benefit:** Provides a secure and testable way to handle session data, removing direct dependency on the global `$_SESSION` variable throughout the codebase.

5. **Service Management**
*   **Purpose:** Defines the process of registering all core framework services (like the session manager, view renderer, etc.) into the Dependency Injection Container.
*   **Benefit:** Centralizes the application's setup logic. It provides a single, predictable place to see how all the major components of the framework are wired together.


## 1.0 

### General List
- [ ] Ability to have global variables in renders
- [ ] Router / Controller Distinction between types of params
  - [ ] GET params
  - [ ] URI params
  - [ ] POST params
  - [ ] Application *globally available* params
- [ ] Add prefixes and names to routes
  - [ ] Debugging function to view all routes and names
  - [ ] Ability to use names in View renders (and redirects?)
- [ ] Controller / Handler heirarchy
- [ ] View Heirarchy
  - [ ] View children have ability to pass on variables
  - [ ] Cascade of renders

### Application Workflow

1. User Creates Controllers, Middleware, etc.
2. ServiceProviders registered / bound (using `app()`) in `config/app.php`

### Fascades

### Helper Functions

### Service Container & `app()` Global Helper Function

**Attributes**
- [ ] Helper Function `app()` injects and returns instances from Service Container
- [ ] Can distinguish between *Singleton* and *Regular* instances
  - [ ] When registering `app()->singleton(SomeClass::class...)`
  - [ ] When returning `return $this->singletons[key]`

**Binding and Returning Service Instances via `app()`:**
- [ ] Make and Resolve an instance; passing argument `$className = app(ClassName::class)`
- [ ] Return ServiceContainer; no argument: `$container = app()`
  - [ ] Explicitly bind methods / dependencies directly: `$container->bind('SomeKey', handler)`

**Automatic Binding of Dependencies**
- [ ] Binding via type-hints; e.g. `__construct(SomeClass $instance)` populates $instance with `SomeClass` from ServiceContainer
- [ ] Workflow:
  - [ ] **Container asked to make an instance of `DependantClass`**
    - [ ] Container cannot find binding for `DependantClass`
    - [ ] As container binds new `DependantClass` it also fulfills auto-wiring process
  - [ ] **Container employs `ReflectionClass(DependantClass::class)`**
    - [ ] Checks if `DependantClass` has a constructor
      - [ ] No `__construct()` means no dependency
      - [ ] Has `__construct()`
        - [ ] Get Constructor
        - [ ] Get Constructor parameters
      - [ ] **Resolve each parameter**
        - [ ] Iterate through $parameters array
        - [ ] Use `$parameter->getType()` to find type-hint: e.g. `SomeClass`
        - [ ] Container calls itself internally with type-hint: `$someDependency = app(SomeClass::class)`
        - [ ] Container binds SomeClass if not already bound
        - [ ] Container returns SomeClass instance
    - [ ] **Instantiate the Original Class** `DependencyClass`
      - [ ] Container now has a resolved instance of every parameter of `DependencyClass` with its dependencies
      - [ ] Return the Final Instance

**Register Boot Methods of ServiceProviders**
- [ ] Service Registered with `ServiceProvider->register()` method
- [ ] ServiceContainer loops through ServiceProvider Classes and calls boot()
  - [ ] `SomeRegisteredService->boot()`
  - [ ] Dont after *ALL* Services have been registered - (at runtime?)
- [ ] 

### Sessions Management

### Router

### *Globally Available Parameters*

**Utilizes the following:**
- ViewFactory
  - View Fascade
    - View::share()
- view() helper function (i.e. `render(...)`)
- Router Object
- ServiceProvider parent class
  - boot() method
    - Uses View::share() within boot()

**Lifecycle of Data**
1. View::share() fascade binds data in key=>value pairs to ViewFactory array property `$this->shared`
2. ViewFactory merges its internal `$this->shared` array with data array passed via `render(key=>'value')`
   1. Data in `$this->shared` overwritten if `render(key...)` is the same as `$this->shared[key]`!
   2. There are 2 layers of data:
      1. Data passed directly into `render('path', [data...])`
      2. Data registerd in `View::share()`
3. Data is intended exlusively for *Views*
   1. Flow is *one-way* 
      1. From application (controllers and services) 
      2. To Views (presentation layer)

**Router passing URI parameters**
- [ ] Find string between `{key}` brackets
  - [ ] Register parameter key
  - [ ] Assign parameter key to route
- [ ] Store value in array of parameters per route
  - [ ] Check incoming GET for URI parameter
  - [ ] Pull value and assign to key from `{key}`
- [ ] Provide URI params array to Controller / Handler

**Passing Data via Router *Globally***
- [ ] Uses / Depends upon **View** and **ServiceContainer**

- [ ] **Passing Data to Controller / Handler**
  - [ ] Router invokes controller / handler method
  - [ ] Uses ServiceContainer to inject route (URI) parameters as argument for method

**Making Data *Globally* for Views** i.e. renders
- Makes data available in the view layer

- [ ] Share it from the *Service Provider*
    - [ ] Create **ServiceProvider** parent class
  - [ ] Uses common method `boot()` in all Service Classes; e.g. `UserService->boot()`
    - [ ] `boot()` employs View::share() to register key => value for *globally available* variable
    - [ ] Variable is now *globally* available

**View::share() Mechanism**
- [ ] `View::share` fascade calls `ViewFactory->share()`
  - [ ] Fascade for ViewFactory registers key=>value pair (i.e. saves) with `$this->shared` array
- [ ] Later, when controller employs `view('path', $data)` (or my `render('path', $data)`)
  - [ ] ViewFactory merges internal `$this->shared` array with `$data` passed into `render(...)`

### ViewFactory and View Fascade

#### ViewFactory

**Attributes**
- [ ] Returns an object for every *View* generated
- [ ] Singleton

**Properties**
- [ ] internal array `$shared`

### ServiceProviders

*What is a ServiceProvider?*
- *NOT* Controllers or Middleware
  - Controllers and Middleware are *Consumers* of the ServiceProviders
- *Setup Classes* whose job is to prepare the application by registering services
- Wires the engine together
  - Controller drives the car

**Lifecycle**
1. Create specific Service Class: 
    ```php
    class GreetService {
        private $greetings = ["hello", "goodbye"];
        public function getGreeting(){return $this->greetings[array_rand($this->greetings)];}
    }
    ```
2. Create ServiceProvider Class for GreetService which inherits **ServiceProvider** parent
    ```php
        class GreetServiceProvider extends ServiceProvider {
            public function register(){app()(GreetService::class, function(){return new GreetService();});}
        }
    ```
3. Populate `boot()` method
4. Register provider with ServiceContainer in `config/app.php`
   ```php
    // Registration Array
    return [
        //...,
        "providers" => [
            App\Providers\GreetServiceProvider::class,
            //...
        ],
        //...
    ];
   ```
5. ServiceContainer calls `GreetServiceProvider->register()`
   1. Automatically registers them
6. Using Service
    ```php
    class SomeController {
        private $greeter;
        public function __construct(GreetService $greeter_service){
            // GreetService dependency automatically injected via ServiceContainer
            $this->greeter = $greeter_service;
        }
        public function index(){
            $phrase = $this->greeter->getGreeting();
        }
    }
    ```
7. 

**Key Features**
- [ ] Prepare Application
- [ ] Register Services
- [ ] Register Event Listeners
- [ ] Register Middleware
- [ ] Register Routes
- [ ] Run when the application *boots* up - before any request is handled
- [ ] Classes Located in `app/Providers`
- [ ] Registed in `config/app.php`

- [ ] **Phases / Key Methods**
  - [ ] `register()` method
    - [ ] Applied to *All* ServiceProviders in application
    - [ ] Bind services into the container
    - [ ] *Never* attempt to resolve the service
  - [ ] `boot()` method
    - [ ] After all providers are registered
      - [ ] **ServiceContainer** Loops through all and call boot() on each one
      - [ ] 