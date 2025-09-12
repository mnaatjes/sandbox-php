# Mermaid Diagrams Reference

## 1.0 Class Diagrams

```mermaid
classDiagram
    direction LR

    class Legend {
        <<Class>>
        +publicMember
        -privateMember
        #protectedMember
        +publicMethod()
        -privateMethod()
        #protectedMethod()
    }
    note for Legend "Defines attributes and methods. \n+ public, - private, # protected"

    class ParentClass
    class ChildClass

    class Whole
    class Part_Composition

    class Aggregate
    class Part_Aggregation

    class Source
    class Target_Association

    class Client
    class Dependency

    ParentClass <|-- ChildClass : Inheritance (Child 'is a' Parent)
    Whole *-- Part_Composition : Composition (Part is owned by Whole)
    Aggregate o-- Part_Aggregation : Aggregation (Part is part of Aggregate)
    Source --> Target_Association : Association (Source 'has a' Target)
    Client ..> Dependency : Dependency (Client 'uses a' Dependency)
```

## 2.0 Organizational Charts

### 2.1 Top-Down
```mermaid
graph TD;
    %% Define Nodes (Positions)
    A[CEO]

    B[VP of Engineering]
    C[VP of Marketing]
    D[VP of Sales]

    E[Director of Engineering]
    F[Engineering Manager]
    G[Lead Designer]

    H[Marketing Manager]
    I[Content Strategist]

    J[Sales Manager]
    K[Account Executive]
    
    L[Software Engineer]
    M[QA Engineer]

    %% Connect Nodes (Reporting Structure)
    A --> B;
    A --> C;
    A --> D;

    B --> E;
    B --> F;
    B --> G;

    E --> L;
    E --> M;
    
    C --> H;
    C --> I;

    D --> J;
    J --> K;
```

### 2.2 Subgraphs
```mermaid
graph TD;
    subgraph Dependency Container
        A(Register Transient ServiceX <br> using a factory closure)
    end

    subgraph Application
        B(ClientA) --> C{Request ServiceX};
        D(ClientB) --> E{Request ServiceX};
    end

    A --> C;
    A --> E;

    C --> F[Creates instance1 of ServiceX];
    E --> G[Creates instance2 of ServiceX];

    F --> B;
    G --> D;
```

## 3.0 UML Sequence Diagrams

### 3.1 Example 1
```mermaid
sequenceDiagram
    participant Application
    participant RegistryManager
    participant EventManager

    %% --- Phase 1: Registration ---
    Note over Application, RegistryManager: Registration Phase
    Application->>RegistryManager: new RegistryManager()
    Application->>RegistryManager: registerEvent('user.created', ...)
    Application->>RegistryManager: registerEvent('user.deleted', ...)

    %% --- Phase 2: Distribution & Execution ---
    Note over Application, EventManager: Execution Phase
    Application->>RegistryManager: getEventRegistry()
    activate RegistryManager
    RegistryManager-->>Application: eventRegistry
    deactivate RegistryManager

    Application->>EventManager: new EventManager(eventRegistry)
    Application->>EventManager: dispatch('user.created')
    activate EventManager
    EventManager-->>Application: void
    deactivate EventManager
```

### 3.2 Example 2

```mermaid
sequenceDiagram
    participant Application
    participant RegistryManager
    participant ContainerManager

    %% --- Phase 1: Registry Creation ---
    Note over Application, RegistryManager: Phase 1: Registry is Created
    Application->>RegistryManager: create('ContainerRegistry')
    activate RegistryManager
    RegistryManager-->>Application: void
    deactivate RegistryManager


    %% --- Phase 2: Instantiation & Injection ---
    Note over Application, ContainerManager: Phase 2: Manager is Instantiated with Registry
    
    # 1. Application gets the created registry from the RegistryManager
    Application->>RegistryManager: getContainerRegistry()
    activate RegistryManager
    RegistryManager-->>Application: containerRegistry
    deactivate RegistryManager
    
    # 2. Application creates the new manager, passing the registry into its constructor
    Application->>ContainerManager: new ContainerManager(containerRegistry)
```

