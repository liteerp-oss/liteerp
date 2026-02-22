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

- `docker exec -it LiteERP-app bash` and next run `php artisan make:extension "Test Ext" Test`

At here we have `Test Ext` is name of extension, and Test is directory. Directory has rules name no using space and special character. After this action you can seen new extension on dashboard or at directory `./app/extensions`

## Rules 

- If you need do anything relate to core module then please use `service` and `usecase` no use `model`, please consider here <a href="../CONTRIBUTING.md">CONTRIBUTING</a>. Maybe you will seen a some place use `Model` of core module on `Extension Example` but it's old and in that we have not yet make this rule.

- And you shuold't use `AuthencationService` because this is verify, it don't need Extension.

# How to make new extension

LiteERP has support generate new Extension by command and this document will talk to you know how to make a extension to you wanna.

### Generate new extension 

To generate new extension you need run: 

    - 1. open terminal and run `docker exec -it LiteERP-app bash`
    - 2. Continue run `php artisan make:extension "Your extension name" Test. 
    
At here Test is directory of extension. After that you will see your extension appear at `extensions/Test`


### Dashboard menu?

This only way to you add new menu into dashboard. If you can add menu into dashboard then you has been knew how to hook working? Consider Extensions example to understand all, we has been created a lots of example Extension, please consider here <a href="../extension-examples/Hrm">HRM Extension</a>

#### How to register new menu? 

To display new menu on sidebar dashboard you need use hook to register new menu. Example:

        <?php

        namespace Extensions\FastMode\Hooks;

        use App\Supports\Hooks\HookContext;
        use App\Contracts\Hooks\HookInterface;
        use App\Supports\Hooks\HookAction;
        use App\Supports\Hooks\HookPhase;
        use App\Supports\Hooks\HookResult;
        use App\Supports\Hooks\HookTiming;
        use Core\Permission\Infrastructure\Helpers\PermissionNode;
        use Core\Permission\Infrastructure\Helpers\SupportUINav;
        use Core\Permission\Infrastructure\Helpers\UINavGroup;

        class AddNavMenu implements HookInterface
        {
            function __construct(
                private PermissionNode $permissionNode,
                private SupportUINav $supportUINav
            ) {}
            public static function supports(HookContext $context): bool
            {
                return $context->action === HookAction::INDEX
                    && $context->phase === HookPhase::RESPONSE
                    && $context->module === 'Permission'
                    && $context->timing === HookTiming::BEFORE;
            }

            public function handle(HookContext $context): HookResult
            {
                $this->permissionNode->setNode('fastmode')
                    ->setGroup("fastmode.title")
                    ->setPermission('index')
                    ->setPermission('create');
                $this->supportUINav->setData($context->payload['nav'])
                    ->addItem(UINavGroup::SYSTEM, [
                        'to'        => '/fastmode',
                        'link'      => null,
                        'icon'      => "bi bi-person-workspace",
                        'label'     => __("extension.fastmode::messages.nav"),
                        'ability'   => $this->permissionNode->getPermission("index"),
                    ]);
                return HookResult::pass([
                    ...$context->payload,
                    'permissions' => [
                        ...$context->payload['permissions'],
                        ...$this->permissionNode->compile()
                    ],
                    'nav' => $this->supportUINav->compile()
                ]);
            }
        }

#### Load 

You should add this class into provider to load into core, go to `Service Provider` your module and implement

    public function register()
    {
        //
        $this->app->tag(
            \Extensions\Hrm\Hooks\AddNavMenu::class,
            'liteerp.hooks'
        );
    }

Go to `Extension` menu on dashboard and active your extension then go to `Permission` at group admin click icon eye scroll to bottom you will see `FastMode`

### Database 

With database you can use like laravel but change `command line` to create migration file

    - php artisan extension:make-migration Weather create_weather_table 

At here `Weather` is directory Extension and `create_weather_table` is migration name and next: 

    - php artisan migrate 

Now you will seen your table appear on database and go to your extension folder also appear new file migration. LiteERP has support `phpmyadmin` you can visit here: 

    - http://localhost:3310/ 

And username and password on `.env` of root folder. And You need register migration file on `install.php` of extension, Example:

```php 
<?php

return [
    'install' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Build frontend assets',
                'risk' => 'low',
            ]
        ],

        'migrations' => [
            '2026_02_12_223808_create_fastmode_table.php',
        ],
    ],

    'uninstall' => [
        'commands' => [
            [
                'name' => 'app:npmbuild',
                'description' => 'Rebuild frontend after uninstall',
                'risk' => 'low',
            ]
        ],

        'migrations' => [
            '2026_02_12_223808_create_fastmode_table.php',
        ],
    ],
];
```



### How to register new router

Default if you don't need create a independent page, you only register <a href="../FE.md">React router</a> and add `Dashboard menu`. But if it is necessary, now we will create a new page and use Laravel router.


#### Middleware 

If you wanna use middleware, we have a some middleware default. We have two types token `personal token` and `business token`. `Personal token` is token login account and `business token` is token if access into business if that user has permission.

Account middlewares:

    - app.isLogged -> only check if user has logged
    - app.isAdmin -> check user role is admin, this role is system admin role

Business middlewares:

    - business.token -> check if has logged business 

And we also a middleware support language:

    - app.language -> use App::setLocale if header has `App-Language`

And a middleware groups is `business`, it is two middlewares necessary for `business` 

    - app.language
    - business.token

If you use API then you only need implement `business` middleware, because only user logged and got approved permission that business by manager has `business token`.

## Create notification 

With extension shouldn't use as module to trigger create notification, you should implement `usecase` of Notification module:

### Use case namespace: 

    - namespace Core\Notifications\Application\UseCases;

#### class:

    - CreateNotification

### Adapter namespace: 

    - namespace Core\Notifications\Application\DTOs;

#### class:

    - CreateNotificationRequest

#### Example 

You can use easy way if you can't implement as injection class: 

    $createNotification = app(CreateNotification::class);
    $createNotification->handle( 
        CreateNotificationRequest::fromArray(
            // your data 
        )
    );

## Send email 

To send email you can use `SendMailJob`, example: 

    SendMailJob::dispatch($user_id,$subject,$message,$link)

We have params:

    - $user_id: (int) is id of user receive email 
    - $subject: (string) is subject will send  
    - $message: (string) is message send,
    - $link: this is link to user take action

If you wanna use muitiple languages then you need implement more one step.

    public function __construct(private UserService $userService) {}

    $user = $this->userService->findById($user_id); 

    $message = __('Test language',[],$user->lang);


## Consider Hook

Please consider hook document to know what can you do?

<a href="./Hook.md"> Hook Document </a>

## Public your extension 

Currently we have not marketplace, so you need copy extension into folder `extension-examples` and push on your branch as rule.

But before you publish please consider rule for contributing <a href="../CONTRIBUTING.md">CONTRIBUTING</a>

## 📄 License

This project is licensed under the **MIT License**.

You are free to use, modify, and distribute these examples for **personal or commercial use**.

---

## 🌍 About LiteERP

LiteERP is an **open-source ERP platform** focused on flexibility, extensibility, and long-term maintainability.

Extensions are the core of LiteERP’s customization strategy.

Learn more:
https://github.com/liteerp-oss