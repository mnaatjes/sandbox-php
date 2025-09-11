## 3.0 Freature Flows

Flows, Lifecycles, and States of existing Components (see feature-components.md).

### 3.1 *Globally Available* Parameters Flow

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

---

### 3.2 Sessions Management Flow

---

### 3.3 Route Definition, Discovery, and Execution Flow

**Route Definition and Namespaces**
- [ ] Route defined in `routes/web.php`
  - [ ] Route string `/users` param
  - [ ] Route handler defined in an array: `[UserController::class, index]`
  - [ ] Applies Middleware
- [ ] Executes Controller via ServiceContainer
  - [ ] Route matched
  - [ ] Pull instance from ServiceContainer
  - [ ] Injects Dependencies
  - [ ] Invokes associated Controller method `index`

**Debugging Names and Groups Map**
- [ ] Lists Controllers as `SomeController@method`
- [ ] Lists Middleware as
- [ ] Lists names as
- [ ] Lists groups as

**Heirarchy of Execution**

*Onion Model: Http Request Travels **inward** through concentric layers of middleware to the core Controller*

**A: Inbound Traverse**
1. [ ] Request reaches `app/Http/Kernel.php` - class is central hub
2. [ ] Request passed through *Global Middleware Stack* which were defined in the `$middleware` property of the Kernel
3. [ ] Router Dispatches Route
   1. [ ] Request hits *Router* class
   2. [ ] Route matched to
      1. [ ] Associated *Group Middleware*
      2. [ ] Associated *Route Specific Middleware*
      3. [ ] Associated Controller

**B: Outbound Traverse**
1. [ ] After Controller Execution, Router matches to
   1. [ ] *Route Specific Middleware*
   2. [ ] *Group Middleware*
   3. [ ] *Global Middleware*
2. [ ] Response - created by Controller - Sent to Browser