### 3.3 Example 3

```mermaid
sequenceDiagram
    participant SM as ServiceManager
    participant SA as ServiceA
    participant SB as ServiceB
    participant NP as NextParticipant

    Note over SM, SB: Phase 1: Initialization
    
    SM->>SA: <<create>>
    activate SA
    SM->>SA: initialize()
    
    SM->>SB: <<create>>
    activate SB
    SM->>SB: configure("some_setting")
    deactivate SA
    deactivate SB

    Note over SM, NP: Phase 2: Main Interaction
    
    SM->>NP: doSomething()
    activate NP
    NP-->>SM: result
    deactivate NP
```

#### 3.4 Example 4

```mermaid
classDiagram
  direction TD

  class Manager {
    <<Abstract>>
    +Factory factory
    +getService()
  }

  class ServiceManager {
    +getService() Service
  }

  class Factory {
    +createService() Service
  }

  class Service {
    <<Interface>>
    +execute()
  }

  Manager <|-- ServiceManager
  Manager -- Factory : has a >
  ServiceManager ..> Factory : uses
  Factory ..> Service : creates >
```

#### 3.5 Example 5
```mermaid
sequenceDiagram
    participant Client
    participant Application
    participant FileSystem

    Client->>Application: start()
    activate Application

    Application->>FileSystem: Read manager_definitions.php
    activate FileSystem
    FileSystem-->>Application: Returns array of definitions
    deactivate FileSystem

    Note over Application: Now, instantiate managers based on definitions.

    loop For each definition in array
        create participant ManagerX as Manager
        Application->>ManagerX: __construct(definition)
    end

    deactivate Application
```

#### 3.6 Example 6

```mermaid
sequenceDiagram
    actor User
    participant Script as "bootstrap/app.php"
    participant App as "Application"

    User->>Script: Initiates Request
    activate Script

    Note over Script: The script's sole job is to create the application.
    Script->>App: new Application(basePath)
    activate App

    Note right of App: **Self-Orientation Process**<br/>The constructor uses the basePath to:<br/>1. Define all core directory paths.<br/>2. Bind these paths into its own container.<br/>3. Prepare for bootstrapping.

    App-->>Script: Returns fully oriented instance ($app)
    deactivate App

    Note over Script: The `$app` instance is now ready to handle the request.
    Script-->>User: (Request handling continues...)
    deactivate Script
```

#### 3.7 Example 7
```mermaid
sequenceDiagram
    participant User
    participant Server

    Note over User, Server: The vertical dashed lines are the Lifelines.

    User->>Server: login("user", "pass")
    activate Server

    Note right of User: This arrow represents a Message.

    Note right of Server: This box is the Activation.<br/>It shows the Server is busy.

    Server-->>User: Login Successful
    deactivate Server
```

#### 3.8 Example 8
```mermaid
sequenceDiagram
    participant EntryPoint as "public/index.php"
    participant App as "Application"

    EntryPoint->>App: new Application(basePath)
```

#### 3.9 Example 9
```mermaid
sequenceDiagram
    participant Client
    participant App as "Application"

    Client->>App: processData()
    activate App

    Note right of App: Now calling internal helper methods.
    
    App->>App: validateData()
    App->>App: saveData()

    App-->>Client: Success
    deactivate App
```

#### 3.10 Example 10
```mermaid
sequenceDiagram
    participant EntryPoint
    participant App as "Application"
    participant ConfigFile as "config/app.php"

    EntryPoint->>App: new Application()
    activate App

    Note right of App: Constructor begins execution.
    
    App->>ConfigFile: Read file contents
    activate ConfigFile
    ConfigFile-->>App: Returns config array
    deactivate ConfigFile

    Note right of App: Internal State Change:<br/>$this->config = [config array]

    App-->>EntryPoint: Returns new instance
    deactivate App
```

