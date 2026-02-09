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

## Module support hooks  

| Module           | Action                            | HookPhase              | Supported Hooks |
|------------------|-----------------------------------|------------------------|-----------------|
| Business Role    | Create/Update/Delete/Show/Index   | HookPhase::RESPONSE    | Before, After   |
| Business Role    | Index                             | HookPhase::UI          | Before, After   |
| CategoryProduct  | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| CategoryProduct  | Index                             | HookPhase::QUERY       | ON              |
| CategoryProduct  | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| CategoryProduct  | Search/Index/Show                 | HookPhase::UI          | ON              |
| Customer         | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| Customer         | Index                             | HookPhase::QUERY       | ON              |
| Customer         | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| Customer         | Search/Index/Show                 | HookPhase::UI          | ON              |
| CustomInvoiceIn  | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| CustomInvoiceIn  | Index                             | HookPhase::QUERY       | ON              |
| CustomInvoiceIn  | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| CustomInvoiceIn  | Search/Index/Show                 | HookPhase::UI          | ON              |
| CustomInvoiceOut | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| CustomInvoiceOut | Index                             | HookPhase::QUERY       | ON              |
| CustomInvoiceOut | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| CustomInvoiceOut | Search/Index/Show                 | HookPhase::UI          | ON              |
| InvoiceIn        | Update/Index/Show                 | HookPhase::RESPONSE    | Before, After   |
| InvoiceIn        | Index                             | HookPhase::QUERY       | ON              |
| InvoiceIn        | Update/Index/Show                 | HookPhase::VALIDATE    | ON              |
| InvoiceIn        | Search/Index/Show                 | HookPhase::UI          | ON              |
| InvoiceOut       | Update/Index/Show                 | HookPhase::RESPONSE    | Before, After   |
| InvoiceOut       | Index                             | HookPhase::QUERY       | ON              |
| InvoiceOut       | Update/Index/Show                 | HookPhase::VALIDATE    | ON              |
| InvoiceOut       | Search/Index/Show                 | HookPhase::UI          | ON              |
| Order            | Create/Update/Index/Show          | HookPhase::RESPONSE    | Before, After   |
| Order            | Index                             | HookPhase::QUERY       | ON              |
| Order            | Create/Update/Index/Show          | HookPhase::VALIDATE    | ON              |
| Order            | Search/Index/Show                 | HookPhase::UI          | ON              |
| OrderShipping    | Create/Update/Index/Show          | HookPhase::RESPONSE    | Before, After   |
| OrderShipping    | Index                             | HookPhase::QUERY       | ON              |
| OrderShipping    | Create/Update/Show                | HookPhase::VALIDATE    | ON              |
| OrderShipping    | Show                              | HookPhase::UI          | ON              |
| Overview         | Index/Update                      | HookPhase::RESPONSE    | Before, After   |
| PriceList        | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| PriceList        | Index                             | HookPhase::QUERY       | ON              |
| PriceList        | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| PriceList        | Search/Index/Show                 | HookPhase::UI          | ON              |
| Purchase         | Create/Update/Index/Show          | HookPhase::RESPONSE    | Before, After   |
| Purchase         | Index                             | HookPhase::QUERY       | ON              |
| Purchase         | Create/Update/Index/Show          | HookPhase::VALIDATE    | ON              |
| Purchase         | Search/Index/Show                 | HookPhase::UI          | ON              |
| Shipping         | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| Shipping         | Index                             | HookPhase::QUERY       | ON              |
| Shipping         | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| Shipping         | Search/Index/Show                 | HookPhase::UI          | ON              |
| StockIn          | Create/Update/Index/Show          | HookPhase::RESPONSE    | Before, After   |
| StockIn          | Index                             | HookPhase::QUERY       | ON              |
| StockIn          | Create/Update/Index/Show          | HookPhase::VALIDATE    | ON              |
| StockIn          | Search/Index/Show                 | HookPhase::UI          | ON              |
| StockOut         | Create/Update/Index/Show          | HookPhase::RESPONSE    | Before, After   |
| StockOut         | Index                             | HookPhase::QUERY       | ON              |
| StockOut         | Create/Update/Index/Show          | HookPhase::VALIDATE    | ON              |
| StockOut         | Search/Index/Show                 | HookPhase::UI          | ON              |
| Supplier         | Create/Update/Delete/Index/Show   | HookPhase::RESPONSE    | Before, After   |
| Supplier         | Index                             | HookPhase::QUERY       | ON              |
| Supplier         | Create/Update/Delete/Index/Show   | HookPhase::VALIDATE    | ON              |
| Supplier         | Search/Index/Show                 | HookPhase::UI          | ON              |

## Make extensions

    php artisan make:extension "My extension" MyExt 