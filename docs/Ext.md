## 🧪 How to install 

1. Go to **LiteERP → Extensions menu**

2. Upload the `.zip` file

3. LiteERP will automatically:
- Validate the extension
- Extract files
- Register the extension
- Run required setup logic

No manual configuration is required.

---

## 🧪 Usage Notes

- Each ZIP file should contain **only one extension**
- Do not rename internal extension files unless you understand the lifecycle
- Extensions can be enabled, disabled, or removed via the LiteERP UI
- Core updates will not overwrite installed extensions
- name ZIP file is name directory `example with directory name is Test then it will be Test.zip`
---

## 👥 Who Is This For?

- LiteERP developers
- ERP integrators
- System implementers
- Developers building custom business workflows
- Teams delivering ERP solutions for SMEs

---

## 🤝 Contributing

Contributions are welcome.

You can:
- Add new example extensions
- Improve existing extensions
- Fix bugs or enhance documentation

How to contribute:
1. Fork this repository
2. Add or improve an extension
3. Submit a Pull Request

---

## Make new extension 

- `docker exec -it LiteERP-8.3 bash` and next run `php artisan make:extension "Test Ext" Test`

At here we have `Test Ext` is name of extension, and Test is directory. Directory has rules name no using space and special character. After this action you can seen new extension on dashboard or at directory `./app/extensions`

## Rules 

- If you need do anything relate to core module then please use `service` no reuse `usecase` and `model`. Maybe you will seen a some place use `Model` of core module on `Extension Example` but it's old and in that we have not yet make this rule.

## 📄 License

This project is licensed under the **MIT License**.

You are free to use, modify, and distribute these examples for **personal or commercial use**.

---

## 🌍 About LiteERP

LiteERP is an **open-source ERP platform** focused on flexibility, extensibility, and long-term maintainability.

Extensions are the core of LiteERP’s customization strategy.

Learn more:
https://github.com/liteerp-oss