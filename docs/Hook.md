# Hook System

The Hook System provides a standardized mechanism to extend, customize, and intercept system behavior without modifying the core source code.

It is a core architectural component designed to keep the system stable, maintainable, and upgrade-safe, while still allowing deep customization through extensions.

---

## Overview

Hooks allow extensions to attach custom logic to predefined execution points in the system lifecycle.  
This approach avoids direct modification of core code and minimizes conflicts during upgrades.

---

## Purpose

The Hook System is designed to:

- Extend functionality without editing core code
- Enable clean integration for extensions
- Reduce upgrade and maintenance risks
- Provide a unified extension mechanism across frontend and backend

---

## How It Works

1. The core emits a hook event at a specific execution point
2. Extensions register listeners for that hook
3. Each listener may inspect or extend the behavior

## Hook Actions 

- HookAction::CREATE  
- HookAction::UPDATE 
- HookAction::DELETE
- HookAction::SHOW

## Hook Lifecycle

- HookPhase::VALIDATE 
- HookPhase::RESPONSE
- HookPhase::QUERY 

## Hook Timing

- HookAction::BEFORE  
- HookAction::ON 
- HookAction::AFTER

---

# Hook Capability Matrix

This matrix defines what combinations are allowed by architecture.

| Phase    | INDEX          | CREATE         | UPDATE         | DELETE         | SHOW           | SEARCH         |
| -------- | -------------- | -------------- | -------------- | -------------- | -------------- | -------------- |
| QUERY    | ON             | ❌             | ❌             | ❌             | ❌             | ❌             |
| VALIDATE | ON             | ON             | ON             | ON             | ON             | ❌             |
| RESPONSE | BEFORE / AFTER | BEFORE / AFTER | BEFORE / AFTER | BEFORE / AFTER | BEFORE / AFTER | BEFORE / AFTER |
| UI       | ON             | ❌             | ❌             | ❌             | ON             | ON             |

---

# Phase Rules



## QUERY
- Only supports `INDEX`
- Only supports `ON`
- Used to wrap or replace query builder logic

---

## VALIDATE

- Supports `INDEX`, `CREATE`, `UPDATE`, `DELETE`, `SHOW`
- Only supports `ON`
- Used to add validation rules

---

## RESPONSE
- Supports all standard actions
- Supports `BEFORE` and `AFTER`
- Used to inject business logic around use case execution

---

## UI
- Supports `INDEX`, `SHOW`, `SEARCH`
- Only supports `ON`
- Used to add UI payload before rendering

---

# Modules Supporting Hooks

Below is the list of modules currently implementing hook interception (scanned from `app/core`).

| Module | Actions | Phases | Timings |
|---|---|---|---|
| CategoryProduct | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| CustomInvoiceIn | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| CustomInvoiceOut | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Customer | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| CustomerGroup | `CREATE,DELETE,INDEX,SHOW,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| Inventory | `CREATE,INDEX,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| InventoryAdjustment | `CREATE,INDEX` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| InvoiceIn | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| InvoiceOut | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Order | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| OrderItem | `CREATE,DELETE,INDEX,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| OrderShipping | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Overview | `CREATE,INDEX` | `RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| Permission | `CREATE,INDEX,SHOW` | `RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| PermissionGroup | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| PermissionGroupUser | `CREATE,DELETE,INDEX,SHOW` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| PriceList | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Product | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Purchase | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| PurchaseItem | `CREATE,DELETE,INDEX,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| Shipping | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| StockIn | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| StockMovementIn | `CREATE,UPDATE,INDEX` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| StockMovementOut | `INDEX` | `QUERY` | `ON` |
| StockOut | `CREATE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| Supplier | `CREATE,DELETE,INDEX,SEARCH,SHOW,UPDATE` | `QUERY,RESPONSE,UI,VALIDATE` | `AFTER,BEFORE,ON` |
| User | `CREATE,DELETE,INDEX,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |
| Warehouse | `CREATE,DELETE,INDEX,SHOW,UPDATE` | `QUERY,RESPONSE,VALIDATE` | `AFTER,BEFORE,ON` |