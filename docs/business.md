# 📘 LiteERP – Business Logic & Operational Principles

## 1. Department-Based Permission Model

LiteERP does **not** use rigid, predefined department roles like traditional ERP systems.

Instead, LiteERP applies a **flexible Group Permission model**, allowing businesses to configure the system according to their real operational structure.

### Why Not Fixed Roles?

In reality:

- Some companies require a Department Manager to approve orders.
- Some companies require the Director to approve directly.
- In some businesses, Accounting both issues invoices and manages receivables.

If roles are hardcoded, the system forces businesses into a structure that may not match their workflow.

### LiteERP Philosophy

> Businesses must own and control how their operations are structured.

---

### How LiteERP Handles Permissions

Businesses can:

- Create groups such as: `Super Manager`, `Leader`, `Accounting`, `Sales`, `Warehouse`, etc.
- Configure permissions for each group.
- Assign employees to appropriate groups.

#### Example: Sales Group

**Allowed permissions:**
- Create sales orders
- Add customers
- Create shipping information

**Not allowed:**
- Approve orders
- Cancel approved orders
- Create warehouse export documents

Only users with the correct permissions will see the corresponding actions in the system.

---

## 2. Workflow in LiteERP

LiteERP supports multi-step workflows to ensure structured and controlled operations.

---

### 2.1 Sales Workflow

Create Sales Order  
→ Approve Order  
→ Create Invoice  
→ Approve Invoice  
→ Create Warehouse Export

The system ensures:

- No step can be skipped
- No user can approve without permission
- Every step is recorded in history

---

### 2.2 Purchase Workflow

Create Purchase Order  
→ Approve Order  
→ Create Invoice  
→ Receive Goods (Warehouse Import)

This helps businesses control:

- Who proposed the purchase
- Who approved it
- Whether goods have been received
- Whether invoices have been processed

---

## 3. Data Integrity Principle

LiteERP follows a strict principle:

> Confirmed data cannot be modified.

### After Approval

- Documents cannot be edited
- Workflow cannot move backward

### After Goods Are Received or Delivered

- Orders cannot be canceled
- Status cannot revert to previous stages

If mistakes occur, a new document must be created for correction.

### Why This Matters

✔ Prevents post-confirmation data manipulation  
✔ Ensures inventory and financial accuracy  
✔ Reduces internal fraud risks  
✔ Maintains system consistency  

---

## 4. Transparent Audit Logs

Throughout the process, LiteERP records:

- Who created the document
- Who modified it (before approval)
- Who approved it
- When actions were performed

This ensures:

- Traceability
- Clear responsibility
- Operational transparency

---

## 5. Not Dependent on Individuals

LiteERP assigns permissions to groups rather than specific individuals.

When personnel changes occur:

- Simply assign the new person to the appropriate group
- No need to redesign workflows

This ensures operational stability and continuity.

---

## 6. Structured Core, Flexible Configuration

LiteERP is built on a clear principle:

> The core must enforce data discipline and operational consistency.

However, structured does not mean rigid.

Businesses can:

- Customize group permissions
- Simplify workflows for small operations
- Expand workflows as the company grows

---

## Suitable for 1 Operator or 100 Operators

### Single-Operator Scenario

A business may:

- Create one group with full permissions
- Allow the same person to create → approve → export

The workflow order remains structured but without unnecessary complexity.

No forced department structure is required.

---

### Multi-Department Scenario (10–100 People)

Businesses may:

- Create separate groups for Sales, Leader, Accounting, Warehouse
- Assign step-based responsibilities
- Ensure accountability per department

As the company grows, simply add groups and configure permissions —  
no architectural changes required.

---

## 🎯 LiteERP Design Philosophy

LiteERP does not force businesses into a predefined structure.

Instead:

- The core maintains control and data integrity
- Businesses decide the level of operational complexity

You can operate simply when small.  
And as you grow, the system scales with you.