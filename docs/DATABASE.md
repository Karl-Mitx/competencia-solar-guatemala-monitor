# Modelo de datos de Solaris

La base de entrega usa MySQL o PostgreSQL. Las pruebas ejecutan las mismas migraciones en SQLite en memoria. La migración real está en `solaris/database/migrations/2026_09_11_100000_create_solar_domain_tables.php`.

```mermaid
erDiagram
    departments ||--o{ solar_farms : contains
    solar_farms ||--o{ farm_panel : installs
    solar_panels ||--o{ farm_panel : defines
    solar_farms ||--o{ generation_records : records
    generation_records ||--o| alerts : triggers
    solar_farms ||--o{ projections : forecasts
    departments {
        bigint id PK
        varchar code UK
        varchar name UK
        decimal latitude
        decimal longitude
        varchar color
    }
    solar_panels {
        bigint id PK
        varchar brand
        varchar model
        decimal nominal_power_kw
        boolean is_active
    }
    solar_farms {
        bigint id PK
        bigint department_id FK
        varchar name
        varchar location_name
        decimal latitude
        decimal longitude
        int families_count
        boolean is_active
        date commissioned_at
        text notes
    }
    farm_panel {
        bigint id PK
        bigint solar_farm_id FK
        bigint solar_panel_id FK
        int quantity
    }
    generation_records {
        bigint id PK
        bigint solar_farm_id FK
        date period
        decimal real_kwh
        decimal expected_kwh
    }
    alerts {
        bigint id PK
        bigint generation_record_id FK UK
        varchar status
        timestamp resolved_at
    }
    projections {
        bigint id PK
        bigint solar_farm_id FK
        date period
        decimal projected_kwh
        varchar method
        date training_through
        smallint sample_size
        timestamp generated_at
    }
```

Foreign keys restringen la eliminación de departamentos, paneles físicos y granjas con historial; la relación de instalación se elimina en cascada al eliminar una granja. Índices cubren periodo, estado y las claves únicas de modelo, granja-periodo y granja-proyección-periodo.

La capacidad no se guarda como una cifra manual: `EnergyService` suma `nominal_power_kw × pivot.quantity`. `GenerationService` actualiza una sola alerta por registro y cambia su estado según la regla de 80 %. `ProjectionService` conserva la primera predicción por granja y periodo.
