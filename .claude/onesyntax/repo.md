project-root/
├── app/
│   ├── Foundation/               # Shared kernel, base classes, traits
│   │   ├── Entities/             # Base entities, models, value objects, shared domain logic
│   │   ├── UseCases/             # Base interactors, shared application services
│   │       ├── Repositories/     # Repository interfaces for shared entities
│   │   ├── Adapters/             # Base presenters, view models, framework-agnostic components
│   │   ├── IO/                   # Shared IO components
│   │       ├── Database/         # Common database concerns, repository implementations
│   │       ├── Http/             # Shared controllers, middleware, API resources
│   │       ├── Web/              # Common layouts, shared components, base templates
│   │       ├── GraphQL/          # Shared GraphQL components
│   │       └── ExternalServices/ # Shared service clients, integrations
│   │       ├── FoundationServiceProvider.php  # Core app-wide service provider
│   │   ├── Specs/                # Foundation specifications
│   │   └── Testing/              # Foundation testing support API
│   │
│   ├── Booking/                  # Booking domain module
│   │   ├── Entities/             # Domain entities, models, and business rules
│   │   ├── UseCases/             # Application business logic
│   │       ├── Repositories/     # Repository interfaces
│   │   ├── Adapters/             # Framework-agnostic interface adapters
│   │   ├── IO/                   # Frameworks, drivers, external services
│   │       ├── Database/         # Booking-specific database migrations, seeders, repositories
│   │       ├── Http/             # Booking-specific HTTP interfaces, controllers, resources
│   │       ├── Web/              # Booking-specific UI elements
│   │       ├── GraphQL/          # Booking-specific GraphQL components
│   │       └── ExternalServices/ # Booking-specific external services
│   │       ├── BookingServiceProvider.php      # Booking domain service provider
│   │   ├── Specs/                # Booking behavior specifications
│   │   └── Testing/              # Booking-specific testing utilities
│   │
│   ├── Payment/                  # Payment domain module (same structure)
│   └── User/                     # User domain module (same structure)
