## 2.0 Major Components

### 2.1 Service Container

Includes `app()` global helper function.

#### 2.1.1 Attributes and Methods of ServiceContainer

**Use Cases**
- Registering Services in Container
- Pulling Service instances from container
- Dependency Injection
  - No difference between a *Service* and a *Dependency*
- 

**Attributes**
- [x] Helper Function `app()` injects and returns instances from Service Container
- [x] Can distinguish between *Singleton* and *Regular* instances
  - [x] When registering `app()->singleton(SomeClass::class...)`
  - [x] When returning `return $this->singletons[key]`
- [x] General Purpose Dependency Injection Container

**Methods**
- [x] `$this->bind()`
  - *Write* Operation
  - Registers the Service
  - [x] Binds a single dependency or service onto the container
  - [x] Binds a *Recipe* with a larger service onto the container
  - [x] Make ServiceContainer available as parameter to bind() $handler
  - [x] Properties
    - [x] Classname::class
    - [x] Closure / Handler / Callable / Recipe
    ```php
    $this->app->bind(UserService::class, function($app){
        return new UserService(
            // $app->make resolves existing dependency in container
            $app->make(SomeDependency::class),
            $app->make(AnotherDependency::class)
        )
    })
    ```
- [x] `$this->make()`
  - *Read* Operation
  - Resolves the Service as Runtime when a service or dependency is needed
  - [x] Create (again if not a singleton) and get instance from container
  - Lifecycle
    - [x] Find the *recipe* (closure)
      - [x] Executes closure (creates object, executes handler)
      - [x] Returns new instance or handler result
    - [ ] Can't find binding (closure)
      - [ ] Creates the object automatically using *Reflection*
      - [ ] Finds and returns

**Binding and Returning Service Instances via `app()`:**
- [x] Make and Resolve an instance; passing argument `$className = app(ClassName::class)`
- [x] Return ServiceContainer; no argument: `$container = app()`
  - [x] Explicitly bind methods / dependencies directly: `$container->bind('SomeKey', handler)`

**Automatic Binding of Dependencies**
- [x] Binding via type-hints; e.g. `__construct(SomeClass $instance)` populates $instance with `SomeClass` from ServiceContainer
- **Lifecycle:**
  - [x] **Container asked to make an instance of `DependantClass`**
    - [x] Container cannot find binding for `DependantClass`
    - [x] As container binds new `DependantClass` it also fulfills auto-wiring process
  - [x] **Container employs `ReflectionClass(DependantClass::class)`**
    - [x] Checks if `DependantClass` has a constructor
      - [x] No `__construct()` means no dependency
      - [x] Has `__construct()`
        - [x] Get Constructor
        - [x] Get Constructor parameters
      - [x] **Resolve each parameter**
        - [x] Iterate through $parameters array
        - [x] Use `$parameter->getType()` to find type-hint: e.g. `SomeClass`
        - [x] Cache calls `register()` itself internally with type-hint: `$someDependency = app(SomeClass::class)`
        - [x] Cache binds SomeClass if not already bound
        - [x] Cache returns SomeClass instance
    - [x] **Instantiate the Original Class** `DependencyClass`
      - [x] Cache now has a resolved instance of every parameter of `DependencyClass` with its dependencies
      - [x] Return the Final Instance

**Register Boot Methods of ServiceProviders**
- [ ] Service Registered with `ServiceProvider->register()` method
- [ ] ServiceContainer loops through ServiceProvider Classes and calls boot()
  - [ ] `SomeRegisteredService->boot()`
  - [ ] Dont after *ALL* Services have been registered - (at runtime?)
- [ ] 

---

### 2.2 ReflectionCache

Reflection Cache is a property of the service container that resolves un-explicitly-bound object instances

**Use Cases**
- [ ] Auto-wiring unbound class instances
- [ ] Stores these instances in static cache array $resolutions
- [ ] Uses Reflection API to resolve unknown / unbound classes
- [x] Validates $key strings from ServiceContainer->resolve() as class within scope
- [ ] Only contains class instances!

**Resolution Lifecycle**
1. [x] Validate
2. [x] Make Reflection
3. [x] Access Construct
   1. [x] No construct -> skip to 7
4. [x] Get Parameters
5. [x] Check type of parameters
6. [x] Check if parameters are objects and valid:
      1. [ ] OPTION: If non-object; check ENV variables for key=>value
   1. [x] Resolve recursively
   2. [x] Append to resolvedObjects array
   3. [x] Inject as dependencies
7. [x] Append to resolved Objects Array
8. [x] Inject as dependencies
9. [x] Return configured instance

---

### 2.2 Views

---

### 2.2.1 View Fascade

