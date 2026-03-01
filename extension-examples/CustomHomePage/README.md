# CustomHomePage

LiteERP extension.

## Description
CustomHomePage is a LiteERP extension that demonstrates how to customize the Home Page UI without modifying any core modules.

This extension serves as a practical example and guideline for developers who want to:
- Override or extend Home Page layout
- Inject custom widgets or components
- Modify dashboard structure
- Add dynamic content to Home Page

All changes are implemented through the Hook system to ensure:
- Core safety
- Upgrade compatibility
- Clean separation of concerns

## Purpose
The main goal of this extension is educational and architectural:
- Show how to customize the Home Page properly
- Encourage extension-based customization instead of core modification
- Maintain long-term maintainability and scalability

## Hooks
This extension utilizes the LiteERP Hook system to:

- Intercept Home Page rendering phase
- Inject custom UI blocks
- Modify data before rendering (if needed)
- Extend layout structure dynamically

(See implementation for specific HookContext usage.)

## Structure
Typical structure:

- Registers hooks in Service Provider
- Uses HookContext with appropriate:
  - Action
  - Phase
  - Timing
- Injects view or UI components into Home Page

## Notes
- This extension does NOT modify any core files.
- Safe for system upgrades.
- Can be used as a template for building other UI customization extensions.
- Recommended approach for all UI overrides.

## Recommended Practice
Never modify core modules directly.

Instead:
1. Create a new extension
2. Register hooks properly
3. Inject UI or logic through the extension layer

This keeps the system clean, maintainable, and scalable.