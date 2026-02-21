# FastMode

LiteERP extension.

---

## 🚀 Overview

**FastMode** is an extension for LiteERP designed for small and streamlined businesses that operate without multiple internal departments.

In many SMEs, a single operator handles:

- Creating sales orders  
- Issuing invoices  
- Generating delivery tickets  
- Processing shipments  

FastMode simplifies this workflow by automating internal approval steps while still preserving full accounting data integrity.

As businesses grow and add dedicated accounting departments, FastMode can be safely removed to restore the standard approval workflow without affecting existing data.

---

## ✨ Features

- ✅ Automatically approve business invoices upon creation  
- ✅ Skip manual accounting approval steps  
- ✅ Allow immediate delivery ticket creation  
- ✅ Configurable default invoice payment status  
- ✅ Maintain full accounting records  
- ✅ Easily removable when transitioning to structured accounting workflows  

> FastMode does **not** remove accounting data.  
> It only automates approval workflows to reduce operational friction.

---

## ⚙️ How It Works

When FastMode is enabled:

1. A sales order is created.
2. The related business invoice is automatically approved.
3. Delivery tickets can be generated immediately.
4. Invoice payment status is applied based on FastMode settings.

This enables a faster operational flow without breaking accounting structure.

When the business expands and introduces formal accounting controls:

- Simply disable or uninstall FastMode.
- The default LiteERP approval workflow is restored.
- All historical accounting data remains intact.

---

## 🛠 Configuration

FastMode provides configurable options inside its settings panel:

- Default invoice payment status  
- Automatic approval behavior  

Administrators can adjust these settings according to their operational needs.

---

## 🧩 Hooks

FastMode integrates into LiteERP via the hook system.  
It does not modify any core modules.

Registered hook behaviors include:

- Auto-approve invoice workflow  
- Override default invoice approval state  
- Trigger delivery ticket creation logic  
- Modify invoice payment status during processing  

---

## 🏢 Suitable For

- Small retail businesses  
- Online sellers  
- Family-run companies  
- Startups in early operational stages  
- Businesses prioritizing speed over internal approval layers  

---

## 📌 Notes

- This extension does **not** modify LiteERP core modules.  
- All logic is implemented via LiteERP hook system.  
- FastMode can be enabled or disabled safely without affecting core accounting integrity.  
- When the company grows and requires structured accounting review, simply remove FastMode to restore full approval control.