**Methods**
- [ ] `View::share('key', 'value')`
- [ ] `View::composer('somepath, ...)`

#### 2.2.2 ViewFactory

**Attributes**
- [ ] Returns an object for every *View* generated
- [ ] Singleton

**Properties**
- [ ] internal array `$shared`

---

### 2.2.3 View Helper Function

---

### 2.3 ServiceProviders

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
   ```php
    public function boot(){
        // registers variable "globally"
        View::share("sitename", "gamestop");
    }
   ```
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
- [ ] Register Services with own property `$this->app` which references global ServiceContainer
- [ ] Register Event Listeners
- [ ] Register Middleware
- [ ] Register Routes
- [ ] Run when the application *boots* up - before any request is handled
- [ ] Classes Located in `app/Providers`
- [ ] Registed in `config/app.php`

- [ ] **Key Methods**
  - [ ] `register()` method
    - [ ] Applied to *All* ServiceProviders in application
    - [ ] Bind services into the container
    - [ ] *Never* attempt to resolve the service
    - [ ] Uses `$this->app->bind()`
  - [ ] `boot()` method
    - [ ] Defined within ServiceProvider class
    - [ ] 
    - [ ] Runs *After* all providers are registered
      - [ ] **ServiceContainer** Loops through all and call boot() on each one
    - [ ] Interacts with other parts of the framework
      - [ ] Use Cases:
        - [ ] Share data with `View::share('key', 'value')`
        - [ ] Register a View Composer `View::composer('partials.nav)`
        - [ ] Define *Authorization Gates* `Gate::define(...)`
        - [ ] Register custom validatation `Validator::extend()`
        - [ ] Register Route Model Bindings

---

#### 2.3.1 ServiceProvider Parent Class

**Attributes**
- Shared functionality which *cannot* be accomplished with an Interface
- [ ] Contains the `app` property allowing access to the *global* ServiceContainer
  - e.g. `$this->app->bind()`
- [ ] Helper Methods
  - [ ] `$this->loadRoutesFrom($path)`
  - [ ] `$this->loadViewsFrom($path)`
  - [ ] `$this->loadMigrationsFrom($path)`
  - [ ] `$this->publishes([...])`

---

### 2.4 Router

**Use Cases**
- [ ] Define routes
- [ ] Associate routes with Controllers
- [ ] Employ Middleware
- [ ] Maintains Heirarchy of Handler Cascade (first method in - first out; all in the same family)
- [ ] Attach names
- [ ] Attach groups

**Attributes**
- [ ] RouteServiceProvider
- [ ] Route Fascade
- [ ] Router helper function?

**Methods**
- [ ] `->middleware()` Assigns aliases
  - [ ] `->middleware('auth')` single alias
  - [ ] `->middleware('[auth, verify]')` assigning multiple
- [ ] Assigning Groups by applying to it to multiple routes
    ```php
        Route::middleware('alias')->group(function(){
            Route::get('/somePath', [SomeController::class, 'method']),
            Route::get('/otherPath', [OtherController::class, 'method'])
        });
    ```
- [ ] Assigning middleware with parameters
  - [ ] `->middleware('aliasName:valueA,valueB')`

**Fascade Methods**
- [ ] `Route::get`
- [ ] `Route::post`
- [ ] `Route::group`
- [ ] `Route::middleware`

---

#### 2.4.1 Route Service Provider

- [ ] Exists in `app/Providers/RouteServiceProvider.php`
- [ ] Loads route files from `routes/web.php`. `routes/api/php`, etc.
  - [ ] Applies default Middleware groups for `web` and `api` etc. respectively
- [ ] Accesses ServiceContainer to load Controllers

---

### 2.5 Http Components

---

#### 2.5.1 Http Kernel

**Use Cases**
- [ ] Orchestrate the process of converting an Http Request into a Response
- [ ] Define the *onion* of global and group Middleware
- [ ] Pass incoming Request though middleware stacks
- [ ] Hand off request to *Router* object to get directions
- [ ] Receive Reponse generated by Controller/Router
- [ ] Pass outward through middleware stack again

**Properties**
- [ ] Global Middleware Stack
  - [ ] `$middleware=[]`
- [ ] Group level Middleware Stack
  - [ ] `$middlewareGroups=["groupName=> [middleware methods...]]`
- [ ] Route Specific Middleware Aliases
  - [ ] `$routeMiddleware=["auth=>Authenticate::class], ...`
  - [ ] Assigned to Groups or used individually
    - [ ] 
  - [ ] Applied to Route files by *RouteServiceProvider*
- [ ] Assigned Controller

---

#### 2.5.2 Http Request Object

---

#### 2.5.2 Http Response Objects