# Contributing to LiteERP

Thank you for your interest in contributing to **LiteERP** 🎉

LiteERP is a **lightweight, practical, and extensible open-source ERP**, built for developers and small to medium-sized businesses.

---

## 📌 Project Goals

* **Readable – maintainable – extensible** code
* Follow **Clean Architecture** and Laravel best practices
* Focus on **stability and real-world use cases**, not unnecessary complexity

---

## 🐞 Bug Reports

When creating an **Issue**, please include:

* A clear description of the bug
* Steps to reproduce the issue
* Expected result vs actual result
* PHP / Laravel / Database version
* Screenshots or logs (if available)

👉 Please **search existing issues** before opening a new one.

---

## ✨ Feature Requests

LiteERP prioritizes:

* **Common, real-world ERP features**
* Features that do not bloat the architecture
* Reusable and extensible solutions

When proposing a feature, please describe:

* The problem you are facing
* Your proposed solution
* Why this feature should be part of LiteERP

---

## 🔀 Pull Request (PR) Guidelines

### 1. No Composer Updates

- **Do NOT modify `composer.json` or `composer.lock`**
- Updating dependencies can introduce unexpected breaking changes.
- If dependency updates cause issues, the **entire system may stop working**.

❌ Any PR that updates Composer files **will be reverted** unless explicitly approved by core maintainers.

---

### 2. Commit Discipline

- **Do NOT use `git add .`**
- Only stage files that are **directly related** to your change.
- Avoid committing:
  - unrelated files
  - formatting noise
  - accidental changes

> Small, focused commits are mandatory.

---

### 3. Code Comments & Language

- **English only** for:
  - code comments
  - commit messages
  - pull request descriptions

LiteERP is a global project.  
Using local languages makes the codebase hard to understand and maintain.

---

### 4. Core Modules Must Not Be Changed

- **Core modules are immutable**
- Respect the project philosophy: **“Simple and Pure”**
- All complexity must be implemented in **Extensions**

Use:
- Hooks
- Events

❌ Do NOT:
- add logic to core modules
- modify core workflows
- expand core responsibilities

> **Core is a stable contract, not a feature layer.**

---

### 5. Correct Way to Reuse Core Data

When you need data from a core module (e.g. user list):

---

## Architecture Access Rules

To preserve domain integrity and maintain clean boundaries,  
the following rules apply **strictly to Core modules only**.

These rules are designed to protect the core domain model and its invariants.

Extensions are free to manage their own tables and internal logic,  
as long as they do not violate Core module boundaries.

---

### Scope of Enforcement

The restrictions below apply to:

- All Core modules
- All Core domain entities
- All Core business state transitions

For tables and models created by an Extension:

- The Extension has full control
- Direct model access is allowed within that Extension
- The Extension defines its own rules and invariants

However, Extensions must never bypass Core domain rules.

---

### ❌ Do NOT use `Model` directly (Core only)

Direct access to Core `Model` classes bypasses:

- Domain rules
- Business invariants
- Validation logic
- Application coordination
- Domain events

Core `Model` classes are data structures only.  
They must never be used directly to mutate business state.

---

### ✅ Read Operations (Core)

For read-only operations (no state mutation):

- You may use a `Service`
- You may use a `UseCase` (recommended for consistency)

Read operations do not affect domain invariants.

---

### ✅ Write Operations (Core)

All state-changing operations in Core modules must go through a `UseCase`.

This includes:

- Create
- Update
- Delete
- Approve / Reject
- Workflow transitions
- Any status change

The `UseCase` layer:

- Defines the application boundary
- Coordinates business flows
- Manages transaction scope
- Calls domain `Service` methods
- Ensures domain invariants are preserved

Direct calls to `Service` for state mutation in Core modules are strictly forbidden.

---

#### Read-only Joins

- You **may** join tables for **read-only queries**
- This must NOT:
  - modify data
  - bypass business rules
  - introduce write|update|delete logic

---

### 6. Branch Rules

We use the following branch naming conventions:

- `extensions/your-feature-name`
- `features/your-feature-name`
- `devops/your-feature-name`

Unauthorized use of `features/*` or `devops/*` branches may result in PR rejection.

### 7. Commit Messages

Use clear and meaningful messages:

```text
feat: add customer debt report
fix: resolve wrong total calculation
refactor: simplify report query
```

### 8. Testing

* Ensure existing features are not broken
* Add tests where possible

### 9. PR Description

A good PR should include:

* A short description of the changes
* Related issue(s), if any

---

## 🧑‍💻 How You Can Contribute

You can help LiteERP in many ways:

* 🐛 Reporting bugs
* ✨ Proposing new features
* 📝 Improving documentation
* 🔧 Fixing bugs or refactoring code
* 🌐 Adding or improving translations

---

## 🏗️ Architecture & Principles

LiteERP applies:

* Clean Architecture (Domain / Application / Infrastructure)
* Repository Pattern
* Event / Listener for critical business workflows

⛔ Please avoid:

* Tight coupling
* Complex queries inside Controllers
* Hard-coded business logic

---

## 🔒 Rule Enforcement

PRs that violate these rules may be:
- reverted
- requested for rework
- or rejected without debate

This is not personal.  
This is how LiteERP stays stable.


## 🌍 Internationalization

* Language files are located in `/core/{Module}/Infrastructure/lang`
* Config files are located in `/core/{Module}/Infrastructure/config`
* Do not hard-code text in backend or frontend

Please consider how to use use translation for frontend at <a href="./docs/FE.md">Frontend document</a>

---

## 🤝 Code of Conduct

* Be respectful and inclusive
* Keep discussions constructive
* No toxic behavior or personal attacks

---

## ❤️ Thank You

Every contribution, big or small, is greatly appreciated.

If you like LiteERP, please ⭐ the repository to support the project!

Happy coding 🚀
