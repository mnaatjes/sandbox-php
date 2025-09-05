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
- [ ] Helper Function `app()` injects and returns instances from Service Container
- [ ] Can distinguish between *Singleton* and *Regular* instances
  - [ ] When registering `app()->singleton(SomeClass::class...)`
  - [ ] When returning `return $this->singletons[key]`
- [ ] General Purpose Dependency Injection Container

**Methods**
- [ ] `$this->bind()`
  - *Write* Operation
  - Registers the Service
  - [ ] Binds a single dependency or service onto the container
  - [ ] Binds a *Recipe* with a larger service onto the container
  - [ ] Properties
    - [ ] Classname::class
    - [ ] Closure / Handler / Callable / Recipe
    ```php
    $this->app->bind(UserService::class, function($app){
        return new UserService(
            // $app->make resolves existing dependency in container
            $app->make(SomeDependency::class),
            $app->make(AnotherDependency::class)
        )
    })
    ```
- [ ] `$this->make()`
  - *Read* Operation
  - Resolves the Service as Runtime when a service or dependency is needed
  - [ ] Create (again if not a singleton) and get instance from container
  - [ ] Lifecycle
    - [ ] Find the *recipe* (closure)
      - [ ] Executes closure (creates object, executes handler)
      - [ ] Returns new instance or handler result
    - [ ] Can't find binding (closure)
      - [ ] Creates the object automatically using *Reflection*
      - [ ] Finds and returns

#### 2.1.2 Lifecycle of ServiceContainer

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