#### 3.11 Example 11
```mermaid
classDiagram
    direction LR

    class Application {
        +DIContainer container
        +boot()
        +get(string serviceName)
    }

    class DIContainer {
        -Map services
        +register(string name, object service)
        +resolve(string name) object
    }

    class FacadeManager {
        +getFacade(string name)
    }

    class HttpManager {
        +handle(Request request) Response
    }

    class DatabaseManager {
        +connect()
    }

    Application *-- DIContainer : has a
    Application ..> DIContainer : uses

    DIContainer ..> FacadeManager : provides
    DIContainer ..> HttpManager : provides
    DIContainer ..> DatabaseManager : provides
```

#### 3.12 Example 12
```mermaid
classDiagram
    direction TB

    class ManagerInterface {
        <<Interface>>
    }

    class AbstractManager {
        <<Abstract>>
    }

    class ServiceContainer {
        +resolve(name)
    }
    class HttpManager
    class DatabaseManager

    ManagerInterface <|.. AbstractManager
    AbstractManager <|-- ServiceContainer
    AbstractManager <|-- HttpManager
    AbstractManager <|-- DatabaseManager

    ServiceContainer ..> HttpManager : provides
    ServiceContainer ..> DatabaseManager : provides
```

#### 3.13 Example: Association Between Classes
```mermaid
classDiagram
  direction LR
  HttpFactory "1" -- "0..*" HttpObject : holds/caches
```

#### 3.14 Example: Composition Relationship

*A part cannot exist independently of the whole*

```mermaid
classDiagram
  HttpFactory "1" *-- "1" HttpRequestObject : owns
```

#### 3.15 Example: Factory as a Gateway

*Ownership Chain composition and Access Path dependency*

```mermaid
classDiagram
    direction LR

    Application "1" *-- "1" HttpFactory : owns
    HttpFactory "1" *-- "1" HttpRequestObject : owns & manages
    
    Application ..> HttpRequestObject : accesses via factory
```

#### 3.16 Example: Factory Managed by a DI Container

- *Application to Container: Composition*
- *Container to HttpFactory: Dependency (Provides)*
- *HttpFactory to HttpReqObj: Composition*
- *Application to HttpFactory: Dependency (Resolves)*

```mermaid
classDiagram
    direction LR

    Application "1" *-- "1" DIContainer : owns
    DIContainer ..> HttpFactory : provides / manages
    HttpFactory "1" *-- "1" HttpRequestObject : owns

    Application ..> HttpFactory : resolves
```

#### 3.17 Example: Gateway Architecture

*Gateway Architecture*

```mermaid
classDiagram
    direction LR

    Application "1" *-- "1" DIManager : owns
    DIManager "1" *-- "1" ServiceContainer : owns
    ServiceContainer o-- "*" Service : provides
    
    Application ..> Service : resolves via DIManager
```

#### 3.18: Provides multiple with interface
```mermaid
classDiagram
    class ServiceContainer
    interface IService

    class Service A {<<Service>>}
    class Service B {<<Service>>}

    IService <|.. ServiceA
    IService <|.. ServiceB

    ServiceContainer o-- "*" IService: provides
```

#### 3.19 Example: Composition and Creation and Management

```mermaid
  classDiagram
    class CarFactory {
      -cars: Car[]
      +createCar(): Car
      +getCar(): Car
    }

    class Car {
      -model: string
      +drive()
    }

    CarFactory ..> Car : << create >>
```

---

## 4.0 Flowchart

```mermaid
graph TD
    subgraph "Phase 1: Registration"
        A[Start] --> B(Create RegistryManager);
        B --> C{Define Events/Routes};
        C --> D[app.registerEvent];
        D --> C;
    end

    subgraph "Phase 2: Execution"
        C --> E[Create EventManager];
        E --> F(Inject Registry into EventManager);
        F --> G[app.dispatch];
        G --> H[End];
    end
```