# TODO

## 09-07-2025

### Application TODOs
- [ ] Add microtime property to track benchmarking
- [ ] Add `$registered` property
- [ ] Add `$booted` property
- [ ] Add `$serviceProviders` array
- [ ] Make `bootstrap()` method
  - [ ] Loads Services
  - [ ] Loads Fascades

### Core Orchestration TODOs
- [ ] Use ***PathRegistry** to Register Remaining Paths:
  - Configuration Paths:
    - [ ] Fascades
    - [ ] Core Services
- [ ] Create **ServiceProvider** parent instance
- [ ] Create **ViewService** and **ViewServiceProvider**
- [ ] Integrade Fascades
  - [ ] have method `isFascade()` for all fascades
  - [ ] **Register Core Fascades**
    - [ ] Load list of fascades
    - [ ] Reset fascades: clear resolved instances
    - [ ] Register / Inject Fascades into Container

### Runtime TODOs
- [ ] Use **Application** to register on *Runtime*:
  - [ ] **Register Services**
	- [ ] ViewServiceProvider
	- [ ] RadioServiceProvider

## Restructure
- [ ] Turn **Container** into an *Abstract Class* to be used
  - [ ] Add a Container Interface
  - [ ] Streamline container methods
  - [ ] A Container can be either:
    - [ ] *Singleton* Container - containing Shared Instances
    - [ ] *Transient* Container - containing closures that render instances
  - [ ] Use it for various types of Containers:
    - [ ] **Cache** - Reflection Cache / AutoWired Dependencies
    - [ ] 