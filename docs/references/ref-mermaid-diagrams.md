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